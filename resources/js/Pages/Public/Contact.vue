<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { NavigationLink, SocialLink } from '@/types';

const props = defineProps<{
    settings: Record<string, string>;
    navLinks: NavigationLink[];
    socialLinks: SocialLink[];
}>();

const siteName = props.settings.site_name || 'Baruna Dwi Cahya';
const siteDescriptor = props.settings.site_descriptor || 'Web Development & Software Systems';
const contactEmail = props.settings.contact_email || '';
const contactWhatsapp = props.settings.contact_whatsapp || '';
const availabilityStatus = props.settings.availability_status || '';

const form = useForm({
    name: '',
    email: '',
    subject: '',
    message: '',
    honeypot: '',
});

function submit() {
    form.post(route('contact.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}
</script>

<template>
    <Head>
        <title>Contact — {{ siteName }}</title>
        <meta name="description" content="Kirim pesan langsung atau hubungi Baruna Dwi Cahya untuk mendiskusikan peluang kerja dan kolaborasi proyek." />
    </Head>

    <PublicLayout
        :nav-links="navLinks"
        :social-links="socialLinks"
        :site-name="siteName"
        :site-descriptor="siteDescriptor"
        :footer-text="settings.footer_text"
    >
        <section class="border-b border-[#293440] bg-[#101419] py-20 sm:py-28">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <div class="max-w-2xl space-y-3">
                    <div class="inline-flex items-center gap-2 rounded border border-[#293440] bg-[#151B22] px-3 py-1 font-mono text-xs text-[#818CF8]">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#22C55E]" />
                        <span>KOMUNIKASI LANGSUNG</span>
                    </div>

                    <h1 class="text-3xl font-semibold tracking-[-0.03em] text-[#F0F2F3] sm:text-5xl">
                        Hubungi Saya
                    </h1>

                    <p class="text-sm sm:text-base leading-relaxed text-[#A4ADB7]">
                        Tinggalkan pesan melalui form terenkripsi di bawah atau hubungi saya langsung melalui surel dan tautan profesional resmi.
                    </p>
                </div>

                <div class="mt-14 grid gap-12 lg:grid-cols-12 lg:items-start">
                    <!-- Left: Verified Channels & Status -->
                    <div class="space-y-6 lg:col-span-5">
                        <div v-if="availabilityStatus" class="rounded border border-[#293440] bg-[#151B22] p-6 space-y-2">
                            <span class="font-mono text-[10px] uppercase tracking-wider text-[#A4ADB7]">Status Ketersediaan</span>
                            <div class="flex items-center gap-2 pt-1">
                                <span class="h-2 w-2 rounded-full bg-[#22C55E]" />
                                <p class="text-xs font-mono font-medium text-[#F0F2F3]">{{ availabilityStatus }}</p>
                            </div>
                        </div>

                        <div class="rounded border border-[#293440] bg-[#151B22] p-6 space-y-4">
                            <h3 class="font-mono text-xs uppercase tracking-wider text-[#818CF8] pb-2 border-b border-[#293440]">
                                Informasi Kontak
                            </h3>

                            <div v-if="contactEmail" class="space-y-1">
                                <span class="font-mono text-[10px] uppercase text-[#A4ADB7]">Alamat Surel</span>
                                <p>
                                    <a :href="`mailto:${contactEmail}`" class="font-mono text-xs text-[#818CF8] hover:underline">
                                        {{ contactEmail }}
                                    </a>
                                </p>
                            </div>

                            <div v-if="contactWhatsapp" class="space-y-1">
                                <span class="font-mono text-[10px] uppercase text-[#A4ADB7]">WhatsApp Bisnis</span>
                                <p class="font-mono text-xs text-[#F0F2F3]">
                                    {{ contactWhatsapp }}
                                </p>
                            </div>
                        </div>

                        <div v-if="socialLinks && socialLinks.length > 0" class="rounded border border-[#293440] bg-[#151B22] p-6 space-y-4">
                            <h3 class="font-mono text-xs uppercase tracking-wider text-[#818CF8] pb-2 border-b border-[#293440]">
                                Platform Terverifikasi
                            </h3>
                            <div class="flex flex-col gap-2 font-mono text-xs">
                                <a
                                    v-for="item in socialLinks"
                                    :key="item.id"
                                    :href="item.url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center justify-between rounded border border-[#293440] bg-[#101419] px-3 py-2 text-[#A4ADB7] hover:border-[#818CF8] hover:text-[#818CF8] transition-colors"
                                >
                                    <span>{{ item.label }}</span>
                                    <span>&nearr;</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Contact Form -->
                    <div class="rounded border border-[#293440] bg-[#151B22] p-8 sm:p-10 lg:col-span-7 space-y-6">
                        <div class="border-b border-[#293440] pb-4">
                            <h3 class="text-lg font-semibold text-[#F0F2F3]">Kirim Pesan</h3>
                            <p class="text-xs text-[#A4ADB7] font-mono mt-0.5">Pesan akan diteruskan ke kotak masuk admin.</p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-5">
                            <!-- Honeypot -->
                            <input
                                v-model="form.honeypot"
                                type="text"
                                name="honeypot"
                                class="hidden"
                                tabindex="-1"
                                autocomplete="off"
                            />

                            <div v-if="(form.errors as Record<string, string>)['rate_limit']" class="rounded border border-[#ffb4ab]/40 bg-[#ffb4ab]/10 p-3 text-xs font-mono text-[#ffb4ab]">
                                {{ (form.errors as Record<string, string>)['rate_limit'] }}
                            </div>

                            <div>
                                <label for="name" class="block font-mono text-xs uppercase text-[#A4ADB7]">
                                    Nama Lengkap <span class="text-[#ffb4ab]">*</span>
                                </label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    required
                                    maxlength="255"
                                    placeholder="Nama Anda"
                                    class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 text-xs text-[#F0F2F3] placeholder-[#A4ADB7]/40 focus:border-[#818CF8] focus:outline-none"
                                />
                                <p v-if="form.errors.name" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.name }}</p>
                            </div>

                            <div>
                                <label for="email" class="block font-mono text-xs uppercase text-[#A4ADB7]">
                                    Alamat Email <span class="text-[#ffb4ab]">*</span>
                                </label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    required
                                    maxlength="255"
                                    placeholder="nama@perusahaan.com"
                                    class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 text-xs text-[#F0F2F3] placeholder-[#A4ADB7]/40 focus:border-[#818CF8] focus:outline-none font-mono"
                                />
                                <p v-if="form.errors.email" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.email }}</p>
                            </div>

                            <div>
                                <label for="subject" class="block font-mono text-xs uppercase text-[#A4ADB7]">
                                    Subjek Pesan
                                </label>
                                <input
                                    id="subject"
                                    v-model="form.subject"
                                    type="text"
                                    maxlength="255"
                                    placeholder="Misal: Peluang Kolaborasi / Rekrutmen"
                                    class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 text-xs text-[#F0F2F3] placeholder-[#A4ADB7]/40 focus:border-[#818CF8] focus:outline-none"
                                />
                                <p v-if="form.errors.subject" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.subject }}</p>
                            </div>

                            <div>
                                <label for="message" class="block font-mono text-xs uppercase text-[#A4ADB7]">
                                    Isi Pesan <span class="text-[#ffb4ab]">*</span>
                                </label>
                                <textarea
                                    id="message"
                                    v-model="form.message"
                                    required
                                    rows="5"
                                    minlength="10"
                                    maxlength="3000"
                                    placeholder="Deskripsikan kebutuhan proyek atau pesan Anda secara ringkas..."
                                    class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 text-xs text-[#F0F2F3] placeholder-[#A4ADB7]/40 focus:border-[#818CF8] focus:outline-none leading-relaxed"
                                />
                                <div class="mt-1.5 flex items-center justify-between text-[11px] text-[#A4ADB7]">
                                    <span v-if="form.errors.message" class="text-[#ffb4ab]">
                                        {{ form.errors.message }}
                                    </span>
                                    <span class="ml-auto font-mono">
                                        {{ form.message.length }}/3000
                                    </span>
                                </div>
                            </div>

                            <div class="pt-2">
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="inline-flex w-full items-center justify-center rounded bg-[#4F46E5] px-5 py-3 font-mono text-xs font-semibold text-white hover:bg-[#4338CA] transition-colors disabled:opacity-50 focus:outline-none"
                                >
                                    <span v-if="form.processing">Mengirim Pesan...</span>
                                    <span v-else>Kirim Pesan Sekarang &rarr;</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
