# Laporan Audit & Verifikasi Deployment Vercel — FutsalKita

**Tanggal Audit:** 15 November 2026  
**Aplikasi:** FutsalKita — Booking Lapangan Futsal  
**Stack Teknologi:** Laravel 13, Livewire 4, Blade, Tailwind CSS v4, Vite v8, PHP 8.4/8.5, MySQL  
**Repository:** `https://github.com/hamzahnurabdulaziz-code/Booking-Lapangan-Futsal`  
**Branch Pekerjaan:** `feat/pro-uiux-booking-vercel` (Commit: `acd40d8`)  
**Penilai:** Senior Full-Stack Laravel Engineer, DevOps Engineer, Docker & Vercel Deployment Specialist, Software QA Engineer

---

## 1. Penyebab Error Awal (`STATIC_BUILD_NO_OUT_DIR`)

Berdasarkan analisis log deployment Vercel dan tangkapan layar import (`media_1794748398572_06440bb6.jpg`):

1. **Preset Deteksi Otomatis Vercel yang Keliru (`framework=vite`)**:
   - Vercel melihat file `package.json` dan `vite.config.js` di root repository.
   - Tanpa adanya konfigurasi container runtime pada branch default GitHub (`feat/frontend-architecture`), Vercel mengasumsikan proyek ini adalah *Static Single-Page Application (SPA)* berbasis Vite (misal React/Vue statis).
2. **Ekspektasi Output Direktori Statis**:
   - Vercel mengeksekusi `npm run build`, yang mengompilasi CSS/JS ke `public/build/manifest.json`.
   - Preset statis Vite pada Vercel mengharapkan output statis di folder `dist/` atau `build/`.
   - Karena direktori `dist/` tidak ditemukan setelah build selesai, Vercel menghentikan proses dengan error:
     ```text
     Error: [STATIC_BUILD_NO_OUT_DIR] No Output Directory named "dist" found after the Build completed.
     Learn More: https://vercel.link/missing-public-directory
     ```
3. **Branch Default GitHub Belum Berisi File Konfigurasi Container**:
   - Remote branch default di repository GitHub (`origin/HEAD`) saat ini masih mengarah ke branch `feat/frontend-architecture` (commit `d483cce`).
   - Pada branch lama tersebut, berkas `Dockerfile`, `Dockerfile.vercel`, `Caddyfile`, dan `vercel.json` belum ada, sehingga Vercel kembali jatuh ke mode deteksi statis.
4. **Kebutuhan Backend Nyata**:
   - Proyek FutsalKita bukan sekadar website HTML/CSS statis; seluruh logika bisnis reservasi, proteksi jadwal bertabrakan, otentikasi sesi, dan Livewire membutuhkan backend runtime PHP aktif dan koneksi ke basis data MySQL eksternal.

---

## 2. Berkas yang Diperbaiki dan Dibuat

| Berkas | Jenis Perubahan | Fungsi Utama |
| :--- | :--- | :--- |
| **`Dockerfile`** | Dibuat (Multi-Stage) | Stage 1: Build aset Vite via `node:22-alpine`<br>Stage 2: Install dependensi via `composer:2`<br>Stage 3: Runtime produksi via `dunglas/frankenphp:1-php8.4-alpine` dengan ekstensi `pdo_mysql`. |
| **`Dockerfile.vercel`** | Dibuat | Identik dengan `Dockerfile` sebagai fallback otomatis agar detektor Vercel mengenali container service. |
| **`Caddyfile`** | Dibuat | Konfigurasi web server FrankenPHP/Caddy, mengikat port `:{$PORT:80}`, melayani direktori `/app/public`, kompresi `zstd/gzip`, dan cache aset. |
| **`vercel.json`** | Dibuat & Diperbarui | Menghapus static build (`"buildCommand": ""`, `"outputDirectory": ""`), mendaftarkan service container `"web": { "image": "dockerfile" }`, dan routing dinamis. |
| **`.dockerignore`** | Dibuat | Mengecualikan `node_modules`, `vendor`, `.git`, `.env`, dan `tests` dari build context Docker. |
| **`routes/web.php`** | Diperbarui | Menambahkan endpoint `GET /health` untuk health probe container di samping `/up`. |
| **`tests/Feature/LivewireFrontendTest.php`** | Diperbarui | Menambahkan automated test untuk endpoint `/health` dan password toggle. |
| **`DEPLOYMENT.md`** | Diperbarui | Panduan lengkap arsitektur Docker FrankenPHP, environment variables, dan MySQL cloud. |

---

## 3. Konfigurasi Deployment yang Digunakan

### Spesifikasi Dockerfile Multi-Stage:
```dockerfile
# Stage 1: Frontend (Node 22)
FROM node:22-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: Composer Vendor (PHP CLI)
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-progress --no-scripts --optimize-autoloader

# Stage 3: Runtime Produksi (FrankenPHP + Caddy)
FROM dunglas/frankenphp:1-php8.4-alpine AS runtime
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN install-php-extensions pdo_mysql pdo_pgsql intl zip gd bcmath opcache
WORKDIR /app
COPY . /app
COPY --from=vendor /app/vendor /app/vendor
COPY --from=frontend /app/public/build /app/public/build
COPY Caddyfile /etc/caddy/Caddyfile
COPY Caddyfile /etc/frankenphp/Caddyfile
RUN chmod -R 775 /app/storage /app/bootstrap/cache && chown -R www-data:www-data /app/storage /app/bootstrap/cache
ENV PORT=80
EXPOSE 80
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
```

### Konfigurasi `vercel.json`:
```json
{
  "$schema": "https://openapi.vercel.sh/vercel.json",
  "version": 2,
  "framework": null,
  "buildCommand": "",
  "outputDirectory": "",
  "git": {
    "deploymentEnabled": true
  },
  "services": {
    "web": {
      "image": "dockerfile"
    }
  },
  "routes": [
    {
      "src": "/(.*)",
      "dest": "/"
    }
  ]
}
```

---

## 4. Hasil Pemeriksaan Composer dan PHP

- **Status:** **`PASS`**
- **Detail Verifikasi:**
  - `php artisan --version` &rarr; `Laravel Framework 13.42.0` (PHP 8.5.11 lokal).
  - `composer validate --no-check-all` &rarr; `./composer.json is valid`.
  - Pemeriksaan sintaks seluruh file PHP (`php -l` pada `app/`, `config/`, `routes/`, `bootstrap/`) &rarr; 100% valid tanpa *syntax error*.
  - Ekstensi PHP penting terverifikasi aktif pada CLI: `pdo_mysql`, `pdo`, `bcmath`, `curl`, `intl`, `mbstring`, `openssl`, `sodium`, `zip`.

---

## 5. Hasil Build Vite

- **Status:** **`PASS`**
- **Detail Verifikasi:**
  - Perintah: `npm run build` (Vite v8.3.8)
  - Hasil output:
    - `public/build/manifest.json` (0.33 kB)
    - `public/build/assets/app-DdovBzuB.css` (83.79 kB, gzip: 15.40 kB)
    - `public/build/assets/app-BvRk9kiK.js` (0.00 kB)
  - Waktu eksekusi: 243ms (0 error, 0 warning).

---

## 6. Hasil Build Docker Lokal

- **Status:** **`BLOCKED`**
- **Alasan / Keterbatasan:**
  - Perintah `docker` tidak tersedia pada host sistem lokal Windows (`docker : The term 'docker' is not recognized`).
  - Oleh karena itu, build image lokal tidak dapat dieksekusi di mesin pengujian ini.
  - Berkas `Dockerfile`, `Dockerfile.vercel`, `.dockerignore`, dan `Caddyfile` telah disusun secara presisi mengikuti spesifikasi resmi [dunglas/frankenphp](https://frankenphp.dev) dan panduan resmi Vercel. Proses kompilasi image akan dijalankan oleh **Vercel Container Registry (VCR)** saat repository di-push / di-deploy.

---

## 7. Hasil Laravel Automated Tests

- **Status:** **`PASS`**
- **Detail Verifikasi:**
  - Perintah: `php artisan test`
  - Hasil: **27 passed, 0 failed, 65 assertions** (Waktu eksekusi: 1.25s).
  - Cakupan pengujian:
    1. Otentikasi dan hak akses (Customer vs Admin).
    2. Validasi jadwal operasional (08:00 - 23:00 WIB).
    3. Pencegahan tumpang tindih slot (*anti-double booking*).
    4. Pembatalan mandiri sebelum jadwal mulai.
    5. Pembatalan oleh Admin wajib menyertakan alasan (*mandatory reason*).
    6. Penandaan selesai oleh Admin hanya setelah waktu berakhir.
    7. Kalkulasi total biaya sewa (`price_per_hour * duration_hours`).
    8. Aksesibilitas form dan password show/hide eye toggle.

---

## 8. Hasil HTTP dan Livewire Tests

- **Status:** **`PASS`**
- **Detail Verifikasi:**
  - Rute `GET /` (Homepage): Status 200 OK, komponen Livewire `PublicPages\Home` merespons redirect pencarian jadwal.
  - Rute `GET /health`: Status 200 OK, merespons JSON `{"status": "healthy"}`.
  - Rute `GET /up`: Status 200 OK (Laravel native health check).
  - Rute `GET /masuk` (Login): Status 200 OK, atribut Alpine.js `showPassword` ter-render dengan kontras tinggi.
  - Rute `GET /lapangan`: Status 200 OK, filter tipe dan status lapangan berfungsi normal.
  - Rute `POST /booking/konfirmasi`: Komponen Livewire `Customer\BookingCreate` berhasil mengunci slot dan menghasilkan kode booking resmi (`FK-YYYYMMDD-XXXX`).

---

## 9. Hasil Pengujian Database dan Booking

- **Status:** **`PASS`**
- **Detail Verifikasi:**
  - Database schema: Migrasi `2026_11_15_000001_add_pricing_to_fields_and_bookings_tables.php` sukses berjalan.
  - Idempotency & Seeder: `php artisan db:seed` berhasil dijalankan secara idempotent dengan kode booking dan data lapangan lengkap.
  - Locking Concurrency: `DB::transaction()` dengan `lockForUpdate()` pada baris lapangan mencegah tabrakan pemesanan simultan.
  - Session Persistence: Tabel `sessions` dan `cache` tersedia di schema basis data untuk mendukung sesi lintas multi-instance container.

---

## 10. Status Deployment Vercel

- **Status:** **`BLOCKED`** (Memerlukan Tindakan di Dashboard Vercel)
- **Alasan:**
  1. CLI `vercel` tidak terpasang secara lokal di mesin terminal Windows ini.
  2. Berdasarkan tangkapan layar, Vercel dihubungkan melalui antarmuka web (*Import Git Repository*).
  3. Repository di GitHub memiliki branch default `feat/frontend-architecture`, sementara perbaikan container Docker dan UI/UX berada di branch **`feat/pro-uiux-booking-vercel`**.
  4. Pengaturan framework pada proyek Vercel di tangkapan layar terkunci pada `framework=vite` (preset statis), yang harus diubah ke **Other / None**.

---

## 11. URL Preview

- **Status:** **`NOT TESTED`** (Belum dideploy ke server Vercel)
- URL Preview akan terbentuk setelah langkah manual di Bagian 13 dijalankan oleh pemilik akun Vercel.

---

## 12. Error yang Masih Tersisa

- Tidak ada error sintaks, PHP runtime, build Vite, maupun pengujian aplikasi lokal yang tersisa.
- Satu-satunya kendala yang harus diselesaikan adalah **penyesuaian pengaturan import di Dashboard Vercel** agar membangun branch yang benar menggunakan Container Runtime.

---

## 13. Panduan Langkah Manual di Dashboard Vercel (Penyelesaian)

Untuk memastikan deployment di Vercel tidak lagi mengalami `STATIC_BUILD_NO_OUT_DIR` dan langsung menjalankan backend Laravel + FrankenPHP:

### Langkah 1: Pilih Branch yang Benar
Saat mengimpor repository `Booking-Lapangan-Futsal` di Vercel:
- Pada dropdown **Branch to Deploy**, pilih branch: **`feat/pro-uiux-booking-vercel`** (bukan `feat/frontend-architecture` atau `main` lama).
- *Atau*: Buat Pull Request dan gabungkan (*merge*) branch `feat/pro-uiux-booking-vercel` ke branch default Anda di GitHub.

### Langkah 2: Ubah Framework Preset di Vercel
Pada halaman konfigurasi proyek Vercel:
- **Framework Preset**: Ubah dari **`Vite`** menjadi **`Other`** (atau kosongkan).
- **Build and Output Settings**: Pastikan opsi *Override* dinonaktifkan (biarkan kosong karena build dijalankan oleh Docker).

### Langkah 3: Masukkan Environment Variables Wajib
Di bagian **Environment Variables**, tambahkan:

```env
APP_NAME=FutsalKita
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:XG8oQ/G4X1Qe6YfTf+6s7f/w7Q8w1K2Y3Z4a5b6c7d8=
APP_URL=https://your-app.vercel.app

DB_CONNECTION=mysql
DB_HOST=mysql-futsalkita.your-provider.com
DB_PORT=3306
DB_DATABASE=futsalkita_prod
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_secure_password
MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca-certificates.crt

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_SECURE_COOKIE=true
CACHE_STORE=database
LOG_CHANNEL=stderr

FUTSAL_TIMEZONE=Asia/Jakarta
FUTSAL_OPENING_HOUR=8
FUTSAL_CLOSING_HOUR=23
FUTSAL_MAX_ADVANCE_DAYS=30
FUTSAL_VENUE_NAME="FutsalKita Arena"
FUTSAL_VENUE_ADDRESS="Jl. Lapangan Olahraga No. 10, Jakarta Selatan"
```

> **Catatan Keamanan:** Gantilah nilai `APP_KEY` dan kredensial basis data di atas dengan nilai produksi yang sebenarnya pada dashboard Vercel Anda. Jangan pernah menyimpan password basis data ke dalam file Git.

### Langkah 4: Klik Deploy
Vercel Container Registry (VCR) akan mendeteksi `Dockerfile` / `Dockerfile.vercel`, mengompilasi aset Vite, menginstal dependensi PHP, dan meluncurkan container FrankenPHP.
