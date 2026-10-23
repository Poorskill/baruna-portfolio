<script setup lang="ts">
import { computed, onMounted, onBeforeUnmount, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import gsap from 'gsap';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Education, Experience, Certificate, NavigationLink, SocialLink } from '@/types';

const props = defineProps<{
    settings: Record<string, string>;
    education: Education[];
    experiences: Experience[];
    certificates: Certificate[];
    navLinks: NavigationLink[];
    socialLinks: SocialLink[];
    hasCv?: boolean;
}>();

const pageRef = ref<HTMLElement | null>(null);
let gsapContext: gsap.Context | null = null;

const siteName = props.settings.site_name || 'Baruna Dwi Cahya';
const siteDescriptor = props.settings.site_descriptor || 'Web Development & Software Systems';
const aboutShort = props.settings.about_short || 'Pengembang perangkat lunak dengan ketertarikan tinggi pada arsitektur sistem, keandalan kode, dan ekosistem web modern.';
const aboutLong = props.settings.about_long || '';
const hasCv = computed(() => {
    if (typeof props.hasCv === 'boolean') {
        return props.hasCv;
    }
    return Boolean(props.settings.cv_path);
});

function formatDate(dateStr?: string | null): string {
    if (!dateStr) return '';
    try {
        const d = new Date(dateStr);
        return d.toLocaleDateString('id-ID', { year: 'numeric', month: 'short' });
    } catch {
        return dateStr;
    }
}

onMounted(() => {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion || !pageRef.value) return;

    gsapContext = gsap.context(() => {
        // Experience timeline nodes
        gsap.from('.reveal-timeline-item', {
            scrollTrigger: {
                trigger: '.reveal-timeline-container',
                start: 'top 80%',
                toggleActions: 'play none none none',
                once: true,
            },
            opacity: 0,
            y: 20,
            duration: 0.6,
            stagger: 0.1,
            ease: 'power2.out',
        });

        // Education rows
        gsap.from('.reveal-edu-row', {
            scrollTrigger: {
                trigger: '.reveal-edu-container',
                start: 'top 80%',
                toggleActions: 'play none none none',
                once: true,
            },
            opacity: 0,
            y: 18,
            duration: 0.55,
            stagger: 0.08,
            ease: 'power2.out',
        });

        // Certificate cards
        gsap.from('.reveal-cert-card', {
            scrollTrigger: {
                trigger: '.reveal-certs-grid',
                start: 'top 80%',
                toggleActions: 'play none none none',
                once: true,
            },
            opacity: 0,
            y: 20,
            duration: 0.55,
            stagger: 0.08,
            ease: 'power2.out',
        });
    }, pageRef.value);
});

onBeforeUnmount(() => {
    gsapContext?.revert();
});
</script>

<template>
    <Head>
        <title>About — {{ siteName }}</title>
        <meta name="description" :content="aboutShort" />
    </Head>

    <PublicLayout
        :nav-links="navLinks"
        :social-links="socialLinks"
        :site-name="siteName"
        :site-descriptor="siteDescriptor"
        :footer-text="settings.footer_text"
    >
        <div ref="pageRef">
        <!-- Hero Introduction: Asymmetric Split -->
        <section class="border-b border-[#293440] bg-[#101419] py-20 sm:py-28">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-12 lg:grid-cols-12 lg:items-start">
                    <div class="space-y-6 lg:col-span-8">
                        <div class="inline-flex items-center gap-2 rounded border border-[#293440] bg-[#151B22] px-3 py-1 font-mono text-xs text-[#818CF8]">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#22C55E]" />
                            <span>PROFIL PENGEMBANG</span>
                        </div>

                        <h1 class="text-3xl font-semibold tracking-[-0.03em] text-[#F0F2F3] sm:text-5xl">
                            Tentang {{ siteName }}
                        </h1>

                        <p class="text-base sm:text-lg leading-relaxed text-[#A4ADB7] max-w-2xl">
                            {{ aboutShort }}
                        </p>

                        <div v-if="hasCv" class="pt-2">
                            <a
                                :href="route('cv.download')"
                                class="inline-flex items-center gap-2 rounded bg-[#4F46E5] px-5 py-2.5 font-mono text-xs font-semibold text-white hover:bg-[#4338CA] transition-colors focus:outline-none"
                            >
                                <span>Unduh Curriculum Vitae (PDF)</span>
                                <span>&darr;</span>
                            </a>
                        </div>
                    </div>

                    <div class="lg:col-span-4">
                        <div class="rounded border border-[#293440] bg-[#151B22] p-6 space-y-4 font-mono text-xs text-[#A4ADB7]">
                            <h3 class="text-xs uppercase tracking-wider text-[#818CF8] pb-2 border-b border-[#293440]">
                                Ringkasan Profil
                            </h3>
                            <div class="space-y-2.5">
                                <div>
                                    <span class="text-[10px] text-[#A4ADB7]/60 block">ROLE UTAMA</span>
                                    <span class="text-[#F0F2F3]">{{ siteDescriptor }}</span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-[#A4ADB7]/60 block">FOKUS TEKNIS</span>
                                    <span class="text-[#F0F2F3]">Backend APIs, Database Design, Full-Stack Architecture</span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-[#A4ADB7]/60 block">STATUS</span>
                                    <span class="text-[#22C55E]">Available for opportunities</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Detailed Background Prose (Alternating Surface) -->
        <section v-if="aboutLong" class="border-b border-[#293440] bg-[#151B22] py-16 sm:py-24">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-8 lg:grid-cols-12">
                    <div class="lg:col-span-4">
                        <span class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">Latar Belakang</span>
                        <h2 class="mt-2 text-2xl font-semibold text-[#F0F2F3]">
                            Filosofi & Pendekatan Kerja
                        </h2>
                    </div>
                    <div class="lg:col-span-8 text-sm leading-relaxed text-[#A4ADB7] space-y-4 whitespace-pre-line">
                        {{ aboutLong }}
                    </div>
                </div>
            </div>
        </section>

        <!-- Experience Timeline: Refined Vertical Line & Markers (No Heavy Cards!) -->
        <section class="border-b border-[#293440] bg-[#101419] py-20 sm:py-28">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <div class="max-w-xl mb-12">
                    <span class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">Riwayat Karir</span>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-[#F0F2F3] sm:text-3xl">
                        Pengalaman Kerja & Kontribusi
                    </h2>
                </div>

                <div v-if="experiences && experiences.length > 0" class="reveal-timeline-container relative pl-6 sm:pl-8 border-l border-[#293440] space-y-12">
                    <div
                        v-for="item in experiences"
                        :key="item.id"
                        class="reveal-timeline-item relative space-y-3"
                    >
                        <!-- Node Marker -->
                        <div class="absolute -left-[31px] sm:-left-[39px] top-1 h-3.5 w-3.5 rounded-full border-2 border-[#101419] bg-[#818CF8]" />

                        <!-- Header Line -->
                        <div class="flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-1">
                            <div>
                                <h3 class="text-lg font-semibold text-[#F0F2F3]">
                                    {{ item.role }}
                                    <span class="text-[#818CF8] font-normal">@ {{ item.organization }}</span>
                                </h3>
                                <div class="flex items-center gap-3 font-mono text-xs text-[#A4ADB7] mt-0.5">
                                    <span v-if="item.type">{{ item.type }}</span>
                                    <span v-if="item.location_mode">&bull; {{ item.location_mode }}</span>
                                </div>
                            </div>

                            <span class="font-mono text-xs text-[#A4ADB7]">
                                {{ formatDate(item.start_date) }} — {{ item.is_current ? 'Sekarang' : formatDate(item.end_date) }}
                            </span>
                        </div>

                        <!-- Summary -->
                        <p v-if="item.summary" class="text-sm leading-relaxed text-[#A4ADB7] max-w-3xl">
                            {{ item.summary }}
                        </p>

                        <!-- Responsibilities Bullets -->
                        <ul v-if="item.responsibilities && item.responsibilities.length > 0" class="space-y-1.5 pt-1 text-xs text-[#A4ADB7] max-w-3xl">
                            <li v-for="(resp, i) in item.responsibilities" :key="i" class="flex items-start gap-2.5">
                                <span class="font-mono text-[#818CF8] select-none">&bull;</span>
                                <span>{{ resp }}</span>
                            </li>
                        </ul>

                        <!-- Measured Outcomes -->
                        <ul v-if="item.outcomes && item.outcomes.length > 0" class="space-y-1.5 pt-2 text-xs font-mono text-[#22C55E] max-w-3xl">
                            <li v-for="(out, i) in item.outcomes" :key="i" class="flex items-start gap-2.5">
                                <span class="select-none">&check;</span>
                                <span>{{ out }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div v-else class="rounded border border-[#293440] bg-[#151B22] p-8 text-center text-xs font-mono text-[#A4ADB7]">
                    Belum ada riwayat pengalaman kerja yang dimasukkan.
                </div>
            </div>
        </section>

        <!-- Education & Academic Background -->
        <section class="border-b border-[#293440] bg-[#151B22] py-20 sm:py-28">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <div class="max-w-xl mb-12">
                    <span class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">Akademik</span>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-[#F0F2F3] sm:text-3xl">
                        Pendidikan Formal
                    </h2>
                </div>

                <div v-if="education && education.length > 0" class="reveal-edu-container divide-y divide-[#293440] border-y border-[#293440]">
                    <div
                        v-for="edu in education"
                        :key="edu.id"
                        class="reveal-edu-row py-8 grid gap-4 md:grid-cols-12 md:items-start"
                    >
                        <div class="md:col-span-4 space-y-1">
                            <span class="font-mono text-xs text-[#818CF8]">{{ edu.institution }}</span>
                            <div class="font-mono text-[11px] text-[#A4ADB7]">
                                {{ formatDate(edu.start_date) }} — {{ edu.end_date ? formatDate(edu.end_date) : (edu.expected_end_date ? `Estimasi ${formatDate(edu.expected_end_date)}` : 'Sekarang') }}
                            </div>
                        </div>

                        <div class="md:col-span-8 space-y-2">
                            <h3 class="text-base font-semibold text-[#F0F2F3]">
                                {{ edu.qualification }}
                                <span v-if="edu.field_of_study" class="text-[#A4ADB7] font-normal">· {{ edu.field_of_study }}</span>
                            </h3>
                            <p v-if="edu.description" class="text-xs text-[#A4ADB7] leading-relaxed max-w-2xl">
                                {{ edu.description }}
                            </p>
                        </div>
                    </div>
                </div>

                <div v-else class="rounded border border-[#293440] bg-[#101419] p-8 text-center text-xs font-mono text-[#A4ADB7]">
                    Belum ada data pendidikan yang dimasukkan.
                </div>
            </div>
        </section>

        <!-- Verified Certificates Section -->
        <section class="py-20 sm:py-28 bg-[#101419]">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <div class="max-w-xl mb-12">
                    <span class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">Kredensial</span>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-[#F0F2F3] sm:text-3xl">
                        Sertifikasi & Lisensi Resmi
                    </h2>
                </div>

                <div v-if="certificates && certificates.length > 0" class="reveal-certs-grid grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <article
                        v-for="cert in certificates"
                        :key="cert.id"
                        class="reveal-cert-card rounded border border-[#293440] bg-[#151B22] p-6 flex flex-col justify-between hover:border-[#364453] transition-colors"
                    >
                        <div class="space-y-2">
                            <span class="font-mono text-[10px] uppercase text-[#818CF8] tracking-wider">
                                {{ cert.issuer }}
                            </span>
                            <h3 class="text-base font-semibold text-[#F0F2F3]">
                                {{ cert.name }}
                            </h3>
                            <p v-if="cert.issued_at" class="font-mono text-xs text-[#A4ADB7]">
                                Diterbitkan: {{ formatDate(cert.issued_at) }}
                            </p>
                            <p v-if="cert.credential_id" class="font-mono text-[11px] text-[#A4ADB7]/70 truncate">
                                ID: {{ cert.credential_id }}
                            </p>
                        </div>

                        <div v-if="cert.credential_url" class="mt-6 pt-4 border-t border-[#293440]">
                            <a
                                :href="cert.credential_url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-1 font-mono text-xs text-[#818CF8] hover:underline"
                            >
                                <span>Verifikasi Kredensial</span>
                                <span>&nearr;</span>
                            </a>
                        </div>
                    </article>
                </div>

                <div v-else class="rounded border border-[#293440] bg-[#151B22] p-8 text-center text-xs font-mono text-[#A4ADB7]">
                    Belum ada sertifikat terverifikasi yang dipublikasikan.
                </div>
            </div>
        </section>
        </div>
    </PublicLayout>
</template>
