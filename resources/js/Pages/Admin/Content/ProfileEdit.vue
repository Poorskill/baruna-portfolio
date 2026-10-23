<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps<{
    settings: Record<string, string>;
}>();

const photoPreview = ref<string | null>(null);

const form = useForm({
    about_short: props.settings.about_short || '',
    about_long: props.settings.about_long || '',
    photo: null as File | null,
    remove_photo: false,
    cv: null as File | null,
});

function handlePhotoChange(e: Event) {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        form.photo = file;
        form.remove_photo = false;
        photoPreview.value = URL.createObjectURL(file);
    }
}

function removeCurrentPhoto() {
    form.photo = null;
    form.remove_photo = true;
    photoPreview.value = null;
}

function handleCvChange(e: Event) {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.cv = target.files[0];
    }
}

function submit() {
    form.transform((data) => ({
        ...data,
        _method: 'PUT',
    })).post(route('admin.content.profile.update'), {
        forceFormData: true,
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Editor Profil & Foto — Admin" />

    <AdminLayout title="Profile & Media Content">
        <div class="max-w-3xl space-y-8">
            <div class="flex items-center justify-between border-b border-[#293440] pb-6">
                <div>
                    <h2 class="text-xl font-semibold text-[#F0F2F3] tracking-tight">Konten Profil, Foto & CV</h2>
                    <p class="text-xs font-mono text-[#A4ADB7] mt-0.5">
                        Kelola foto profil visual 3D hero, narasi biografi, dan dokumen CV resmi.
                    </p>
                </div>
                <button
                    type="button"
                    @click="submit"
                    :disabled="form.processing"
                    class="rounded bg-[#4F46E5] px-5 py-2 font-mono text-xs font-semibold text-white hover:bg-[#4338CA] transition-colors disabled:opacity-50"
                >
                    <span v-if="form.processing">Menyimpan...</span>
                    <span v-else>Simpan Perubahan</span>
                </button>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- 01 // Photo Profile Upload -->
                <div class="rounded border border-[#293440] bg-[#151B22] p-6 sm:p-8 space-y-4">
                    <div class="border-b border-[#293440] pb-2 flex items-center justify-between">
                        <h3 class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">
                            01 // Foto Profil / Visual Portrait 3D Hero
                        </h3>
                        <span class="font-mono text-[10px] text-[#A4ADB7]/70">PNG, JPG, WebP (Maks 5MB)</span>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center gap-6 pt-2">
                        <!-- Preview Thumbnail -->
                        <div class="relative h-28 w-28 shrink-0 overflow-hidden rounded-2xl border border-[#293440] bg-[#101419] flex items-center justify-center shadow-inner">
                            <img
                                v-if="photoPreview"
                                :src="photoPreview"
                                alt="Pratinjau Baru"
                                class="h-full w-full object-cover"
                            />
                            <img
                                v-else-if="settings.photo_path && !form.remove_photo"
                                :src="settings.photo_path.startsWith('http') ? settings.photo_path : `/storage/${settings.photo_path}`"
                                alt="Foto Saat Ini"
                                class="h-full w-full object-cover"
                            />
                            <div v-else class="text-center font-mono text-[10px] text-[#A4ADB7]/50 px-2">
                                [ Tanpa Foto ]
                            </div>
                        </div>

                        <!-- Upload Controls -->
                        <div class="space-y-3 flex-1">
                            <div>
                                <label class="block font-mono text-xs uppercase text-[#A4ADB7] mb-1.5">
                                    Pilih Berkas Foto Baru
                                </label>
                                <input
                                    type="file"
                                    accept="image/png,image/jpeg,image/webp"
                                    @change="handlePhotoChange"
                                    class="block w-full text-xs text-[#A4ADB7] file:mr-4 file:rounded file:border-0 file:bg-[#101419] file:px-3.5 file:py-1.5 file:font-mono file:text-xs file:text-[#818CF8] hover:file:bg-[#1C2530]"
                                />
                                <p v-if="form.errors.photo" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.photo }}</p>
                            </div>

                            <div v-if="(settings.photo_path && !form.remove_photo) || photoPreview" class="pt-1">
                                <button
                                    type="button"
                                    @click="removeCurrentPhoto"
                                    class="font-mono text-xs text-[#ffb4ab] hover:underline transition-colors"
                                >
                                    &times; Hapus / Kosongkan Foto Profil (Gunakan Fallback Insignia BDC)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 02 // Short Intro -->
                <div class="rounded border border-[#293440] bg-[#151B22] p-6 sm:p-8 space-y-4">
                    <div class="border-b border-[#293440] pb-2">
                        <h3 class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">
                            02 // Ringkasan Profil Singkat
                        </h3>
                    </div>

                    <div>
                        <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                            Paragraf Pengantar
                        </label>
                        <textarea
                            v-model="form.about_short"
                            rows="3"
                            maxlength="2000"
                            placeholder="Pengembang perangkat lunak dengan fokus pada sistem backend dan rekayasa web..."
                            class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none leading-relaxed"
                        />
                        <p v-if="form.errors.about_short" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.about_short }}</p>
                    </div>
                </div>

                <!-- 03 // Detailed Bio -->
                <div class="rounded border border-[#293440] bg-[#151B22] p-6 sm:p-8 space-y-4">
                    <div class="border-b border-[#293440] pb-2">
                        <h3 class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">
                            03 // Latar Belakang & Filosofi Rekayasa
                        </h3>
                    </div>

                    <div>
                        <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                            Uraian Lengkap (Mendukung Multi-paragraf)
                        </label>
                        <textarea
                            v-model="form.about_long"
                            rows="6"
                            maxlength="5000"
                            placeholder="Tuliskan perjalanan teknis, pendekatan dalam menyelesaikan masalah arsitektur, dan cara belajar..."
                            class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none leading-relaxed"
                        />
                        <p v-if="form.errors.about_long" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.about_long }}</p>
                    </div>
                </div>

                <!-- 04 // CV Document Upload -->
                <div class="rounded border border-[#293440] bg-[#151B22] p-6 sm:p-8 space-y-4">
                    <div class="border-b border-[#293440] pb-2">
                        <h3 class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">
                            04 // Berkas Curriculum Vitae (PDF Maks. 10MB)
                        </h3>
                    </div>

                    <div class="space-y-3">
                        <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                            Unggah Berkas CV Baru
                        </label>
                        <input
                            type="file"
                            accept="application/pdf"
                            @change="handleCvChange"
                            class="block w-full text-xs text-[#A4ADB7] file:mr-4 file:rounded file:border-0 file:bg-[#101419] file:px-3 file:py-1.5 file:font-mono file:text-xs file:text-[#818CF8]"
                        />
                        <p v-if="form.errors.cv" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.cv }}</p>

                        <div v-if="settings.cv_path" class="mt-3 flex items-center gap-3 rounded border border-[#293440] bg-[#101419] p-3 text-xs">
                            <span class="font-mono text-[#22C55E]">&check; Berkas CV aktif tersimpan:</span>
                            <a
                                :href="route('cv.download')"
                                target="_blank"
                                class="font-mono text-[#818CF8] hover:underline"
                            >
                                Unduh / Pratinjau Dokumen &nearr;
                            </a>
                        </div>
                        <p v-else class="text-xs font-mono text-[#A4ADB7]/60">
                            Belum ada berkas CV yang diunggah.
                        </p>
                    </div>
                </div>

                <div class="flex justify-end pt-2 pb-12">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded bg-[#4F46E5] px-6 py-2.5 font-mono text-xs font-semibold text-white hover:bg-[#4338CA] transition-colors disabled:opacity-50"
                    >
                        <span v-if="form.processing">Menyimpan...</span>
                        <span v-else>Simpan Konten Profil & Berkas</span>
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
