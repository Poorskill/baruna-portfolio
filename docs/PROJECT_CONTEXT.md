# PROJECT_CONTEXT.md — Konteks dan Gambaran Umum Proyek
# Baruna Dwi Cahya Personal Portfolio & CMS

## 1. Identitas & Tujuan Proyek

- **Nama Proyek**: Baruna Dwi Cahya Personal Portfolio & Full-Stack CMS
- **Pemilik Portofolio**: Baruna Dwi Cahya
- **Fokus Keahlian**: Web Development, Software Systems, Informatics Engineering
- **Lokasi Workspace**: `E:\Portofolio\baruna-portfolio`
- **Lokasi Dokumen Blueprint Induk**: `E:\Portofolio\`

### Tujuan Utama:
1. **Representasi Profesional**: Menampilkan identitas, keahlian teknis, dan portofolio implementasi nyata Baruna Dwi Cahya secara kredibel dan terukur.
2. **Evaluasi Rekruter & Klien**: Memudahkan Human Resource / Tech Lead mengevaluasi kode, arsitektur, dan cara berpikir teknis; serta memberikan rasa percaya kepada calon klien freelance.
3. **Kemandirian Konten (CMS Terintegrasi)**: Menyediakan dashboard admin komprehensif agar teks, studi kasus proyek, skill, pengalaman, dan konfigurasi situs dapat dikelola penuh dari browser tanpa intervensi manual pada source code.
4. **Anti-Gimmick Engineering**: Menghindari tipuan visual umum (AI slop, progress bar persentase fiktif, fake system monitoring, uptime/telemetri palsu).

---

## 2. Stack Teknologi & Runtime Aktual

| Lapisan | Teknologi | Versi Terdeteksi | Catatan Implementasi |
|---|---|---|---|
| **Backend** | Laravel Framework | 13.38.0 | PHP 8.3+, MVC murni, Eloquent ORM, Session Auth |
| **Frontend** | Vue 3 + TypeScript | Vue 3.4, TS 5.6 | SFC, `<script setup lang="ts">`, Single-Page feel |
| **Penghubung** | Inertia.js | 2.0 (Laravel & Vue) | Shared layouts, page resolver dinamis, zero custom REST overhead |
| **Styling** | Tailwind CSS + Vite | Tailwind 3.2, Vite 8 | Dark theme Obsidian Precision, tipografi Geist & JetBrains Mono |
| **Interaktivitas** | Three.js | 0.186.1 | Scene hero 3D WebGL, torus & wireframe icosahedron, memory disposal |
| **Scroll Engine** | Lenis + GSAP | Lenis 1.3, GSAP 3.15 | Smooth scrolling terintegrasi dengan GSAP ScrollTrigger dan Inertia |
| **Database** | MySQL / SQLite | MySQL (dev/prod), SQLite `:memory:` (testing) | 14 migrasi kustom, foreign key cascade, index teroptimasi |
| **Testing** | PHPUnit | 12.5.12 | 51 feature & unit tests, 151 assertions, 100% lulus |

---

## 3. Lingkungan Development

- **Sistem Operasi**: Windows 11 (Platform `win32`).
- **Web Server / PHP Environment**: Windows CLI / Laragon (PHP 8.3+).
- **Status Repository Git**: Folder workspace lokal belum diinisialisasi sebagai repository Git (`git status` mengembalikan fatal not a git repo). Seluruh file aman dan tidak ada riwayat commit yang hilang.
- **Konfigurasi Database Pengujian**: `phpunit.xml` secara otomatis mengarahkan koneksi ke driver `sqlite` dengan database `:memory:`. Pengujian tidak bergantung pada daemon MySQL lokal yang sedang berjalan.
- **Konfigurasi Database Aplikasi**: Terkoneksi ke MySQL database `baruna_portfolio` pada port 3306 saat server MySQL aktif.

---

## 4. Struktur Direktori Utama

```text
E:\Portofolio\
├── baruna-portfolio\                  # Root aplikasi Laravel + Vue
│   ├── app\
│   │   ├── Http\
│   │   │   ├── Controllers\
│   │   │   │   ├── Admin\             # 14 Admin Controllers (CMS, Dashboard, Media)
│   │   │   │   ├── Auth\              # Breeze Auth Controllers (Register ditutup)
│   │   │   │   └── Public\            # 5 Public Controllers (Home, About, Projects, Contact, Cv)
│   │   │   └── Middleware\            # EnsureAdmin, HandleInertiaRequests
│   │   └── Models\                    # 14 Eloquent Models
│   ├── bootstrap\                     # bootstrap/app.php konfigurasi middleware & routes
│   ├── database\
│   │   ├── migrations\                # 14 migration tables + default Laravel tables
│   │   └── seeders\                   # DatabaseSeeder, SiteSettingSeeder, NavigationSeeder, SkillSeeder
│   ├── docs\                          # Dokumentasi kontinuitas dan arsitektur
│   ├── resources\
│   │   └── js\
│   │       ├── Components\            # Reusable UI primitives & Public specific components
│   │       ├── composables\           # useSmoothScroll (Lenis + GSAP)
│   │       ├── Layouts\               # PublicLayout, AdminLayout, GuestLayout, AuthenticatedLayout
│   │       ├── Pages\
│   │       │   ├── Admin\             # 14 Halaman Dashboard & Form CMS
│   │       │   ├── Auth\              # Login, Reset Password
│   │       │   └── Public\            # Home, About, Contact, Projects (Index & Show)
│   │       └── types\                 # TypeScript interfaces (Project, Skill, Nav, Setting, etc.)
│   ├── routes\
│   │   ├── web.php                    # Public & Admin route definitions
│   │   └── auth.php                   # Authentication routes
│   └── tests\                         # 51 Feature and Unit tests
```

---

## 5. Ringkasan Status Fitur Saat Ini

- **Halaman Publik**:
  - `Home`: Menampilkan Hero 3D Three.js responsif, pengantar dinamis, proyek unggulan terpilih, skill grouped, dan CTA kontak.
  - `About`: Menampilkan bio lengkap, riwayat pendidikan, riwayat pengalaman, dan sertifikat terverifikasi dari DB.
  - `Projects Directory`: Menampilkan grid proyek published, filter kategori dinamis, dan pencarian teks.
  - `Project Detail`: Studi kasus terstruktur (problem, solution, role, features, challenges, outcome, stack, screenshot gallery).
  - `Contact`: Form kontak tervalidasi dengan proteksi honeypot anti-spam dan IP rate limiting.
  - `CV Download`: Endpoint `/cv` yang aman menyajikan file PDF dengan nama file terstandarisasi.
- **Admin CMS**:
  - `Dashboard`: Statistik real-time terhitung dari database (total project, draft, published, unread messages).
  - `Content Management`: Home editor, Profile/About editor, Site Settings editor.
  - `CRUD Modules`: Projects (dengan upload multi-image & cover), Skill Groups, Skills, Experiences, Education, Certificates, Navigation Links, Social Links, Contact Inbox.
  - `Audit Log`: Pencatatan otomatis aksi admin (`ActivityLog`) tanpa membocorkan data rahasia.

---

## 6. Protokol Memulai Sesi AI Berikutnya

Sebelum melakukan instruksi apa pun:
1. Baca `AGENTS.md` untuk memahami aturan kerja dan larangan desain/keamanan.
2. Baca `docs/SESSION_HANDOFF.md` untuk mengetahui catatan teknis terakhir.
3. Jalankan `php artisan test` dan `npm run build` untuk memverifikasi kondisi awal.
4. Lakukan perubahan minimal terfokus, verifikasi kembali, dan perbarui dokumen kontinuitas.
