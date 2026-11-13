<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Field;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Default Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@futsalkita.test'],
            [
                'name' => 'Admin FutsalKita',
                'phone' => '081234567890',
                'role' => UserRole::Admin,
                'password' => Hash::make('password'),
            ]
        );

        // 2. Create Default Customer
        $customer = User::firstOrCreate(
            ['email' => 'pelanggan@futsalkita.test'],
            [
                'name' => 'Budi Santoso',
                'phone' => '089876543210',
                'role' => UserRole::Customer,
                'password' => Hash::make('password'),
            ]
        );

        // 3. Create Realistic Fields
        $fieldsData = [
            [
                'name' => 'Lapangan Vinyl Premium (A)',
                'slug' => 'lapangan-vinyl-premium-a',
                'type' => 'indoor',
                'description' => 'Lapangan indoor beralaskan vinyl standar kompetisi nasional dengan daya cengkeram optimal dan peredam benturan ramah sendi. Cocok untuk pertandingan kompetitif maupun latihan intensif.',
                'facilities' => [
                    'Lantai Vinyl Taraflex Standar FIFA',
                    'Pencahayaan LED 800 Lux Anti-Silau',
                    'Papan Skor Digital Elektronik',
                    'Tribun Penonton Berkapasitas 50 Orang',
                    'Ruang Ganti & Shower Air Hangat',
                    'Bola Futsal & Rompi Latihan Disediakan',
                ],
                'image_path' => 'images/fields/field-vinyl-a.svg',
                'is_active' => true,
            ],
            [
                'name' => 'Lapangan Interlock Pro (B)',
                'slug' => 'lapangan-interlock-pro-b',
                'type' => 'indoor',
                'description' => 'Lapangan futsal indoor beralaskan modular interlock polypropylene elastis tinggi. Menjamin pantulan bola konsisten dan sirkulasi udara sejuk untuk kenyamanan bertanding.',
                'facilities' => [
                    'Lantai Modular Interlock Futsal',
                    'Jaring Pengaman Keliling 360 Derajat',
                    'Papan Skor Digital',
                    'Area Istirahat Pemain & Bangku Cadangan',
                    'Ruang Ganti Berpendingin Ruangan (AC)',
                    'Dispenser Air Mineral Gratis',
                ],
                'image_path' => 'images/fields/field-interlock-b.svg',
                'is_active' => true,
            ],
            [
                'name' => 'Lapangan Rumput Sintetis Outdoor (C)',
                'slug' => 'lapangan-rumput-sintetis-c',
                'type' => 'outdoor',
                'description' => 'Lapangan outdoor dengan rumput sintetis monofilament tebal 50mm dengan infill rubber merata. Suasana bermain terbuka yang segar dengan lampu sorot bertenaga tinggi untuk main malam.',
                'facilities' => [
                    'Rumput Sintetis Monofilament 50mm',
                    '4 Tiang Lampu Sorot Malam Hari',
                    'Area Parkir Luas (Mobil & Motor)',
                    'Dekat Musholla & Kantin Minuman',
                    'Gazebo Santai Tepi Lapangan',
                ],
                'image_path' => 'images/fields/field-outdoor-c.svg',
                'is_active' => true,
            ],
            [
                'name' => 'Lapangan Mini Arena D (Nonaktif)',
                'slug' => 'lapangan-mini-arena-d',
                'type' => 'indoor',
                'description' => 'Lapangan mini latihan indoor yang saat ini sedang dalam masa peremajaan lantai dan pengecatan ulang berkala. Tidak dapat dipesan untuk sementara waktu.',
                'facilities' => [
                    'Lantai Vinyl Standar',
                    'Gawang Mini Latihan',
                ],
                'image_path' => 'images/fields/field-arena-d.svg',
                'is_active' => false,
            ],
        ];

        $createdFields = [];
        foreach ($fieldsData as $data) {
            $createdFields[$data['slug']] = Field::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }

        // 4. Create Sample Bookings for demonstration
        $tz = config('futsal.timezone', 'Asia/Jakarta');
        $fieldA = $createdFields['lapangan-vinyl-premium-a'];
        $fieldB = $createdFields['lapangan-interlock-pro-b'];

        // Sample 1: Upcoming Confirmed Booking tomorrow 19:00 - 21:00 WIB
        $tomorrow19Local = Carbon::now($tz)->addDay()->setTime(19, 0, 0);
        $tomorrow21Local = $tomorrow19Local->copy()->addHours(2);
        Booking::updateOrCreate(
            ['booking_code' => 'FK-' . $tomorrow19Local->format('Ymd') . '-A19'],
            [
                'request_key' => 'b1000000-0000-0000-0000-000000000001',
                'user_id' => $customer->id,
                'field_id' => $fieldA->id,
                'starts_at' => $tomorrow19Local->copy()->setTimezone('UTC'),
                'ends_at' => $tomorrow21Local->copy()->setTimezone('UTC'),
                'team_name' => 'Garuda Muda FC',
                'notes' => 'Tolong siapkan 10 rompi hijau dan bola standar match.',
                'status' => BookingStatus::Confirmed,
            ]
        );

        // Sample 2: Completed Booking 2 days ago 16:00 - 18:00 WIB
        $pastLocal = Carbon::now($tz)->subDays(2)->setTime(16, 0, 0);
        $pastEndLocal = $pastLocal->copy()->addHours(2);
        Booking::updateOrCreate(
            ['booking_code' => 'FK-' . $pastLocal->format('Ymd') . '-B16'],
            [
                'request_key' => 'b1000000-0000-0000-0000-000000000002',
                'user_id' => $customer->id,
                'field_id' => $fieldB->id,
                'starts_at' => $pastLocal->copy()->setTimezone('UTC'),
                'ends_at' => $pastEndLocal->copy()->setTimezone('UTC'),
                'team_name' => 'Rajawali Futsal Club',
                'notes' => 'Latihan rutin mingguan.',
                'status' => BookingStatus::Completed,
            ]
        );

        // Sample 3: Cancelled Booking
        $cancelLocal = Carbon::now($tz)->addDays(3)->setTime(14, 0, 0);
        $cancelEndLocal = $cancelLocal->copy()->addHour();
        Booking::updateOrCreate(
            ['booking_code' => 'FK-' . $cancelLocal->format('Ymd') . '-A14'],
            [
                'request_key' => 'b1000000-0000-0000-0000-000000000003',
                'user_id' => $customer->id,
                'field_id' => $fieldA->id,
                'starts_at' => $cancelLocal->copy()->setTimezone('UTC'),
                'ends_at' => $cancelEndLocal->copy()->setTimezone('UTC'),
                'team_name' => 'Bintang Timur Squad',
                'notes' => 'Batal karena jadwal bentrok dengan turnamen antar-kampus.',
                'status' => BookingStatus::Cancelled,
                'cancelled_at' => now(),
                'cancellation_reason' => 'Perubahan agenda internal tim.',
            ]
        );
    }
}
