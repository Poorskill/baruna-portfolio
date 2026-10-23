# FRONTEND_GUIDE.md — Panduan Frontend Vue & Arsitektur UI
# Baruna Dwi Cahya Personal Portfolio & CMS

## 1. Konvensi Vue & TypeScript

- **Versi**: Vue 3.4+ dengan TypeScript 5.6+.
- **SFC Paradigm**: Wajib menggunakan `<script setup lang="ts">`.
- **Styling**: Tailwind CSS utility classes dengan design tokens Obsidian Precision.
- **Tipe Data**: Seluruh entitas didefinisikan secara tersentralisasi pada `resources/js/types/index.d.ts`.
- **Routing Client**: Menggunakan `@inertiajs/vue3` (`Link`, `router`, `useForm`, `usePage`) dan Ziggy `route(...)`.

---

## 2. Struktur Direktori Frontend (`resources/js/`)

```text
resources/js/
├── app.ts                         # Inisialisasi Inertia app & Ziggy plugin
├── bootstrap.ts                   # Axios configuration
├── composables/
│   └── useSmoothScroll.ts         # Integrasi Lenis + GSAP ScrollTrigger
├── Layouts/
│   ├── PublicLayout.vue           # Wrapper halaman publik (Nav, Flash, Footer, Lenis)
│   ├── AdminLayout.vue            # Wrapper panel CMS (Sidebar, Topbar, Flash)
│   ├── GuestLayout.vue            # Wrapper halaman auth (Login, Reset password)
│   └── AuthenticatedLayout.vue    # Breeze starter layout
├── Components/
│   ├── Public/
│   │   ├── HeroThreeScene.vue     # Canvas WebGL Three.js untuk Hero Home
│   │   ├── TopNavBar.vue          # Header publik dengan wordmark Baruna Dwi Cahya & nav
│   │   ├── Footer.vue             # Footer dengan sitemap & link sosial resmi
│   │   └── ProjectCard.vue        # Kartu proyek (varian: featured, default, compact)
│   └── (UI Primitives)
│       ├── PrimaryButton.vue, SecondaryButton.vue, DangerButton.vue
│       ├── TextInput.vue, InputLabel.vue, InputError.vue, Checkbox.vue
│       ├── Dropdown.vue, DropdownLink.vue, Modal.vue, NavLink.vue
├── Pages/
│   ├── Public/                    # Halaman publik (Home, About, Contact, Projects/*)
│   ├── Admin/                     # Halaman admin CMS (Dashboard, Projects/*, Content/*, dll.)
│   └── Auth/                      # Halaman auth (Login, ForgotPassword, dll.)
└── types/
    ├── index.d.ts                 # Definisi tipe TypeScript untuk seluruh entitas
    ├── global.d.ts                # Deklarasi global Ziggy route()
    └── vite-env.d.ts              # Deklarasi env Vite
```

---

## 3. Sistem Layouts & Komponen Reusable

### A. `PublicLayout.vue`
- Bertindak sebagai root chrome untuk seluruh halaman publik (`Home.vue`, `About.vue`, `Contact.vue`, `Projects/Index.vue`, `Projects/Show.vue`).
- Menyediakan container dengan background `#101419` dan tipografi sans-serif antialiased.
- Mengintegrasikan banner flash session global (`flashSuccess` dan `flashError`).
- Mengaktifkan `useSmoothScroll` saat layout di-mount dan membersihkannya saat unmount.

### B. `AdminLayout.vue`
- Cockpit manajemen berdensitas tinggi untuk admin.
- Dilengkapi sidebar navigasi teks presisi dengan indikator rute aktif (`#818CF8`).
- Header menampilkan profil admin login dan tombol aksi cepat menuju website publik.

### C. `ProjectCard.vue`
Mendukung 3 varian render untuk fleksibilitas tata letak:
1. `variant="featured"`: Layout asimetris 12-kolom besar dengan media rasio lebar di sisi kiri dan studi kasus detail di sisi kanan.
2. `variant="default"`: Kartu grid 16:10 dengan thumbnail gambar, judul, cuplikan summary, badge skill, dan link aksi studi kasus.
3. `variant="compact"`: Baris ringkas horizontal untuk daftar proyek sekunder pada archive/timeline.

---

## 4. Panduan Library Interaktif

### A. Three.js Engine (`HeroThreeScene.vue`)
- **Tujuan**: Visual engineering 3D yang halus dan elegan di hero section, tanpa membebani GPU/baterai.
- **Konfigurasi Kamera & Renderer**:
  ```ts
  camera = new PerspectiveCamera(42, width / height, 0.1, 100);
  camera.position.set(0, 0, 6.4);
  renderer = new WebGLRenderer({ alpha: true, antialias: true, powerPreference: 'low-power' });
  ```
- **Lifecycle & Memory Management**:
  - `ResizeObserver` memantau container client width/height.
  - `IntersectionObserver` menghentikan rendering jika canvas berada di luar viewport layar.
  - `document.addEventListener('visibilitychange')` menonaktifkan frame loop jika tab browser diminimalkan atau tidak aktif.
  - `onBeforeUnmount` wajib memanggil `.dispose()` pada seluruh geometry dan material, serta membersihkan renderer DOM element.
- **Aksesibilitas (Reduced Motion)**:
  - Memeriksa `window.matchMedia('(prefers-reduced-motion: reduce)')`. Jika bernilai `true`, animasi perputaran kontinu dinonaktifkan sepenuhnya.

### B. Lenis + GSAP ScrollTrigger (`useSmoothScroll.ts`)
- **Inisialisasi**: Singleton instance Lenis dengan durasi scroll 1.1s dan kurva easing halus.
- **Sinkronisasi GSAP**: Menggunakan `gsap.ticker` untuk memicu `lenis.raf()` dengan lag smoothing 0.
- **Inertia Route Bridge**:
  ```ts
  removeNavigateListener = router.on('finish', () => {
      setTimeout(() => {
          ScrollTrigger.refresh();
      }, 50);
  });
  ```
  Ini mencegah trigger offset GSAP rusak saat berpindah halaman via Inertia.

---

## 5. Form Handling & Mutasi State dengan Inertia

Formulir admin menggunakan composable `useForm` dari `@inertiajs/vue3`:
```ts
const form = useForm({
    title: props.project?.title ?? '',
    slug: props.project?.slug ?? '',
    summary: props.project?.summary ?? '',
    status: props.project?.status ?? 'draft',
    cover_image: null as File | null,
    skill_ids: props.project?.skills?.map((s) => s.id) ?? [],
});

function submit() {
    if (isEditing) {
        form.put(route('admin.projects.update', props.project.id));
    } else {
        form.post(route('admin.projects.store'));
    }
}
```
- Menangani error validasi server per-field secara otomatis via `form.errors`.
- Menandai tombol submit dalam state disabled/processing via `form.processing`.
- Menggunakan `preserveScroll: true` pada mutasi kecil (seperti menghapus foto galeri) agar posisi scroll admin tidak meloncat.
