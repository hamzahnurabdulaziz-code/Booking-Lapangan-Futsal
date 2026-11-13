# Kontrak Komunikasi Frontend – Backend — FutsalKita

Dokumen ini adalah kesepakatan formal kontrak data, aksi, dan penanganan error antara **Frontend (Anggota 1)** dan **Backend (Anggota 2)**, serta **Database (Anggota 3)**.

---

## 1. Arsitektur Komunikasi
- **Paradigma:** Full-stack Livewire v4 (berjalan di atas Laravel 13 & PHP 8.5).
- **Format Pertukaran:** Komponen Livewire reaktif terisolasi, tanpa perlu REST API endpoint terpisah.
- **Waktu & Timezone:**
  - Database menyimpan dalam **UTC**.
  - Frontend menampilkan dan memvalidasi dalam **Asia/Jakarta (WIB)**.
- **Alpine.js:** Memanfaatkan bundle terintegrasi bawaan Livewire; **tidak memasang Alpine eksternal ganda**.

---

## 2. Kontrak Properti & Aksi Komponen

### A. Komponen Daftar Lapangan (`App\Livewire\PublicPages\FieldList`)
- **Properti Input:**
  - `type` (`string`): `'all'`, `'indoor'`, atau `'outdoor'` (tersinkronisasi ke URL query).
  - `status` (`string`): `'active'` atau `'all'`.
- **Objek Lapangan (`Field`):**
  - `id` (`int`)
  - `name` (`string`)
  - `slug` (`string`)
  - `type` (`string`: `'indoor'` / `'outdoor'`)
  - `description` (`string|null`)
  - `facilities` (`array` JSON)
  - `image_path` (`string|null`)
  - `is_active` (`bool`)

---

### B. Komponen Pemilih Jadwal & Slot (`App\Livewire\PublicPages\FieldDetail`)
- **Properti Input / State:**
  - `field`: Model binding `App\Models\Field`.
  - `date` (`string` format `YYYY-MM-DD`): Tanggal main, default hari ini.
  - `hour` (`int|null`): Jam mulai yang dipilih (8–22).
  - `duration` (`int`): Durasi bermain (1, 2, atau 3 jam).
- **Struktur Objek Slot (`slots[]`):**
  ```php
  [
      'start_hour' => 19,
      'end_hour' => 21,
      'starts_at_local' => '2026-11-14 19:00:00',
      'ends_at_local' => '2026-11-14 21:00:00',
      'label' => '19:00 - 21:00',
      'is_available' => true, // false jika ada tabrakan/lewat/lapangan nonaktif
      'is_past' => false,
      'reason' => null, // 'Waktu telah lewat', 'Jadwal telah terisi', 'Lapangan sedang tidak aktif'
  ]
  ```
- **Aksi Livewire:**
  - `setDuration(int $hours)`: Mengubah durasi bermain dan mereset pemilihan jam jika perlu.
  - `selectSlot(int $startHour)`: Memilih jam mulai slot.
  - `proceedToBooking()`:
    - Jika belum login: Simpan payload ke session `intended_booking` lalu redirect ke rute `login`.
    - Jika sudah login: Redirect ke rute `customer.booking.create`.

---

### C. Komponen Konfirmasi & Pembuatan Booking (`App\Livewire\Customer\BookingCreate`)
- **Payload yang Diberikan:**
  - `fieldId` (`int`)
  - `selectedDate` (`string` format `YYYY-MM-DD`)
  - `startHour` (`int`)
  - `durationHours` (`int`, 1–3)
  - `teamName` (`string|null`, maks. 100 karakter)
  - `notes` (`string|null`, maks. 500 karakter)
  - `requestKey` (`string UUID` idempotensi unik)
- **Data yang Dihasilkan / Diverifikasi Server (Tidak Diterima dari Input Klien):**
  - `user_id`: Diambil dari `Auth::id()`.
  - `booking_code`: Dihasilkan server (`FK-YYYYMMDD-XXXX`).
  - `starts_at` & `ends_at`: Dikonversi ke UTC oleh server.
  - `status`: Selalu diawali dengan `'confirmed'`.
- **Aksi Livewire:**
  - `submitBooking()`:
    - Sukses: Mengisi properti `$confirmedBooking`, membersihkan session `intended_booking`, menampilkan state sukses.
    - Error Validasi / Konflik: Menangkap `ValidationException` dan menampilkan banner alert spesifik.

---

### D. Komponen Riwayat Booking Pelanggan (`App\Livewire\Customer\BookingIndex`)
- **Properti:**
  - `tab` (`string`): `'upcoming'`, `'completed'`, `'cancelled'` (tersinkronisasi ke URL).
  - `search` (`string`): Debounce pencarian kode booking.
  - `confirmingCancel` (`bool`): State modal pembatalan.
  - `bookingIdToCancel` (`int|null`): ID booking target.
- **Aksi Livewire:**
  - `promptCancel(int $bookingId)`: Membuka modal konfirmasi pembatalan.
  - `cancelBooking()`: Memanggil `BookingService::cancelBooking()` dengan otorisasi pemilik.
  - `closeCancelModal()`: Menutup modal pembatalan.

---

### E. Komponen Manajemen Admin (`App\Livewire\Admin\*`)
- **Field Management (`Admin\FieldManagement`):**
  - `createField()` / `editField(int $fieldId)`: Membuka modal form.
  - `save()`: Simpan/update data lapangan.
  - `toggleStatus(int $fieldId)`: Mengaktifkan/menonaktifkan lapangan.
- **Booking Management (`Admin\BookingManagement`):**
  - Filter: `fieldId`, `status`, `date`, `search`.
  - `promptCancel(int $bookingId)`: Membuka modal pembatalan admin.
  - `submitCancel()`: Wajib menyertakan `cancellationReason` (3–500 karakter).
  - `completeBooking(int $bookingId)`: Menandai status `'completed'`, hanya diperbolehkan jika waktu sekarang `>= ends_at`.

---

## 3. Penanganan Error & Kode Respons

| Field / Key Error | Pemicu | Pesan yang Ditampilkan |
|---|---|---|
| `conflict` | Jadwal bertabrakan dengan booking confirmed/completed lain | *Jadwal yang dipilih sudah terisi oleh pemesan lain. Silakan pilih jam atau lapangan lain.* |
| `selected_date` | Tanggal lampau atau > 30 hari ke depan | *Tanggal booking tidak boleh di masa lampau / Booking maksimal 30 hari ke depan.* |
| `start_hour` | Jam di luar operasional (08.00–23.00) atau waktu telah lewat | *Waktu mulai bermain harus di masa mendatang.* |
| `duration_hours` | Nilai < 1 atau > 3 | *Durasi booking harus antara 1 sampai 3 jam.* |
| `cancellation_reason` | Admin membatalkan tanpa alasan | *Admin wajib mengisi alasan pembatalan booking.* |
| `ends_at` | Menandai selesai sebelum waktu berakhir | *Booking hanya dapat ditandai selesai setelah waktu bermain berakhir.* |
| `field_id` | Lapangan nonaktif | *Lapangan ini sedang tidak aktif dan tidak dapat dipesan.* |
| `request_key` | Token sama dengan payload berbeda | *Permintaan dengan token yang sama memiliki data berbeda.* |

---

## 4. Integritas Data & Larangan Hard-Delete
- Pengguna maupun lapangan yang memiliki riwayat booking tidak boleh dihapus secara permanen (`RESTRICT` foreign key).
- Produksi tidak boleh diam-diam memakai data tiruan tanpa label jelas; data awal menggunakan seeder resmi `DatabaseSeeder`.
