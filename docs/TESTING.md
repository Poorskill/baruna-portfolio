# TESTING.md — Dokumentasi Pengujian dan Hasil Audit
# Baruna Dwi Cahya Personal Portfolio & CMS

Dokumen ini mencatat konfigurasi pengujian otomatis, cakupan test suites, dan hasil eksekusi riil dari audit sistem.

---

## 1. Konfigurasi Lingkungan Pengujian

- **Framework**: PHPUnit 12.5.12 di atas Laravel 13.38.0.
- **File Konfigurasi**: `phpunit.xml` pada root proyek.
- **Database Pengujian**: `sqlite` in-memory (`:memory:`).
  - Setiap test case memanfaatkan trait `Illuminate\Foundation\Testing\RefreshDatabase`.
  - Skema database di-migrate ulang secara otomatis di memori pada setiap sesi test.
  - Pengujian sepenuhnya independen dan tidak membutuhkan database MySQL lokal aktif.

---

## 2. Hasil Eksekusi Uji Terakhir (Audit Riil)

### A. Backend PHPUnit Test Suite
- **Perintah**: `php artisan test`
- **Hasil**:
  ```text
  Laravel Framework 13.38.0
  {"tool":"phpunit","result":"passed","tests":51,"passed":51,"assertions":151,"duration_ms":11222}
  ```
- **Status**: **100% LULUS (51 Passed, 0 Failed, 151 Assertions)**.

### B. Frontend Typecheck & Asset Build
- **Perintah**: `npm run build` (menjalankan `vue-tsc && vite build`)
- **Hasil**:
  ```text
  vite v8.3.5 building client environment for production...
  ✓ 869 modules transformed.
  rendering chunks...
  computing gzip size...
  ✓ built in 11.47s
  ```
- **Status**: **0 Type Errors, Build Sukses**.

### C. PHP Code Style (Laravel Pint)
- **Perintah**: `vendor\bin\pint --test`
- **Hasil**:
  ```text
  {"tool":"pint","result":"passed"}
  ```
- **Status**: **Format kode 100% patuh terhadap standar PSR-12 / Laravel**.

---

## 3. Rincian Test Suites dan Kasus Uji

### A. Keamanan & Otorisasi Admin (`tests/Feature/AdminGuardTest.php`)
1. `test_guest_is_redirected_to_login_when_accessing_admin_dashboard`: Guest diarahkan ke login saat mengakses `/admin`.
2. `test_guest_is_redirected_to_login_when_accessing_admin_projects`: Guest diarahkan ke login saat mengakses `/admin/projects`.
3. `test_authenticated_non_admin_gets_forbidden_status_on_admin_dashboard`: User non-admin menerima HTTP 403 Forbidden.
4. `test_authenticated_non_admin_cannot_mutate_settings`: User non-admin dilarang mengubah setting (HTTP 403).
5. `test_authenticated_admin_can_access_admin_dashboard`: Admin berhasil mengakses dashboard (HTTP 200).
6. `test_authenticated_admin_can_access_admin_projects`: Admin berhasil mengakses list proyek (HTTP 200).
7. `test_authenticated_admin_can_access_settings`: Admin berhasil membuka form settings (HTTP 200).

### B. Form Kontak & Rate Limiting (`tests/Feature/ContactRateLimitTest.php`)
1. `test_contact_form_saves_valid_message`: Pesan valid tersimpan di database dengan status `unread`.
2. `test_contact_form_rejects_honeypot_spam`: Bot yang mengisi field `honeypot` langsung ditolak dan tidak tersimpan.
3. `test_contact_form_enforces_rate_limiting_after_three_attempts`: Request ke-4 dari IP yang sama diblokir oleh rate limiter.

### C. Unduh & Kelola CV (`tests/Feature/CvDownloadTest.php`)
1. `test_cv_download_returns_404_when_no_cv_is_configured`: Mengembalikan 404 jika belum ada CV diatur.
2. `test_cv_download_returns_404_when_file_does_not_exist_on_disk`: Mengembalikan 404 jika file tidak ada di storage.
3. `test_cv_download_serves_pdf_with_proper_filename_when_available`: Mengunduh file PDF dengan nama `Baruna-Dwi-Cahya-CV.pdf`.
4. `test_home_page_indicates_cv_availability`: Home page secara akurat mengirim props `hasCv: true/false`.
5. `test_admin_can_upload_and_replace_cv`: Admin dapat upload CV dan file lama otomatis terhapus dari storage.

### D. Isolasi Draft Proyek (`tests/Feature/DraftProjectAccessTest.php`)
1. `test_guest_cannot_view_draft_project`: Guest menerima HTTP 404 saat mengakses detail proyek draft.
2. `test_non_admin_user_cannot_view_draft_project`: User non-admin menerima HTTP 404 untuk proyek draft.
3. `test_guest_cannot_view_archived_project`: Guest menerima HTTP 404 untuk proyek berstatus archived.
4. `test_guest_can_view_published_project`: Guest dapat membuka proyek berstatus published (HTTP 200).

### E. CRUD Proyek Admin (`tests/Feature/ProjectCrudTest.php`)
1. `test_admin_can_create_valid_project`: Admin berhasil membuat proyek, menghubungkan skill, dan mencatat activity log.
2. `test_title_and_status_are_required_to_create_project`: Validasi form menolak jika title atau status kosong.
3. `test_duplicate_slug_is_rejected`: Slug yang sama ditolak oleh validasi keunikan database.
4. `test_invalid_demo_url_scheme_is_rejected`: Skema URL berbahaya (`javascript:alert(1)`) ditolak server.
5. `test_admin_can_update_project`: Admin dapat memperbarui detail proyek dan tercatat di audit log.
6. `test_admin_can_delete_project`: Admin dapat menghapus proyek dan tercatat di audit log.

### F. Manajemen Profil Akun (`tests/Feature/ProfileTest.php`)
1. Menguji update nama & email profil admin.
2. Menguji status verifikasi email jika email diubah.
3. Menguji penghapusan akun admin dengan konfirmasi password yang valid.

### G. Suite Autentikasi (`tests/Feature/Auth/*`)
1. `RegistrationTest`: Memastikan layar registrasi dan endpoint registrasi publik dinonaktifkan (404).
2. `AuthenticationTest`: Memastikan login berhasil dengan kredensial benar dan gagal jika salah; logout bekerja.
3. `PasswordResetTest`, `PasswordUpdateTest`, `PasswordConfirmationTest`, `EmailVerificationTest`: Seluruh alur keamanan akun terverifikasi.

---

## 4. Cara Menjalankan Uji Sistem

```powershell
# Berpindah ke root aplikasi
Set-Location "E:\Portofolio\baruna-portfolio"

# Jalankan seluruh test suite
php artisan test

# Jalankan test suite tertentu (contoh: draft access)
php artisan test --filter=DraftProjectAccessTest

# Jalankan pemeriksaan gaya penulisan kode PHP
vendor\bin\pint --test

# Jalankan typecheck Vue dan build asset
npm run build
```
