# Portofolio Pribadi & CMS — Baruna Dwi Cahya

Situs portofolio pribadi dan Content Management System (CMS) terintegrasi berbasis Laravel 13, Inertia.js, Vue 3, TypeScript, dan Tailwind CSS. Dibangun dengan sistem desain **Obsidian Precision** (dark minimalism, editorial tech, typography Geist & JetBrains Mono, accent lime `#D7FF64`).

---

## Persyaratan Sistem

- **Sistem Operasi**: Windows 10/11 (disarankan lingkungan Laragon) atau Linux/macOS
- **PHP**: ^8.3 (ekstensi: `pdo_mysql`, `mbstring`, `fileinfo`, `gd`/`imagick`, `curl`)
- **Composer**: ^2.7
- **Node.js**: ^20.x atau ^22.x
- **NPM**: ^10.x
- **Basis Data**: MySQL 8.0+ / MariaDB 10.4+

---

## Panduan Instalasi Lokal (Windows + Laragon)

### 1. Buka Terminal Workspace
Jalankan PowerShell atau terminal Laragon dan arahkan ke direktori proyek:
```powershell
Set-Location "E:\Portofolio\baruna-portfolio"
```

### 2. Pasang Dependensi Backend & Frontend
```powershell
composer install
npm install
```

### 3. Konfigurasi Lingkungan (.env)
Salin berkas contoh konfigurasi:
```powershell
Copy-Item .env.example .env
php artisan key:generate
```

Sesuaikan koneksi basis data di `.env` (Lokal atau Aiven Cloud MySQL):
```env
# Opsi 1: Aiven Cloud MySQL
DB_CONNECTION=mysql
DB_HOST=mysql-37b0b0c8-barunadcahya-aa2c.d.aivencloud.com
DB_PORT=26297
DB_DATABASE=defaultdb
DB_USERNAME=avnadmin
DB_PASSWORD=<password_baru_aiven>
MYSQL_ATTR_SSL_CA=

# Opsi 2: MySQL Lokal (Laragon / XAMPP)
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=baruna_portfolio
# DB_USERNAME=root
# DB_PASSWORD=
```

Uji koneksi basis data kapan saja dengan perintah aman:
```powershell
php artisan db:check
```

### 4. Jalankan Migrasi & Buat Symlink Media
```powershell
php artisan migrate
php artisan storage:link
```

### 5. Buat Akun Administrator
Gunakan perintah interaktif Artisan untuk mendaftarkan akun administrator pertama:
```powershell
php artisan admin:create
```
Perintah akan meminta nama, email, dan password administrator secara aman.
*(Catatan: Registrasi akun publik dinonaktifkan secara ketat pada sistem ini).*

---

## Menjalankan Aplikasi Development

Jalankan server aplikasi Laravel dan bundler Vite pada dua terminal terpisah:

**Terminal 1 (Laravel Server):**
```powershell
php artisan serve
```
Aplikasi dapat diakses pada `http://localhost:8000`.

**Terminal 2 (Vite Hot Module Replacement):**
```powershell
npm run dev
```

---

## Pengujian & Verifikasi (Automated Tests)

Suite pengujian fitur otomatis menggunakan PHPUnit dengan basis data SQLite in-memory:
```powershell
php artisan test
```

Skenario pengujian mencakup:
- Proteksi draft dan konten terarsip (404 untuk publik dan non-admin).
- Autentikasi dan otorisasi middleware admin guard.
- Operasi CRUD katalog proyek, validasi slug unik, serta relasi keahlian.
- Rate limiting form kontak (3 percobaan / IP / jam) dan proteksi spam honeypot.
- Penonaktifan registrasi akun publik.

---

## Kompilasi Aset Produksi

Untuk menghasilkan bundle produksi berukuran optimal:
```powershell
npm run build
```
Menghasilkan bundle dengan pemeriksaan tipe ketat (`vue-tsc`) dan 0 error ke direktori `public/build`.

---

## Struktur Modul & Fitur Terpasang

### 1. Halaman Publik
- `/`: Beranda dinamis (Hero, ringkasan profil, proyek unggulan, keahlian terstruktur, CTA).
- `/about`: Biografi lengkap, riwayat karir/magang, pendidikan, sertifikat terverifikasi, serta unduh CV PDF.
- `/projects`: Katalog proyek dengan pencarian judul/ringkasan, filter kategori, dan pagination.
- `/projects/{slug}`: Studi kasus teknis mendalam (problem, solusi, fitur fungsional, kendala, galeri tangkapan layar, link demo & source).
- `/contact`: Formulir kontak aman dengan validasi server, proteksi honeypot, dan rate limiter.
- `/cv`: Endpoint pengunduhan berkas PDF CV aktif.

### 2. Panel CMS Admin (`/admin`)
- **Dashboard**: Ringkasan statistik riil basis data (proyek terbit/draft, jumlah skill, pesan unread).
- **Katalog Proyek**: CRUD proyek, pengurutan, pemilihan featured, upload cover & multiple gallery images.
- **Keahlian & Kategori**: Manajemen grup keahlian dan katalog teknologi.
- **Karir & Pendidikan**: Manajemen riwayat organisasi dan studi formal.
- **Sertifikasi**: Pengelolaan kredensial terverifikasi beserta tautan validasi.
- **Konten Halaman Utama & Profil**: Pengaturan teks hero, deskripsi, CTA, dan penggantian berkas CV.
- **Tautan Navigasi & Sosial**: Pengaturan dinamis menu header, footer, dan tautan platform sosial.
- **Kotak Masuk Pesan**: Tinjauan pesan pengunjung, penandaan status baca, dan penghapusan aman.
- **Audit Log**: Pencatatan riwayat aksi administratif tanpa membocorkan data sensitif.
- **Pengaturan Global**: Identitas brand, SEO default, kontak langsung, dan teks hak cipta.

---

## Standar Keamanan & Desain

- **Otorisasi Sisi Server**: Rute admin dilindungi middleware `auth` dan `admin`.
- **Anti-XSS**: Seluruh teks pengguna dan pesan kontak dirender melalui teks ter-escape tanpa `v-html`.
- **Integritas Unggahan**: Validasi tipe MIME dan batasan ukuran server (maks 5MB untuk gambar raster, 10MB untuk PDF CV). Ekstensi berbahaya ditolak.
- **Desain Bebas Ornamen AI**: Bebas emoji dekoratif, bebas metrik fiktif, serta layout presisi sesuai `DESIGN.md`.

---

## Deployment Otomatis ke Render (Docker + GitHub CI/CD)

Proyek ini telah dikonfigurasi penuh untuk deployment otomatis ke Render:

1. **Dockerfile Multi-Stage**: Mengompilasi asset Vite/Vue 3 di Node 22, menginstal dependensi Composer produksi, dan menjalankan server Nginx + PHP 8.3-FPM di atas Alpine Linux.
2. **Render Blueprint (`render.yaml`)**: Mengotomatisasi konfigurasi web service Docker di region Singapore dengan health check `/up`.
3. **GitHub Actions CI (`.github/workflows/ci.yml`)**: Menguji build asset dan 53 test suites otomatis di setiap push/PR ke branch `main`.
4. **Migrasi Database Produksi (`.github/workflows/migrate.yml`)**: Workflow manual yang aman untuk migrasi skema database cloud tanpa mengunci kontainer.

> Panduan langkah demi langkah selengkapnya dapat dibaca pada [docs/RENDER_DEPLOYMENT.md](docs/RENDER_DEPLOYMENT.md).

