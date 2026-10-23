<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps<{
    settings: Record<string, string>;
}>();

const form = useForm({
    site_name: props.settings.site_name || 'Baruna Dwi Cahya',
    site_descriptor: props.settings.site_descriptor || 'Web Development & Software Systems',
    contact_email: props.settings.contact_email || '',
    contact_whatsapp: props.settings.contact_whatsapp || '',
    availability_status: props.settings.availability_status || '',
    footer_text: props.settings.footer_text || '',
    default_meta_title: props.settings.default_meta_title || '',
    default_meta_description: props.settings.default_meta_description || '',
});

function submit() {
    form.put(route('admin.settings.update'));
}
</script>

<template>
    <Head title="Pengaturan Global — Admin" />

    <AdminLayout title="Global Settings">
        <div class="max-w-3xl space-y-8">
            <div class="flex items-center justify-between border-b border-[#293440] pb-6">
                <div>
                    <h2 class="text-xl font-semibold text-[#F0F2F3] tracking-tight">Pengaturan Global Situs</h2>
                    <p class="text-xs font-mono text-[#A4ADB7] mt-0.5">
                        Konfigurasi identitas pemilik, informasi kontak, status ketersediaan, dan default SEO.
                    </p>
                </div>
                <button
                    type="button"
                    @click="submit"
                    :disabled="form.processing"
                    class="rounded bg-[#4F46E5] px-5 py-2 font-mono text-xs font-semibold text-white hover:bg-[#4338CA] transition-colors disabled:opacity-50"
                >
                    <span v-if="form.processing">Menyimpan...</span>
                    <span v-else>Simpan Pengaturan</span>
                </button>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Identity -->
                <div class="rounded border border-[#293440] bg-[#151B22] p-6 sm:p-8 space-y-4">
                    <div class="border-b border-[#293440] pb-2 flex items-center justify-between">
                        <h3 class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">
                            01 // Identitas Pemilik & Domain
                        </h3>
                    </div>

                    <div>
                        <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                            Nama Pemilik Situs <span class="text-[#ffb4ab]">*</span>
                        </label>
                        <input
                            v-model="form.site_name"
                            type="text"
                            required
                            class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                        />
                        <p v-if="form.errors.site_name" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.site_name }}</p>
                    </div>

                    <div>
                        <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                            Role Descriptor
                        </label>
                        <input
                            v-model="form.site_descriptor"
                            type="text"
                            placeholder="misal: Web Development & Software Systems"
                            class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                        />
                        <p v-if="form.errors.site_descriptor" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.site_descriptor }}</p>
                    </div>

                    <div>
                        <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                            Status Ketersediaan Kerja
                        </label>
                        <input
                            v-model="form.availability_status"
                            type="text"
                            placeholder="misal: Terbuka untuk peluang freelance & full-time"
                            class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                        />
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="rounded border border-[#293440] bg-[#151B22] p-6 sm:p-8 space-y-4">
                    <div class="border-b border-[#293440] pb-2 flex items-center justify-between">
                        <h3 class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">
                            02 // Saluran Kontak Resmi
                        </h3>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">Email Kontak Publik</label>
                            <input
                                v-model="form.contact_email"
                                type="email"
                                placeholder="baruna@example.com"
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 font-mono text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
                            <p v-if="form.errors.contact_email" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.contact_email }}</p>
                        </div>

                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">Nomor WhatsApp</label>
                            <input
                                v-model="form.contact_whatsapp"
                                type="text"
                                placeholder="+62 812-xxxx-xxxx"
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 font-mono text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
                        </div>
                    </div>
                </div>

                <!-- SEO Defaults -->
                <div class="rounded border border-[#293440] bg-[#151B22] p-6 sm:p-8 space-y-4">
                    <div class="border-b border-[#293440] pb-2 flex items-center justify-between">
                        <h3 class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">
                            03 // Metadata Global (SEO)
                        </h3>
                    </div>

                    <div>
                        <label class="block font-mono text-xs uppercase text-[#A4ADB7]">Default Meta Title</label>
                        <input
                            v-model="form.default_meta_title"
                            type="text"
                            placeholder="Baruna Dwi Cahya — Portfolio"
                            class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                        />
                    </div>

                    <div>
                        <label class="block font-mono text-xs uppercase text-[#A4ADB7]">Default Meta Description</label>
                        <textarea
                            v-model="form.default_meta_description"
                            rows="2"
                            class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                        />
                    </div>
                </div>

                <!-- Footer -->
                <div class="rounded border border-[#293440] bg-[#151B22] p-6 sm:p-8 space-y-4">
                    <div class="border-b border-[#293440] pb-2 flex items-center justify-between">
                        <h3 class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">
                            04 // Teks Hak Cipta
                        </h3>
                    </div>

                    <div>
                        <label class="block font-mono text-xs uppercase text-[#A4ADB7]">Teks Catatan Kaki (Footer)</label>
                        <input
                            v-model="form.footer_text"
                            type="text"
                            placeholder="© 2026 Baruna Dwi Cahya. Hak cipta dilindungi undang-undang."
                            class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                        />
                    </div>
                </div>

                <div class="flex justify-end pt-2 pb-12">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded bg-[#4F46E5] px-6 py-2.5 font-mono text-xs font-semibold text-white hover:bg-[#4338CA] transition-colors disabled:opacity-50"
                    >
                        <span v-if="form.processing">Menyimpan...</span>
                        <span v-else>Simpan Seluruh Pengaturan</span>
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
