# AGENTS.md — Working Rules for AI Coding Agents
# Baruna Dwi Cahya Personal Portfolio & CMS

Dokumen ini adalah aturan kerja permanen bagi setiap AI coding agent yang bekerja pada repository ini.
Setiap sesi baru WAJIB membaca file ini dan `docs/SESSION_HANDOFF.md` sebelum melakukan perubahan apa pun.

---

## 1. Ringkasan Proyek & Stack Aktual

- **Aplikasi**: Baruna Dwi Cahya Personal Portfolio & Full-Stack CMS.
- **Tujuan**: Menampilkan profil profesional, portofolio proyek, keahlian, pengalaman, dan sistem CMS terpadu untuk perekrut & calon klien.
- **Backend**: Laravel 13 (PHP 8.3+) dengan Eloquent ORM, Session auth, Rate Limiting, Storage abstraction.
- **Frontend**: Vue 3.4 (`<script setup lang="ts">`), Inertia.js 2.0, Vite 8, Tailwind CSS 3.2.
- **Interaktif**: Three.js 0.186 (hero WebGL), Lenis 1.3 (smooth scroll), GSAP 3.15 (ScrollTrigger).
- **Database**: MySQL (local/production), SQLite `:memory:` untuk pengujian otomatis.

---

## 2. Indeks Dokumentasi Kontinuitas

Seluruh detail teknis terdokumentasi di folder `docs/`:
- `docs/PROJECT_CONTEXT.md` — Identitas, arsitektur umum, dan batasan sistem.
- `docs/ARCHITECTURE.md` — Alur request-response Laravel + Inertia + Vue, diagram Mermaid.
- `docs/ROUTES_AND_DATA_FLOW.md` — Katalog lengkap 74 route, controllers, props, dan redirect.
- `docs/DATABASE.md` — ERD lengkap, tabel, foreign keys, indeks, dan relasi Eloquent.
- `docs/FRONTEND_GUIDE.md` — Standar komponen Vue, layout, types, form handling, Three.js & Lenis.
- `docs/DESIGN_SYSTEM.md` — Desain tokens, warna Obsidian Precision, tipografi, aturan anti-AI-slop.
- `docs/SECURITY_MODEL.md` — Matriks otorisasi, proteksi draft, rate limiting, mitigasi upload & CSRF.
- `docs/DEPENDENCIES.md` — Daftar pustaka backend & frontend aktual beserta justifikasinya.
- `docs/TESTING.md` — Dokumentasi 51 automated feature tests dan verifikasi build.
- `docs/DECISIONS.md` — Catatan keputusan arsitektur (ADR).
- `docs/IMPLEMENTATION_STATUS.md` — Status verifikasi per modul.
- `docs/SESSION_HANDOFF.md` — Status terakhir sesi kerja dan panduan serah terima.
- `docs/CODEBASE_MAP.md` — Peta navigasi cepat file kode.

---

## 3. Aturan Desain & Branding (Anti-AI-Slop)

1. **Brand Name**: Teks wordmark WAJIB `Baruna Dwi Cahya`. DILARANG menggunakan `baruna.dev` sebagai brand/logo.
2. **Tanpa Telemetri Palsu**: DILARANG menambahkan widget palsu seperti "server ping", "uptime 99.9%", "fake terminal emulator", "fake memory monitor", atau counter palsu.
3. **Tanpa Angka Fiktif**: Skill tidak boleh menggunakan progress bar persentase buatan. Pengalaman dan sertifikat harus data nyata.
4. **Warna & Aksen**: Gunakan palette Obsidian Precision (`#101419` base, `#151B22` surface, `#293440` border, `#818CF8` / `#D7FF64` aksen presisi). DILARANG menggunakan background neon acak.
5. **Responsif**: Layout harus terverifikasi pada viewport 1440px, 1280px, 768px, dan 390px tanpa horizontal scroll overflow.

---

## 4. Aturan Coding & Arsitektur

1. **CMS adalah Sumber Kebenaran**: Seluruh teks konten publik (hero, deskripsi, bio, proyek, skill, kontak, link sosial, meta SEO) WAJIB bersumber dari database (`site_settings`, `projects`, dll.). JANGAN melakukan hardcode konten pada file `.vue`.
2. **YAGNI & Kesederhanaan**: Utamakan penghapusan daripada penambahan. Jangan membuat abstraction layer spekulatif (repository pattern berlebihan, interface untuk satu implementasi).
3. **Inertia Contracts**: Pertahankan penamaan props dan named routes (`route('...')`). Jangan memanggil internal API via Axios jika bisa disalurkan lewat Inertia props.
4. **Vue SFC**: Selalu gunakan `<script setup lang="ts">`, pastikan type safety pada props menggunakan TypeScript interfaces di `@/types`.
5. **Memory Safety**: Komponen interaktif (Three.js, event listeners, observers) WAJIB membersihkan instance, geometry, materials, dan listeners pada `onBeforeUnmount`. Wajib memeriksa `prefers-reduced-motion`.

---

## 5. Aturan Keamanan Mutlak

1. **Registrasi Publik DITUTUP**: Route `/register` harus selalu mengembalikan 404. Hanya admin yang diizinkan mengelola konten.
2. **Proteksi Draft Proyek**: Route publik `/projects/{slug}` WAJIB melempar 404 jika status proyek bukan `published`. Draft hanya dapat dilihat oleh admin.
3. **Middleware Admin**: Seluruh route di `/admin/*` WAJIB berada di bawah middleware `['auth', 'admin']`.
4. **Validasi Server-Side**: Semua mutasi data WAJIB divalidasi ketat di Laravel controller / form request. Tolak scheme URL berbahaya (`javascript:`).
5. **Upload Aman**: File CV hanya menerima `pdf` (maks 10MB). Gambar proyek divalidasi raster (jpg, png, webp). File lama wajib dihapus dari disk saat diganti.
6. **Anti-Spam & Rate Limiting**: Form kontak wajib dilindungi honeypot (`prohibited`) dan rate limiter IP (`throttle:3,60` / `RateLimiter`).

---

## 6. Protokol Sebelum & Sesudah Mengubah Kode

### Sebelum Mengubah:
1. Baca file ini (`AGENTS.md`) dan `docs/SESSION_HANDOFF.md`.
2. Periksa status git atau perubahan file lokal.
3. Buka kode sumber yang hendak diubah dan telusuri pemanggilnya.
4. Pastikan solusi minimal yang tidak merusak arsitektur yang sudah ada.

### Setelah Mengubah:
1. Jalankan test suite backend: `php artisan test` (harus 100% pass).
2. Jalankan build frontend & typecheck: `npm run build` (harus 0 errors).
3. Jalankan pemformatan kode: `vendor\bin\pint --test`.
4. Perbarui file dokumentasi terkait di `docs/`.
5. Perbarui `docs/IMPLEMENTATION_STATUS.md` dan `docs/SESSION_HANDOFF.md`.

---

## 7. Perintah Kerja Standar

```bash
# Jalankan test suite (SQLite in-memory)
php artisan test

# Format kode backend
vendor\bin\pint

# Jalankan typecheck & production build frontend
npm run build

# Menjalankan dev server frontend
npm run dev

# Menjalankan server backend lokal
php artisan serve
```
