# ROUTES_AND_DATA_FLOW.md — Katalog Route dan Alur Data
# Baruna Dwi Cahya Personal Portfolio & CMS

Dokumen ini mendokumentasikan seluruh 74 route aktual aplikasi yang terdaftar di Laravel router (`routes/web.php` dan `routes/auth.php`).

---

## 1. Rute Publik (Public Routes)

| Method | URI | Route Name | Controller Action | Middleware | Vue Component / Action | Props yang Dikirim / Catatan |
|---|---|---|---|---|---|---|
| `GET` | `/` | `home` | `Public\HomeController` | `web` | `Public/Home` | `settings`, `featuredProjects`, `skillGroups`, `navLinks`, `socialLinks`, `hasCv` |
| `GET` | `/dashboard` | `dashboard` | Inline Closure | `web` | Redirect ke `admin.dashboard` | Memudahkan akses cepat bagi admin yang login |
| `GET` | `/about` | `about` | `Public\AboutController` | `web` | `Public/About` | `settings`, `education`, `experiences`, `certificates`, `navLinks`, `socialLinks`, `hasCv` |
| `GET` | `/projects` | `projects.index` | `Public\ProjectController@index` | `web` | `Public/Projects/Index` | `projects` (paginated), `categories`, `filters`, `navLinks`, `socialLinks`, `siteName` |
| `GET` | `/projects/{project:slug}` | `projects.show` | `Public\ProjectController@show` | `web` | `Public/Projects/Show` | `project` (with skills & images), `settings`, `navLinks`, `socialLinks`. **Throws 404 jika status != published** |
| `GET` | `/contact` | `contact.show` | `Public\ContactController@show` | `web` | `Public/Contact` | `settings`, `navLinks`, `socialLinks` |
| `POST` | `/contact` | `contact.store` | `Public\ContactController@store` | `web`, `throttle:3,60` | Back redirect + Flash `success` | Validasi honeypot + rate limit IP manual (3 percobaan / jam). Menyimpan pesan status `unread` |
| `GET` | `/cv` | `cv.download` | `Public\CvController` | `web` | Streamed Download (PDF) | Mengunduh file CV aktif sebagai `Baruna-Dwi-Cahya-CV.pdf`. Melempar 404 jika file tidak tersedia |

---

## 2. Rute Autentikasi (Authentication Routes)

| Method | URI | Route Name | Controller Action | Middleware | Catatan Keamanan |
|---|---|---|---|---|---|
| `GET` | `/register` | `register` | `Auth\RegisteredUserController@create` | `guest` | **DITUTUP**: Melempar HTTP 404 secara permanen |
| `POST` | `/register` | - | `Auth\RegisteredUserController@store` | `guest` | **DITUTUP**: Melempar HTTP 404 secara permanen |
| `GET` | `/login` | `login` | `Auth\AuthenticatedSessionController@create` | `guest` | Render `Auth/Login` |
| `POST` | `/login` | - | `Auth\AuthenticatedSessionController@store` | `guest` | Autentikasi session, rate limited oleh Breeze default |
| `POST` | `/logout` | `logout` | `Auth\AuthenticatedSessionController@destroy` | `auth` | Invalidate session, regenerate CSRF token |
| `GET` | `/forgot-password`| `password.request`| `Auth\PasswordResetLinkController@create` | `guest` | Render `Auth/ForgotPassword` |
| `POST`| `/forgot-password`| `password.email` | `Auth\PasswordResetLinkController@store` | `guest` | Kirim token reset password |
| `GET` | `/reset-password/{token}` | `password.reset` | `Auth\NewPasswordController@create` | `guest` | Render `Auth/ResetPassword` |
| `POST`| `/reset-password` | `password.store` | `Auth\NewPasswordController@store` | `guest` | Simpan password baru |
| `GET` | `/verify-email` | `verification.notice` | `Auth\EmailVerificationPromptController` | `auth` | Render `Auth/VerifyEmail` |
| `GET` | `/verify-email/{id}/{hash}` | `verification.verify` | `Auth\VerifyEmailController` | `auth`, `signed`, `throttle:6,1` | Verifikasi email |
| `POST`| `/email/verification-notification` | `verification.send` | `Auth\EmailVerificationNotificationController@store` | `auth`, `throttle:6,1` | Kirim ulang link verifikasi |
| `GET` | `/confirm-password` | `password.confirm` | `Auth\ConfirmablePasswordController@show` | `auth` | Render `Auth/ConfirmPassword` |
| `POST`| `/confirm-password` | - | `Auth\ConfirmablePasswordController@store` | `auth` | Konfirmasi password untuk aksi sensitif |
| `PUT` | `/password` | `password.update` | `Auth\PasswordController@update` | `auth` | Update password akun aktif |

---

## 3. Rute Admin CMS (Admin Routes)

Seluruh route pada grup ini memiliki prefix `admin/`, name prefix `admin.`, dan dilindungi middleware `['auth', 'admin']`. Non-admin melempar HTTP 403, guest dialihkan ke `/login`.

### A. Dashboard & Konten Halaman
| Method | URI | Route Name | Controller Action | Vue Component / Mutasi | Data / Payload Utama |
|---|---|---|---|---|---|
| `GET` | `admin` | `admin.dashboard` | `Admin\DashboardController@index` | `Admin/Dashboard` | `stats`: totalProjects, publishedProjects, draftProjects, totalSkills, unreadMessages |
| `GET` | `admin/content/home` | `admin.content.home.edit` | `Admin\HomeContentController@edit` | `Admin/Content/HomeEdit` | `settings` (group `home`) |
| `PUT` | `admin/content/home` | `admin.content.home.update` | `Admin\HomeContentController@update` | Back + Flash `success` | `hero_headline`, `hero_description`, `hero_cta_*` |
| `GET` | `admin/content/profile` | `admin.content.profile.edit` | `Admin\ProfileContentController@edit` | `Admin/Content/ProfileEdit` | `settings` (`about_short`, `about_long`, `cv_path`) |
| `PUT` | `admin/content/profile` | `admin.content.profile.update` | `Admin\ProfileContentController@update` | Back + Flash `success` | `about_short`, `about_long`, file `cv` (PDF maks 10MB) |
| `GET` | `admin/settings` | `admin.settings.edit` | `Admin\SiteSettingController@edit` | `Admin/Settings/Edit` | `settings` lengkap |
| `PUT` | `admin/settings` | `admin.settings.update` | `Admin\SiteSettingController@update` | Back + Flash `success` | `site_name`, `site_descriptor`, `contact_email`, `footer_text`, dll. |

### B. Manajemen Proyek (Projects CRUD & Galeri)
| Method | URI | Route Name | Controller Action | Vue Component / Mutasi | Keterangan |
|---|---|---|---|---|---|
| `GET` | `admin/projects` | `admin.projects.index` | `Admin\ProjectController@index` | `Admin/Projects/Index` | List proyek terpaginasi + filter pencarian & status |
| `GET` | `admin/projects/create` | `admin.projects.create` | `Admin\ProjectController@create` | `Admin/Projects/Form` | Form buat proyek baru, kirim list skill |
| `POST` | `admin/projects` | `admin.projects.store` | `Admin\ProjectController@store` | Redirect `admin.projects.index` | Simpan proyek, sync `project_skill`, upload cover & gallery |
| `GET` | `admin/projects/{project}/edit` | `admin.projects.edit` | `Admin\ProjectController@edit` | `Admin/Projects/Form` | Form edit proyek, memuat relasi skill & gallery images |
| `PUT/PATCH` | `admin/projects/{project}` | `admin.projects.update` | `Admin\ProjectController@update` | Redirect `admin.projects.index` | Update data, hapus cover lama jika ada file baru, upload image baru |
| `DELETE` | `admin/projects/{project}` | `admin.projects.destroy` | `Admin\ProjectController@destroy` | Redirect `admin.projects.index` | Hapus cover & gallery file fisik lalu hapus record proyek |
| `DELETE` | `admin/projects/{project}/images/{image}` | `admin.projects.images.destroy` | `Admin\ProjectController@destroyImage` | Back + Flash `success` | Hapus satu foto galeri dari storage dan tabel `project_images` |

### C. Skill Groups & Skills
| Method | URI | Route Name | Controller Action | Vue Component / Mutasi |
|---|---|---|---|---|
| `GET` | `admin/skill-groups` | `admin.skill-groups.index` | `Admin\SkillGroupController@index` | `Admin/SkillGroups/Index` |
| `POST` | `admin/skill-groups` | `admin.skill-groups.store` | `Admin\SkillGroupController@store` | Back + Flash `success` |
| `PUT` | `admin/skill-groups/{skillGroup}` | `admin.skill-groups.update` | `Admin\SkillGroupController@update` | Back + Flash `success` |
| `DELETE` | `admin/skill-groups/{skillGroup}` | `admin.skill-groups.destroy` | `Admin\SkillGroupController@destroy` | Back + Flash `success` (Foreign key skill diset NULL) |
| `GET` | `admin/skills` | `admin.skills.index` | `Admin\SkillController@index` | `Admin/Skills/Index` (Memuat skill-groups untuk dropdown) |
| `POST` | `admin/skills` | `admin.skills.store` | `Admin\SkillController@store` | Back + Flash `success` |
| `PUT` | `admin/skills/{skill}` | `admin.skills.update` | `Admin\SkillController@update` | Back + Flash `success` |
| `DELETE` | `admin/skills/{skill}` | `admin.skills.destroy` | `Admin\SkillController@destroy` | Back + Flash `success` (Cascade hapus dari pivot `project_skill`) |

### D. Karir, Pendidikan, dan Sertifikat
| Modul | Route Index | Route Store | Route Update | Route Destroy | Vue Component |
|---|---|---|---|---|---|
| **Experience** | `admin.experience.index` | `admin.experience.store` | `admin.experience.update` | `admin.experience.destroy` | `Admin/Experiences/Index` |
| **Education** | `admin.education.index` | `admin.education.store` | `admin.education.update` | `admin.education.destroy` | `Admin/Education/Index` |
| **Certificates**| `admin.certificates.index`| `admin.certificates.store`| `admin.certificates.update`| `admin.certificates.destroy`| `Admin/Certificates/Index` |

### E. Navigasi & Social Links
| Modul | Route Index | Route Store | Route Update | Route Destroy | Vue Component |
|---|---|---|---|---|---|
| **Navigation** | `admin.navigation.index` | `admin.navigation.store` | `admin.navigation.update` | `admin.navigation.destroy` | `Admin/Navigation/Index` |
| **Social Links**| `admin.social-links.index`| `admin.social-links.store`| `admin.social-links.update`| `admin.social-links.destroy`| `Admin/SocialLinks/Index` |

### F. Inbox Kontak, Log Aktivitas, dan Akun
| Method | URI | Route Name | Controller Action | Vue Component / Mutasi | Keterangan |
|---|---|---|---|---|---|
| `GET` | `admin/messages` | `admin.messages.index` | `Admin\ContactMessageController@index` | `Admin/Messages/Index` | Filter status (`unread`, `read`, `archived`) |
| `GET` | `admin/messages/{message}` | `admin.messages.show` | `Admin\ContactMessageController@show` | `Admin/Messages/Show` | Membuka detail pesan, otomatis set status `read` |
| `PUT` | `admin/messages/{message}/read` | `admin.messages.read` | `Admin\ContactMessageController@markRead` | Back + Flash `success` | Tandai sudah dibaca tanpa buka detail |
| `DELETE` | `admin/messages/{message}` | `admin.messages.destroy` | `Admin\ContactMessageController@destroy` | Redirect `admin.messages.index` | Hapus permanen pesan kontak |
| `GET` | `admin/activity` | `admin.activity.index` | `Admin\ActivityLogController@index` | `Admin/ActivityLog/Index` | Audit log read-only aksi admin |
| `GET` | `admin/account` | `admin.account.edit` | `ProfileController@edit` | `Profile/Edit` | Edit profil nama & email admin |
| `PATCH` | `admin/account` | `admin.account.update` | `ProfileController@update` | Redirect `admin.account.edit` | Simpan profil admin |
| `DELETE` | `admin/account` | `admin.account.destroy` | `ProfileController@destroy` | Redirect `/` | Hapus akun admin (dengan konfirmasi password) |
