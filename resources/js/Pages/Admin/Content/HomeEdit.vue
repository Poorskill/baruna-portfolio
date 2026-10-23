<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps<{
    settings: Record<string, string>;
}>();

const form = useForm({
    hero_headline: props.settings.hero_headline || '',
    hero_description: props.settings.hero_description || '',
    hero_cta_primary_label: props.settings.hero_cta_primary_label || '',
    hero_cta_primary_url: props.settings.hero_cta_primary_url || '',
    hero_cta_secondary_label: props.settings.hero_cta_secondary_label || '',
    hero_cta_secondary_url: props.settings.hero_cta_secondary_url || '',
});

function submit() {
    form.put(route('admin.content.home.update'));
}
</script>

<template>
    <Head title="Editor Beranda — Admin" />

    <AdminLayout title="Home Content Editor">
        <div class="max-w-3xl space-y-8">
            <div class="flex items-center justify-between border-b border-[#293440] pb-6">
                <div>
                    <h2 class="text-xl font-semibold text-[#F0F2F3] tracking-tight">Konten Halaman Utama (Home)</h2>
                    <p class="text-xs font-mono text-[#A4ADB7] mt-0.5">
                        Perubahan headline dan tombol aksi langsung terhubung dengan antarmuka publik.
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
                <!-- Hero Section Content -->
                <div class="rounded border border-[#293440] bg-[#151B22] p-6 sm:p-8 space-y-4">
                    <div class="border-b border-[#293440] pb-2">
                        <h3 class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">
                            01 // Teks Hero Banner
                        </h3>
                    </div>

                    <div>
                        <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                            Headline Utama <span class="text-[#ffb4ab]">*</span>
                        </label>
                        <input
                            v-model="form.hero_headline"
                            type="text"
                            required
                            maxlength="500"
                            placeholder="Pengembangan web dan solusi sistem digital."
                            class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                        />
                        <p v-if="form.errors.hero_headline" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.hero_headline }}</p>
                    </div>

                    <div>
                        <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                            Deskripsi Ringkas Profil <span class="text-[#ffb4ab]">*</span>
                        </label>
                        <textarea
                            v-model="form.hero_description"
                            required
                            rows="4"
                            maxlength="1000"
                            placeholder="Saya Baruna Dwi Cahya, dengan latar belakang Teknik Informatika dan fokus pada pengembangan aplikasi web serta sistem digital. Portofolio ini menampilkan proyek, implementasi teknis, dan teknologi yang saya gunakan."
                            class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none leading-relaxed"
                        />
                        <p v-if="form.errors.hero_description" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.hero_description }}</p>
                    </div>
                </div>

                <!-- Primary CTA -->
                <div class="rounded border border-[#293440] bg-[#151B22] p-6 sm:p-8 space-y-4">
                    <div class="border-b border-[#293440] pb-2">
                        <h3 class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">
                            02 // Tombol Aksi Utama (Primary CTA)
                        </h3>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">Label Tombol</label>
                            <input
                                v-model="form.hero_cta_primary_label"
                                type="text"
                                placeholder="Lihat Proyek"
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
                        </div>
                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">URL Tujuan</label>
                            <input
                                v-model="form.hero_cta_primary_url"
                                type="text"
                                placeholder="/projects"
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 font-mono text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
                        </div>
                    </div>
                </div>

                <!-- Secondary CTA -->
                <div class="rounded border border-[#293440] bg-[#151B22] p-6 sm:p-8 space-y-4">
                    <div class="border-b border-[#293440] pb-2">
                        <h3 class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">
                            03 // Tombol Aksi Sekunder (Secondary CTA)
                        </h3>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">Label Tombol</label>
                            <input
                                v-model="form.hero_cta_secondary_label"
                                type="text"
                                placeholder="Profil & Pengalaman"
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
                        </div>
                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">URL Tujuan</label>
                            <input
                                v-model="form.hero_cta_secondary_url"
                                type="text"
                                placeholder="/about"
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 font-mono text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-2 pb-12">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded bg-[#4F46E5] px-6 py-2.5 font-mono text-xs font-semibold text-white hover:bg-[#4338CA] transition-colors disabled:opacity-50"
                    >
                        <span v-if="form.processing">Menyimpan...</span>
                        <span v-else>Simpan Konten Beranda</span>
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
