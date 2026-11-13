<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Field;
use App\Models\User;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class FutsalBookingTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected User $admin;
    protected Field $activeField;
    protected Field $inactiveField;
    protected BookingService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create([
            'name' => 'Budi Pelanggan',
            'email' => 'budi@test.com',
            'phone' => '081234567890',
            'role' => UserRole::Customer,
        ]);

        $this->admin = User::factory()->admin()->create([
            'name' => 'Super Admin',
            'email' => 'admin@test.com',
            'phone' => '089999999999',
        ]);

        $this->activeField = Field::factory()->create([
            'name' => 'Lapangan Vinyl 1',
            'slug' => 'lapangan-vinyl-1',
            'type' => 'indoor',
            'is_active' => true,
        ]);

        $this->inactiveField = Field::factory()->inactive()->create([
            'name' => 'Lapangan Rusak 2',
            'slug' => 'lapangan-rusak-2',
            'type' => 'outdoor',
        ]);

        $this->service = app(BookingService::class);
    }

    /**
     * 1. Public Pages Response
     */
    public function test_public_pages_render_successfully(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('Jadwal Main Aman');
        $response->assertSee($this->activeField->name);

        $responseList = $this->get(route('fields.index'));
        $responseList->assertStatus(200);
        $responseList->assertSee($this->activeField->name);

        $responseShow = $this->get(route('fields.show', $this->activeField->slug));
        $responseShow->assertStatus(200);
        $responseShow->assertSee($this->activeField->name);
    }

    /**
     * 2. Operating Hours & Available Slots Calculation
     */
    public function test_slots_availability_respects_operating_hours(): void
    {
        $tz = config('futsal.timezone', 'Asia/Jakarta');
        $targetDate = Carbon::now($tz)->addDays(2)->toDateString();

        $slots = $this->service->getAvailableSlots($this->activeField->id, $targetDate, 1);

        // From 08:00 to 23:00 (15 hourly slots: 8,9,10,11,12,13,14,15,16,17,18,19,20,21,22)
        $this->assertCount(15, $slots);
        $this->assertEquals(8, $slots[0]['start_hour']);
        $this->assertEquals(22, $slots[14]['start_hour']);
        $this->assertTrue($slots[0]['is_available']);
    }

    /**
     * 3. Successful Booking Creation
     */
    public function test_user_can_create_a_valid_booking(): void
    {
        $tz = config('futsal.timezone', 'Asia/Jakarta');
        $targetDate = Carbon::now($tz)->addDays(3)->toDateString();

        $booking = $this->service->createBooking($this->customer, [
            'field_id' => $this->activeField->id,
            'selected_date' => $targetDate,
            'start_hour' => 10,
            'duration_hours' => 2,
            'team_name' => 'Garuda FC',
            'notes' => 'Siapkan rompi kuning.',
        ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'booking_code' => $booking->booking_code,
            'user_id' => $this->customer->id,
            'field_id' => $this->activeField->id,
            'status' => 'confirmed',
            'team_name' => 'Garuda FC',
        ]);

        $this->assertEquals(BookingStatus::Confirmed, $booking->status);
    }

    /**
     * 4. Consecutive Adjacent Slots Allowed
     */
    public function test_adjacent_non_overlapping_slots_are_allowed(): void
    {
        $tz = config('futsal.timezone', 'Asia/Jakarta');
        $targetDate = Carbon::now($tz)->addDays(4)->toDateString();

        // Booking 1: 09:00 - 10:00 WIB
        $booking1 = $this->service->createBooking($this->customer, [
            'field_id' => $this->activeField->id,
            'selected_date' => $targetDate,
            'start_hour' => 9,
            'duration_hours' => 1,
        ]);

        // Booking 2: 10:00 - 11:00 WIB (adjacent, touching boundary)
        $anotherUser = User::factory()->create();
        $booking2 = $this->service->createBooking($anotherUser, [
            'field_id' => $this->activeField->id,
            'selected_date' => $targetDate,
            'start_hour' => 10,
            'duration_hours' => 1,
        ]);

        $this->assertNotNull($booking1->id);
        $this->assertNotNull($booking2->id);
    }

    /**
     * 5. Overlapping Booking Collision Rejection
     */
    public function test_overlapping_booking_is_rejected(): void
    {
        $tz = config('futsal.timezone', 'Asia/Jakarta');
        $targetDate = Carbon::now($tz)->addDays(5)->toDateString();

        // First booking: 14:00 - 16:00 (2 hours)
        $this->service->createBooking($this->customer, [
            'field_id' => $this->activeField->id,
            'selected_date' => $targetDate,
            'start_hour' => 14,
            'duration_hours' => 2,
        ]);

        // Second booking attempting partial overlap: 15:00 - 16:00
        $this->expectException(ValidationException::class);

        $anotherUser = User::factory()->create();
        $this->service->createBooking($anotherUser, [
            'field_id' => $this->activeField->id,
            'selected_date' => $targetDate,
            'start_hour' => 15,
            'duration_hours' => 1,
        ]);
    }

    /**
     * 6. Same Time Slot on Different Fields Allowed
     */
    public function test_same_slot_on_different_field_is_allowed(): void
    {
        $tz = config('futsal.timezone', 'Asia/Jakarta');
        $targetDate = Carbon::now($tz)->addDays(5)->toDateString();

        $secondField = Field::factory()->create(['is_active' => true]);

        $b1 = $this->service->createBooking($this->customer, [
            'field_id' => $this->activeField->id,
            'selected_date' => $targetDate,
            'start_hour' => 18,
            'duration_hours' => 1,
        ]);

        $b2 = $this->service->createBooking($this->customer, [
            'field_id' => $secondField->id,
            'selected_date' => $targetDate,
            'start_hour' => 18,
            'duration_hours' => 1,
        ]);

        $this->assertNotNull($b1);
        $this->assertNotNull($b2);
    }

    /**
     * 7. Idempotency Token Retry Protection
     */
    public function test_idempotent_retry_returns_same_booking(): void
    {
        $tz = config('futsal.timezone', 'Asia/Jakarta');
        $targetDate = Carbon::now($tz)->addDays(6)->toDateString();
        $uuid = (string) Str::uuid();

        $firstAttempt = $this->service->createBooking($this->customer, [
            'field_id' => $this->activeField->id,
            'selected_date' => $targetDate,
            'start_hour' => 12,
            'duration_hours' => 1,
            'request_key' => $uuid,
        ]);

        // Identical retry with same request_key
        $secondAttempt = $this->service->createBooking($this->customer, [
            'field_id' => $this->activeField->id,
            'selected_date' => $targetDate,
            'start_hour' => 12,
            'duration_hours' => 1,
            'request_key' => $uuid,
        ]);

        $this->assertEquals($firstAttempt->id, $secondAttempt->id);
        $this->assertEquals($firstAttempt->booking_code, $secondAttempt->booking_code);
    }

    /**
     * 8. Inactive Field Cannot Be Booked
     */
    public function test_inactive_field_cannot_be_booked(): void
    {
        $tz = config('futsal.timezone', 'Asia/Jakarta');
        $targetDate = Carbon::now($tz)->addDays(7)->toDateString();

        $this->expectException(ValidationException::class);

        $this->service->createBooking($this->customer, [
            'field_id' => $this->inactiveField->id,
            'selected_date' => $targetDate,
            'start_hour' => 10,
            'duration_hours' => 1,
        ]);
    }

    /**
     * 9. Customer Cancellation Before Start
     */
    public function test_customer_can_cancel_own_booking_before_start(): void
    {
        $tz = config('futsal.timezone', 'Asia/Jakarta');
        $targetDate = Carbon::now($tz)->addDays(8)->toDateString();

        $booking = $this->service->createBooking($this->customer, [
            'field_id' => $this->activeField->id,
            'selected_date' => $targetDate,
            'start_hour' => 19,
            'duration_hours' => 2,
        ]);

        $cancelled = $this->service->cancelBooking($this->customer, $booking, 'Ada urusan mendadak');

        $this->assertEquals(BookingStatus::Cancelled, $cancelled->status);
        $this->assertNotNull($cancelled->cancelled_at);

        // Verify slot is available again for rebooking
        $slots = $this->service->getAvailableSlots($this->activeField->id, $targetDate, 2);
        $slot19 = collect($slots)->firstWhere('start_hour', 19);
        $this->assertTrue($slot19['is_available']);
    }

    /**
     * 10. Customer Cannot Cancel Other User's Booking
     */
    public function test_customer_cannot_cancel_another_users_booking(): void
    {
        $tz = config('futsal.timezone', 'Asia/Jakarta');
        $targetDate = Carbon::now($tz)->addDays(9)->toDateString();

        $booking = $this->service->createBooking($this->customer, [
            'field_id' => $this->activeField->id,
            'selected_date' => $targetDate,
            'start_hour' => 16,
            'duration_hours' => 1,
        ]);

        $anotherCustomer = User::factory()->create();

        $this->expectException(ValidationException::class);
        $this->service->cancelBooking($anotherCustomer, $booking);
    }

    /**
     * 11. Admin Cancellation Requires Reason
     */
    public function test_admin_cancellation_requires_mandatory_reason(): void
    {
        $tz = config('futsal.timezone', 'Asia/Jakarta');
        $targetDate = Carbon::now($tz)->addDays(10)->toDateString();

        $booking = $this->service->createBooking($this->customer, [
            'field_id' => $this->activeField->id,
            'selected_date' => $targetDate,
            'start_hour' => 14,
            'duration_hours' => 1,
        ]);

        // Expect failure when reason is empty
        $this->expectException(ValidationException::class);
        $this->service->cancelBooking($this->admin, $booking, '');
    }

    /**
     * 12. Admin Completing Past Booking
     */
    public function test_admin_can_mark_completed_only_after_ends_at(): void
    {
        // Past booking (starts 3 hours ago, ends 1 hour ago)
        $pastStart = Carbon::now('UTC')->subHours(3);
        $pastEnd = Carbon::now('UTC')->subHour();

        $pastBooking = Booking::create([
            'booking_code' => 'FK-PAST-TEST',
            'request_key' => (string) Str::uuid(),
            'user_id' => $this->customer->id,
            'field_id' => $this->activeField->id,
            'starts_at' => $pastStart,
            'ends_at' => $pastEnd,
            'status' => BookingStatus::Confirmed,
        ]);

        $completed = $this->service->completeBooking($this->admin, $pastBooking);
        $this->assertEquals(BookingStatus::Completed, $completed->status);
    }

    /**
     * 13. Admin Access Control (Customer cannot access admin route)
     */
    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->customer)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    /**
     * 14. Admin Can Access Admin Dashboard
     */
    public function test_admin_can_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Dashboard Ringkas FutsalKita');
    }
}
