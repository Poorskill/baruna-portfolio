# DESIGN_SYSTEM.md — Sistem Desain Obsidian Precision
# Baruna Dwi Cahya Personal Portfolio & CMS

## 1. Filosofi & Karakter Brand

Sistem desain mengadopsi estetika **Obsidian Precision**, memadukan **Dark Minimalism**, **Editorial Tech**, dan **Instrument-Grade Functionalism**.

### Prinsip Utama:
1. **Architectural Restraint**: Menghindari gradien menyala tanpa arti, drop shadow berlebihan, atau hiasan visual semu. Kedalaman tampilan dicapai melalui penjenjangan warna permukaan (surface tiers) dan garis pembatas struktural 1px yang tajam.
2. **Editorial Density**: Tipografi yang kuat dengan kontras tajam antara judul sans-serif humanis dan metadata monospace teknis. Spasi teratur berbasis ritme 4px.
3. **Instrumen Fungsional**: Target interaktif memberikan feedback visual yang tegas. Tampilan terasa tenang, stabil, dan presisi tinggi seperti IDE atau instrumen laboratorium.
4. **Anti-AI-Slop**:
   - Wordmark brand WAJIB berupa teks `BARUNA DWI CAHYA`. Dilarang menggunakan label domain gimmick seperti `BARUNA.DEV`.
   - Tanpa panel fake monitoring, server ping, uptime, latency, atau status simulasi.
   - Tanpa progress bar skill persentase buatan.
   - Screenshot proyek harus foto tangkapan layar produk asli, bukan placeholder mockup generik.

---

## 2. Palet Warna (Color Tokens)

| Token Nama | Nilai HEX | Peran & Penggunaan Fungsional |
|---|---|---|
| **Canvas Bedrock** | `#0B0F14` | Latar belakang kanvas viewport terdalam, tidak memantulkan cahaya. |
| **Surface Base** | `#101419` | Latar belakang utama komponen halaman publik dan kartu dasar. |
| **Surface Panel** | `#151B22` | Header sticky, sidebar admin, panel inset, dan card elevated surface. |
| **Precision Border** | `#293440` | Garis batas tipis 1px untuk mendefinisikan batas struktural dan pemisah. |
| **Border Active** | `#364453` | Garis batas pada state hover atau card focus. |
| **Primary Text** | `#F0F2F3` | Teks judul utama dan nilai penting berkontras tinggi (Titanium White). |
| **Muted Text** | `#A4ADB7` | Teks paragraf, deskripsi, label metadata, dan header tabel. |
| **Indigo Accent** | `#818CF8` | Aksen fokus utama untuk indikator tab aktif, garis hover, dan wireframe. |
| **Lime Tension** | `#D7FF64` | Aksen penanda fokus aksi primer dan selection highlight. |
| **Status Operational** | `#22C55E` | Indikator status stabil, published, atau ketersediaan kolaborasi. |
| **Status Error** | `#FFB4AB` | Indikator validasi gagal, peringatan destruktif, atau flash error. |

---

## 3. Tipografi (Typography)

Sistem tipografi menggunakan perpaduan antara **Geist** (humanist sans-serif) dan **JetBrains Mono** (industrial monospace).

| Gaya | Font Family | Ukuran Desktop / Mobile | Line Height | Tracking | Penggunaan |
|---|---|---|---|---|---|
| **Display Hero** | Geist Sans | 48–56px / 34–38px | 56–64px | `-0.035em` | Headline utama halaman Home |
| **Headline Lg** | Geist Sans | 30–32px / 24–26px | 38–40px | `-0.025em` | Judul section utama & nama proyek |
| **Headline Md** | Geist Sans | 20–22px / 18–20px | 28px | `-0.015em` | Judul kartu & sub-heading |
| **Body Lg** | Geist Sans | 16px / 15px | 26px | `-0.005em` | Pengantar & paragraf lead |
| **Body Md** | Geist Sans | 14px / 14px | 22px | `0` | Paragraf reguler & deskripsi |
| **Label Monospace**| JetBrains Mono| 12–13px / 11–12px | 18px | `-0.01em` | Tag skill, tombol teks, link |
| **Micro Tag** | JetBrains Mono| 10–11px / 10px | 14px | `+0.05em` | Badge uppercase, timestamp, status |

---

## 4. Layout, Spacing, dan Grid Responsif

- **Sistem Spacing**: Kelipatan matematis 4px (`0.25rem = 4px`, `0.5rem = 8px`, `1rem = 16px`, `1.5rem = 24px`, `2.5rem = 40px`).
- **Container Max-Width**: `max-w-6xl` (1152px) untuk halaman publik terfokus; `max-w-7xl` untuk grid admin berdensitas tinggi.
- **Breakpoints**:
  - `Desktop (>= 1280px)`: 12-kolom grid asimetris dengan margin luar 48px.
  - `Tablet (768px – 1279px)`: 8-kolom grid dengan margin luar 32px.
  - `Mobile (< 768px)`: Kolom tunggal alami (single-stack), margin luar 16–20px, tanpa horizontal overflow.

---

## 5. Komponen Primitif & State Interaksi

- **Buttons**:
  - `PrimaryButton`: Background `#F0F2F3` dengan teks gelap `#101419` atau `#818CF8` border, sudut lengkung presisi 4px (`rounded`), transisi 150ms.
  - `SecondaryButton`: Background transparan, border `#293440`, teks `#A4ADB7`, hover teks `#F0F2F3`.
  - `DangerButton`: Aksen merah bata `#FFB4AB` untuk konfirmasi hapus permanen.
- **Forms**:
  - `TextInput`, `SelectField`, `TextareaField`: Background `#101419`, border `#293440`, teks `#F0F2F3`, placeholder `#A4ADB7/40`, focus ring `#818CF8`.
- **Selection Color**:
  - CSS global: `selection:bg-[#4F46E5] selection:text-white` atau `selection:bg-[#D7FF64] selection:text-[#0B0F14]`.
- **Accessible Motion**:
  - Seluruh transisi dan animasi CSS menghormati media query `(prefers-reduced-motion: reduce)`.
