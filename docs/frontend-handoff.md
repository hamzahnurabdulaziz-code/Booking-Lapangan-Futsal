# Catatan Serah Terima (Frontend Handoff) — FutsalKita

**Dari:** Anggota 1 (Senior Frontend Engineer)  
**Kepada:** Anggota 2 (Backend Engineer) & Anggota 3 (Database & Integrasi)  
**Proyek:** FutsalKita — Web Booking Lapangan Futsal  

---

## 1. Pekerjaan yang Telah Diselesaikan oleh Frontend

### A. Design System & Antarmuka Sesuai Referensi Visual
1. **Palet Warna & Tipografi:**
   - Ivory Background: `#F7F7F2`
   - Primary Accent: Futsal Green `#2F6B3D` (Hover `#245430`, Tint `#EBF3ED`)
   - Text Utama: Deep Dark `#17231B` (Muted `#58695C`)
   - Card Surfaces: White `#FFFFFF` dengan radius sudut `rounded-[24px]` (20–24px) dan bayangan halus (`shadow-sm`, `shadow-md`).
   - Font sans-serif terbaca bersih dengan fallback sistem.
2. **Hero & Quick Search Overlapping:**
   - Hero banner berlatar belakang futsal dengan gradient overlay gelap dan tipografi putih kontras tinggi.
   - Headline: *"Jadwal Main Aman, Tinggal Ajak Tim."*
   - Subtitle: *"Pilih lapangan dan waktu bermain. Simpan booking timmu dalam beberapa langkah."*
   - Form pencarian cepat putih dengan sudut melengkung di bawah hero: Lapangan, Tanggal, Jam Mulai, Durasi, dan tombol aksi "Cek Jadwal" dengan ikon panah.
3. **Komponen Halaman Publik:**
   - **Beranda (`Home`):** Hero, quick search slot, pilihan lapangan unggulan, 3 langkah cara booking, fasilitas arena lengkap, dan FAQ accordion.
   - **Daftar Lapangan (`FieldList`):** Grid kartu lapangan dengan chip filter tipe (semua/indoor/outdoor) dan status (hanya aktif/semua).
   - **Detail Lapangan & Kalender Slot (`FieldDetail`):** Pemilihan tanggal interaktif (hari ini s/d 30 hari ke depan), durasi (1–3 jam), grid slot interaktif (08.00–23.00 WIB) dengan indikasi tekstual dan visual (`Tersedia`, `Terisi`, `Lewat`, `Nonaktif`), serta panel ringkasan pemesanan melayang.
4. **Komponen Autentikasi (`Auth\Login`, `Auth\Register`):**
   - Form responsif dengan validasi ramah pengguna.
   - Fitur **Session Restore**: Tamu yang memilih jadwal di detail lapangan sebelum login akan otomatis dipulihkan pilihannya setelah login dan diarahkan langsung ke konfirmasi booking tanpa harus mengulang input.
   - Pilihan akun demo pengujian 1-klik (`admin@futsalkita.test` & `pelanggan@futsalkita.test`).
5. **Komponen Pelanggan (`Customer\BookingCreate`, `Customer\BookingIndex`, `Customer\BookingDetail`):**
   - Ringkasan pemesanan sebelum simpan, input nama tim (opsional maks 100 char), catatan khusus (opsional maks 500 char).
   - Halaman/state berhasil menampilkan kode booking resmi (`FK-YYYYMMDD-XXXX`).
   - Riwayat pemesanan dengan tab **Mendatang**, **Selesai**, dan **Dibatalkan**, pencarian kode booking, dan modal konfirmasi pembatalan.
6. **Komponen Admin (`Admin\Dashboard`, `Admin\FieldManagement`, `Admin\BookingManagement`):**
   - Dashboard ringkas dengan metrik lapangan aktif, booking hari ini, booking mendatang, dan booking selesai/batal.
   - Manajemen lapangan: Tambah/edit data lapangan, toggle status aktif/nonaktif.
   - Manajemen booking: Pencarian teks, filter lapangan, tanggal, dan status. Aksi pembatalan admin dengan **wajib mengisi alasan**, serta tombol "Selesaikan" hanya untuk booking yang telah lewat (`ends_at <= now()`).
7. **Responsivitas & Aksesibilitas:**
   - Telah diverifikasi pada lebar layar HP sempit (360px – 390px): form tersusun vertikal satu kolom, touch targets >= 44px, navigasi hamburger drawer, tanpa scroll horizontal yang rusak.
   - Aksesibilitas keyboard (Escape key menutup modal, focus ring terlihat, status jadwal dibedakan tidak hanya lewat warna, dukungan `prefers-reduced-motion`).

---

## 2. Struktur File Selesai

```text
app/
├── Enums/
│   ├── BookingStatus.php       # Confirmed, Completed, Cancelled (label & badge classes)
│   └── UserRole.php            # Customer, Admin
├── Http/
│   └── Middleware/
│       └── EnsureUserIsAdmin.php # Proteksi rute admin
├── Livewire/
│   ├── Admin/
│   │   ├── BookingManagement.php
│   │   ├── Dashboard.php
│   │   └── FieldManagement.php
│   ├── Auth/
│   │   ├── Login.php
│   │   └── Register.php
│   ├── Customer/
│   │   ├── BookingCreate.php
│   │   ├── BookingDetail.php
│   │   └── BookingIndex.php
│   └── PublicPages/
│       ├── FieldDetail.php
│       ├── FieldList.php
│       └── Home.php
├── Models/
│   ├── Booking.php             # Relasi, cast UTC -> Asia/Jakarta, canBeCancelledBy, canBeCompletedBy
│   ├── Field.php               # Relasi, facilities JSON, scopeActive
│   └── User.php                # Relasi, isAdmin, isCustomer
├── Policies/
│   └── BookingPolicy.php
└── Services/
    └── BookingService.php      # Concurrency row-locking, idempotency, interval conflict [start, end)

resources/
├── css/
│   └── app.css                 # Konfigurasi Tailwind v4 theme, palette, utility ring & reduced-motion
└── views/
    ├── layouts/
    │   └── app.blade.php       # Master layout responsif, navbar, flash alerts, footer
    └── livewire/
        ├── admin/
        ├── auth/
        ├── customer/
        └── public-pages/

public/images/
├── placeholder-field.svg
└── fields/
    ├── field-arena-d.svg
    ├── field-interlock-b.svg
    ├── field-outdoor-c.svg
    └── field-vinyl-a.svg

tests/Feature/
├── ExampleTest.php
├── FutsalBookingTest.php       # 14 skenario pengujian bisnis & konkurensi
└── LivewireFrontendTest.php    # 7 skenario pengujian interaksi komponen Livewire
```

---

## 3. Tugas Lanjutan Rekan Tim

### A. Untuk Anggota 2 (Backend Engineer)
1. **Penguatan Concurrency Locking di MySQL:**
   - Skema saat ini telah menyertakan `Field::where('id', $fieldId)->lockForUpdate()->first()` dalam `DB::transaction()`.
   - Pastikan pada environment deployment MySQL/InnoDB, isolation level transaksi menggunakan setidaknya `READ COMMITTED` atau `REPEATABLE READ`.
2. **Penyempurnaan Kebijakan Otorisasi:**
   - `BookingPolicy` telah didaftarkan. Anda dapat menambahkan granular policies jika nanti ada kebutuhan audit trail log aktivitas admin.

### B. Untuk Anggota 3 (Database & Integrasi)
1. **Pengujian Konkurensi Paralel MySQL:**
   - Sesuai PRD, pengujian SQLite tidak dapat menggantikan bukti race condition MySQL multi-connection.
   - Buat skrip integrasi CI atau command test yang menjalankan 2 proses PHP/MySQL secara simultan dengan interval waktu yang sama untuk membuktikan hanya 1 booking yang berhasil tersimpan.
2. **Data Seeder Tambahan:**
   - Seeder saat ini (`DatabaseSeeder`) telah menyertakan 4 lapangan dan 3 booking sampel. Rekan database dapat memperluas data untuk demonstrasi pengujian performa query.

---

## 4. Alur Kerja Kolaborasi GitHub (3 Anggota)

1. **Undangan Kolaborator:**
   - Pemilik repositori membuka `Settings` &rarr; `Collaborators` &rarr; `Add people`, undang username kedua rekan, dan rekan menerima undangan melalui email/GitHub.
2. **Aturan Branching:**
   - Branch `main` adalah branch produksi yang dilindungi. Dilarang `force-push` (`git push -f`) ke `main`.
   - Buat branch fitur kecil:
     - `feat/frontend-enhancements` (Anggota 1)
     - `feat/backend-locking` (Anggota 2)
     - `feat/database-ci` (Anggota 3)
3. **File Bersama yang Membutuhkan Konsensus PR:**
   - `routes/web.php`
   - `composer.json` & `composer.lock`
   - `package.json` & `package-lock.json`
   - `config/futsal.php`
   Setiap perubahan pada file bersama dilakukan via Pull Request dan di-review sebelum di-merge.
4. **Keamanan Database Bersama:**
   - Dilarang menjalankan `php artisan migrate:fresh` pada database staging/bersama yang sedang digunakan tim.
