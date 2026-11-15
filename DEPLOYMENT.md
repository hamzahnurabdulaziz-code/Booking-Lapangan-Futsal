# Panduan Deployment Produksi Vercel — FutsalKita

Dokumen ini menjelaskan arsitektur, konfigurasi teknis, dan langkah demi langkah (*step-by-step*) untuk melakukan deployment aplikasi **FutsalKita** (Laravel 12 + Livewire 3 + Tailwind CSS + Vite) ke platform **Vercel Serverless**.

---

## 1. Arsitektur Serverless: Laravel di Vercel

Vercel adalah platform *serverless* berbasis AWS Lambda di lapisan infrastrukturnya. Saat menjalankan aplikasi monolitik Laravel di Vercel, ada beberapa batasan arsitektural yang wajib dipahami:

| Aspek | Server Tradisional (VPS/EC2) | Vercel Serverless | Solusi FutsalKita |
| :--- | :--- | :--- | :--- |
| **Filesystem** | Baca & Tulis (*Read-Write*) | Hanya Baca (*Read-Only*) kecuali direktori `/tmp` | View cache & session diarahkan ke `/tmp` atau cookie |
| **Database** | SQLite lokal atau MySQL localhost | SQLite lokal akan hilang setiap instance cold-start | **Wajib MySQL / PostgreSQL Eksternal** (PlanetScale, Supabase, Aiven, Railway, AWS RDS) |
| **Session** | `file` session driver di `storage/framework/sessions` | Multi-instance lambda tidak berbagi storage | Gunakan driver `cookie` atau `database` |
| **Aset Frontend** | Disajikan via Nginx / Apache | Edge CDN Static Hosting Vercel | Vite build (`npm run build`) dipublikasikan ke Edge Vercel |
| **Eksekusi Cron/Queue** | Supervisor / Crontab sistem | Tidak ada worker daemon persisten | Gunakan Vercel Cron Jobs jika dibutuhkan |

---

## 2. Struktur Konfigurasi Vercel

Repository ini telah dilengkapi dengan dua berkas utama untuk deployment Vercel:

1. **`api/index.php`**: Entry point Lambda yang secara otomatis membuat direktori `/tmp/views`, `/tmp/cache`, dan `/tmp/sessions` sebelum memuat `public/index.php`.
2. **`vercel.json`**: Konfigurasi runtime PHP community (`vercel-php@0.7.3`), routing aset statis (`/build/*`, `/images/*`, `/favicon.ico`), dan routing fallback ke `api/index.php`.

---

## 3. Penyediaan Database Cloud (Wajib untuk Produksi)

Karena filesystem serverless bersifat ephemeral, **SQLite lokal tidak dapat digunakan di Vercel**. Anda membutuhkan basis data MySQL yang dapat diakses publik dengan TLS/SSL:

### Rekomendasi Provider Gratis / Murah:
1. **Aiven for MySQL** (Gratis trial / $19/bln, dukungan SSL penuh)
2. **Railway MySQL** (Free trial $5 kredit / $5 per bulan)
3. **PlanetScale** atau **Supabase (PostgreSQL)**
4. **AWS RDS / DigitalOcean Managed Database**

### Contoh Konfigurasi `.env` Database Eksternal:
```env
DB_CONNECTION=mysql
DB_HOST=mysql-futsalkita-cluster.aivencloud.com
DB_PORT=18475
DB_DATABASE=futsalkita_prod
DB_USERNAME=avnadmin
DB_PASSWORD=your_secure_password
MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca-certificates.crt
```

---

## 4. Environment Variables di Vercel Project Settings

Buka **Vercel Dashboard** &rarr; Pilih Proyek **futsalkita** &rarr; **Settings** &rarr; **Environment Variables**. Masukkan variabel-variabel berikut:

### Core Laravel Settings:
```env
APP_NAME=FutsalKita
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_GENERATED_32_BYTE_APP_KEY
APP_URL=https://futsalkita.vercel.app
APP_LOCALE=id
APP_FALLBACK_LOCALE=id
```

### Storage & Serverless Paths:
```env
VIEW_COMPILED_PATH=/tmp/views
APP_CONFIG_CACHE=/tmp/config.php
APP_EVENTS_CACHE=/tmp/events.php
APP_PACKAGES_CACHE=/tmp/packages.php
APP_ROUTES_CACHE=/tmp/routes.php
APP_SERVICES_CACHE=/tmp/services.php
LOG_CHANNEL=stderr
```

### Session & Cache (Stateless):
```env
CACHE_STORE=array
SESSION_DRIVER=cookie
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true
```

### Database Credentials:
```env
DB_CONNECTION=mysql
DB_HOST=YOUR_REMOTE_HOST
DB_PORT=3306
DB_DATABASE=YOUR_DB_NAME
DB_USERNAME=YOUR_DB_USER
DB_PASSWORD=YOUR_DB_PASSWORD
```

### Futsal Kita Business Rules:
```env
FUTSAL_TIMEZONE=Asia/Jakarta
FUTSAL_OPENING_HOUR=8
FUTSAL_CLOSING_HOUR=23
FUTSAL_MAX_ADVANCE_DAYS=30
FUTSAL_VENUE_NAME="FutsalKita Arena"
FUTSAL_VENUE_ADDRESS="Jl. Lapangan Olahraga No. 10, Jakarta Selatan"
```

---

## 5. Langkah-Langkah Deployment via Git / Vercel CLI

### Opsi A: Hubungkan Repository GitHub (Rekomendasi untuk Tim)
1. Buka [https://vercel.com/dashboard](https://vercel.com/dashboard).
2. Klik **Add New...** &rarr; **Project**.
3. Hubungkan repository GitHub: `ProjectTKIT/Booking-Lapangan-Futsal`.
4. Pilih Framework Preset: **Other**.
5. Atur Build & Output Settings:
   - **Build Command**: `npm run build`
   - **Output Directory**: `public`
   - **Install Command**: `npm install`
6. Buka bagian **Environment Variables** dan masukkan semua variabel dari Bagian 4.
7. Klik **Deploy**.

### Opsi B: Menggunakan Vercel CLI
```bash
# 1. Install Vercel CLI jika belum
npm install -g vercel

# 2. Login ke akun Vercel
vercel login

# 3. Hubungkan proyek lokal ke Vercel
vercel link

# 4. Deploy ke Production
vercel --prod
```

---

## 6. Menjalankan Migrasi & Seeding Awal di Database Cloud

Karena Vercel tidak memiliki terminal SSH persisten, migrasi database dapat dilakukan dengan menghubungkan terminal lokal ke database cloud yang sama:

1. Di komputer lokal Anda, buka file `.env` sementara atau set variabel:
   ```bash
   php artisan migrate --force
   php artisan db:seed --force
   ```
2. Atau jalankan command sekali pakai dari runner CI/CD GitHub Actions:
   ```yaml
   - name: Run Database Migrations
     run: php artisan migrate --force
     env:
       DB_CONNECTION: mysql
       DB_HOST: ${{ secrets.DB_HOST }}
       DB_DATABASE: ${{ secrets.DB_DATABASE }}
       DB_USERNAME: ${{ secrets.DB_USERNAME }}
       DB_PASSWORD: ${{ secrets.DB_PASSWORD }}
   ```

---

## 7. Troubleshooting & Solusi Kendala Umum

### 1. Pesan Kesalahan: `500 Server Error` saat pertama kali dibuka
- **Penyebab**: `APP_KEY` belum diset atau ada folder `storage/framework/views` yang tidak dapat ditulis.
- **Solusi**: Periksa tab **Logs** di Vercel Dashboard. Pastikan `VIEW_COMPILED_PATH=/tmp/views` telah diset di Environment Variables dan `api/index.php` telah memanggil `mkdir('/tmp/views')`.

### 2. Tampilan UI / CSS Berantakan (*Unstyled*)
- **Penyebab**: Aset Vite belum ter-build atau path asset di `manifest.json` salah.
- **Solusi**: Pastikan `npm run build` dijalankan pada build command Vercel dan `public/build` diizinkan rutenya di `vercel.json`.

### 3. Sesi Login Logout Sendiri (*Session Lost*)
- **Penyebab**: Driver session menggunakan `file`. Setiap kali Vercel menjalankan request di lambda instance baru, session file lama tidak ada.
- **Solusi**: Ubah `SESSION_DRIVER=cookie` atau `SESSION_DRIVER=database` di Vercel Environment Variables.

### 4. Livewire Mengalami `419 Page Expired`
- **Penyebab**: CSRF token tidak cocok atau domain session tidak terkonfigurasi.
- **Solusi**: Pastikan `SESSION_DOMAIN=null` dan `SESSION_SECURE_COOKIE=true` jika menggunakan HTTPS (Vercel otomatis menyediakan HTTPS).

---

## 8. Verifikasi Pasca-Deployment Checklist

- [ ] Halaman Beranda (`/`) memuat animasi lapangan futsal dan form pencarian jadwal.
- [ ] Filter lapangan (`/fields`) menampilkan tarif sewa dan fasilitas lengkap.
- [ ] Slot waktu terhitung otomatis sesuai durasi 1–3 jam.
- [ ] Registrasi akun pelanggan baru berhasil.
- [ ] Password toggle (ikon mata) berfungsi di form login dan register.
- [ ] Pembuatan booking menghasilkan kode resmi (`FK-YYYYMMDD-XXXX`) dan total biaya tercatat.
- [ ] Pembatalan booking oleh pelanggan berfungsi sebelum waktu `starts_at`.
- [ ] Dashboard admin (`/admin/dashboard`) dan manajemen lapangan/booking berfungsi normal.
