# PANDUAN INTEGRASI DATABASE AIVEN MYSQL & DEPLOYMENT
# Baruna Dwi Cahya Personal Portfolio & CMS

Dokumen ini mencatat konfigurasi basis data remote **Aiven MySQL**, prosedur pengujian koneksi, verifikasi migrasi yang aman, serta evaluasi platform deployment (**Vercel vs. Render**).

---

## 1. Rincian Koneksi Aiven MySQL

- **Host**: `mysql-37b0b0c8-barunadcahya-aa2c.d.aivencloud.com`
- **Port**: `26297`
- **Database**: `defaultdb`
- **Username**: `avnadmin`
- **SSL / TLS**: `REQUIRED` (Enforced oleh Aiven)
- **Password**: Diisi secara lokal melalui `.env` atau environment variable platform deployment (jangan pernah commit password ke Git).

### Variabel `.env` yang Digunakan:
```dotenv
DB_CONNECTION=mysql
DB_HOST=mysql-37b0b0c8-barunadcahya-aa2c.d.aivencloud.com
DB_PORT=26297
DB_DATABASE=defaultdb
DB_USERNAME=avnadmin
DB_PASSWORD=<masukkan_password_baru_aiven_anda>
MYSQL_ATTR_SSL_CA=
```

---

## 2. Penanganan SSL / TLS pada PHP & Laravel

Aiven MySQL menerapkan `require_secure_transport=ON`.
Pada driver PHP `pdo_mysql` (mysqlnd):
1. **Default Mode (Tanpa File CA)**: PHP mysqlnd secara otomatis melakukan negosiasi enkripsi TLS dengan server MySQL Aiven.
2. **Strict Verification (Dengan File CA)**: Jika sertifikat CA Aiven (`ca.pem`) diunduh dari konsol Aiven, atur path-nya pada `MYSQL_ATTR_SSL_CA`:
   ```dotenv
   MYSQL_ATTR_SSL_CA=storage/app/ca.pem
   ```
3. **Kompatibilitas PHP 8.3**: Konfigurasi `config/database.php` telah diamankan menggunakan `(defined('PDO::MYSQL_ATTR_SSL_CA') ? PDO::MYSQL_ATTR_SSL_CA : 1007)` agar tidak memicu fatal error class `Pdo\Mysql` yang belum ada pada PHP 8.3.

---

## 3. Prosedur Verifikasi Koneksi Aman (`db:check`)

Telah disiapkan command Artisan khusus yang aman:
```powershell
php artisan db:check
```

Perintah ini melakukan:
1. Validasi parameter koneksi tanpa menampilkan string password.
2. Uji coba koneksi PDO ke host dan port Aiven (26297).
3. Eksekusi query read-only `SELECT VERSION()` dan `SELECT CURRENT_USER()`.
4. Pemeriksaan status enkripsi TLS: `SHOW STATUS LIKE 'Ssl_cipher'`.
5. Inspeksi daftar tabel yang ada di `defaultdb` tanpa mengubah data apa pun.
6. Pengecekan tabel `migrations` dan riwayat skema.

---

## 4. Prosedur Migrasi Skema Basis Data

### Aturan Keselamatan:
- **DILARANG** menjalankan `migrate:fresh`, `db:wipe`, atau operasi penghapusan tabel pada database produksi.
- Periksa status migrasi terlebih dahulu:
  ```powershell
  php artisan migrate:status
  ```
- Jika database masih kosong (`defaultdb` belum memiliki tabel), jalankan migrasi terarah:
  ```powershell
  php artisan migrate --force
  ```
- Setelah migrasi selesai, buat akun administrator pertama melalui command resmi:
  ```powershell
  php artisan admin:create
  ```

---

## 5. Analisis Kelayakan Deployment: Vercel vs. Render

### Opsi A: Render (Sudah Dikonfigurasi Penuh & Sangat Direkomendasikan)
- **Status Proyek**: File `Dockerfile`, `render.yaml`, `docker/nginx.conf`, dan GitHub Actions CI sudah terpasang dan teruji di repository.
- **Kelebihan**:
  - Menjalankan kontainer Docker standar (Nginx + PHP 8.3-FPM) tanpa trik serverless.
  - Mendukung request berdurasi panjang, upload file hingga 25MB, dan memory limit 256MB.
  - Health check bawaan di `/up`.
  - Terkoneksi langsung ke Aiven MySQL melalui port `26297`.

### Opsi B: Vercel (Hambatan Teknis & Keterbatasan Serverless)
Jika Anda ingin tetap menggunakan Vercel, berikut hambatan teknis yang harus dipahami:
1. **Ketiadaan Runtime PHP Bawaan**: Vercel hanya mendukung Node.js, Edge, Go, Python, dan Ruby secara resmi. Menjalankan Laravel memerlukan runtime komunitas pihak ketiga seperti `vercel-php`.
2. **Filesystem Read-Only**: Direktori aplikasi di Vercel bersifat read-only. Laravel membutuhkan akses tulis ke `storage/` dan `bootstrap/cache`, sehingga memerlukan bootstrap khusus untuk me-redirect cache ke direktori `/tmp` (ephemeral).
3. **Execution Timeout**: Batas waktu eksekusi serverless function Vercel Free adalah 10–15 detik.
4. **Batas Payload 4.5MB**: Unggahan berkas lebih dari 4.5MB ditolak langsung oleh API gateway Vercel (membatasi upload berkas PDF CV 10MB).
5. **Storage Media**: Wajib menggunakan Cloudflare R2 atau AWS S3 karena storage lokal Vercel langsung hilang setelah request selesai.

---

## 6. Panduan Deployment ke Vercel

Konfigurasi serverless Vercel telah disiapkan di repositori:
- `vercel.json`: Konfigurasi runtime `vercel-php@0.7.3` dan URL rewriting ke `api/index.php`.
- `api/index.php`: Serverless bridge yang otomatis membuat direktori writable di `/tmp/storage` dan mem-forward request ke kernel Laravel.
- `.vercelignore`: Mengabaikan file yang tidak dibutuhkan untuk meminimalkan ukuran payload serverless.
- `bootstrap/app.php`: Telah mendukung `env('APP_STORAGE')` dinamis untuk merutekan cache & views ke `/tmp/storage`.

### Langkah-langkah Deploy ke Vercel:
1. Push branch `main` ke GitHub setelah perubahan di-commit.
2. Buka [vercel.com](https://vercel.com) dan login dengan akun GitHub Anda.
3. Klik **Add New...** &rarr; **Project**.
4. Pilih repositori **`Poorskill/baruna-portfolio`** &rarr; klik **Import**.
5. Di bagian **Build and Output Settings**:
   - Framework Preset: **Other**
   - Root Directory: `./` (atau default root)
   - Build Command: Biarkan default (`npm run build`)
   - Output Directory: Biarkan default (`public`)
6. Buka bagian **Environment Variables**, lalu tambahkan:
   - `APP_NAME`: `Baruna Dwi Cahya`
   - `APP_ENV`: `production`
   - `APP_DEBUG`: `false`
   - `APP_KEY`: `base64:qlZJ9tzzZ6oca62Db5WB7hA7hG4i3Z6gMGFiYmDW//Q=`
   - `APP_URL`: URL Vercel Anda (misal `https://baruna-portfolio.vercel.app`)
   - `DB_CONNECTION`: `mysql`
   - `DB_HOST`: `mysql-37b0b0c8-barunadcahya-aa2c.d.aivencloud.com`
   - `DB_PORT`: `26297`
   - `DB_DATABASE`: `defaultdb`
   - `DB_USERNAME`: `avnadmin`
   - `DB_PASSWORD`: *(password Aiven Anda)*
   - `SESSION_DRIVER`: `database`
   - `CACHE_STORE`: `database`
   - `LOG_CHANNEL`: `stderr`
   - `APP_STORAGE`: `/tmp/storage`
   - `VIEW_COMPILED_PATH`: `/tmp/storage/framework/views`
7. Klik **Deploy**.

---

## 7. Checklist Status Akhir

1. [x] Database Aiven MySQL 8.4 aktif di port 26297.
2. [x] 17 migrasi database terpasang di `defaultdb` (23 tabel).
3. [x] Akun admin `barunadcahya@gmail.com` dan seeders dasar terisi.
4. [x] Konfigurasi serverless Vercel (`vercel.json`, `api/index.php`, `.vercelignore`) siap.
5. [x] Konfigurasi Docker Render (`Dockerfile`, `render.yaml`) tetap siap sebagai alternatif.
