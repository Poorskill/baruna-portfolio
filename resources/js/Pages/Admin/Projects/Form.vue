<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Project, Skill, ProjectImage } from '@/types';

const props = defineProps<{
    project?: (Project & { images?: ProjectImage[] }) | null;
    skills: Skill[];
}>();

const isEdit = Boolean(props.project);

const form = useForm({
    title: props.project?.title || '',
    slug: props.project?.slug || '',
    category: props.project?.category || '',
    role: props.project?.role || '',
    summary: props.project?.summary || '',
    body: props.project?.body || '',
    problem: props.project?.problem || '',
    solution: props.project?.solution || '',
    features: props.project?.features || '',
    challenges: props.project?.challenges || '',
    outcome_or_learning: props.project?.outcome_or_learning || '',
    demo_url: props.project?.demo_url || '',
    source_url: props.project?.source_url || '',
    documentation_url: props.project?.documentation_url || '',
    meta_title: props.project?.meta_title || '',
    meta_description: props.project?.meta_description || '',
    status: props.project?.status || 'draft',
    is_featured: Boolean(props.project?.is_featured),
    sort_order: props.project?.sort_order ?? 0,
    skill_ids: props.project?.skills ? props.project.skills.map((s) => s.id) : ([] as number[]),
    cover_image: null as File | null,
    gallery: [] as File[],
});

function handleCoverChange(e: Event) {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.cover_image = target.files[0];
    }
}

function handleGalleryChange(e: Event) {
    const target = e.target as HTMLInputElement;
    if (target.files) {
        form.gallery = Array.from(target.files);
    }
}

function toggleSkill(skillId: number) {
    const idx = form.skill_ids.indexOf(skillId);
    if (idx > -1) {
        form.skill_ids.splice(idx, 1);
    } else {
        form.skill_ids.push(skillId);
    }
}

function deleteGalleryImage(image: ProjectImage) {
    if (!props.project) return;
    if (confirm('Hapus gambar ini dari galeri proyek secara permanen?')) {
        router.delete(route('admin.projects.images.destroy', [props.project.id, image.id]), {
            preserveScroll: true,
        });
    }
}

function submit() {
    if (isEdit && props.project) {
        form.transform((data) => ({
            ...data,
            _method: 'PUT',
        })).post(route('admin.projects.update', props.project.id), {
            forceFormData: true,
        });
    } else {
        form.post(route('admin.projects.store'), {
            forceFormData: true,
        });
    }
}
</script>

<template>
    <Head :title="isEdit ? `Edit: ${project?.title}` : 'Proyek Baru — Admin'" />

    <AdminLayout :title="isEdit ? 'Edit Proyek' : 'Tambah Proyek Baru'">
        <div class="max-w-4xl space-y-8">
            <!-- Top Action Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-[#293440] pb-6">
                <div>
                    <h2 class="text-xl font-semibold text-[#F0F2F3] tracking-tight">
                        {{ isEdit ? `Edit Proyek: ${project?.title}` : 'Entri Proyek Baru' }}
                    </h2>
                    <p class="text-xs font-mono text-[#A4ADB7] mt-0.5">
                        Lengkapi studi kasus arsitektur, parameter teknis, dan aset tangkapan layar.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('admin.projects.index')"
                        class="rounded border border-[#293440] bg-[#151B22] px-4 py-2 font-mono text-xs text-[#A4ADB7] hover:text-[#F0F2F3] transition-colors"
                    >
                        &larr; Kembali
                    </Link>
                    <button
                        type="button"
                        @click="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center justify-center rounded bg-[#4F46E5] px-5 py-2 font-mono text-xs font-semibold text-white hover:bg-[#4338CA] transition-colors disabled:opacity-50"
                    >
                        <span v-if="form.processing">Menyimpan...</span>
                        <span v-else>{{ isEdit ? 'Perbarui Proyek' : 'Simpan Proyek' }}</span>
                    </button>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-8">
                <!-- Section 1: Core Metadata -->
                <div class="rounded border border-[#293440] bg-[#151B22] p-6 sm:p-8 space-y-6">
                    <div class="border-b border-[#293440] pb-3 flex items-center justify-between">
                        <h3 class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">
                            01 // Metadata & Visibilitas Publik
                        </h3>
                        <span class="font-mono text-[10px] text-[#A4ADB7]">WAJIB & INTI</span>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                                Judul Proyek <span class="text-[#ffb4ab]">*</span>
                            </label>
                            <input
                                v-model="form.title"
                                type="text"
                                required
                                placeholder="misal: Enterprise Billing Ledger System"
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
                            <p v-if="form.errors.title" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.title }}</p>
                        </div>

                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                                Slug URL (Kosongkan untuk auto-generate)
                            </label>
                            <input
                                v-model="form.slug"
                                type="text"
                                placeholder="misal: billing-ledger-system"
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 font-mono text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
                            <p v-if="form.errors.slug" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.slug }}</p>
                        </div>

                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                                Kategori / Domain
                            </label>
                            <input
                                v-model="form.category"
                                type="text"
                                placeholder="misal: Financial Technology, Distributed Systems"
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
                            <p v-if="form.errors.category" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.category }}</p>
                        </div>

                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                                Peran / Kontribusi Pribadi
                            </label>
                            <input
                                v-model="form.role"
                                type="text"
                                placeholder="misal: Solo Engineer, Lead Full-stack Developer"
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
                        </div>

                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                                Status Publikasi <span class="text-[#ffb4ab]">*</span>
                            </label>
                            <select
                                v-model="form.status"
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3 py-2.5 font-mono text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            >
                                <option value="draft">Draft (Tertutup publik)</option>
                                <option value="published">Published (Tayang)</option>
                                <option value="archived">Archived (Diarsipkan)</option>
                            </select>
                            <p v-if="form.errors.status" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.status }}</p>
                        </div>

                        <div class="sm:col-span-2 flex flex-wrap items-center justify-between gap-4 pt-2 border-t border-[#293440]/60">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input
                                    v-model="form.is_featured"
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-[#293440] bg-[#101419] text-[#818CF8] focus:ring-0"
                                />
                                <span class="text-xs font-mono text-[#F0F2F3]">Sorot sebagai Proyek Unggulan (Featured Showcase)</span>
                            </label>

                            <div class="flex items-center gap-2 font-mono text-xs text-[#A4ADB7]">
                                <span>Urutan Sortir:</span>
                                <input
                                    v-model.number="form.sort_order"
                                    type="number"
                                    min="0"
                                    class="w-20 rounded border border-[#293440] bg-[#101419] px-2 py-1 font-mono text-xs text-[#F0F2F3]"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Case Study Breakdown -->
                <div class="rounded border border-[#293440] bg-[#151B22] p-6 sm:p-8 space-y-6">
                    <div class="border-b border-[#293440] pb-3 flex items-center justify-between">
                        <h3 class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">
                            02 // Uraian Studi Kasus Rekayasa
                        </h3>
                        <span class="font-mono text-[10px] text-[#A4ADB7]">KONTEN UTAMA</span>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                                Ringkasan Eksekutif (Summary)
                            </label>
                            <textarea
                                v-model="form.summary"
                                rows="2"
                                maxlength="1000"
                                placeholder="Ringkasan singkat untuk tampilan kartu dan cuplikan katalog..."
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
                            <p v-if="form.errors.summary" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.summary }}</p>
                        </div>

                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                                Gambaran Umum Arsitektur Sistem (Overview Body)
                            </label>
                            <textarea
                                v-model="form.body"
                                rows="4"
                                placeholder="Penjelasan menyeluruh tentang konteks, arsitektur, dan perancangan..."
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none leading-relaxed"
                            />
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                                    Problem Statement (Masalah yang Dihadapi)
                                </label>
                                <textarea
                                    v-model="form.problem"
                                    rows="3"
                                    class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                                />
                            </div>

                            <div>
                                <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                                    Solusi & Pendekatan Teknis
                                </label>
                                <textarea
                                    v-model="form.solution"
                                    rows="3"
                                    class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                                />
                            </div>

                            <div>
                                <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                                    Fitur Utama yang Telah Berfungsi Nyata
                                </label>
                                <textarea
                                    v-model="form.features"
                                    rows="3"
                                    class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                                />
                            </div>

                            <div>
                                <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                                    Kendala & Keputusan Rekayasa
                                </label>
                                <textarea
                                    v-model="form.challenges"
                                    rows="3"
                                    class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                                />
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                                    Dampak / Hasil Terukur & Pelajaran
                                </label>
                                <textarea
                                    v-model="form.outcome_or_learning"
                                    rows="2"
                                    class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Tech Stack Association -->
                <div class="rounded border border-[#293440] bg-[#151B22] p-6 sm:p-8 space-y-4">
                    <div class="border-b border-[#293440] pb-3 flex items-center justify-between">
                        <h3 class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">
                            03 // Relasi Teknologi Terkait
                        </h3>
                        <span class="font-mono text-[10px] text-[#A4ADB7]">TAGS</span>
                    </div>

                    <div v-if="skills && skills.length > 0" class="flex flex-wrap gap-2 pt-2">
                        <button
                            v-for="skill in skills"
                            :key="skill.id"
                            type="button"
                            @click="toggleSkill(skill.id)"
                            :class="[
                                'rounded px-3 py-1 font-mono text-xs transition-colors',
                                form.skill_ids.includes(skill.id)
                                    ? 'bg-[#4F46E5] text-white font-semibold'
                                    : 'border border-[#293440] bg-[#101419] text-[#A4ADB7] hover:border-[#818CF8] hover:text-[#F0F2F3]'
                            ]"
                        >
                            {{ skill.name }}
                        </button>
                    </div>
                    <p v-else class="text-xs font-mono text-[#A4ADB7]">
                        Belum ada skill yang terdaftar dalam database.
                    </p>
                </div>

                <!-- Section 4: External Links -->
                <div class="rounded border border-[#293440] bg-[#151B22] p-6 sm:p-8 space-y-4">
                    <div class="border-b border-[#293440] pb-3 flex items-center justify-between">
                        <h3 class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">
                            04 // Tautan Eksternal
                        </h3>
                        <span class="font-mono text-[10px] text-[#A4ADB7]">VERIFIKASI URL</span>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-3">
                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">Live Demo URL</label>
                            <input
                                v-model="form.demo_url"
                                type="url"
                                placeholder="https://..."
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2 font-mono text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
                            <p v-if="form.errors.demo_url" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.demo_url }}</p>
                        </div>

                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">Source Code (GitHub)</label>
                            <input
                                v-model="form.source_url"
                                type="url"
                                placeholder="https://github.com/..."
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2 font-mono text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
                            <p v-if="form.errors.source_url" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.source_url }}</p>
                        </div>

                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">Dokumentasi URL</label>
                            <input
                                v-model="form.documentation_url"
                                type="url"
                                placeholder="https://..."
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2 font-mono text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
                        </div>
                    </div>
                </div>

                <!-- Section 5: Media Uploads -->
                <div class="rounded border border-[#293440] bg-[#151B22] p-6 sm:p-8 space-y-6">
                    <div class="border-b border-[#293440] pb-3 flex items-center justify-between">
                        <h3 class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">
                            05 // Gambar Sampul & Galeri
                        </h3>
                        <span class="font-mono text-[10px] text-[#A4ADB7]">ASET MEDIA</span>
                    </div>

                    <div class="grid gap-8 sm:grid-cols-2">
                        <!-- Cover Image -->
                        <div class="space-y-3">
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                                Gambar Sampul (Maks. 5MB)
                            </label>
                            <input
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                                @change="handleCoverChange"
                                class="block w-full text-xs text-[#A4ADB7] file:mr-4 file:rounded file:border-0 file:bg-[#101419] file:px-3 file:py-1.5 file:font-mono file:text-xs file:text-[#818CF8] hover:file:bg-[#293440]"
                            />
                            <p v-if="form.errors.cover_image" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.cover_image }}</p>

                            <div v-if="project?.cover_image" class="pt-2">
                                <span class="font-mono text-[10px] text-[#A4ADB7] block">Sampul Saat Ini:</span>
                                <img
                                    :src="project.cover_image.startsWith('http') ? project.cover_image : `/storage/${project.cover_image}`"
                                    class="mt-1.5 h-24 w-36 rounded border border-[#293440] object-cover"
                                />
                            </div>
                        </div>

                        <!-- Gallery Upload -->
                        <div class="space-y-3">
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                                Tambah Gambar Galeri (Multi-upload)
                            </label>
                            <input
                                type="file"
                                multiple
                                accept="image/jpeg,image/png,image/webp"
                                @change="handleGalleryChange"
                                class="block w-full text-xs text-[#A4ADB7] file:mr-4 file:rounded file:border-0 file:bg-[#101419] file:px-3 file:py-1.5 file:font-mono file:text-xs file:text-[#818CF8] hover:file:bg-[#293440]"
                            />

                            <!-- Existing Gallery Items -->
                            <div v-if="project?.images && project.images.length > 0" class="pt-2 space-y-2">
                                <span class="font-mono text-[10px] text-[#A4ADB7] block">Galeri Tersimpan:</span>
                                <div class="grid grid-cols-3 gap-2">
                                    <div
                                        v-for="img in project.images"
                                        :key="img.id"
                                        class="group relative h-16 rounded border border-[#293440] overflow-hidden"
                                    >
                                        <img
                                            :src="img.path.startsWith('http') ? img.path : `/storage/${img.path}`"
                                            class="h-full w-full object-cover"
                                        />
                                        <button
                                            type="button"
                                            @click="deleteGalleryImage(img)"
                                            class="absolute inset-0 flex items-center justify-center bg-black/75 opacity-0 group-hover:opacity-100 transition-opacity font-mono text-[10px] text-[#ffb4ab]"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 6: SEO Metadata -->
                <div class="rounded border border-[#293440] bg-[#151B22] p-6 sm:p-8 space-y-4">
                    <div class="border-b border-[#293440] pb-3 flex items-center justify-between">
                        <h3 class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">
                            06 // Optimasi Mesin Pencari (SEO)
                        </h3>
                        <span class="font-mono text-[10px] text-[#A4ADB7]">METADATA</span>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">Custom Meta Title</label>
                            <input
                                v-model="form.meta_title"
                                type="text"
                                placeholder="Judul khusus untuk SERP"
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
                        </div>
                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">Custom Meta Description</label>
                            <input
                                v-model="form.meta_description"
                                type="text"
                                placeholder="Cuplikan deskripsi hasil pencarian"
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
                        </div>
                    </div>
                </div>

                <!-- Action Footer -->
                <div class="flex items-center justify-end gap-4 border-t border-[#293440] pt-6 pb-12">
                    <Link
                        :href="route('admin.projects.index')"
                        class="rounded border border-[#293440] bg-[#151B22] px-5 py-2.5 font-mono text-xs text-[#A4ADB7] hover:text-[#F0F2F3]"
                    >
                        Batalkan
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center justify-center rounded bg-[#4F46E5] px-6 py-2.5 font-mono text-xs font-semibold text-white hover:bg-[#4338CA] transition-colors disabled:opacity-50"
                    >
                        <span v-if="form.processing">Menyimpan...</span>
                        <span v-else>{{ isEdit ? 'Perbarui Proyek' : 'Simpan Proyek' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
