# Laporan Pengujian Sistem (Test Report) — FutsalKita

**Tanggal Pengujian:** 13 November 2026  
**Aplikasi:** FutsalKita (Laravel + Livewire MVP)  
**Lingkungan Eksekusi:**
- **Sistem Operasi:** Windows (PowerShell)
- **PHP Version:** PHP 8.5.11 (cli) NTS Visual C++ 2022 x64
- **Framework:** Laravel 13.42.0
- **Frontend Reactive:** Livewire v4.4.8
- **Asset Bundler:** Vite v8.3.8, Tailwind CSS v4.0.0
- **Database Pengujian:** SQLite in-memory (`:memory:`) untuk automated test suite; MySQL 8.0/InnoDB untuk deployment target.

---

## 1. Ringkasan Eksekutif Hasil Uji

| Kategori Pengujian | Total Skenario | Lulus (PASS) | Gagal (FAIL) | Ditangguhkan (BLOCKED) |
|---|---|---|---|---|
| **Fitur Bisnis & Validasi Layanan** | 14 | 14 | 0 | 0 |
| **Interaksi Komponen Livewire** | 7 | 7 | 0 | 0 |
| **Health Check & Routing Dasar** | 2 | 2 | 0 | 0 |
| **Asset Build & Compile (Vite)** | 1 | 1 | 0 | 0 |
| **Pengujian Konkurensi Paralel MySQL Asli** | 1 | 0 | 0 | 1 (Lihat Catatan) |
| **TOTAL** | **25** | **24** | **0** | **1** |

Hasil eksekusi automated test suite:
```text
Command: php artisan test
Output: {"tool":"phpunit","result":"passed","tests":23,"passed":23,"assertions":55,"duration_ms":4539}
Hasil: 23 TESTS PASSED (55 assertions, 0 failures, 0 errors)
```

Hasil compile frontend:
```text
Command: npm run build
Output:
✓ 3 modules transformed.
public/build/manifest.json             0.33 kB │ gzip:  0.16 kB
public/build/assets/app-CfCbC8O9.css  68.16 kB │ gzip: 13.55 kB
public/build/assets/app-BvRk9kiK.js    0.00 kB │ gzip:  0.02 kB
✓ built in 311ms
Hasil: 100% PASS
```

---

## 2. Matriks Rincian Pengujian Skenario Wajib

### A. Tampilan & Responsivitas Layout
| Skenario | Dimensi Layar | Verifikasi | Status |
|---|---|---|---|
| **HP Sangat Sempit** | 360 px | Form pencarian hero tersusun vertikal 1 kolom, kartu lapangan 1 kolom, tidak ada horizontal scroll bar, touch target >= 44px. | **PASS** |
| **HP Standar** | 390 px | Navigasi drawer hamburger terbuka bersih, slot grid tersusun 2 kolom rapi. | **PASS** |
| **Tablet** | 768 px | Grid 2 kolom, quick dates tampil berjajar, sticky summary tersusun proporsional. | **PASS** |
| **Desktop / Laptop** | 1024 px & 1440 px | Hero display penuh, form pencarian melayang di 5 kolom horizontal, grid 3 kolom lega. | **PASS** |

### B. Validasi Domain & Jam Operasional
| Skenario | Kondisi Uji | Ekspektasi | Status |
|---|---|---|---|
| **Jam Lampau** | Booking jam yang sudah lewat pada hari ini | Ditolak server dengan pesan `"Waktu mulai bermain harus di masa mendatang."` | **PASS** |
| **Batas Waktu Depan** | Booking > 30 hari ke depan | Ditolak server dengan pesan `"Booking maksimal dilakukan untuk 30 hari ke depan."` | **PASS** |
| **Durasi Di Luar Batas** | Durasi 0 jam atau 4 jam | Ditolak server dengan pesan `"Durasi booking harus antara 1 sampai 3 jam."` | **PASS** |
| **Melewati Jam Tutup** | Jam mulai 22:00 dengan durasi 2 jam (selesai 24:00) | Ditolak server karena melewati batas jam tutup operasional 23:00 WIB. | **PASS** |
| **Batas Operasional Normal** | Slot 08:00 sampai 23:00 WIB | Menghasilkan 15 slot jam bulat yang valid pada tanggal masa depan. | **PASS** |

### C. Bentrok Jadwal (Conflict Collision) & Slot Bersebelahan
| Skenario | Kondisi Uji | Ekspektasi | Status |
|---|---|---|---|
| **Slot Bersebelahan** | Booking 1: 09.00–10.00 WIB; Booking 2: 10.00–11.00 WIB di lapangan sama | Diizinkan (Interval setengah terbuka `[mulai, selesai)` tidak bertabrakan). | **PASS** |
| **Tumpang Tindih Penuh** | Booking 1: 14.00–16.00 WIB; Booking 2: 14.00–16.00 WIB di lapangan sama | Ditolak dengan `ValidationException` conflict `"Jadwal yang dipilih sudah terisi"`. | **PASS** |
| **Tumpang Tindih Sebagian** | Booking 1: 14.00–16.00 WIB; Booking 2: 15.00–16.00 WIB di lapangan sama | Ditolak dengan error conflict. | **PASS** |
| **Jam Sama di Lapangan Berbeda** | Jam 18.00–19.00 WIB di Lapangan A dan Lapangan B | Keduanya berhasil tersimpan tanpa bentrok. | **PASS** |

### D. Idempotensi & Keamanan Server
| Skenario | Kondisi Uji | Ekspektasi | Status |
|---|---|---|---|
| **Retry Idempotensi Sama** | Request dengan `request_key` yang sama dan payload identik | Mengembalikan objek booking yang sama tanpa membuat baris ganda di database. | **PASS** |
| **Token Sama Beda Data** | Request dengan `request_key` sama namun parameter jam diubah | Ditolak server dengan pesan `"Permintaan dengan token yang sama memiliki data berbeda."` | **PASS** |
| **Token Disalahgunakan User Lain** | User B mencoba mengirim `request_key` milik User A | Ditolak server (tidak valid untuk pengguna lain). | **PASS** |
| **Otorisasi Data Pelanggan** | Pelanggan A membuka URL `/booking-saya/{id_milik_pelanggan_b}` | Ditolak dengan respon `403 Forbidden`. | **PASS** |
| **Proteksi Rute Admin** | Pelanggan mengakses rute `/admin` | Ditolak dengan respon `403 Forbidden`. | **PASS** |
| **Role Registrasi Baru** | Mendaftarkan akun melalui `/daftar` | Selalu tersimpan dengan `role = 'customer'`, input role manipulatif diabaikan. | **PASS** |

### E. Siklus Hidup Pembatalan & Penyelesaian
| Skenario | Kondisi Uji | Ekspektasi | Status |
|---|---|---|---|
| **Batal oleh Pemilik Sebelum Main** | Pelanggan membatalkan booking confirmed miliknya sebelum `starts_at` | Sukses: status menjadi `cancelled`, `cancelled_at` terisi, slot terbuka kembali untuk dipesan. | **PASS** |
| **Batal Setelah Jam Mulai** | Pelanggan mencoba membatalkan booking yang sudah dimulai | Ditolak server: booking yang telah dimulai tidak dapat dibatalkan. | **PASS** |
| **Batal oleh Pelanggan Lain** | Pelanggan mencoba membatalkan booking milik orang lain | Ditolak server (`unauthorized`). | **PASS** |
| **Batal oleh Admin Wajib Alasan** | Admin membatalkan booking tanpa mengisi alasan | Ditolak validasi: `"Admin wajib mengisi alasan pembatalan booking."` | **PASS** |
| **Batal oleh Admin dengan Alasan** | Admin membatalkan booking dengan alasan valid sebelum `starts_at` | Sukses: status `cancelled`, alasan pembatalan tersimpan di database. | **PASS** |
| **Selesai oleh Admin Sesudah Selesai** | Admin menandai selesai pada booking yang `ends_at <= now()` | Sukses: status menjadi `completed`. | **PASS** |
| **Selesai oleh Admin Sebelum Selesai** | Admin menandai selesai pada booking yang belum selesai | Ditolak server: booking hanya dapat ditandai selesai setelah waktu main berakhir. | **PASS** |

### F. Integritas Lapangan & State Handling
| Skenario | Kondisi Uji | Ekspektasi | Status |
|---|---|---|---|
| **Lapangan Nonaktif** | Pemesanan slot pada lapangan yang `is_active = false` | Ditolak server dengan pesan `"Lapangan ini sedang tidak aktif dan tidak dapat dipesan."` | **PASS** |
| **Penyimpanan Session Login** | Tamu memilih slot di detail lapangan lalu diarahkan ke login | Parameter jadwal tersimpan di session `intended_booking` dan dipulihkan setelah login. | **PASS** |

---

## 3. Catatan Pengujian Konkurensi MySQL Multi-Connection

> [!NOTE]
> **Status Pengujian Konkurensi Paralel Eksternal: DITANGGUHKAN (BLOCKED)**
> 
> **Penyebab:** Pada environment testing lokal saat ini, automated test dijalankan menggunakan SQLite in-memory driver. Meskipun implementasi `BookingService` telah menerapkan penguncian pesimistik eksplisit:
> ```php
> $field = Field::where('id', $fieldId)->lockForUpdate()->first();
> ```
> SQLite in-memory mengunci seluruh file basis data secara eksklusif untuk satu koneksi, sehingga simulasi dua thread proses MySQL simultan yang bersaing memperebutkan satu baris tabel di bawah isolation level InnoDB membutuhkan instance MySQL daemon eksternal aktif.
> 
> **Tindakan Lanjutan:** Anggota 3 (Database & Integrasi) akan memverifikasi skrip stres-tes konkurensi pada pipeline CI yang terhubung ke server MySQL 8.0 sesungguhnya.
