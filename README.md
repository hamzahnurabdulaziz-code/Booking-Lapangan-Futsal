# FutsalKita — Web Booking Lapangan Futsal Real-Time

> **"Jadwal Main Aman, Tinggal Ajak Tim."**  
> Aplikasi website untuk melihat jadwal dan reservasi lapangan futsal secara real-time tanpa risiko jadwal bertabrakan.  
> *Final Project Kelompok 3 Orang (Fokus Implementasi: Frontend Architecture).*

---

## 🌟 Fitur Utama (Batas MVP)

- **Single Venue, Multi-Field:** Mengelola satu tempat arena futsal dengan beberapa tipe lapangan (`indoor` vinyl & interlock, `outdoor` rumput sintetis).
- **Bahasa & Waktu Lokal:** Seluruh UI dalam Bahasa Indonesia dan waktu Asia/Jakarta (WIB), dengan penyimpanan database UTC.
- **Murni Booking:** Tanpa modul pembayaran, invoice, checkout, atau tagihan.
- **Konfirmasi Instan:** Booking yang valid langsung berstatus **Terkonfirmasi (Confirmed)** tanpa persetujuan manual admin.
- **Jam Operasional:** 08.00 – 23.00 WIB setiap hari (terpusat di `config/futsal.php`).
- **Slot Waktu Fleksibel:** Jam mulai bulat, durasi 1 sampai 3 jam, selesai maksimal pukul 23.00 WIB di hari yang sama.
- **Jendela Reservasi:** Dibuka mulai hari ini hingga 30 hari ke depan.
- **Pembatalan Mandiri:** Pelanggan dapat membatalkan booking miliknya sendiri sebelum jam mulai bermain.
- **Panel Administrator:** Dashboard metrik, CRUD lapangan & toggle status aktif, filter pencarian booking, pembatalan dengan alasan wajib, dan penyelesaian booking yang telah lewat.
- **Proteksi Concurrency:** Penguncian baris pesimistik (`Field::lockForUpdate()`) di dalam database transaction untuk mencegah jadwal bertabrakan, serta token idempotensi `request_key`.

---

## 🎨 Desain & UI/UX

Mengacu pada referensi desain sporty dan modern:
- **Palet Warna:** Ivory `#F7F7F2`, Putih `#FFFFFF`, Futsal Green `#2F6B3D`, Deep Text `#17231B`.
- **Bentuk Visual:** Kartu beradius `rounded-[24px]` (20–24px), bayangan lembut, spasi lega, tipografi sans-serif bersih.
- **Hero & Floating Form:** Hero berlatar atmosfer futsal dengan gradient gelap, dipadukan form pencarian melayang dengan sudut membulat di bawah hero.
- **Mobile First:** Form dan kartu tersusun vertikal pada layar sempit 360–390px, touch target >= 44px, navigasi mobile drawer yang mudah diakses.
- **Aksesibilitas (a11y):** Status slot tidak dibedakan hanya oleh warna, modal mendukung keyboard dan tombol Escape, animasi menghormati `prefers-reduced-motion`.

---

## 🚀 Panduan Menjalankan Aplikasi

### 1. Prasyarat Lingkungan
- PHP >= 8.3 (Teruji pada PHP 8.5)
- Composer
- Node.js & npm

### 2. Instalasi Dependensi
```bash
# Clone repository
git clone <url-repository>
cd Booking-Lapangan-Futsal

# Install dependensi PHP
composer install

# Install dependensi frontend
npm install
```

### 3. Konfigurasi Environment & Database
```bash
# Salin konfigurasi environment jika belum ada
cp .env.example .env

# Generate application key
php artisan key:generate

# Jalankan migrasi dan isi data seeder awal
php artisan migrate:fresh --seed
```

### 4. Build Aset Frontend
```bash
# Build produksi
npm run build

# Atau jalankan dev server
npm run dev
```

### 5. Jalankan Web Server
```bash
php artisan serve
```
Akses aplikasi melalui peramban: `http://localhost:8000`

---

## 🔑 Akun Demo Pengujian

Aplikasi telah dilengkapi seeder akun untuk evaluasi:

| Peran (Role) | Alamat Email | Kata Sandi | Keterangan |
|---|---|---|---|
| **Administrator** | `admin@futsalkita.test` | `password` | Mengakses `/admin` untuk manajemen lapangan & booking |
| **Pelanggan** | `pelanggan@futsalkita.test` | `password` | Mengakses `/booking-saya` untuk riwayat & pembatalan |

---

## 🧪 Menjalankan Pengujian Otomatis

Seluruh 23 skenario automated tests (unit, feature, dan Livewire interaction) dapat dijalankan dengan:
```bash
php artisan test
```

Laporan hasil pengujian riil tercatat di [docs/TEST-REPORT.md](docs/TEST-REPORT.md).

---

## 📚 Dokumentasi Proyek

- [docs/PRD.md](docs/PRD.md) — Product Requirements Document lengkap
- [docs/flowcharts.md](docs/flowcharts.md) — Diagram alur Mermaid (booking, pembatalan, state machine)
- [docs/database.dbml](docs/database.dbml) — Spesifikasi DBML skema basis data
- [docs/frontend-contract.md](docs/frontend-contract.md) — Kontrak properti, data, dan aksi Frontend & Backend
- [docs/frontend-handoff.md](docs/frontend-handoff.md) — Catatan serah terima tugas dan panduan kolaborasi GitHub 3 anggota
- [docs/TEST-REPORT.md](docs/TEST-REPORT.md) — Laporan pengujian jujur dan komprehensif
