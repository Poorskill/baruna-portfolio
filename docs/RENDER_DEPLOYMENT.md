# PANDUAN DEPLOYMENT OTOMATIS RENDER (DOCKER + GITHUB CI/CD)
# Baruna Dwi Cahya Personal Portfolio & CMS

Dokumen ini menjelaskan alur, konfigurasi, dan prosedur lengkap untuk mendeploy portofolio pribadi & CMS Baruna Dwi Cahya dari repository GitHub ke Render secara otomatis setiap kali ada `git push`.

---

## 1. Arsitektur Alur Deployment

```text
+-----------------------+        git push          +-----------------------+
|  Local Development    | ----------------------> |   GitHub Repository   |
| (Windows + Laragon)   |                         |  (branch: main)       |
+-----------------------+                         +-----------------------+
                                                              |
                                           +------------------+------------------+
                                           |                                     |
                                           v                                     v
                        +----------------------+              +----------------------+
                        | GitHub Actions CI    |              | Render Webhook       |
                        | - Composer & NPM     |              | - Pull latest commit |
                        | - Vue/Vite Build     |              | - Multi-stage Docker |
                        | - 53 PHPUnit Tests   |              | - Nginx + PHP 8.3 FPM|
                        | - Pint Style Check   |              | - Health Check (/up) |
                        +----------------------+              +----------------------+
                                                                                 |
                                                                                 v
                                                              +----------------------+
                                                              |   Website Publik     |
                                                              |   *.onrender.com     |
                                                              +----------------------+
```

---

## 2. Berkas Konfigurasi Deployment yang Telah Disiapkan

1. **`Dockerfile`**: Multi-stage build (Stage 1: Node 22 Vite build, Stage 2: Composer 2 no-dev, Stage 3: PHP 8.3-FPM + Nginx di Alpine).
2. **`.dockerignore`**: Mencegah file rahasia (`.env`), cache lokal, `node_modules`, dan `vendor` masuk ke build image.
3. **`docker/nginx.conf`**: Server block Nginx untuk port dinamis Render (`${PORT}` default `10000`), reverse proxy PHP-FPM, kompresi gzip, dan cache aset statis.
4. **`docker/supervisord.conf`**: Process manager untuk menjalankan Nginx dan PHP-FPM secara bersamaan di foreground.
5. **`docker/php.ini`**: Konfigurasi PHP produksi (`memory_limit = 256M`, `opcache.enable = 1`, `upload_max_filesize = 20M`).
6. **`docker/entrypoint.sh`**: Startup script untuk substitusi port Nginx, penyesuaian izin `storage`, symlink storage, dan warming cache route/view.
7. **`render.yaml`**: Spesifikasi Render Blueprint untuk web service Docker (`plan: free`, `region: singapore`, `healthCheckPath: /up`).
8. **`.github/workflows/ci.yml`**: Automated pipeline yang memvalidasi build asset frontend, format kode, dan 53 test suites di SQLite in-memory pada setiap push/PR.
9. **`.github/workflows/migrate.yml`**: Manual trigger (`workflow_dispatch`) untuk menjalankan `php artisan migrate --force` ke database produksi secara terkontrol.

---

## 3. Langkah Inisialisasi Git & Upload ke GitHub

Karena folder lokal saat ini belum memiliki repository Git, lakukan langkah berikut di terminal PowerShell:

### Langkah A: Inisialisasi Git Lokal
```powershell
Set-Location "E:\Portofolio\baruna-portfolio"

# 1. Inisialisasi git repository
git init

# 2. Pastikan branch utama bernama main
git branch -M main

# 3. Periksa status file (pastikan .env tidak terdeteksi karena sudah di-.gitignore)
git status --short

# 4. Tambahkan seluruh berkas proyek
git add .

# 5. Buat commit pertama
git commit -m "feat: complete portfolio & cms with render docker deployment config"
```

### Langkah B: Hubungkan ke GitHub
1. Buat repository baru di [GitHub](https://github.com/new) (misal bernama `baruna-portfolio`). Pilih **Private** atau **Public**.
2. Sambungkan remote repository dan lakukan push:
```powershell
# Ganti URL di bawah dengan URL repository GitHub Anda
git remote add origin https://github.com/<username-github>/baruna-portfolio.git

# Push ke GitHub
git push -u origin main
```

---

## 4. Konfigurasi Database MySQL Produksi

Render Web Service paket Free tidak menyertakan managed MySQL gratis (hanya PostgreSQL yang kedaluwarsa setelah 30 hari). Karena portofolio ini dibangun menggunakan MySQL, pilih salah satu penyedia MySQL gratis/terjangkau:

### Rekomendasi Penyedia Database MySQL Cloud:
1. **TiDB Serverless** (Recommended, Free tier 5GB, 100% MySQL 8.0 compatible, latency rendah ke Singapore).
2. **Aiven MySQL** (Tersedia free trial / tier).
3. **Railway / PlanetScale** (Mendukung MySQL).
4. **VPS Sendiri** (MySQL pada VPS Ubuntu).

### Variabel Database yang Diperlukan:
- `DB_CONNECTION`: `mysql`
- `DB_HOST`: Hostname dari provider (misal: `gateway01.ap-southeast-1.prod.aws.tidbcloud.com`)
- `DB_PORT`: `3306` (atau port yang diberikan provider)
- `DB_DATABASE`: Nama database (misal: `baruna_portfolio`)
- `DB_USERNAME`: Username database
- `DB_PASSWORD`: Password database

---

## 5. Konfigurasi Penyimpanan Media (Upload Gambar & Dokumen)

Pada **Render Free Tier**, filesystem container bersifat *ephemeral* (file baru yang diunggah akan hilang jika container di-restart atau di-deploy ulang).

### Solusi Penyimpanan Permanen:
1. **Cloudflare R2** (Sangat Direkomendasikan — Gratis 10GB, Zero Egress Fees, S3-Compatible):
   - Buat bucket di Cloudflare R2 (misal `baruna-portfolio`).
   - Buat API Token dengan izin Read & Write.
   - Atur environment variable pada Render:
     - `FILESYSTEM_DISK_PUBLIC`: `s3`
     - `AWS_ACCESS_KEY_ID`: `<R2 Access Key ID>`
     - `AWS_SECRET_ACCESS_KEY`: `<R2 Secret Access Key>`
     - `AWS_DEFAULT_REGION`: `auto`
     - `AWS_BUCKET`: `baruna-portfolio`
     - `AWS_ENDPOINT`: `https://<account_id>.r2.cloudflarestorage.com`
     - `AWS_URL`: `https://pub-<id>.r2.dev` (atau custom domain R2 Anda)
     - `AWS_USE_PATH_STYLE_ENDPOINT`: `false`
2. **Disk Lokal Sementara (Default)**:
   - Jika belum mengaktifkan Cloudflare R2, biarkan `FILESYSTEM_DISK_PUBLIC=local`. File akan disimpan di `/var/www/html/storage/app/public` selama container aktif.

---

## 6. Langkah Menghubungkan Repository ke Render

### Opsi 1: Menggunakan Render Blueprint (Recommended)
1. Login ke [Render Dashboard](https://dashboard.render.com).
2. Klik tombol **New +** di kanan atas, pilih **Blueprint**.
3. Hubungkan akun GitHub Anda jika belum terhubung, lalu pilih repository `baruna-portfolio`.
4. Render akan otomatis mendeteksi berkas `render.yaml`.
5. Render akan meminta Anda memasukkan environment variables bertanda `sync: false`:
   - `APP_KEY`: Generate via terminal lokal: `php artisan key:generate --show`
   - `APP_URL`: Masukkan URL yang diberikan Render (misal: `https://baruna-portfolio.onrender.com`)
   - `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`: Kredensial MySQL Anda.
6. Klik **Apply**. Render akan memulai build container Docker secara otomatis.

### Opsi 2: Menggunakan Web Service Manual
1. Di Render Dashboard, klik **New +** -> **Web Service**.
2. Pilih repository `baruna-portfolio`.
3. Isi konfigurasi:
   - **Name**: `baruna-portfolio`
   - **Region**: `Singapore`
   - **Branch**: `main`
   - **Runtime**: `Docker`
   - **Instance Type**: `Free`
   - **Health Check Path**: `/up`
4. Di tab **Environment Variables**, masukkan seluruh variabel sesuai daftar di bagian 7.
5. Klik **Create Web Service**.

---

## 7. Daftar Lengkap Environment Variables Produksi

| Key | Value Contoh / Keterangan | Rahasia? |
|---|---|---|
| `APP_NAME` | `Baruna Dwi Cahya` | Tidak |
| `APP_ENV` | `production` | Tidak |
| `APP_DEBUG` | `false` | Tidak |
| `APP_KEY` | `base64:xxxx...` (generate via `php artisan key:generate --show`) | **YA** |
| `APP_URL` | `https://baruna-portfolio.onrender.com` | Tidak |
| `LOG_CHANNEL` | `stderr` | Tidak |
| `LOG_LEVEL` | `info` | Tidak |
| `DB_CONNECTION` | `mysql` | Tidak |
| `DB_HOST` | `gateway01.ap-southeast-1.prod.aws.tidbcloud.com` | **YA** |
| `DB_PORT` | `3306` | Tidak |
| `DB_DATABASE` | `baruna_portfolio` | **YA** |
| `DB_USERNAME` | `xxxx` | **YA** |
| `DB_PASSWORD` | `xxxx` | **YA** |
| `SESSION_DRIVER` | `database` | Tidak |
| `CACHE_STORE` | `database` | Tidak |
| `QUEUE_CONNECTION` | `sync` | Tidak |
| `FILESYSTEM_DISK` | `local` | Tidak |
| `FILESYSTEM_DISK_PUBLIC`| `local` (atau `s3` jika pakai Cloudflare R2) | Tidak |
| `PORT` | `10000` (disediakan otomatis oleh Render) | Tidak |

---

## 8. Menjalankan Migrasi Database dan Akun Admin Pertama

Jangan memasukkan command migration ke dalam `entrypoint.sh` container web service agar proses deployment tidak terkunci jika ada koneksi database lambat.

### Cara Menjalankan Migrasi Produksi:
#### Pilihan A: Menggunakan GitHub Actions (Sangat Mudah & Aman)
1. Di GitHub repository, buka **Settings** -> **Secrets and variables** -> **Actions**.
2. Tambahkan Repository Secrets:
   - `APP_KEY`
   - `DB_HOST`
   - `DB_DATABASE`
   - `DB_USERNAME`
   - `DB_PASSWORD`
3. Masuk ke tab **Actions** di GitHub -> pilih workflow **Production Database Migration** -> klik **Run workflow**.
4. Workflow akan menjalankan `php artisan migrate --force` secara aman dari runner GitHub ke database cloud Anda.

#### Pilihan B: Menjalankan dari Komputer Lokal ke Database Cloud
Jika IP lokal Anda diizinkan mengakses database cloud:
```powershell
php artisan migrate --force
```

### Membuat Akun Admin Pertama:
Jalankan Tinker di komputer lokal yang terkoneksi ke DB cloud atau via script SQL:
```bash
php artisan tinker --execute="App\Models\User::updateOrCreate(['email' => 'admin@baruna.test'], ['name' => 'Baruna Dwi Cahya', 'password' => 'password_rahasia_anda', 'is_admin' => true]);"
```

---

## 9. Karakteristik & Batasan Render Free Tier

1. **Spin-down Saat Inaktif**: Web service gratis akan tertidur (*sleep*) jika tidak ada pengunjung selama 15 menit.
2. **Cold Start Latency**: Saat ada pengunjung pertama setelah tidur, service membutuhkan waktu sekitar 30–50 detik untuk bangun dan melayani request.
3. **Filesystem Ephemeral**: Data pada disk container hilang saat container di-restart (gunakan Cloudflare R2 untuk aset upload CMS permanen).
4. **Health Check `/up`**: Render secara otomatis memantau endpoint `/up` setiap beberapa menit. Jika endpoint merespons HTTP 200, service dinyatakan sehat (`Live`).

---

## 10. Panduan Verifikasi Setelah Deploy

1. Buka URL Render: `https://<nama-service>.onrender.com`.
2. Pastikan halaman publik memuat Hero Three.js, featured project carousel, dan footer dengan benar.
3. Buka `/login` dan login menggunakan akun admin yang telah dibuat.
4. Masuk ke `/admin` dan uji pengunggahan foto profil atau proyek untuk memverifikasi fungsionalitas CMS.
5. Periksa log deployment di Render Dashboard: pastikan statusnya **Live** dengan indikator hijau.
