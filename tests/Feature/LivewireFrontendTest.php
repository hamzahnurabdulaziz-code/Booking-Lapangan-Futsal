<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Livewire\Admin\BookingManagement;
use App\Livewire\Admin\FieldManagement;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Customer\BookingCreate;
use App\Livewire\Customer\BookingIndex;
use App\Livewire\PublicPages\FieldDetail;
use App\Livewire\PublicPages\Home;
use App\Models\Booking;
use App\Models\Field;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class LivewireFrontendTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected User $admin;
    protected Field $field;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create([
            'role' => UserRole::Customer,
        ]);

        $this->admin = User::factory()->admin()->create();

        $this->field = Field::factory()->create([
            'name' => 'Lapangan A',
            'slug' => 'lapangan-a',
            'is_active' => true,
        ]);
    }

    public function test_home_component_redirects_on_check_schedule(): void
    {
        $tz = config('futsal.timezone', 'Asia/Jakarta');
        $date = Carbon::now($tz)->addDays(2)->toDateString();

        Livewire::test(Home::class)
            ->set('selectedFieldId', $this->field->id)
            ->set('selectedDate', $date)
            ->set('startHour', 19)
            ->set('durationHours', 2)
            ->call('checkSchedule')
            ->assertRedirect(route('fields.show', [
                'field' => $this->field->slug,
                'date' => $date,
                'hour' => 19,
                'duration' => 2,
            ]));
    }

    public function test_field_detail_preserves_choice_in_session_for_guests(): void
    {
        $tz = config('futsal.timezone', 'Asia/Jakarta');
        $date = Carbon::now($tz)->addDays(3)->toDateString();

        Livewire::test(FieldDetail::class, ['field' => $this->field])
            ->set('date', $date)
            ->set('duration', 2)
            ->call('selectSlot', 18)
            ->call('proceedToBooking')
            ->assertRedirect(route('login'));

        $this->assertEquals($this->field->id, session('intended_booking.field_id'));
        $this->assertEquals(18, session('intended_booking.start_hour'));
        $this->assertEquals(2, session('intended_booking.duration_hours'));
    }

    public function test_booking_create_submits_and_renders_success_booking_code(): void
    {
        $tz = config('futsal.timezone', 'Asia/Jakarta');
        $date = Carbon::now($tz)->addDays(4)->toDateString();

        session(['intended_booking' => [
            'field_id' => $this->field->id,
            'selected_date' => $date,
            'start_hour' => 20,
            'duration_hours' => 1,
        ]]);

        Livewire::actingAs($this->customer)
            ->test(BookingCreate::class)
            ->set('teamName', 'Persija Muda')
            ->set('notes', 'Mohon rompi orange.')
            ->call('submitBooking')
            ->assertHasNoErrors()
            ->assertSee('Booking Berhasil Disimpan!')
            ->assertSee('Persija Muda');

        $this->assertDatabaseHas('bookings', [
            'field_id' => $this->field->id,
            'user_id' => $this->customer->id,
            'team_name' => 'Persija Muda',
            'status' => 'confirmed',
        ]);
    }

    public function test_booking_index_allows_cancelling_upcoming_booking(): void
    {
        $tz = config('futsal.timezone', 'Asia/Jakarta');
        $start = Carbon::now($tz)->addDays(2)->setTime(15, 0, 0)->setTimezone('UTC');
        $end = $start->copy()->addHour();

        $booking = Booking::create([
            'booking_code' => 'FK-CANCEL-TEST',
            'request_key' => (string) Str::uuid(),
            'user_id' => $this->customer->id,
            'field_id' => $this->field->id,
            'starts_at' => $start,
            'ends_at' => $end,
            'status' => BookingStatus::Confirmed,
        ]);

        Livewire::actingAs($this->customer)
            ->test(BookingIndex::class)
            ->call('promptCancel', $booking->id)
            ->assertSet('confirmingCancel', true)
            ->call('cancelBooking')
            ->assertSet('confirmingCancel', false);

        $this->assertEquals(BookingStatus::Cancelled, $booking->fresh()->status);
    }

    public function test_admin_can_toggle_field_status(): void
    {
        Livewire::actingAs($this->admin)
            ->test(FieldManagement::class)
            ->call('toggleStatus', $this->field->id);

        $this->assertFalse($this->field->fresh()->is_active);

        Livewire::actingAs($this->admin)
            ->test(FieldManagement::class)
            ->call('toggleStatus', $this->field->id);

        $this->assertTrue($this->field->fresh()->is_active);
    }

    public function test_admin_booking_management_cancellation_with_reason(): void
    {
        $start = Carbon::now('UTC')->addDays(3)->setTime(10, 0, 0);
        $end = $start->copy()->addHour();

        $booking = Booking::create([
            'booking_code' => 'FK-ADM-CANCEL',
            'request_key' => (string) Str::uuid(),
            'user_id' => $this->customer->id,
            'field_id' => $this->field->id,
            'starts_at' => $start,
            'ends_at' => $end,
            'status' => BookingStatus::Confirmed,
        ]);

        Livewire::actingAs($this->admin)
            ->test(BookingManagement::class)
            ->call('promptCancel', $booking->id)
            ->set('cancellationReason', 'Perbaikan atap bocor')
            ->call('submitCancel')
            ->assertHasNoErrors();

        $fresh = $booking->fresh();
        $this->assertEquals(BookingStatus::Cancelled, $fresh->status);
        $this->assertEquals('Perbaikan atap bocor', $fresh->cancellation_reason);
    }

    public function test_register_creates_customer_and_redirects(): void
    {
        Livewire::test(Register::class)
            ->set('name', 'Joko Baru')
            ->set('email', 'joko@baru.test')
            ->set('phone', '081122334455')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasNoErrors()
            ->assertRedirect(route('customer.bookings'));

        $this->assertDatabaseHas('users', [
            'email' => 'joko@baru.test',
            'role' => 'customer',
        ]);
    }
}
