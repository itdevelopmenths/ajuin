# Ajuin - Sistem Tiket & Pengaduan

Ajuin adalah aplikasi sistem tiket/pengaduan berbasis web yang dibangun menggunakan framework Laravel. Aplikasi ini memungkinkan pengguna umum untuk mengirimkan tiket pengaduan secara publik, melacak status tiket mereka, dan menyediakan dashboard yang komprehensif bagi admin/staf untuk mengelola tiket, pengguna, peran (roles), serta data toko/cabang.

## 🌟 Fitur Utama

### 1. Antarmuka Publik (Public Interface)
*   **Kirim Tiket (Submit Ticket):** Formulir publik bagi masyarakat/pengguna untuk mengajukan tiket pengaduan atau layanan tanpa perlu login. Dilengkapi dengan fitur pelindung dari spam (*rate limiting*).
*   **Lacak Tiket (Track Ticket):** Fitur bagi publik untuk mencari dan memantau status terkini dari tiket yang telah diajukan hanya dengan menggunakan nomor tiket (Ticket Number).

### 2. Autentikasi & Keamanan (Authentication & Security)
*   **Login & Logout:** Sistem masuk yang aman untuk staf dan admin.
*   **Registrasi & Verifikasi OTP:** Pengguna baru dapat mendaftar untuk mendapatkan akses sistem, namun diwajibkan memverifikasi akun mereka menggunakan kode OTP (One Time Password) yang dikirimkan melalui email. Terdapat juga fitur kirim ulang (resend) OTP.
*   **Throttling:** Perlindungan *brute-force* pada *endpoint* krusial seperti halaman login, register, submit form, dan verifikasi OTP.

### 3. Manajemen Tiket (Ticket Management)
*   **Daftar Tiket Cepat:** Tampilan seluruh tiket yang masuk dengan performa tinggi, didukung oleh **DataTables** untuk fitur pencarian, pengurutan, dan paginasi secara *server-side*.
*   **Detail Tiket:** Melihat informasi terperinci dari setiap tiket.
*   **Pembuatan Tiket Manual:** Memungkinkan staf/admin internal untuk membuatkan tiket secara manual jika diperlukan (misalnya pengaduan masuk lewat telepon).
*   **Update Status Tiket:** Memperbarui tahapan status tiket (contoh: Menunggu, Diproses, Selesai) untuk menjaga transparansi penanganan pengaduan.
*   **Ekspor Data:** Kemampuan mengekspor data tiket laporan ke dalam format **Excel** dan **PDF** untuk keperluan *backup* atau *reporting* (menggunakan library `maatwebsite/excel` & `barryvdh/laravel-dompdf`).

### 4. Manajemen Pengguna & Hak Akses (User & Role Management)
*   **Sistem RBAC Terpusat:** Pengelolaan hak akses menggunakan paket handal `spatie/laravel-permission`.
*   **Manajemen Peran (Roles):** Admin super dapat membuat, mengubah, dan menghapus *role* (peran) serta menentukan secara spesifik fitur atau *permission* apa saja yang boleh diakses oleh *role* tersebut.
*   **Manajemen Pengguna (Users):** Admin dapat melihat daftar seluruh pengguna sistem (staf/admin lain), mendaftarkan staf baru, mengubah data, mengatur perannya (*assign role*), dan menghapus pengguna.

### 5. Manajemen Toko/Cabang (Store Management)
*   **Kelola Data Toko:** Modul lengkap (CRUD) untuk menambahkan dan mengelola daftar toko, cabang, atau unit bisnis.
*   **Global QR Code:** Fasilitas untuk *generate* (menghasilkan) QR Code secara instan (`simplesoftwareio/simple-qrcode`), yang nantinya bisa dicetak dan dipasang di lokasi fisik agar pengunjung/pelanggan dapat memindainya dan langsung diarahkan ke form submit tiket untuk cabang terkait.

### 6. Laporan (Reports)
*   Halaman dasbor khusus untuk meninjau ringkasan, rekapitulasi kinerja, atau pelaporan (*reporting*) terkait data tiket yang telah dikumpulkan.

---

## 🛠️ Teknologi & *Packages* yang Digunakan
*   **Framework Utama:** Laravel (PHP ^8.3)
*   **Manajemen Hak Akses:** `spatie/laravel-permission`
*   **Grid & Tabel Data:** `yajra/laravel-datatables-oracle`
*   **Ekspor Data Excel:** `maatwebsite/excel`
*   **Ekspor Data PDF:** `barryvdh/laravel-dompdf`
*   **Pembuat QR Code:** `simplesoftwareio/simple-qrcode`

---

## 🚀 Cara Instalasi & Menjalankan di Lokal

Jika Anda ingin mengembangkan atau menjalankan aplikasi ini di komputer lokal (local development), ikuti langkah-langkah berikut:

1. **Clone repository ini:**
   ```bash
   git clone https://github.com/andyka-salom/ajuin.git
   ```
2. **Masuk ke direktori aplikasi:**
   ```bash
   cd ajuin
   ```
3. **Instal dependensi *backend* (PHP):**
   ```bash
   composer install
   ```
4. **Persiapkan file konfigurasi (*environment*):**
   ```bash
   cp .env.example .env
   ```
5. **Buat Application Key:**
   ```bash
   php artisan key:generate
   ```
6. **Konfigurasi Database:** Buka file `.env` di teks editor, lalu sesuaikan kredensial koneksi database Anda (terutama `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`). Pastikan Anda juga mengonfigurasi pengaturan Mail (`MAIL_MAILER`, dll.) agar fitur OTP via email berfungsi.
7. **Jalankan Migrasi Database beserta Seeder:**
   ```bash
   php artisan migrate --seed
   ```
8. **Instal dan kompilasi *asset frontend* (CSS/JS):**
   ```bash
   npm install
   npm run build
   ```
9. **Jalankan server *development* Laravel:**
   ```bash
   php artisan serve
   ```
10. **Selesai!** Anda dapat mengakses aplikasi di browser melalui URL: `http://localhost:8000`
