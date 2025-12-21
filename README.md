# ThreeKey – Sistem Manajemen Jadwal & Tugas Akademik

**ThreeKey** adalah aplikasi manajemen waktu dan tugas berbasis web yang dirancang khusus untuk membantu pelajar dan mahasiswa dalam mengelola jadwal akademik mereka. Aplikasi ini mengintegrasikan *deadline* tugas, jadwal ujian, hafalan, dan proyek dalam satu *timeline* visual (Gantt Chart) yang interaktif, serta dilengkapi dengan sistem notifikasi pintar.

---

# 📑 Daftar Isi

1. [Tentang Aplikasi](#-tentang-aplikasi)
2. [Fitur Utama](#-fitur-utama)
3. [Teknologi yang Digunakan](#-teknologi-yang-digunakan)
4. [Instalasi & Konfigurasi](#-instalasi--konfigurasi)
5. [Skema Database](#-skema-database)
6. [Struktur Direktori](#-struktur-direktori)
7. [Kontribusi](#-kontribusi)

---

# 💡 Tentang Aplikasi

Manajemen waktu yang buruk seringkali menjadi musuh utama akademisi. **ThreeKey** hadir sebagai solusi "Kunci Ketiga" kesuksesan studi Anda, menawarkan platform terpusat untuk:
*   Melacak *deadline* tugas dan proyek.
*   Menjadwalkan persiapan ujian dan kuis.
*   Mengelola target hafalan.
*   Sinkronisasi otomatis dengan Google Calendar agar tidak ada jadwal yang terlewat.

---

# 🚀 Fitur Utama

### 📊 Manajemen Tugas & Visualisasi
*   **Smart Timeline (Gantt Chart)**: Visualisasi jadwal tugas, ujian, dan proyek dalam bentuk *timeline bar* yang interaktif. Memudahkan Anda melihat tumpang tindih jadwal dan prioritas.
*   **Kategori Fleksibel**: Dukungan template tugas bawaan:
    *   📘 **Tugas**: PR harian atau mingguan.
    *   📝 **Ujian**: Jadwal UTS/UAS.
    *   🧠 **Hafalan**: Target hafalan surat/materi.
    *   🚀 **Project**: Proyek jangka panjang.
    *   ⚡ **Kuis**: Ulangan harian.

### 🔔 Notifikasi & Pengingat
*   **Email Reminder Otomatis**: Sistem akan mengirimkan email pengingat secara otomatis pada interval kritis (H-3, H-1, dan Hari-H) sebelum *deadline*.
*   **Admin Broadcast**: Notifikasi sistem dari administrator.

### 🔐 Integrasi & Keamanan
*   **Google Sign-In**: Login cepat dan aman menggunakan akun Google.
*   **Google Calendar Sync**: Setiap jadwal yang dibuat di ThreeKey dapat otomatis disinkronkan ke Google Calendar pribadi Anda.
*   **Keamanan Akun**: Verifikasi email berbasis OTP (One-Time Password) untuk registrasi dan reset password.

---

# 🛠 Teknologi yang Digunakan

Aplikasi ini dibangun dengan teknologi web modern yang ringan dan cepat:

*   **Backend**: PHP 8.1+ (Native MVC Architecture)
*   **Database**: MySQL 8.0
*   **Frontend**: HTML5, CSS3 (Custom Styling), JavaScript (Vanilla)
*   **Libraries**:
    *   `phpmailer/phpmailer`: Pengiriman email notifikasi (SMTP).
    *   `google/apiclient`: Integrasi Google Auth & Calendar API.
    *   `vlucas/phpdotenv`: Manajemen konfigurasi environment.
    *   `frappe-gantt`: Visualisasi chart timeline.

---

# ⚙ Instalasi & Konfigurasi

Ikuti langkah berikut untuk menjalankan aplikasi di komputer lokal Anda (XAMPP/Laragon):

### 1. Clone Repository
```bash
git clone https://github.com/username/threekey.git
cd threekey
```

### 2. Install Dependencies
Pastikan **Composer** sudah terinstall, lalu jalankan perintah ini di terminal root project:
```bash
composer install
```

### 3. Setup Database
1.  Buat database baru di MySQL bernama `threekey`.
2.  Import file `threekey.sql` yang ada di root direktori ke database tersebut.

### 4. Konfigurasi Environment
Salin file `.env.example` menjadi `.env`, lalu sesuaikan isinya:

```env
# Database Configuration
DB_HOST=localhost
DB_NAME=threekey
DB_USER=YOUR_DATABASE_USER
DB_PASS=YOUR_DATABASE_PASSWORD

# Google API Configuration (Untuk Login & Calendar)
GOOGLE_CLIENT_ID=YOUR_GOOGLE_CLIENT_ID
GOOGLE_CLIENT_SECRET=YOUR_GOOGLE_CLIENT_SECRET
GOOGLE_REDIRECT_URI=http://localhost/threekey/auth/google_callback.php

# SMTP Helper (Untuk Email Notifikasi)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=YOUR_EMAIL@gmail.com
MAIL_PASSWORD=YOUR_GOOGLE_APP_PASSWORD
```

### 5. Jalankan Aplikasi
Buka browser dan akses:
`http://localhost/threekey/public` (atau sesuaikan dengan konfigurasi virtual host Anda).

---

# � Skema Database

Berikut adalah struktur tabel utama yang digunakan dalam **ThreeKey**:

### 1. `users`
Menyimpan data akun pengguna baik yang mendaftar manual maupun via Google.
*   **Columns**: `id`, `name`, `email`, `password`, `profile_pic`, `role`, `provider` (manual/google), `provider_id`, `provider_refresh_token`.

### 2. `schedules`
Tabel inti yang menyimpan semua aktivitas (Tasks, Exams, Projects).
*   **Columns**: `id`, `user_id`, `template_id` (Jenis Jadwal), `title`, `description`, `start_datetime`, `end_datetime`.
*   **Google Sync**: Kolom `google_event_id` menyimpan ID referensi untuk sinkronisasi 2 arah dengan Google Calendar.

### 3. `templates`
Master data untuk kategori jadwal.
*   **Data**: "Tugas", "Ujian", "Hafalan", "Project", "Kuis".

### 4. `subjects` & `user_subjects`
Manajemen mata pelajaran/kuliah.
*   **subjects**: Master data mata pelajaran global.
*   **user_subjects**: Mata pelajaran yang diambil oleh user spesifik.

### 5. `notifications`
Menyimpan antrian pengingat yang akan dikirim via Email.
*   **Columns**: `title`, `message`, `reminder_day` (untuk H-7, H-3, H-1 dan Hari-H).

### 6. `otp` & `forgot_password`
Sistem keamanan berbasis token.
*   Digunakan untuk verifikasi email pendaftaran dan reset password.

---

# �📁 Struktur Direktori

```
threekey/
├── app/
│   ├── config/       # Koneksi Database
│   ├── api/          # Endpoint API internal (AJAX handler)
│   ├── classes/      # Helper Class (GoogleClient, Mailer)
│   ├── controllers/  # Logic (Auth, Schedule, Task)
│   └── models/       # Interaksi Database (User, Schedule, OTP)
├── public/
│   ├── assets/       # CSS, JS, Images
│   ├── auth/         # Halaman Login/Register/Lupa Password
│   ├── user/         # Dashboard Utama User (Gantt, Calendar)
│   ├── admin/        # Dashboard Admin
│   └── index.php     # Entry point
├── threekey.sql      # Database Schema
└── vendor/           # Composer Libraries
```

---

# 🤝 Kontribusi

Kontribusi selalu diterima! Silakan buat *Issues* atau *Pull Request* jika Anda menemukan bug atau ingin menambahkan fitur baru.
