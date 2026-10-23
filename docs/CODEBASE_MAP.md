# CODEBASE_MAP.md — Peta Navigasi Cepat Kode Sumber
# Baruna Dwi Cahya Personal Portfolio & CMS

Gunakan peta ini untuk menemukan file implementasi terkait secara instan tanpa melakukan pencarian berulang.

---

## 1. Modul Publik (Public Portal)

| Modul & Fitur | Backend Controller | Model Terkait | Migrasi Database | Halaman Vue / Komponen | Route Name |
|---|---|---|---|---|---|
| **Home Page** | `app/Http/Controllers/Public/HomeController.php` | `SiteSetting`, `Project`, `SkillGroup`, `NavigationLink`, `SocialLink` | `*_create_site_settings_table.php`, `*_create_projects_table.php` | `resources/js/Pages/Public/Home.vue`, `HeroThreeScene.vue` | `home` (`GET /`) |
| **About & Career** | `app/Http/Controllers/Public/AboutController.php` | `SiteSetting`, `Education`, `Experience`, `Certificate` | `*_create_experiences_table.php`, `*_create_education_table.php`, `*_create_certificates_table.php` | `resources/js/Pages/Public/About.vue` | `about` (`GET /about`) |
| **Projects Directory**| `app/Http/Controllers/Public/ProjectController.php:16` (`index`) | `Project`, `Skill` | `*_create_projects_table.php`, `*_create_project_skill_table.php` | `resources/js/Pages/Public/Projects/Index.vue`, `ProjectCard.vue` | `projects.index` (`GET /projects`) |
| **Project Case Study**| `app/Http/Controllers/Public/ProjectController.php:47` (`show`) | `Project`, `ProjectImage`, `Skill` | `*_create_projects_table.php`, `*_create_project_images_table.php` | `resources/js/Pages/Public/Projects/Show.vue` | `projects.show` (`GET /projects/{slug}`) |
| **Contact Form** | `app/Http/Controllers/Public/ContactController.php` | `ContactMessage`, `SiteSetting` | `*_create_contact_messages_table.php` | `resources/js/Pages/Public/Contact.vue` | `contact.show` (`GET /contact`), `contact.store` (`POST /contact`) |
| **CV Stream Download**| `app/Http/Controllers/Public/CvController.php` | `SiteSetting` (`cv_path`) | `*_create_site_settings_table.php` | Streamed PDF download response | `cv.download` (`GET /cv`) |

---

## 2. Modul Admin & CMS (Management Cockpit)

| Modul CMS | Backend Controller | Model Terkait | Halaman Vue | Route Name Prefix |
|---|---|---|---|---|
| **Overview Dashboard** | `app/Http/Controllers/Admin/DashboardController.php` | `Project`, `Skill`, `ContactMessage` | `resources/js/Pages/Admin/Dashboard.vue` | `admin.dashboard` (`GET /admin`) |
| **Home Content Editor**| `app/Http/Controllers/Admin/HomeContentController.php` | `SiteSetting` (group `home`) | `resources/js/Pages/Admin/Content/HomeEdit.vue` | `admin.content.home.edit`, `update` |
| **Profile Content** | `app/Http/Controllers/Admin/ProfileContentController.php` | `SiteSetting` (group `about`, `identity`) | `resources/js/Pages/Admin/Content/ProfileEdit.vue` | `admin.content.profile.edit`, `update` |
| **Site Settings** | `app/Http/Controllers/Admin/SiteSettingController.php` | `SiteSetting` (group `seo`, `contact`, dll.) | `resources/js/Pages/Admin/Settings/Edit.vue` | `admin.settings.edit`, `update` |
| **Projects Management**| `app/Http/Controllers/Admin/ProjectController.php` | `Project`, `ProjectImage`, `Skill`, `ActivityLog` | `resources/js/Pages/Admin/Projects/Index.vue`, `Form.vue` | `admin.projects.*` (Resource) |
| **Skill Groups** | `app/Http/Controllers/Admin/SkillGroupController.php` | `SkillGroup` | `resources/js/Pages/Admin/SkillGroups/Index.vue` | `admin.skill-groups.*` (Resource) |
| **Skills Management** | `app/Http/Controllers/Admin/SkillController.php` | `Skill`, `SkillGroup` | `resources/js/Pages/Admin/Skills/Index.vue` | `admin.skills.*` (Resource) |
| **Experience (Karir)** | `app/Http/Controllers/Admin/ExperienceController.php` | `Experience` | `resources/js/Pages/Admin/Experiences/Index.vue` | `admin.experience.*` (Resource) |
| **Education (Studi)** | `app/Http/Controllers/Admin/EducationController.php` | `Education` | `resources/js/Pages/Admin/Education/Index.vue` | `admin.education.*` (Resource) |
| **Certificates** | `app/Http/Controllers/Admin/CertificateController.php` | `Certificate` | `resources/js/Pages/Admin/Certificates/Index.vue` | `admin.certificates.*` (Resource) |
| **Navigation Links** | `app/Http/Controllers/Admin/NavigationController.php` | `NavigationLink` | `resources/js/Pages/Admin/Navigation/Index.vue` | `admin.navigation.*` (Resource) |
| **Social Links** | `app/Http/Controllers/Admin/SocialLinkController.php` | `SocialLink` | `resources/js/Pages/Admin/SocialLinks/Index.vue` | `admin.social-links.*` (Resource) |
| **Contact Messages** | `app/Http/Controllers/Admin/ContactMessageController.php`| `ContactMessage` | `resources/js/Pages/Admin/Messages/Index.vue`, `Show.vue` | `admin.messages.*` |
| **Activity Audit Log** | `app/Http/Controllers/Admin/ActivityLogController.php` | `ActivityLog` | `resources/js/Pages/Admin/ActivityLog/Index.vue` | `admin.activity.index` (`GET /admin/activity`) |
| **Account Profile** | `app/Http/Controllers/ProfileController.php` | `User` | `resources/js/Pages/Profile/Edit.vue` | `admin.account.edit`, `update`, `destroy` |

---

## 3. Komponen Bersama & Primitif UI

| Komponen | Lokasi File | Fungsi |
|---|---|---|
| `PublicLayout` | `resources/js/Layouts/PublicLayout.vue` | Shell utama halaman publik, navbar, footer, flash banner, Lenis scroll. |
| `AdminLayout` | `resources/js/Layouts/AdminLayout.vue` | Shell panel admin CMS, sidebar navigasi, user header, flash banner. |
| `TopNavBar` | `resources/js/Components/Public/TopNavBar.vue` | Navigasi atas dengan wordmark `BARUNA DWI CAHYA` & link responsif. |
| `Footer` | `resources/js/Components/Public/Footer.vue` | Footer publik dengan manifesto, link navigasi, dan link sosial resmi. |
| `ProjectCard` | `resources/js/Components/Public/ProjectCard.vue` | Kartu proyek dengan varian `featured`, `default`, dan `compact`. |
| `FeaturedProjectCarousel` | `resources/js/Components/Public/FeaturedProjectCarousel.vue` | Carousel interaktif proyek unggulan (autoplay, pause on hover/focus, swipe, keyboard, indicators). |
| `HeroPortraitScene` | `resources/js/Components/Public/HeroPortraitScene.vue` | WebGL canvas Three.js (single violet circular disc backdrop). |
| `useSmoothScroll` | `resources/js/composables/useSmoothScroll.ts` | Composable singleton Lenis + GSAP ScrollTrigger + Inertia hook. |
| `TypeScript Types`| `resources/js/types/index.d.ts` | Definisi interface untuk Project, Skill, NavLink, User, Settings. |

---

## 4. Keamanan & Middleware

| File | Tipe | Tanggung Jawab |
|---|---|---|
| `app/Http/Middleware/EnsureAdmin.php` | Middleware | Memeriksa `user.is_admin === true`. Melempar 403 Forbidden untuk non-admin. |
| `app/Http/Controllers/Auth/RegisteredUserController.php` | Controller | Melempar HTTP 404 pada `create()` dan `store()` (Registrasi ditutup permanen). |
| `app/Http/Middleware/HandleInertiaRequests.php` | Middleware | Menyediakan global props Inertia (`auth.user`, `flash.success`, `flash.error`). |
| `bootstrap/app.php` | Konfigurasi | Mendaftarkan alias middleware `'admin'`, routing, dan exception handler. |

---

## 5. Deployment & DevOps

| File | Tipe | Tanggung Jawab |
|---|---|---|
| `Dockerfile` | Containerization | Multi-stage Docker build (Node 22 Vite build, Composer 2, PHP 8.3-FPM + Nginx di Alpine). |
| `.dockerignore` | Build optimization | Membatasi file yang masuk ke Docker build context. |
| `docker/nginx.conf` | Web server | Reverse proxy Nginx dengan port dinamis `${PORT}`, caching aset statis, dan handling `/up`. |
| `docker/supervisord.conf`| Process manager | Mengelola proses Nginx dan PHP-FPM di foreground kontainer. |
| `docker/entrypoint.sh` | Startup script | Inisialisasi port Nginx, izin storage, storage symlink, dan warming route cache. |
| `docker/php.ini` | PHP Config | Konfigurasi PHP produksi (opcache, memory limit 256M, upload 20M). |
| `render.yaml` | Infrastructure as Code | Render Blueprint spec untuk Docker web service, region Singapore, health check `/up`. |
| `.github/workflows/ci.yml` | CI Pipeline | Automasi build aset Vite, Pint style check, dan 53 test suites di SQLite memory. |
| `.github/workflows/migrate.yml` | CD Pipeline | Manual trigger (`workflow_dispatch`) migrasi database produksi secara aman. |
| `docs/RENDER_DEPLOYMENT.md` | Dokumentasi | Panduan komprehensif langkah demi langkah deploy GitHub ke Render. |
