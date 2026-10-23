# DECISIONS.md — Architecture Decision Records (ADR)
# Baruna Dwi Cahya Personal Portfolio & CMS

Dokumen ini mencatat keputusan-keputusan arsitektur dan teknis penting, konteks di balik keputusan tersebut, serta konsekuensinya.

---

## ADR 001: Monolith Modern (Laravel 13 + Inertia.js 2.0 + Vue 3) dibanding Decoupled SPA/REST API

- **Status**: Diterima & Diimplementasikan.
- **Konteks**: Website portofolio pribadi memerlukan kecepatan pengembangan, SEO yang baik, integrasi autentikasi yang aman tanpa token storage di localStorage, dan kemudahan deployment satu server.
- **Keputusan**: Menggunakan Laravel 13 sebagai monolithic application dengan Vue 3 dan Inertia.js 2.0 sebagai rendering layer.
- **Konsekuensi**:
  - *Positif*: Tidak memerlukan dokumentasi REST API terpisah atau penanganan CORS; session cookie berbasis HTTP-only bawaan Laravel langsung aktif; props langsung disalurkan dari controller ke komponen Vue dengan type safety; efisiensi waktu pemeliharaan.
  - *Negatif*: Client Vue terikat dengan backend Laravel (bukan aplikasi mobile atau static decoupled).

---

## ADR 002: Model `site_settings` Key-Value dibanding Page Builder Dinamis

- **Status**: Diterima & Diimplementasikan.
- **Konteks**: Kebutuhan mengelola teks hero, profil bio, dan pengaturan situs melalui CMS sering kali memicu kecenderungan membangun generic block/page builder yang rumit dan rentan bug.
- **Keputusan**: Menggunakan tabel `site_settings` berbasis key-value dengan pengelompokan (`group`) yang divalidasi ketat di controller (`SiteSettingController`, `HomeContentController`, `ProfileContentController`), dipadukan dengan tabel konten spesifik (`projects`, `skills`, `experiences`, `education`, `certificates`).
- **Konsekuensi**:
  - *Positif*: Kode sangat ringkas (YAGNI); skema validasi server eksplisit; tidak ada resiko injeksi script dari raw JSON page builder; UI admin bersih dan fokus.
  - *Negatif*: Penambahan section baru dengan struktur data baru memerlukan migration atau key setting baru.

---

## ADR 003: Model Single-Admin & Penutupan Registrasi Publik

- **Status**: Diterima & Diimplementasikan.
- **Konteks**: Aplikasi ini adalah portofolio pribadi milik Baruna Dwi Cahya. Tidak ada use-case di mana pengunjung umum atau pihak luar boleh mendaftar akun di situs ini.
- **Keputusan**: Menonaktifkan registrasi publik secara permanen (`RegisteredUserController` melempar HTTP 404). Akun admin hanya dibuat melalui proses seeder / CLI lokal.
- **Konsekuensi**:
  - *Positif*: Menghilangkan vektor serangan registrasi bot, brute-force spam akun, dan potensi privilege escalation.
  - *Negatif*: Tidak dapat digunakan sebagai CMS multi-tenant tanpa modifikasi arsitektur lanjutan.

---

## ADR 004: Tiga Dimensi WebGL (Three.js) & Smooth Scroll (Lenis) Dibatasi pada Halaman Publik

- **Status**: Diterima & Diimplementasikan.
- **Konteks**: Hero visual interaktif dan smooth scroll memberikan impresi visual engineering tinggi, namun dapat membebani GPU atau mengganggu kenyamanan pengguna jika diterapkan ceroboh atau masuk ke area dashboard admin.
- **Keputusan**:
  - Three.js hanya dirender pada Hero section halaman Home (`HeroThreeScene.vue`). Dilengkapi `ResizeObserver`, `IntersectionObserver`, jeda saat tab tidak aktif, dan pembersihan memori total (`dispose()`) pada unmount.
  - Lenis hanya diinisialisasi pada `PublicLayout.vue`, terhubung dengan GSAP ticker, dan di-refresh saat navigasi Inertia selesai.
  - Keduanya wajib memeriksa media query `(prefers-reduced-motion: reduce)`.
- **Konsekuensi**:
  - *Positif*: Tampilan modern tanpa mengorbankan aksesibilitas, konsumsi baterai, atau stabilitas dashboard admin.
  - *Negatif*: Memerlukan perhatian ekstra pada pemeliharaan lifecycle unmounting Vue.

---

## ADR 005: Isolasi Konten Draft Melalui Respons HTTP 404

- **Status**: Diterima & Diimplementasikan.
- **Konteks**: Proyek yang belum siap dipublikasikan atau masih dalam pengembangan internal tidak boleh bocor ke publik atau terindeks mesin pencari.
- **Keputusan**: Menggunakan respons HTTP 404 Not Found (bukan 403 atau redirect) saat pengunjung umum mengakses URL `/projects/{slug}` dari proyek yang statusnya bukan `published`.
- **Konsekuensi**:
  - *Positif*: Menyerupai perilaku halaman yang memang tidak ada; tidak membocorkan eksistensi slug rahasia kepada pihak luar.
  - *Negatif*: Admin harus login terlebih dahulu atau melihat preview dari panel admin untuk memeriksa tampilan draft.

---

## ADR 006: Proteksi Form Kontak Berlapis (Honeypot + Dual-Rate Limiter)

- **Status**: Diterima & Diimplementasikan.
- **Konteks**: Form kontak publik rentan dimanfaatkan spambot untuk spamming inbox atau resource exhaustion.
- **Keputusan**: Mengombinasikan field `honeypot` (prohibited validation), middleware `throttle:3,60`, dan application-level `RateLimiter::tooManyAttempts` (maksimal 3 pesan per jam per IP).
- **Konsekuensi**:
  - *Positif*: Mencegah spam secara otomatis tanpa mengharuskan pengunjung memecahkan CAPTCHA yang merepotkan UX.
  - *Negatif*: Pengguna pada IP publik bersama (misal jaringan kampus) berbagi kuota pengiriman form kontak.

---

## ADR 007: Branding Wordmark "Baruna Dwi Cahya" & Toleransi Nol Terhadap AI Slop

- **Status**: Diterima & Diimplementasikan.
- **Konteks**: Banyak template portofolio developer modern terjebak dalam gaya generic AI slop: logo domain buatan (`baruna.dev`), widget telemetri palsu, ping server fiktif, angka performa buatan, dan progress bar skill persentase tanpa dasar.
- **Keputusan**: Wordmark brand wajib berupa teks nama lengkap `Baruna Dwi Cahya`. Dilarang menampilkan metrik sistem palsu. Semua data proyek dan sertifikat harus berupa data riil.
- **Konsekuensi**:
  - *Positif*: Portofolio terlihat matang, dewasa, berintegritas tinggi, dan dinilai positif oleh technical recruiter dan engineering manager.
  - *Negatif*: Memerlukan kurasi konten nyata oleh pemilik portofolio sebelum data dipublikasikan.
