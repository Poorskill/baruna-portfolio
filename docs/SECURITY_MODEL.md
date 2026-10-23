# SECURITY_MODEL.md — Model dan Standar Keamanan Aplikasi
# Baruna Dwi Cahya Personal Portfolio & CMS

## 1. Prinsip Keamanan Utama

Keamanan aplikasi berpusat pada perlindungan hak akses server-side di Laravel, bukan hanya pengkondisian visual di Vue. Input dari browser diperlakukan sebagai untrusted input.

---

## 2. Autentikasi dan Otorisasi (Auth & Access Control)

### A. Autentikasi Admin
- Berbasis session bawaan Laravel (`laravel/breeze` starter kit).
- Pengguna login divalidasi kredensialnya dan session di-regenerate untuk mencegah *Session Fixation*.
- Cookie session dikonfigurasi dengan flag `httpOnly`, `sameSite=lax`, dan `secure` di lingkungan HTTPS production.

### B. Otorisasi Admin Middleware (`EnsureAdmin`)
- Seluruh endpoint di bawah `/admin/*` dilindungi oleh middleware `['auth', 'admin']`.
- Implementasi pada `app/Http/Middleware/EnsureAdmin.php`:
  ```php
  if (! $request->user() || ! $request->user()->is_admin) {
      abort(403);
  }
  ```
- **Guest** yang mencoba membuka `/admin/*` dialihkan ke `/login`.
- **Pengguna login non-admin** (`is_admin === false`) langsung menerima respons HTTP 403 Forbidden.
- Otorisasi diuji secara otomatis pada `tests/Feature/AdminGuardTest.php`.

### C. Penutupan Registrasi Publik (Disabled Public Registration)
- Portofolio ini adalah sistem *Single-Admin*. Registrasi akun publik ditiadakan secara permanen.
- `app/Http/Controllers/Auth/RegisteredUserController.php`:
  ```php
  public function create(): never { abort(404); }
  public function store(Request $request): never { abort(404); }
  ```
- Percobaan `GET /register` maupun `POST /register` langsung menghasilkan HTTP 404 Not Found dan tidak membuat record pengguna di database (terverifikasi di `tests/Feature/Auth/RegistrationTest.php`).

---

## 3. Isolasi Konten Draft (Draft Protection)

1. **Aturan Bisnis**: Pengunjung publik atau perekrut tidak boleh melihat data proyek berstatus `draft` atau `archived`.
2. **Implementasi pada Controller Publik**:
   - Query daftar proyek publik (`PublicProjectController@index`) wajib menggunakan query scope `Project::published()`.
   - Query detail proyek publik (`PublicProjectController@show`) melakukan pemeriksaan eksplisit:
     ```php
     if ($project->status !== 'published') {
         abort(404);
     }
     ```
3. **Pencegahan Kebocoran Informasi**: Jika draft diakses via URL publik, sistem melempar 404 tanpa merender summary atau body sensitif (terverifikasi di `tests/Feature/DraftProjectAccessTest.php`).

---

## 4. Keamanan Form Kontak & Perlindungan Anti-Spam

Endpoint publik `POST /contact` rentan terhadap bot spam dan automated abuse. Diterapkan 3 lapis perlindungan:

1. **Honeypot Trap**:
   - Field `honeypot` disembunyikan dari antarmuka manusia.
   - Aturan validasi Laravel: `'honeypot' => 'prohibited'`. Jika field ini terisi oleh bot otomatis, request langsung ditolak dengan pesan error.
2. **Dual-Layer Rate Limiting**:
   - Lapis 1 (Middleware): `middleware('throttle:3,60')` membatasi request per IP.
   - Lapis 2 (Application Limiter): `RateLimiter::tooManyAttempts('contact:'.$request->ip(), 3)` dengan waktu penguncian 3600 detik (1 jam).
3. **Pencegahan XSS & Injeksi**:
   - Seluruh input string (`name`, `email`, `subject`, `message`) divalidasi batas panjangnya (`max:3000`).
   - Teks disimpan sebagai string murni dan Inertia/Vue secara otomatis melakukan HTML escaping saat data dirender di panel admin.

---

## 5. Keamanan Upload File (File Upload Hardening)

1. **Validasi MIME & Ukuran**:
   - Upload Dokumen CV: Dibatasi hanya format `pdf` dengan ukuran maksimal 10MB (`mimes:pdf|max:10240`).
   - Upload Gambar Proyek: Dibatasi gambar raster (`image|mimes:jpg,jpeg,png,webp|max:5120`). Format SVG dinonaktifkan untuk mencegah SVG-based stored XSS (XML script injection).
2. **Penyimpanan di Disk Publik**:
   - Path dan nama file digenerate secara acak oleh Laravel filesystem (`$file->store('projects/covers', 'public')`).
   - Nama asli file dari client tidak pernah digunakan sebagai nama file di disk server, mencegah *Path Traversal*.
3. **Pembersihan File Usang (Old File Deletion)**:
   - Penggantian file CV atau cover proyek memanggil `Storage::disk('public')->delete(...)` untuk menghapus file lama dari server, mencegah penumpukan file yatim.

---

## 6. Validasi Input & Pencegahan URL Berbahaya

1. **Pencegahan Injeksi Script URL**:
   - Field `demo_url`, `source_url`, dan `documentation_url` divalidasi menggunakan rule `nullable|url`.
   - Skema berbahaya seperti `javascript:alert(1)` atau `data:text/html` secara otomatis ditolak oleh validator Laravel (teruji di `tests/Feature/ProjectCrudTest.php`).
2. **Database Transactions**:
   - Mutasi kompleks yang melibatkan tabel relasi (`Project`, `project_images`, `project_skill`) dibungkus dalam `DB::transaction(...)` untuk menjamin konsistensi atomik jika terjadi error di tengah proses.
3. **Audit Logging Tanpa Rahasia**:
   - Setiap mutasi admin dicatat ke `activity_logs`.
   - Ringkasan log hanya mencatat tipe aksi (`created`, `updated`, `deleted`) dan ID entitas. Token sesi, password, dan nilai rahasia tidak pernah dicatat.

---

## 7. Matriks Status Uji Keamanan

| Skenario Pengujian Keamanan | File Test | Status Hasil Uji |
|---|---|---|
| Guest diblokir dari seluruh route admin | `tests/Feature/AdminGuardTest.php` | PASS (Redirect 302 to /login) |
| Non-admin diblokir dari route admin | `tests/Feature/AdminGuardTest.php` | PASS (HTTP 403 Forbidden) |
| Non-admin dilarang mengubah setting | `tests/Feature/AdminGuardTest.php` | PASS (HTTP 403 Forbidden) |
| Public registration ditutup | `tests/Feature/Auth/RegistrationTest.php` | PASS (HTTP 404 Not Found) |
| Publik dilarang melihat proyek draft | `tests/Feature/DraftProjectAccessTest.php` | PASS (HTTP 404 Not Found) |
| Honeypot mendeteksi dan menolak spam | `tests/Feature/ContactRateLimitTest.php` | PASS (Validation Error) |
| Rate limit form kontak aktif setelah 3 request | `tests/Feature/ContactRateLimitTest.php` | PASS (Rate limited) |
| Skema URL berbahaya (`javascript:`) ditolak | `tests/Feature/ProjectCrudTest.php` | PASS (Validation Error) |
| Unduh CV aman & tolak file non-existent | `tests/Feature/CvDownloadTest.php` | PASS (HTTP 404 jika kosong) |
| Penggantian file CV menghapus file lama | `tests/Feature/CvDownloadTest.php` | PASS (Storage assertion verified) |
