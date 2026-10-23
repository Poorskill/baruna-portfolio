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

## 6. Checklist Langkah Selanjutnya

1. [ ] Buka konsol Aiven dan reset password user `avnadmin`.
2. [ ] Buka berkas `.env` lokal Anda di `E:\Portofolio\baruna-portfolio\.env`.
3. [ ] Masukkan password baru pada baris `DB_PASSWORD=`.
4. [ ] Jalankan pengujian: `php artisan db:check`.
5. [ ] Jalankan migrasi: `php artisan migrate --force`.
6. [ ] Buat akun admin: `php artisan admin:create`.
7. [ ] Masukkan kredensial yang sama ke Environment Variables Render Dashboard.
