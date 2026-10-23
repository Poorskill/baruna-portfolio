# DEPENDENCIES.md — Dependensi Aplikasi dan Integrasi Runtime
# Baruna Dwi Cahya Personal Portfolio & CMS

Dokumen ini mencatat pustaka utama yang digunakan pada backend dan frontend, fungsi riilnya dalam sistem, serta status integrasinya.

---

## 1. Dependensi Backend (PHP / Composer)

| Nama Paket | Versi Terkunci | Peran & Alasan Penggunaan | Lokasi Penggunaan | Status Integrasi |
|---|---|---|---|---|
| `php` | `^8.3` (8.3.16 detected) | Bahasa server utama. Memanfaatkan Typed Properties, Match Expressions, dan Readonly classes. | Global server runtime | Aktif & Terverifikasi |
| `laravel/framework` | `13.38.0` | Framework inti: HTTP routing, Eloquent ORM, filesystem abstraction, database transaction, middleware. | `app/`, `bootstrap/`, `routes/` | Aktif & Terverifikasi |
| `inertiajs/inertia-laravel`| `^2.0` | Protokol pertukaran props dan response antara Laravel dan Vue tanpa overhead REST API terpisah. | `app/Http/Middleware/HandleInertiaRequests.php`, seluruh Controller | Aktif & Terverifikasi |
| `laravel/sanctum` | `^4.0` | Pengelolaan token API dan otentikasi SPA/session aman bawaan starter kit. | `config/sanctum.php` | Aktif |
| `tightenco/ziggy` | `^2.0` | Menyediakan helper fungsi `route('name')` di lingkungan Vue dan TypeScript. | `resources/js/app.ts`, seluruh komponen Vue | Aktif & Terverifikasi |
| `laravel/breeze` | Dev | Autentikasi dasar (login, reset password, update profile). | `routes/auth.php`, `app/Http/Controllers/Auth/` | Aktif (Registrasi dimatikan) |
| `laravel/pint` | `^1.27` | Code style fixer untuk memastikan kode PHP mengikuti standar PSR-12 dan konvensi Laravel. | `vendor/bin/pint` | Aktif & Lulus Test |
| `phpunit/phpunit` | `^12.5.12` | Framework pengujian otomatis backend. | `tests/Feature/`, `tests/Unit/` | Aktif (51 test lulus) |

---

## 2. Dependensi Frontend (Node.js / npm)

| Nama Paket | Versi Terkunci | Peran & Alasan Penggunaan | Lokasi Penggunaan | Status Integrasi |
|---|---|---|---|---|
| `vue` | `^3.4.0` | Framework reaktif UI berbasis Single File Component (`.vue`) dan Composition API. | Seluruh antarmuka di `resources/js/` | Aktif & Terverifikasi |
| `@inertiajs/vue3` | `^2.0.0` | Client adapter Inertia untuk Vue 3: `Link`, `useForm`, `router`, `usePage`. | `resources/js/app.ts`, Layouts, Pages, Components | Aktif & Terverifikasi |
| `three` & `@types/three` | `^0.186.1` | Rendering 3D WebGL untuk visual geometri interaktif pada hero section halaman Home. | `resources/js/Components/Public/HeroThreeScene.vue` | Aktif & Terverifikasi |
| `lenis` | `^1.3.27` | Engine smooth scrolling modern dengan performa tinggi dan sinkronisasi frame loop. | `resources/js/composables/useSmoothScroll.ts` | Aktif & Terverifikasi |
| `gsap` | `^3.15.0` | Engine animasi dan ScrollTrigger yang disinkronkan dengan Lenis ticker dan Inertia page finish. | `resources/js/composables/useSmoothScroll.ts` | Aktif & Terverifikasi |
| `tailwindcss` | `^3.2.1` | Framework utility CSS untuk penerapan sistem desain Obsidian Precision. | `resources/css/app.css`, `tailwind.config.js` | Aktif & Terverifikasi |
| `typescript` | `^5.6.3` | Type checking statis untuk menjamin integritas tipe data dan props komponen. | `tsconfig.json`, `resources/js/**/*.ts` | Aktif & Terverifikasi |
| `vue-tsc` | `^2.0.24` | Type checker CLI untuk template dan script Vue sebelum proses bundling. | `npm run build` | Aktif (0 type errors) |
| `vite` | `^8.0.0` (8.3.5) | Bundler modern berkecepatan tinggi untuk hot module replacement (HMR) dan asset compilation. | `vite.config.js` | Aktif & Terverifikasi |
| `axios` | `^1.20.0` | HTTP client untuk komunikasi request asinkronus (jika diperlukan di luar alur Inertia). | `resources/js/bootstrap.ts` | Aktif |

---

## 3. Justifikasi Pustaka Interaktif

- **Three.js**: Dipilih untuk menampilkan representasi teknis komputasi grafis dan software engineering secara elegan di hero section tanpa mengorbankan performa (dilengkapi memory cleanup dan reduced motion check).
- **Lenis + GSAP**: Dipilih untuk menghadirkan pengalaman membaca editorial yang halus pada halaman publik tanpa merusak scroll native browser. Dilengkapi mekanisme refresh otomatis setelah transisi halaman Inertia.
