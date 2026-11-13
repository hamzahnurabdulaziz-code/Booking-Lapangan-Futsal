# PRD (Product Requirements Document) — FutsalKita

## 1. Ringkasan Eksekutif & Tujuan Produk
**FutsalKita** adalah aplikasi web pemesanan dan pengecekan jadwal lapangan futsal realtime untuk satu tempat arena futsal (single venue) dengan beberapa lapangan. Produk ini dirancang untuk menyelesaikan masalah bentrok jadwal, ketidakjelasan ketersediaan lapangan, dan proses booking manual yang lambat.

Aplikasi ini dikembangkan oleh tim 3 orang pengembang melalui kolaborasi Git & GitHub:
- **Anggota 1 (Frontend):** Design system, Blade views, layout responsif, komponen Livewire UI, aksesibilitas, visual state handling.
- **Anggota 2 (Backend):** Layanan reservasi (`BookingService`), locking concurrency, validasi server, autentikasi, role & policy.
- **Anggota 3 (Database/Integrasi):** Skema migrasi, model Eloquent, factory, database seeder, integrasi PR dan pengujian otomatis.

---

## 2. Batasan Ruang Lingkup MVP (Minimum Viable Product)
Untuk menjaga keandalan pengiriman dan kualitas fondasi teknis, batas MVP ditentukan secara tegas:

1. **Satu Tempat Arena, Banyak Lapangan:**
   - Mengelola satu lokasi arena fisik (`FutsalKita Arena`) yang memiliki beberapa lapangan (misalnya Lapangan Vinyl A, Lapangan Interlock B, Lapangan Rumput Sintetis C).
2. **Bahasa & Zona Waktu:**
   - Seluruh teks antarmuka menggunakan **Bahasa Indonesia**.
   - Seluruh waktu yang ditampilkan kepada pengguna dikonversi dan diformat berdasarkan zona waktu **Asia/Jakarta (WIB)**.
   - Waktu disimpan di basis data dalam format **UTC**.
3. **Fokus Murni Reservasi (Tanpa Pembayaran):**
   - MVP **hanya menangani booking jadwal**.
   - **TIDAK ADA** sistem pembayaran, harga/tarif/rupiah, checkout, invoice, tagihan, bukti transfer, refund, payment gateway, maupun tabel pembayaran.
4. **Status Booking Instan:**
   - Booking yang divalidasi dan disimpan server langsung berstatus **Confirmed (Terkonfirmasi)**.
   - Tidak ada proses approval/persetujuan manual oleh admin sebelum booking dikonfirmasi.
5. **Akses Pengunjung vs Pelanggan:**
   - Pengunjung tanpa login (guest) dapat melihat halaman beranda, daftar lapangan, detail fasilitas, dan memeriksa ketersediaan slot jadwal.
   - Pengunjung wajib login/mendaftar saat ingin mengonfirmasi dan menyimpan booking. Pilihan jadwal pengunjung disimpan di session dan dipulihkan setelah login.
6. **Kebijakan Perubahan Jadwal (Reschedule):**
   - MVP tidak memiliki fitur reschedule/ubah jam langsung.
   - Pelanggan yang ingin mengganti jadwal harus membatalkan booking lama terlebih dahulu (sebelum jam mulai), lalu membuat booking baru.
7. **Jam Operasional & Konfigurasi:**
   - Jam operasional awal: **08.00 – 23.00 WIB** setiap hari.
   - Diatur dalam file konfigurasi terpusat (`config/futsal.php`) sehingga fleksibel diubah.
8. **Aturan Slot Jadwal:**
   - Jam mulai wajib pada jam bulat (misal 08:00, 09:00, 10:00, ..., 22:00).
   - Durasi bermain: **1 hingga 3 jam**.
   - Waktu selesai maksimal pukul 23:00 WIB pada hari yang sama.
   - **Tidak ada booking lintas tengah malam** (no midnight crossover).
9. **Jendela Pemesanan (Booking Window):**
   - Pemesanan dibuka mulai hari ini hingga **maksimal 30 hari ke depan**.
   - Jam mulai harus berada di masa depan (`starts_at > now()`) menurut waktu Asia/Jakarta.
10. **Tipe Lapangan:**
    - Lapangan dapat bertipe `indoor` atau `outdoor`.
    - Atribut lapangan: Nama, slug, tipe, deskripsi, fasilitas (JSON array), foto/path gambar, dan status aktif (`is_active`).

---

## 3. Aktor & Kebutuhan Pengguna

### A. Pengunjung (Guest)
- Melihat beranda informasi arena, fasilitas, cara booking, dan FAQ.
- Melihat daftar dan detail lapangan lengkap beserta foto dan fasilitas.
- Melihat kalender ketersediaan slot per jam pada tanggal tertentu.
- Melakukan registrasi akun baru (selalu role `customer`).
- Melakukan login ke akun terdaftar.

### B. Pelanggan (Customer)
- Seluruh kemampuan pengunjung.
- Mengonfirmasi ringkasan pemesanan dengan mengisi nama tim (opsional, maks 100 char) dan catatan khusus (opsional, maks 500 char).
- Menerima kode booking unik resmi (misal `FK-YYYYMMDD-XXXX`).
- Melihat riwayat booking miliknya yang terbagi atas tab: **Mendatang**, **Selesai**, dan **Dibatalkan**.
- Membatalkan booking miliknya sendiri **hanya jika dilakukan sebelum waktu bermain dimulai (`starts_at > now()`)**.
- Tidak dapat melihat, mengakses, atau membatalkan booking milik pelanggan lain.

### C. Administrator (Admin)
- Mengakses Dashboard ringkas yang menampilkan ringkasan metrik lapangan dan aktivitas booking.
- Mengelola data lapangan: menambah lapangan baru, mengubah data, serta mengaktifkan/menonaktifkan lapangan.
- Melihat seluruh riwayat booking dengan fitur pencarian kode/nama/nomor HP/tim serta filter lapangan, tanggal, dan status.
- Membatalkan booking pelanggan sebelum waktu bermain dimulai dengan **wajib mengisi alasan pembatalan** (maks 500 char).
- Menandai booking berstatus confirmed menjadi **Selesai (Completed)** hanya sesudah waktu booking berakhir (`ends_at <= now()`).

---

## 4. Aturan Status Booking & Siklus Hidup

| Status Database | Label UI | Deskripsi |
|---|---|---|
| `confirmed` | Terkonfirmasi | Booking aktif tersimpan. Mengunci slot jadwal agar tidak dapat dipesan pengguna lain. |
| `completed` | Selesai | Jadwal telah lewat dan ditandai selesai oleh admin. Tetap mengunci histori slot yang telah berlalu. |
| `cancelled` | Dibatalkan | Booking dibatalkan oleh pemilik atau admin sebelum jam mulai. Slot waktu langsung terbuka kembali untuk pemesan lain. Histori tetap tersimpan di basis data. |

### Prinsip Proteksi Data:
- **No Hard-Delete:** Lapangan atau pengguna yang sudah memiliki data booking dilarang keras dihapus secara permanen (`restrict on delete`). Lapangan yang sedang dalam perbaikan dinonaktifkan (`is_active = false`).
- **Interval Booking Setengah Terbuka `[mulai, selesai)`:**
  - Jadwal 09.00–10.00 boleh bersebelahan langsung dengan jadwal 10.00–11.00 tanpa dianggap bentrok.
  - Bentrok terjadi jika dan hanya jika:  
    `existing.starts_at < requested.ends_at AND existing.ends_at > requested.starts_at` untuk status `confirmed` atau `completed`.

---

## 5. Keamanan & Idempotensi Server
1. **Server Deterministic Authority:** `user_id`, `status` awal, `booking_code`, `ends_at`, `role`, dan token permintaan diverifikasi dan ditentukan sepenuhnya oleh server; tidak mempercayai input manipulasi dari browser.
2. **Idempotensi `request_key`:**
   - Setiap form booking menghasilkan token idempotensi UUID unik.
   - Pengiriman ulang identik (misal double-click atau network retry) mengembalikan booking yang sama tanpa membuat duplikasi.
   - Pengiriman payload berbeda dengan token yang sama ditolak server.
3. **Row Locking Hierarchy:**
   - Seluruh transaksi perubahan booking mengunci baris lapangan (`Field::lockForUpdate()`) terlebih dahulu sebelum memeriksa konflik dan menyimpan perubahan, mencegah race condition pada akses konkurensi tinggi.
