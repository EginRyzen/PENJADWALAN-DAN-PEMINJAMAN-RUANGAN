<div align="center">

# 🏛️ Sistem Peminjaman Ruangan

**Aplikasi web manajemen peminjaman ruangan kampus berbasis alur persetujuan multi-level**

[![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)](https://vuejs.org)
[![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Vite](https://img.shields.io/badge/Vite-7.x-646CFF?style=for-the-badge&logo=vite&logoColor=white)](https://vitejs.dev)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)](https://opensource.org/licenses/MIT)

</div>

---

## 📋 Daftar Isi

- [Tentang Proyek](#-tentang-proyek)
- [Fitur Utama](#-fitur-utama)
- [Tech Stack](#-tech-stack)
- [Persyaratan Sistem](#-persyaratan-sistem)
- [Instalasi](#-instalasi)
- [Konfigurasi Environment](#-konfigurasi-environment)
- [Menjalankan Aplikasi](#-menjalankan-aplikasi)
- [Struktur Proyek](#-struktur-proyek)
- [API Endpoints](#-api-endpoints)
- [Alur Workflow](#-alur-workflow)
- [Peran & Hak Akses](#-peran--hak-akses)
- [Akun Default (Seeder)](#-akun-default-seeder)
- [Kontribusi](#-kontribusi)

---

## 🎯 Tentang Proyek

**Sistem Peminjaman Ruangan** adalah aplikasi web full-stack yang dirancang untuk lingkungan kampus perguruan tinggi. Aplikasi ini mengelola seluruh siklus hidup pengajuan peminjaman ruang — mulai dari pengajuan oleh mahasiswa/dosen, proses persetujuan bertingkat, hingga konfirmasi akhir oleh Wakil Direktur.

Selain modul peminjaman, sistem ini juga dilengkapi dengan:
- **Manajemen Data Master** — program studi, kelas, mata kuliah, dosen, mahasiswa
- **Manajemen Gedung & Ruangan** — inventaris fasilitas dan kapasitas ruang
- **Penjadwalan Ujian Otomatis** — generator jadwal berbasis algoritma CSP (Constraint Satisfaction Problem)
- **Sistem Notifikasi** — notifikasi real-time untuk setiap perubahan status pengajuan

---

## ✨ Fitur Utama

### 📝 Manajemen Pengajuan Peminjaman
- Pengajuan peminjaman ruangan dengan tanggal, waktu, dan alasan
- Upload dokumen pendukung
- Peminjaman multi-item (banyak ruangan dalam satu pengajuan)
- Ekspor data pengajuan ke format Excel

### 🔄 Workflow Persetujuan Multi-Level
- Alur persetujuan bertingkat yang dikonfigurasi per tipe pengajuan
- Fitur **Approve**, **Reject**, dan **Revisi/Koreksi**
- Riwayat penuh setiap perubahan status (audit trail)
- Notifikasi otomatis ke pihak terkait

### 🏢 Manajemen Gedung & Ruangan
- CRUD data gedung, ruangan, dan fasilitas
- Manajemen kapasitas dan fasilitas per ruangan
- Import data ruangan via template Excel

### 📚 Data Master Akademik
- Program Studi, Kelas, Mata Kuliah
- Data Mahasiswa & Dosen
- Hari Libur & Jadwal Operasional
- Import massal via Excel untuk semua entitas

### 🗓️ Penjadwalan Ujian Otomatis
- Generator jadwal ujian berbasis algoritma CSP
- Deteksi konflik jadwal secara otomatis
- Mode draft sebelum jadwal dipermanenkan
- Filter dan manajemen jadwal per periode

### 🔔 Notifikasi Real-time
- Notifikasi in-app untuk setiap aksi workflow
- Penanda "belum dibaca" dengan badge counter
- Tandai semua notifikasi sebagai sudah dibaca

### 🔐 Autentikasi & RBAC
- Login berbasis token dengan Laravel Sanctum
- Role-Based Access Control (RBAC) multi-role per pengguna
- Menu dinamis berdasarkan peran pengguna

---

## 🛠️ Tech Stack

| Lapisan | Teknologi |
|--------|-----------|
| **Backend Framework** | Laravel 10.x (PHP 8.1+) |
| **Frontend Framework** | Vue.js 3.x (Composition API) |
| **State Management** | Vuex 4.x |
| **Routing (Frontend)** | Vue Router 4.x |
| **Build Tool** | Vite 7.x |
| **Database** | MySQL 8.0 |
| **ORM** | Eloquent (Laravel) |
| **Autentikasi** | Laravel Sanctum |
| **Styling** | Tailwind CSS 3.4 + DaisyUI 5.x |
| **HTTP Client** | Axios |
| **Icon** | Font Awesome 6.x |
| **Excel Import/Export** | Maatwebsite/Laravel-Excel 3.x |
| **Date Handling** | Moment.js |
| **Calendar UI** | v-calendar |

---

## 💻 Persyaratan Sistem

Pastikan environment Anda memenuhi persyaratan berikut sebelum memulai instalasi:

| Komponen | Versi Minimum |
|----------|---------------|
| PHP | `^8.1` |
| Composer | `^2.x` |
| Node.js | `^18.x` |
| NPM | `^9.x` |
| MySQL | `^8.0` |
| Git | `^2.x` |

> **Ekstensi PHP yang Dibutuhkan:** `BCMath`, `Ctype`, `Fileinfo`, `JSON`, `Mbstring`, `OpenSSL`, `PDO`, `PDO_MySQL`, `Tokenizer`, `XML`, `zip`

---

## 🚀 Instalasi

Ikuti langkah-langkah berikut secara berurutan untuk menjalankan proyek di lingkungan lokal Anda.

### 1. Clone Repository

```bash
git clone https://github.com/EginRyzen/PEMINJAMAN-RUANGAN.git
cd PEMINJAMAN-RUANGAN
```

### 2. Instalasi Dependensi PHP (Composer)

```bash
composer install
```

### 3. Instalasi Dependensi JavaScript (NPM)

```bash
npm install
```

### 4. Konfigurasi File Environment

Salin file konfigurasi contoh, lalu sesuaikan isinya:

```bash
cp .env.example .env
```

Kemudian edit file `.env` sesuai kebutuhan (lihat bagian [Konfigurasi Environment](#-konfigurasi-environment)).

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Buat Database

Buat database baru di MySQL Anda (misalnya dengan nama `peminjaman_ruangan`), lalu perbarui konfigurasi di `.env`:

```
DB_DATABASE=peminjaman_ruangan
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 7. Jalankan Migrasi Database

Perintah ini akan membuat semua tabel yang diperlukan:

```bash
php artisan migrate
```

### 8. Jalankan Seeder (Data Awal)

Perintah ini akan mengisi data peran (role) dan akun pengguna default:

```bash
php artisan db:seed
```

### 9. Buat Symlink Storage (untuk upload file)

```bash
php artisan storage:link
```

---

## ⚙️ Konfigurasi Environment

Berikut adalah variabel `.env` yang perlu dikonfigurasi:

```dotenv
# ============================================================
# KONFIGURASI APLIKASI
# ============================================================
APP_NAME="Sistem Peminjaman Ruangan"
APP_ENV=local          # local | production
APP_KEY=               # Di-generate otomatis oleh artisan key:generate
APP_DEBUG=true         # Ubah ke false di production
APP_URL=http://localhost

# ============================================================
# KONFIGURASI DATABASE
# ============================================================
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=peminjaman_ruangan   # Nama database Anda
DB_USERNAME=root
DB_PASSWORD=                     # Password MySQL Anda

# ============================================================
# KONFIGURASI QUEUE (Notifikasi & Job)
# ============================================================
QUEUE_CONNECTION=sync   # Gunakan 'database' untuk production

# ============================================================
# KONFIGURASI MAIL (Opsional)
# ============================================================
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@kampus.ac.id"
MAIL_FROM_NAME="${APP_NAME}"
```

---

## ▶️ Menjalankan Aplikasi

Anda perlu menjalankan **dua proses** secara bersamaan: server Laravel dan server Vite (untuk frontend).

### Terminal 1 — Backend (Laravel)

```bash
php artisan serve
```

Server akan berjalan di: `http://127.0.0.1:8000`

### Terminal 2 — Frontend (Vite + Vue)

```bash
npm run dev
```

Server Vite akan berjalan di: `http://localhost:5173` (atau port yang tersedia)

> **Akses Aplikasi:** Buka browser dan kunjungi `http://127.0.0.1:8000`

---

### 🔧 Perintah Artisan Berguna

```bash
# Jalankan migrasi ulang + seed (HAPUS semua data)
php artisan migrate:fresh --seed

# Jalankan queue worker (jika QUEUE_CONNECTION=database)
php artisan queue:work

# Bersihkan cache aplikasi
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Build aset untuk production
npm run build
```

---

## 📁 Struktur Proyek

```
PEMINJAMAN-RUANGAN/
│
├── app/                              # Kode inti aplikasi Laravel
│   ├── Console/                      # Artisan commands
│   ├── Exceptions/                   # Custom exception handler
│   ├── Exports/                      # Kelas export Excel (Maatwebsite)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php        # Login & logout (Sanctum)
│   │   │   ├── NotificationController.php
│   │   │   ├── DataDocumentController.php # Upload dokumen
│   │   │   ├── ExcelImportController.php  # Import massal via Excel
│   │   │   ├── Building/
│   │   │   │   ├── DataBaseBuildingController.php
│   │   │   │   ├── DataBaseBuildingRoomController.php
│   │   │   │   └── DataBaseBuildingFacilityController.php
│   │   │   ├── MasterData/
│   │   │   │   ├── MasterDataProgramStudiController.php
│   │   │   │   ├── MasterDataKelasController.php
│   │   │   │   ├── MasterDataMataKuliahController.php
│   │   │   │   ├── MasterDataMahasiswaController.php
│   │   │   │   ├── MasterDataDosenController.php
│   │   │   │   ├── MasterDataHariLiburController.php
│   │   │   │   ├── MasterDataKelasMataKuliahController.php
│   │   │   │   ├── MasterPeriodeController.php
│   │   │   │   ├── MasterSksSettingController.php
│   │   │   │   ├── MasterOperasionalScheduleController.php
│   │   │   │   ├── MenuController.php
│   │   │   │   └── RoleMenuController.php
│   │   │   ├── Pengajuan/
│   │   │   │   ├── PengajuanPeminjamanController.php  # Inti workflow peminjaman
│   │   │   │   └── PengajuanWorkflowController.php
│   │   │   └── Penjadwalan/
│   │   │       └── JadwalUjianController.php
│   │   ├── Kernel.php
│   │   ├── Middleware/
│   │   └── Requests/                 # Form Request Validation
│   ├── Imports/                      # Kelas import Excel (Maatwebsite)
│   ├── Models/
│   │   ├── User.php
│   │   ├── Role.php
│   │   ├── RoleUser.php
│   │   ├── RoleMenu.php
│   │   ├── Menu.php
│   │   ├── PengajuanRuangan.php      # Model pengajuan utama
│   │   ├── PengajuanRuanganItem.php  # Item ruangan dalam pengajuan
│   │   ├── PengajuanHistory.php      # Riwayat status pengajuan
│   │   ├── WorkflowStep.php          # Langkah-langkah workflow
│   │   ├── DataBaseBuilding.php
│   │   ├── DataBaseBuildingRoom.php
│   │   ├── DataBaseBuildingFacility.php
│   │   ├── BuildingFacilityRoom.php
│   │   ├── DataDocument.php
│   │   ├── JadwalUjian.php
│   │   ├── MasterDataDosen.php
│   │   ├── MasterDataHariLibur.php
│   │   ├── MasterDataKelas.php
│   │   ├── MasterDataKelasMataKuliah.php
│   │   ├── MasterDataMahasiswa.php
│   │   ├── MasterDataMataKuliah.php
│   │   ├── MasterDataPeriode.php
│   │   ├── MasterDataProgramStudi.php
│   │   ├── MasterSksSetting.php
│   │   ├── MatserOperationalSchedule.php
│   │   └── Notification.php
│   ├── Notifications/                # Laravel Notification classes
│   ├── Providers/                    # Service providers
│   ├── Services/
│   │   └── CspGeneratorService.php   # Algoritma CSP penjadwalan ujian
│   └── Traits/
│
├── database/
│   ├── migrations/                   # 34 file migrasi database
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   ├── RoleSeeder.php            # Seed 7 peran sistem
│   │   └── UserSeeder.php            # Seed akun pengguna default
│   └── factories/
│
├── resources/
│   ├── css/                          # Global styles
│   ├── js/
│   │   ├── app.js                    # Entry point Vue.js
│   │   ├── bootstrap.js
│   │   ├── core/                     # Shared / global assets
│   │   │   ├── App.vue
│   │   │   ├── assets/
│   │   │   ├── components/           # Komponen reusable (modal, tabel, dll)
│   │   │   ├── helper/               # Fungsi utilitas frontend
│   │   │   ├── layouts/              # Layout utama (sidebar, navbar)
│   │   │   ├── plugins/              # Plugin Vue (axios, dll)
│   │   │   ├── router/               # Vue Router konfigurasi
│   │   │   └── services/             # HTTP services (API calls)
│   │   └── modules/                  # Fitur-fitur modular
│   │       ├── auth/                 # Halaman login
│   │       ├── dashboard/            # Dashboard utama
│   │       ├── gedung/               # Manajemen gedung & ruangan
│   │       ├── list-pengajuan/       # Daftar & detail pengajuan
│   │       │   ├── components/
│   │       │   ├── page/
│   │       │   ├── router/
│   │       │   └── store/
│   │       ├── master-data/          # Halaman data master
│   │       ├── penjadwalan/          # Halaman penjadwalan ujian
│   │       └── settings/             # Pengaturan aplikasi
│   └── views/
│       └── app.blade.php             # Template HTML utama (SPA)
│
├── routes/
│   ├── api.php                       # Definisi semua API endpoint
│   ├── web.php                       # Route web (SPA catch-all)
│   ├── channels.php
│   └── console.php
│
├── config/                           # Konfigurasi Laravel
├── storage/                          # File upload, log, cache
├── tests/                            # Unit & Feature tests
├── public/                           # Aset publik & index.php
├── bootstrap/
│
├── .env                              # Konfigurasi environment (tidak di-commit)
├── .env.example                      # Template konfigurasi
├── composer.json                     # Dependensi PHP
├── package.json                      # Dependensi JavaScript
├── vite.config.js                    # Konfigurasi Vite
├── tailwind.config.js                # Konfigurasi Tailwind CSS
└── artisan                           # CLI Laravel
```

---

## 🌐 API Endpoints

Semua endpoint (kecuali `/api/login`) membutuhkan header `Authorization: Bearer {token}`.

### 🔐 Autentikasi

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `POST` | `/api/login` | Login & mendapatkan token |
| `POST` | `/api/logout` | Logout & invalidasi token |
| `GET` | `/api/user/profile` | Profil pengguna yang login |
| `GET` | `/api/user` | Data pengguna saat ini |
| `GET` | `/api/app-menu` | Menu dinamis berdasarkan peran |

### 🔔 Notifikasi

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `GET` | `/api/notifications` | Daftar semua notifikasi |
| `GET` | `/api/notifications/unread-count` | Jumlah notifikasi belum dibaca |
| `POST` | `/api/notifications/mark-all-read` | Tandai semua sudah dibaca |
| `POST` | `/api/notifications/{id}/mark-read` | Tandai satu notifikasi |

### 🏢 Gedung & Ruangan

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `GET` | `/api/building/buildings` | Daftar gedung (dengan filter) |
| `GET` | `/api/building/buildings-simple` | Daftar gedung (list only) |
| `POST` | `/api/building/buildings` | Tambah gedung baru |
| `PUT` | `/api/building/buildings/{id}` | Update gedung |
| `DELETE` | `/api/building/buildings/{id}` | Hapus gedung |
| `GET` | `/api/building/rooms` | Daftar ruangan |
| `GET` | `/api/building/rooms/{id}/facilities` | Fasilitas per ruangan |
| `GET` | `/api/building/excel/template` | Download template Excel ruangan |
| `POST` | `/api/building/excel/import` | Import data ruangan via Excel |

### 📚 Data Master

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `GET/POST/PUT/DELETE` | `/api/master-data/program-studi` | CRUD Program Studi |
| `GET/POST/PUT/DELETE` | `/api/master-data/kelas` | CRUD Kelas |
| `GET/POST/PUT/DELETE` | `/api/master-data/mata-kuliah` | CRUD Mata Kuliah |
| `GET/POST/PUT/DELETE` | `/api/master-data/mahasiswa` | CRUD Mahasiswa |
| `GET/POST/PUT/DELETE` | `/api/master-data/dosen` | CRUD Dosen |
| `GET/POST/PUT/DELETE` | `/api/master-data/hari-libur` | CRUD Hari Libur |
| `GET/POST/PUT/DELETE` | `/api/master-data/kelas-mata-kuliah` | CRUD Kelas-Mata Kuliah |
| `GET/POST/PUT/DELETE` | `/api/master-data/periodes` | CRUD Periode |
| `GET` | `/api/master-data/excel/{entity}/template` | Download template Excel |
| `POST` | `/api/master-data/excel/{entity}/import` | Import data master via Excel |

### 📝 Pengajuan Peminjaman

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `GET` | `/api/pengajuan/peminjaman` | Daftar pengajuan |
| `POST` | `/api/pengajuan/peminjaman` | Buat pengajuan baru |
| `GET` | `/api/pengajuan/peminjaman/{id}` | Detail pengajuan |
| `PUT` | `/api/pengajuan/peminjaman/{id}` | Update pengajuan (revisi) |
| `GET` | `/api/pengajuan/peminjaman/export` | Export data ke Excel |
| `POST` | `/api/pengajuan/approve` | Setujui pengajuan |
| `POST` | `/api/pengajuan/reject` | Tolak pengajuan |
| `POST` | `/api/pengajuan/revision` | Kembalikan untuk revisi |
| `GET` | `/api/pengajuan/peminjaman/{id}/workflow` | Riwayat workflow |

### 🗓️ Penjadwalan Ujian

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `GET` | `/api/jadwal` | Daftar jadwal ujian permanen |
| `GET` | `/api/jadwal/draft` | Daftar jadwal ujian draft |
| `POST` | `/api/jadwal/generate` | Generate jadwal otomatis (CSP) |
| `POST` | `/api/jadwal/draft` | Simpan jadwal sebagai draft |
| `PATCH` | `/api/jadwal/draft/{id}` | Update baris jadwal draft |
| `PATCH` | `/api/jadwal/permanen` | Permanenkan jadwal draft |
| `DELETE` | `/api/jadwal/draft` | Hapus semua jadwal draft |

---

## 🔄 Alur Workflow

Sistem menggunakan model **Workflow Step** yang fleksibel dan dikonfigurasi di database. Berikut adalah contoh alur persetujuan peminjaman ruangan:

```
[MAHASISWA/DOSEN]
       │
       │ Buat Pengajuan
       ▼
┌─────────────┐
│   PENDING   │  ◄─── Status awal setelah pengajuan
└──────┬──────┘
       │ Review oleh TENAGA_TU
       ▼
┌─────────────────────┐     ┌───────────┐
│   DIPROSES (TU)     │────►│  KOREKSI  │ ──► Pemohon revisi & ajukan ulang
└──────┬──────────────┘     └───────────┘
       │ Lanjut ke Wadir
       ▼
┌──────────────────────┐    ┌──────────┐
│  MENUNGGU WADIR 1    │───►│  DITOLAK │
└──────┬───────────────┘    └──────────┘
       │ Disetujui Wadir 1
       ▼
┌──────────────────────┐
│  MENUNGGU WADIR 2    │
└──────┬───────────────┘
       │ Disetujui Wadir 2
       ▼
┌─────────────┐
│  DISETUJUI  │  ◄─── Status final (is_final = true)
└─────────────┘
```

Setiap transisi status dicatat pada tabel `pengajuan_histories` sebagai **audit trail** lengkap.

---

## 👥 Peran & Hak Akses

| Peran | Deskripsi | Hak Akses Utama |
|-------|-----------|-----------------|
| `ADMIN` | Administrator Sistem | Kontrol penuh terhadap sistem dan manajemen pengguna |
| `DIREKTUR` | Pimpinan tertinggi kampus | Lihat semua data, laporan |
| `WADIR 1` | Wakil Direktur Bidang Akademik | Persetujuan akhir pengajuan |
| `WADIR 2` | Wakil Direktur Bidang Umum | Persetujuan tingkat 2 |
| `KAPRODI` | Kepala Program Studi | Manajemen data akademik |
| `DOSEN` | Tenaga Pengajar | Pengajuan peminjaman, lihat jadwal |
| `TENAGA_TU` | Staff Tata Usaha / Admin Sarpras | Verifikasi awal, manajemen gedung & ruangan |
| `MAHASISWA` | Mahasiswa aktif | Pengajuan peminjaman |

> Satu pengguna dapat memiliki **lebih dari satu peran** (misalnya Wadir 1 juga berstatus Dosen).

---

## 🔑 Akun Default (Seeder)

Setelah menjalankan `php artisan db:seed`, akun-akun berikut tersedia untuk testing:

| Nama | Username | Email | Peran | Password |
|------|----------|-------|-------|----------|
| Super Admin | `admin_sistem` | admin@kampus.ac.id | Admin | `password123` |
| Prof. Ahmad Subagjo | `ahmad_dir` | ahmad@kampus.ac.id | Direktur, Dosen | `password123` |
| Dr. Ir. Heru Prasetyo, M.T. | `heru_wadir1` | heru.wadir1@kampus.ac.id | Wadir 1, Dosen | `password123` |
| Dra. Siti Aminah, M.Si. | `siti_wadir2` | siti.wadir2@kampus.ac.id | Wadir 2, Dosen | `password123` |
| Admin Sarpras | `admin_tu` | tu@kampus.ac.id | Tenaga TU | `password123` |
| Andi Wijaya | `andi_mhs` | andi@student.kampus.ac.id | Mahasiswa | `password123` |

> ⚠️ **Catatan Keamanan:** Segera ganti password default sebelum deploy ke lingkungan production!

---

## 🤝 Kontribusi

Kontribusi sangat disambut! Untuk berkontribusi:

1. **Fork** repository ini
2. Buat **branch** baru untuk fitur/perbaikan Anda:
   ```bash
   git checkout -b feature/nama-fitur-anda
   ```
3. **Commit** perubahan Anda dengan pesan yang deskriptif:
   ```bash
   git commit -m "feat: tambahkan fitur notifikasi email"
   ```
4. **Push** ke branch Anda:
   ```bash
   git push origin feature/nama-fitur-anda
   ```
5. Buat **Pull Request** ke branch `main`

### Konvensi Commit Message

Gunakan format [Conventional Commits](https://www.conventionalcommits.org/):

| Prefix | Digunakan untuk |
|--------|----------------|
| `feat:` | Fitur baru |
| `fix:` | Perbaikan bug |
| `docs:` | Perubahan dokumentasi |
| `refactor:` | Refactoring kode |
| `chore:` | Perubahan konfigurasi/build |

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).

---

<div align="center">

**Dibuat dengan ❤️ untuk lingkungan kampus**

*Laravel 10 · Vue.js 3 · Vite · Tailwind CSS · DaisyUI*

</div>
