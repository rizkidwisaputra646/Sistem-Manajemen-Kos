# KosKu - Platform Reservasi Kontrakan Online ✨

**KosKu** adalah platform modern berbasis web untuk mengelola dan memesan kamar kontrakan/kos secara online. Dirancang dengan estetika SaaS kontemporer, platform ini menawarkan pengalaman pengguna yang mulus bagi calon penghuni dan kemudahan pengelolaan bagi pemilik properti (Admin).

## 🚀 Fitur Utama

- **UI/UX Modern & Clean**: Didesain menggunakan Tailwind CSS dengan estetika startup (SaaS).
- **Sistem Reservasi Real-time**: Booking kamar dengan validasi tanggal otomatis menggunakan Flatpickr.
- **Integrasi Pembayaran Aman**: Pembayaran instan melalui gateway **Midtrans** (Sandbox).
- **Dashboard Pengguna**: Kelola pesanan aktif, lihat riwayat sewa, dan edit profil dengan mudah.
- **Admin Panel**: Manajemen kamar (CRUD), monitoring reservasi, data penghuni, dan laporan keuangan.
- **Responsif**: Pengalaman optimal di perangkat Desktop maupun Mobile (Bottom Nav Bar).
- **Notifikasi Interaktif**: Feedback pengguna yang cantik menggunakan SweetAlert2.

## 🛠️ Tech Stack

- **Framework**: Laravel 11
- **Styling**: Tailwind CSS (Modern Slate & Indigo Palette)
- **Frontend Interactivity**: Alpine.js
- **Database**: MySQL / SQLite
- **Payment Gateway**: Midtrans API
- **Asset Manager**: Vite
- **Components**: 
  - [Heroicons](https://heroicons.com/) (Icons)
  - [Flatpickr](https://flatpickr.js.org/) (Date Picker)
  - [SweetAlert2](https://sweetalert2.github.io/) (Alerts)

## 📦 Instalasi

Ikuti langkah-langkah berikut untuk menjalankan project di lokal:

1. **Clone Repository**
   ```bash
   git clone https://github.com/rizkidwisaputra646/reservasi-kontrakan.git
   cd reservasi-kontrakan
   ```

2. **Install Dependensi PHP & JS**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**
   Salin file `.env.example` menjadi `.env` dan sesuaikan konfigurasi database serta Midtrans Key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Migrasi Database & Seeding**
   ```bash
   php artisan migrate --seed
   ```

5. **Generate Symlink Storage**
   ```bash
   php artisan storage:link
   ```

6. **Jalankan Aplikasi**
   ```bash
   npm run dev
   php artisan serve
   ```

## 📸 Tampilan
*(Screenshot akan ditambahkan segera)*

## 📄 Lisensi
Project ini berlisensi di bawah [MIT License](LICENSE).

---
Dibuat dengan ❤️ oleh **Rizki Dwi Saputra**
