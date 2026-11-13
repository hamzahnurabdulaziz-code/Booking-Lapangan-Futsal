# Diagram Alur & Flowchart — FutsalKita

Dokumen ini memuat diagram alur interaksi dan logika transisi sistem FutsalKita menggunakan sintaks Mermaid standar.

---

## 1. Alur Pemesanan Jadwal (Booking Flow)

```mermaid
flowchart TD
    A["Pengguna memilih Lapangan, Tanggal, Jam Mulai, dan Durasi (1-3 Jam)"] --> B{"Sudah Login?"}
    B -- "Belum" --> C["Simpan pilihan ke Session; Arahkan ke Login/Register"]
    C --> D["Pengguna berhasil autentikasi"]
    D --> E["Pulihkan pilihan dari Session; Validasi ulang parameter di server"]
    B -- "Sudah" --> E
    E --> F["Tampilkan Ringkasan Pemesanan; Input Nama Tim & Catatan"]
    F --> G["Klik 'Konfirmasi & Simpan Booking' (Kirim request_key)"]
    G --> H["Server memulai DB Transaction & Lock Baris Lapangan (lockForUpdate)"]
    H --> I{"Jadwal masih tersedia? (Tidak ada bentrok interval)"}
    I -- "Tidak (Bentrok)" --> J["Rollback Transaction; Tampilkan Notifikasi Konflik; Arahkan pilih slot lain"]
    J --> A
    I -- "Ya (Tersedia)" --> K["Simpan Booking dengan status 'confirmed' & Generate kode unik"]
    K --> L["Commit Transaction & Bersihkan Session pending"]
    L --> M["Tampilkan Halaman Berhasil dengan Kode Booking Resmi"]
```

---

## 2. Alur Pembatalan & Penyelesaian Booking

```mermaid
flowchart TD
    A["Booking Berstatus Confirmed"] --> B{"Aksi yang Dipilih"}
    
    B -- "Aksi: Batalkan" --> C{"Otorisasi: Pemilik atau Admin?"}
    C -- "Bukan Pemilik/Admin" --> E["Tolak Aksi (403 Forbidden)"]
    C -- "Sah" --> D{"Waktu Mulai: Apakah starts_at masih di masa depan?"}
    D -- "Tidak (Sudah Lewat/Dimulai)" --> F["Tolak: Booking yang telah dimulai tidak dapat dibatalkan"]
    D -- "Ya (Belum Dimulai)" --> G{"Apakah Pelaku adalah Admin?"}
    G -- "Ya" --> H{"Apakah Alasan Pembatalan Diisi?"}
    H -- "Tidak Diisi" --> I["Validasi Gagal: Admin wajib menyertakan alasan"]
    H -- "Diisi Lengkap" --> J["Simpan status 'cancelled', catat cancelled_at & alasan; Slot terbuka kembali"]
    G -- "Bukan (Pelanggan Sendiri)" --> J

    B -- "Aksi: Tandai Selesai" --> K{"Otorisasi: Apakah Pengguna adalah Admin?"}
    K -- "Bukan Admin" --> E
    K -- "Admin" --> L{"Waktu Selesai: Apakah ends_at sudah terlewati?"}
    L -- "Belum Selesai (ends_at > now)" --> M["Tolak: Booking hanya dapat diselesaikan setelah waktu main berakhir"]
    L -- "Sudah Selesai (ends_at <= now)" --> N["Simpan status 'completed'; Histori terkunci permanen"]
```

---

## 3. Diagram State Status Booking

```mermaid
stateDiagram-v2
    [*] --> confirmed: Booking berhasil dibuat oleh Pelanggan / Server
    
    confirmed --> cancelled: Dibatalkan oleh Pelanggan / Admin (Sebelum starts_at)
    confirmed --> completed: Ditandai Selesai oleh Admin (Setelah ends_at)
    
    cancelled --> [*]: Slot terbuka kembali, riwayat tersimpan
    completed --> [*]: Selesai, riwayat terkunci
```
