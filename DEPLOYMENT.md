# Panduan Deployment Produksi Vercel (Docker + FrankenPHP) — FutsalKita

Dokumen ini menjelaskan konfigurasi deployment resmi aplikasi **FutsalKita** (Laravel 12 + Livewire 3 + Tailwind CSS + Vite) ke platform **Vercel** menggunakan **Docker** dan **FrankenPHP** berdasarkan panduan resmi [Vercel Knowledge Base: Deploy Laravel with Docker](https://vercel.com/kb/guide/laravel-php-with-docker).

---

## 1. Analisis Masalah: Error `STATIC_BUILD_NO_OUT_DIR`

### Penyebab Error
Ketika repository Laravel dihubungkan ke Vercel tanpa konfigurasi container, Vercel secara otomatis mendeteksi keberadaan file `package.json` dan mengasumsikan proyek ini adalah *Static Single-Page Application (SPA)* berbasis frontend (seperti React/Vue).
1. Vercel mengeksekusi perintah build default `npm run build`.
2. Vite menghasilkan aset statis ke direktori `public/build/`.
3. Vercel mencari folder output statis (seperti `dist` atau `build`).
4. Karena folder tersebut tidak ditemukan sesuai ekspektasi builder statis Vercel, proses deployment gagal dengan error:
   ```text
   No Output Directory named "..." found after the Build completed. (STATIC_BUILD_NO_OUT_DIR)
   ```
5. Lebih jauh lagi, meskipun aset statis disajikan, PHP dan Livewire tidak dapat dieksekusi di atas static hosting biasa karena membutuhkan runtime PHP aktif dan koneksi ke database.

### Solusi Resmi Vercel: Container-Backed Functions (Docker + FrankenPHP)
Vercel kini secara resmi mendukung **Container Services** via **FrankenPHP** (yang menggabungkan runtime modern PHP 8.4 dengan web server Caddy berkecepatan tinggi). Dengan arsitektur ini:
- `buildCommand` dan `outputDirectory` dikosongkan (`""`) pada `vercel.json` agar Vercel tidak lagi menjalankan static builder.
- Vercel Container Registry (VCR) membangun image menggunakan multi-stage `Dockerfile` (`node:22-alpine` -> `composer:2` -> `dunglas/frankenphp:1-php8.4-alpine`).
- Caddy menyajikan aset statis `public/build` secara langsung dengan caching header dan meneruskan seluruh permintaan dinamis ke `public/index.php`.

---

## 2. Berkas Konfigurasi Deployment

Repository ini dilengkapi berkas konfigurasi siap pakai:

1. **`Dockerfile` / `Dockerfile.vercel`**:
   - **Stage 1 (Frontend)**: Menggunakan `node:22-alpine` untuk menjalankan `npm ci` dan `npm run build` yang menghasilkan bundle CSS/JS di `public/build`.
   - **Stage 2 (Vendor)**: Menggunakan `composer:2` untuk menginstal dependensi produksi PHP secara teroptimasi (`--no-dev --optimize-autoloader --no-scripts`).
   - **Stage 3 (Runtime)**: Menggunakan `dunglas/frankenphp:1-php8.4-alpine` dengan ekstensi PHP penting:
     - `pdo_mysql` (koneksi MySQL eksternal dengan SSL)
     - `pdo_pgsql`
     - `intl`
     - `zip`
     - `gd`
     - `bcmath`
     - `opcache`
   - Mengatur izin hak akses direktori `storage` dan `bootstrap/cache` untuk user `www-data`.
   - Mengikat port dinamis Vercel via `ENV PORT=80` dan mengeksekusi Caddy server.

2. **`Caddyfile`**:
   Mengonfigurasi Caddy untuk mengikat port `$PORT`, menyajikan direktori `/app/public`, mengompresi respon (`zstd`, `gzip`), memberikan header cache immutable pada aset `/build/*`, dan meneruskan request dinamis ke PHP server.

3. **`vercel.json`**:
   Menonaktifkan static output build dan mendaftarkan container service:
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

4. **`.dockerignore`**:
   Mengecualikan `node_modules`, `vendor`, `.git`, tests, dan `.env` lokal agar build context Docker tetap ringan dan cepat.

---

## 3. Basis Data Eksternal & Sesi Persisten

Dalam arsitektur container serverless Vercel:
- Setiap instance container bersifat *stateless* (dapat bertambah saat trafik tinggi dan *scale-to-zero* saat tidak ada request).
- **SQLite lokal tidak dapat digunakan** karena data yang disimpan di dalam container akan hilang saat instance dimatikan atau berganti container.
- **Wajib menggunakan Database MySQL Cloud Eksternal** yang mendukung koneksi aman via SSL/TLS.

### Rekomendasi Provider MySQL Gratis / Managed:
1. **Aiven for MySQL** (Free trial, dukungan SSL penuh)
2. **Railway MySQL** ($5 free credit / bulan)
3. **AWS RDS / DigitalOcean Managed Database**
4. **Supabase / Neon** (Jika menggunakan PostgreSQL)

### Sesi Persisten Lintas Kontainer:
Untuk memastikan sesi login pelanggan dan admin tidak terputus saat request ditangani oleh instance container yang berbeda:
- Gunakan driver sesi: `SESSION_DRIVER=database` (memanfaatkan tabel `sessions` yang sudah ada di database MySQL) atau `SESSION_DRIVER=cookie` (dienkripsi di sisi browser).
- Konfigurasi cache: `CACHE_STORE=database` (memanfaatkan tabel `cache`).

---

## 4. Daftar Environment Variables di Vercel

Buka **Vercel Dashboard** &rarr; Pilih Proyek **FutsalKita** &rarr; **Settings** &rarr; **Environment Variables**. Tambahkan variabel-variabel berikut:

### Core Laravel Settings:
```env
APP_NAME=FutsalKita
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:GENERATE_KEY_DARI_PHP_ARTISAN_KEY_GENERATE
APP_URL=https://your-futsalkita-domain.vercel.app
APP_LOCALE=id
APP_FALLBACK_LOCALE=id
```

### Database Credentials (MySQL Eksternal):
```env
DB_CONNECTION=mysql
DB_HOST=your-database-host.aivencloud.com
DB_PORT=3306
DB_DATABASE=futsalkita_prod
DB_USERNAME=your_username
DB_PASSWORD=your_secure_password
MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca-certificates.crt
```

### Session & Cache (Multi-Instance Safe):
```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_SECURE_COOKIE=true
CACHE_STORE=database
LOG_CHANNEL=stderr
```

### FutsalKita Business Rules:
```env
FUTSAL_TIMEZONE=Asia/Jakarta
FUTSAL_OPENING_HOUR=8
FUTSAL_CLOSING_HOUR=23
FUTSAL_MAX_ADVANCE_DAYS=30
FUTSAL_VENUE_NAME="FutsalKita Arena"
FUTSAL_VENUE_ADDRESS="Jl. Lapangan Olahraga No. 10, Jakarta Selatan"
```

---

## 5. Langkah Migrasi & Seeding Awal Database Cloud

> **PENTING:** Jangan menjalankan `php artisan migrate` di dalam `Dockerfile` atau saat startup container secara otomatis, karena ketika Vercel meluncurkan beberapa instance sekaligus, migrasi dapat mengalami race condition / deadlock.

Jalankan migrasi dari mesin lokal Anda yang terhubung ke database cloud:
```bash
# 1. Pastikan .env lokal diarahkan ke DB Cloud
# 2. Jalankan migrasi dan seeding
php artisan migrate --force
php artisan db:seed --force
```

---

## 6. Prosedur Deployment ke Vercel

### Opsi A: Melalui GitHub Integration (Rekomendasi)
1. Push branch `feat/pro-uiux-booking-vercel` ke repository remote.
2. Buat Pull Request ke branch `main`.
3. Setelah merge ke `main`, Vercel akan otomatis mendeteksi `Dockerfile` / `Dockerfile.vercel` dan `vercel.json`.
4. Vercel Container Registry akan mem-build image multi-stage dan meluncurkan container.

### Opsi B: Melalui Vercel CLI
```bash
# Login ke akun Vercel
vercel login

# Hubungkan proyek
vercel link

# Deploy ke production
vercel --prod
```

---

## 7. Verifikasi Pasca-Deployment

Periksa endpoint berikut setelah container aktif:
1. `GET /health` atau `GET /up` &rarr; Memastikan container hidup dan merespons `200 OK`.
2. `GET /` &rarr; Memastikan beranda termuat dengan ilustrasi futsal beranimasi dan form pencarian jadwal.
3. `GET /lapangan` &rarr; Memastikan daftar lapangan dan tarif per jam (`formatted_price`) tampil.
4. `GET /masuk` &rarr; Menguji form login dengan eye toggle kata sandi.
5. `POST /booking/konfirmasi` &rarr; Menguji pembuatan booking dan penyimpanan ke database eksternal.
