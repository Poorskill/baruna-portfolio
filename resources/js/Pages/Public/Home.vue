<script setup lang="ts">
import { computed, onMounted, onBeforeUnmount, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import gsap from 'gsap';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import FeaturedProjectCarousel from '@/Components/Public/FeaturedProjectCarousel.vue';
import HeroPortraitScene from '@/Components/Public/HeroPortraitScene.vue';
import { Project, SkillGroup, NavigationLink, SocialLink } from '@/types';
import { useSmoothScroll } from '@/composables/useSmoothScroll';

const props = defineProps<{
    settings: Record<string, string>;
    featuredProjects: Project[];
    skillGroups: SkillGroup[];
    navLinks: NavigationLink[];
    socialLinks: SocialLink[];
    hasCv?: boolean;
}>();

const pageRef = ref<HTMLElement | null>(null);
const { scrollTo } = useSmoothScroll();
let gsapContext: gsap.Context | null = null;

const siteName = props.settings.site_name || 'Baruna Dwi Cahya';
const siteDescriptor = props.settings.site_descriptor || 'Web Development & Software Systems';
const heroHeadline = props.settings.hero_headline || 'Pengembangan web dan solusi sistem digital.';
const heroDescription = props.settings.hero_description || 'Saya Baruna Dwi Cahya, dengan latar belakang Teknik Informatika dan fokus pada pengembangan aplikasi web serta sistem digital. Portofolio ini menampilkan proyek, implementasi teknis, dan teknologi yang saya gunakan.';
const primaryCtaLabel = computed(() => {
    if (props.settings.hero_cta_primary_label && props.settings.hero_cta_primary_label !== 'Lihat Proyek') {
        return props.settings.hero_cta_primary_label;
    }
    return 'Kenali Saya';
});
const primaryCtaUrl = computed(() => {
    if (props.settings.hero_cta_primary_url && props.settings.hero_cta_primary_url !== '/projects') {
        return props.settings.hero_cta_primary_url;
    }
    return route('about');
});
const secondaryCtaLabel = props.settings.hero_cta_secondary_label || 'Download CV';

const profilePhoto = computed(() => {
    return props.settings.photo_path || props.settings.avatar_path || null;
});

const hasCv = computed(() => {
    if (typeof props.hasCv === 'boolean') {
        return props.hasCv;
    }
    return Boolean(props.settings.cv_path);
});

const contactEmail = computed(() => props.settings.contact_email || '');
const contactWhatsapp = computed(() => props.settings.contact_whatsapp || '');
const availabilityStatus = computed(() => props.settings.availability_status || '');

interface ContactChannel {
    name: string;
    platform: string;
    url: string;
    display: string;
    isExternal: boolean;
}

const contactChannels = computed<ContactChannel[]>(() => {
    const channels: ContactChannel[] = [];

    if (contactEmail.value) {
        channels.push({
            name: 'Email',
            platform: 'email',
            url: `mailto:${contactEmail.value}`,
            display: contactEmail.value,
            isExternal: false,
        });
    }

    if (contactWhatsapp.value) {
        const cleanNumber = contactWhatsapp.value.replace(/[^0-9]/g, '');
        channels.push({
            name: 'WhatsApp',
            platform: 'whatsapp',
            url: contactWhatsapp.value.startsWith('http')
                ? contactWhatsapp.value
                : `https://wa.me/${cleanNumber}`,
            display: contactWhatsapp.value,
            isExternal: true,
        });
    }

    if (props.socialLinks) {
        for (const link of props.socialLinks) {
            const platform = (link.platform || 'link').toLowerCase();
            if (platform === 'email' && contactEmail.value && link.url.includes(contactEmail.value)) {
                continue;
            }
            if (platform === 'whatsapp' && contactWhatsapp.value) {
                continue;
            }
            const cleanDisplay = link.url.replace(/^https?:\/\/(www\.)?/, '').replace(/\/$/, '');
            channels.push({
                name: link.label || link.platform,
                platform: platform,
                url: link.url,
                display: cleanDisplay || link.label || link.platform,
                isExternal: !link.url.startsWith('mailto:'),
            });
        }
    }

    return channels;
});

onMounted(() => {
    if (window.location.hash === '#contact') {
        const el = document.getElementById('contact');
        if (el) {
            setTimeout(() => {
                scrollTo(el, { offset: -70 });
            }, 120);
        }
    }

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion || !pageRef.value) return;

    gsapContext = gsap.context(() => {
        // Hero entrance (gentle fade, immediate)
        gsap.from('.hero-fade', {
            opacity: 0,
            y: 16,
            duration: 0.65,
            stagger: 0.08,
            ease: 'power2.out',
        });

        // Featured projects header
        gsap.from('.reveal-projects-header', {
            scrollTrigger: {
                trigger: '.reveal-projects-header',
                start: 'top 85%',
                toggleActions: 'play none none none',
                once: true,
            },
            opacity: 0,
            y: 18,
            duration: 0.6,
            ease: 'power2.out',
        });

        // Featured projects carousel
        gsap.from('.reveal-projects-carousel', {
            scrollTrigger: {
                trigger: '.reveal-projects-carousel',
                start: 'top 80%',
                toggleActions: 'play none none none',
                once: true,
            },
            opacity: 0,
            y: 20,
            duration: 0.65,
            ease: 'power2.out',
        });

        // Skills section
        gsap.from('.reveal-skills-header', {
            scrollTrigger: {
                trigger: '.reveal-skills-section',
                start: 'top 85%',
                toggleActions: 'play none none none',
                once: true,
            },
            opacity: 0,
            y: 18,
            duration: 0.55,
            ease: 'power2.out',
        });

        gsap.from('.reveal-skill-row', {
            scrollTrigger: {
                trigger: '.reveal-skills-list',
                start: 'top 80%',
                toggleActions: 'play none none none',
                once: true,
            },
            opacity: 0,
            y: 16,
            duration: 0.5,
            stagger: 0.08,
            ease: 'power2.out',
        });

        // Contact section
        gsap.from('.reveal-contact-left', {
            scrollTrigger: {
                trigger: '#contact',
                start: 'top 80%',
                toggleActions: 'play none none none',
                once: true,
            },
            opacity: 0,
            y: 20,
            duration: 0.6,
            ease: 'power2.out',
        });

        gsap.from('.reveal-contact-channel', {
            scrollTrigger: {
                trigger: '#contact',
                start: 'top 75%',
                toggleActions: 'play none none none',
                once: true,
            },
            opacity: 0,
            y: 16,
            duration: 0.5,
            stagger: 0.06,
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
        <title>{{ siteName }} — {{ siteDescriptor }}</title>
        <meta name="description" :content="heroDescription" />
    </Head>

    <PublicLayout
        :nav-links="navLinks"
        :social-links="socialLinks"
        :site-name="siteName"
        :site-descriptor="siteDescriptor"
        :footer-text="settings.footer_text"
    >
        <div ref="pageRef">
        <!-- Hero Section: Obsidian Precision Dark (#101419) with 3D Violet Backdrop & Natural Portrait -->
        <section class="relative bg-[#101419] text-[#F0F2F3] py-16 sm:py-24 lg:py-28 overflow-hidden border-b border-[#293440]">
            <!-- Subtle localized radial gradient in the portrait field -->
            <div class="pointer-events-none absolute right-0 top-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-[radial-gradient(circle,rgba(129,140,248,0.06)_0%,transparent_65%)]" aria-hidden="true" />

            <div class="relative mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-12 lg:grid-cols-12 lg:items-center">
                    <!-- Left: Dominant Editorial Column -->
                    <div class="space-y-6 lg:col-span-7">
                        <!-- Eyebrow -->
                        <div class="hero-fade inline-flex items-center gap-2 font-mono text-xs font-semibold uppercase tracking-widest text-[#818CF8]">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#818CF8]" />
                            <span>PORTOFOLIO PRIBADI</span>
                        </div>

                        <!-- Sapaan + Heading Utama Paling Dominan -->
                        <div class="hero-fade space-y-1.5">
                            <p class="font-mono text-xs sm:text-sm tracking-wider uppercase text-[#A4ADB7]">
                                Halo, saya
                            </p>
                            <h1 class="text-4xl sm:text-6xl lg:text-7xl font-bold tracking-tight text-[#F0F2F3] leading-[1.08]">
                                Baruna Dwi Cahya
                            </h1>
                        </div>

                        <!-- Descriptor -->
                        <p class="hero-fade font-mono text-sm sm:text-base font-semibold text-[#818CF8]">
                            Web Development · Software Systems
                        </p>

                        <!-- Deskripsi Terukur -->
                        <p class="hero-fade text-base sm:text-lg leading-relaxed text-[#A4ADB7] max-w-xl">
                            Portofolio ini menampilkan proyek aplikasi web dan sistem yang saya kembangkan, teknologi yang saya gunakan, serta pengalaman yang relevan di bidang teknologi informasi.
                        </p>

                        <!-- CTA Actions: Kenali Saya & Download CV Berdampingan -->
                        <div class="hero-fade flex flex-wrap items-center gap-4 pt-2">
                            <Link
                                :href="primaryCtaUrl"
                                class="inline-flex items-center justify-center rounded bg-[#4F46E5] px-7 py-3.5 font-mono text-xs font-semibold text-white shadow-sm hover:bg-[#4338CA] transition-colors focus:outline-none focus:ring-2 focus:ring-[#818CF8] focus:ring-offset-2 focus:ring-offset-[#101419]"
                            >
                                <span>{{ primaryCtaLabel }}</span>
                                <span class="ml-2">&rarr;</span>
                            </Link>

                            <a
                                :href="route('cv.download')"
                                class="inline-flex items-center gap-2 rounded border border-[#293440] bg-[#151B22] px-6 py-3.5 font-mono text-xs font-medium text-[#F0F2F3] hover:border-[#818CF8] hover:text-[#818CF8] transition-colors focus:outline-none"
                            >
                                <span>Download CV</span>
                                <span class="text-xs">&darr;</span>
                            </a>
                        </div>
                    </div>

                    <!-- Right: Large 3D Violet Circle Backdrop + Portrait Cutout -->
                    <div class="relative lg:col-span-5 flex items-center justify-center py-6 sm:py-8 lg:py-0">
                        <!-- Stage Container: Responsive Square Frame -->
                        <div class="relative w-[280px] sm:w-[340px] md:w-[370px] lg:w-[390px] aspect-square flex items-center justify-center">
                            <!-- Three.js 3D Violet Disc Backdrop (Centered in stage) -->
                            <HeroPortraitScene />

                            <!-- Foreground Portrait Cutout: Head naturally breaks above top rim of circle -->
                            <div class="absolute inset-x-0 bottom-0 top-[-20%] z-10 flex items-end justify-center pointer-events-none select-none">
                                <img
                                    v-if="profilePhoto"
                                    :src="profilePhoto.startsWith('http') ? profilePhoto : `/storage/${profilePhoto}`"
                                    alt="Baruna Dwi Cahya"
                                    class="w-full h-full object-contain object-bottom drop-shadow-[0_12px_24px_rgba(0,0,0,0.4)]"
                                />
                                <div v-else class="relative flex flex-col items-center justify-end h-[85%] w-auto max-w-xs pb-2">
                                    <svg viewBox="0 0 280 340" fill="none" class="w-56 sm:w-64 h-auto drop-shadow-[0_12px_24px_rgba(0,0,0,0.3)]">
                                        <!-- Head & Torso Architectural Silhouette -->
                                        <circle cx="140" cy="100" r="54" fill="#0B0F14" opacity="0.95" />
                                        <path d="M40 330 C40 235, 80 195, 140 195 C200 195, 240 235, 240 330 Z" fill="#0B0F14" opacity="0.95" />
                                        <!-- Monogram Token on Silhouette -->
                                        <rect x="108" y="240" width="64" height="28" rx="6" fill="#151B22" stroke="#818CF8" stroke-width="1.5" />
                                        <text x="140" y="259" text-anchor="middle" fill="#F0F2F3" font-family="JetBrains Mono, monospace" font-size="12" font-weight="600" letter-spacing="2">BDC</text>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Projects Section: Carousel Presentation -->
        <section class="border-b border-[#293440] bg-[#101419] py-20 sm:py-28">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 space-y-10">
                <div class="reveal-projects-header flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 border-b border-[#293440] pb-6">
                    <div>
                        <span class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">Karya Terpilih</span>
                        <h2 class="mt-2 text-2xl font-semibold tracking-tight text-[#F0F2F3] sm:text-3xl">
                            Studi Kasus & Proyek Unggulan
                        </h2>
                    </div>
                    <Link
                        href="/projects"
                        class="inline-flex items-center gap-1 font-mono text-xs text-[#A4ADB7] hover:text-[#818CF8] transition-colors"
                    >
                        <span>Lihat Semua Proyek</span>
                        <span>&rarr;</span>
                    </Link>
                </div>

                <div class="reveal-projects-carousel">
                    <FeaturedProjectCarousel :projects="featuredProjects" />
                </div>
            </div>
        </section>

        <!-- Skills Section: Modern Grid (3x2 Balanced) -->
        <section class="reveal-skills-section border-b border-[#293440] bg-[#101419] py-20 sm:py-28">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <div class="reveal-skills-header max-w-xl mb-12">
                    <span class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">Tech Stack</span>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-[#F0F2F3] sm:text-3xl">
                        Keahlian & Alat Kerja
                    </h2>
                    <p class="mt-2.5 text-sm leading-relaxed text-[#A4ADB7]">
                        Teknologi yang saya gunakan untuk merancang dan membangun sistem web, dari backend, frontend, hingga infrastruktur.
                    </p>
                </div>

                <div v-if="skillGroups && skillGroups.length > 0" class="reveal-skills-list grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div
                        v-for="group in skillGroups"
                        :key="group.id"
                        class="reveal-skill-row rounded-lg border border-[#293440] bg-[#151B22] p-5 sm:p-6 transition-colors hover:border-[#364453] flex flex-col justify-between"
                    >
                        <div>
                            <div class="flex items-center justify-between pb-3 mb-3.5 border-b border-[#293440]/60">
                                <h3 class="font-mono text-xs font-semibold uppercase tracking-wider text-[#F0F2F3]">
                                    {{ group.name }}
                                </h3>
                                <span class="h-1.5 w-1.5 rounded-full bg-[#818CF8]" />
                            </div>
                            <div class="flex flex-wrap gap-1.5">
                                <span
                                    v-for="skill in (group.visible_skills || group.skills || [])"
                                    :key="skill.id"
                                    class="rounded border border-[#293440] bg-[#101419] px-2.5 py-1 font-mono text-xs text-[#A4ADB7] hover:text-[#F0F2F3] hover:border-[#818CF8] transition-colors"
                                >
                                    {{ skill.name }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="rounded border border-[#293440] bg-[#151B22] p-8 text-center text-xs font-mono text-[#A4ADB7]">
                    Katalog keahlian belum tersedia.
                </div>
            </div>
        </section>

        <!-- Official Contacts Hub Section (No Form) -->
        <section id="contact" class="scroll-mt-24 border-t border-[#293440] bg-[#101419] py-24 sm:py-32">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-12 lg:grid-cols-12 lg:items-start">
                    <!-- Left Column: Editorial Headline & Context -->
                    <div class="space-y-6 lg:col-span-5">
                        <div class="inline-flex items-center gap-2 font-mono text-[11px] uppercase tracking-wider text-[#818CF8]">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#818CF8]" />
                            <span>LET'S CONNECT</span>
                        </div>

                        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-semibold tracking-[-0.03em] text-[#F0F2F3] leading-[1.14]">
                            Ada proyek atau peluang<br class="hidden sm:inline" />
                            yang ingin dibahas?
                        </h2>

                        <p class="text-sm sm:text-base leading-relaxed text-[#A4ADB7] max-w-md">
                            Punya ide website yang ingin diwujudkan, ingin mengembangkan sistem yang sudah ada, atau ingin membicarakan peluang kerja? Saya terbuka untuk terhubung dan bertukar ide.
                        </p>

                        <div v-if="availabilityStatus" class="inline-flex items-center gap-2.5 rounded border border-[#293440] bg-[#151B22] px-3.5 py-1.5 font-mono text-xs text-[#A4ADB7]">
                            <span class="h-2 w-2 rounded-full bg-[#22C55E]" />
                            <span class="text-[#F0F2F3] uppercase text-[11px]">{{ availabilityStatus }}</span>
                        </div>
                    </div>

                    <!-- Right Column: Official Contact Channels Grid -->
                    <div class="space-y-4 lg:col-span-7">
                        <div class="flex items-center justify-between border-b border-[#293440] pb-3">
                            <span class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">
                                Kontak Resmi & Media Sosial
                            </span>
                            <span class="font-mono text-[10px] text-[#A4ADB7]">
                                VERIFIED CHANNELS
                            </span>
                        </div>

                        <div v-if="contactChannels.length > 0" class="grid gap-3 sm:grid-cols-2">
                            <div
                                v-for="channel in contactChannels"
                                :key="channel.name"
                                class="reveal-contact-channel"
                            >
                                <a
                                    :href="channel.url"
                                    :target="channel.isExternal ? '_blank' : undefined"
                                    :rel="channel.isExternal ? 'noopener noreferrer' : undefined"
                                    :title="channel.name"
                                    :aria-label="channel.name"
                                    class="group flex items-center justify-between rounded border border-[#293440] bg-[#151B22] p-4 hover:border-[#818CF8] hover:bg-[#1B242E] transition-colors duration-150 focus:outline-none focus:ring-1 focus:ring-[#818CF8]"
                                >
                                    <div class="flex items-center gap-3.5 min-w-0">
                                        <!-- Circular Icon Button (44x44px target) -->
                                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-[#293440] bg-[#101419] text-[#A4ADB7] group-hover:border-[#818CF8] group-hover:text-[#818CF8] transition-colors duration-150">
                                            <svg v-if="channel.platform === 'email'" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                                <rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                                            </svg>
                                            <svg v-else-if="channel.platform === 'github'" class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                                                <path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0 0 24 12c0-6.63-5.37-12-12-12z"/>
                                            </svg>
                                            <svg v-else-if="channel.platform === 'linkedin'" class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                                                <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                            </svg>
                                            <svg v-else-if="channel.platform === 'whatsapp'" class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                            </svg>
                                            <svg v-else-if="channel.platform === 'instagram'" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                                <rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/>
                                            </svg>
                                            <svg v-else class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/>
                                            </svg>
                                        </span>

                                        <!-- Channel Details -->
                                        <div class="min-w-0">
                                            <span class="font-mono text-xs font-semibold text-[#F0F2F3] group-hover:text-[#818CF8] transition-colors block">
                                                {{ channel.name }}
                                            </span>
                                            <span class="font-mono text-[11px] text-[#A4ADB7] block truncate">
                                                {{ channel.display }}
                                            </span>
                                        </div>
                                    </div>

                                    <span class="font-mono text-xs text-[#A4ADB7] group-hover:text-[#818CF8] transition-colors pl-2 shrink-0">
                                        &nearr;
                                    </span>
                                </a>
                            </div>
                        </div>
                        <p v-else class="text-xs font-mono text-[#A4ADB7] rounded border border-[#293440] bg-[#151B22] p-8 text-center">
                            Belum ada kanal kontak resmi yang dikonfigurasi.
                        </p>
                    </div>
                </div>
            </div>
        </section>
        </div>
    </PublicLayout>
</template>
