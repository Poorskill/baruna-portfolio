<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import gsap from 'gsap';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Project, NavigationLink, SocialLink } from '@/types';

const props = defineProps<{
    project: Project;
    settings: { site_name?: string };
    navLinks: NavigationLink[];
    socialLinks: SocialLink[];
}>();

const pageRef = ref<HTMLElement | null>(null);
let gsapContext: gsap.Context | null = null;

const siteName = props.settings.site_name || 'Baruna Dwi Cahya';

onMounted(() => {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion || !pageRef.value) return;

    gsapContext = gsap.context(() => {
        gsap.from('.reveal-casestudy-section', {
            scrollTrigger: {
                trigger: '.reveal-casestudy-body',
                start: 'top 85%',
                toggleActions: 'play none none none',
                once: true,
            },
            opacity: 0,
            y: 18,
            duration: 0.6,
            stagger: 0.1,
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
        <title>{{ project.meta_title || project.title }} — {{ siteName }}</title>
        <meta name="description" :content="project.meta_description || project.summary || ''" />
    </Head>

    <PublicLayout
        :nav-links="navLinks"
        :social-links="socialLinks"
        :site-name="siteName"
    >
        <article ref="pageRef" class="py-16 sm:py-24 bg-[#101419]">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 space-y-12">
                <!-- Navigation Breadcrumb -->
                <nav class="flex items-center justify-between border-b border-[#293440] pb-4 font-mono text-xs">
                    <Link
                        href="/projects"
                        class="inline-flex items-center gap-1.5 text-[#A4ADB7] hover:text-[#818CF8] transition-colors"
                    >
                        <span>&larr;</span>
                        <span>Kembali ke Katalog Proyek</span>
                    </Link>

                    <span class="text-[#A4ADB7]/60 uppercase">
                        STUDI KASUS TEKNIS
                    </span>
                </nav>

                <!-- Case Study Header -->
                <header class="space-y-6 max-w-4xl">
                    <div class="flex flex-wrap items-center gap-3">
                        <span v-if="project.category" class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">
                            {{ project.category }}
                        </span>
                        <span v-if="project.is_featured" class="rounded border border-[#818CF8]/40 bg-[#151B22] px-2 py-0.5 font-mono text-[10px] uppercase text-[#818CF8]">
                            Featured Case Study
                        </span>
                    </div>

                    <h1 class="text-3xl font-semibold tracking-[-0.03em] text-[#F0F2F3] sm:text-5xl sm:leading-[1.12]">
                        {{ project.title }}
                    </h1>

                    <p v-if="project.summary" class="text-base sm:text-lg leading-relaxed text-[#A4ADB7]">
                        {{ project.summary }}
                    </p>
                </header>

                <!-- Big Cover Image Hero -->
                <div v-if="project.cover_image" class="overflow-hidden rounded border border-[#293440] bg-[#151B22]">
                    <img
                        :src="project.cover_image.startsWith('http') ? project.cover_image : `/storage/${project.cover_image}`"
                        :alt="project.title"
                        class="w-full max-h-[540px] object-cover"
                    />
                </div>

                <!-- Case Study Content: Asymmetric Layout with Sticky Metadata Rail -->
                <div class="grid gap-12 lg:grid-cols-12 lg:items-start pt-4">
                    <!-- Left Rail: Metadata & Quick Links (Sticky on Desktop) -->
                    <aside class="space-y-6 lg:col-span-4 lg:sticky lg:top-24">
                        <div class="rounded border border-[#293440] bg-[#151B22] p-6 space-y-6">
                            <h3 class="font-mono text-xs uppercase tracking-wider text-[#818CF8] pb-2 border-b border-[#293440]">
                                Spesifikasi Proyek
                            </h3>

                            <div class="space-y-4 font-mono text-xs">
                                <div v-if="project.role">
                                    <span class="text-[10px] text-[#A4ADB7]/60 uppercase block">Peran & Tanggung Jawab</span>
                                    <span class="text-[#F0F2F3] font-medium">{{ project.role }}</span>
                                </div>

                                <div v-if="project.category">
                                    <span class="text-[10px] text-[#A4ADB7]/60 uppercase block">Domain / Kategori</span>
                                    <span class="text-[#F0F2F3]">{{ project.category }}</span>
                                </div>

                                <div>
                                    <span class="text-[10px] text-[#A4ADB7]/60 uppercase block">Status Rilis</span>
                                    <span class="text-[#22C55E] uppercase">{{ project.status }}</span>
                                </div>
                            </div>

                            <!-- External Links -->
                            <div v-if="project.demo_url || project.source_url || project.documentation_url" class="space-y-2 pt-4 border-t border-[#293440]">
                                <span class="font-mono text-[10px] text-[#A4ADB7]/60 uppercase block">Tautan Langsung</span>
                                <div class="flex flex-col gap-2 font-mono text-xs">
                                    <a
                                        v-if="project.demo_url"
                                        :href="project.demo_url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex items-center justify-between rounded bg-[#4F46E5] px-3 py-1.5 font-semibold text-white hover:bg-[#4338CA] transition-colors"
                                    >
                                        <span>Buka Live Demo</span>
                                        <span>&nearr;</span>
                                    </a>
                                    <a
                                        v-if="project.source_url"
                                        :href="project.source_url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex items-center justify-between rounded border border-[#293440] bg-[#101419] px-3 py-1.5 text-[#F0F2F3] hover:border-[#818CF8] transition-colors"
                                    >
                                        <span>Source Code (GitHub)</span>
                                        <span>&nearr;</span>
                                    </a>
                                    <a
                                        v-if="project.documentation_url"
                                        :href="project.documentation_url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex items-center justify-between rounded border border-[#293440] bg-[#101419] px-3 py-1.5 text-[#F0F2F3] hover:border-[#818CF8] transition-colors"
                                    >
                                        <span>Dokumentasi Teknis</span>
                                        <span>&nearr;</span>
                                    </a>
                                </div>
                            </div>

                            <!-- Tech Stack Badges -->
                            <div v-if="project.skills && project.skills.length > 0" class="pt-4 border-t border-[#293440]">
                                <span class="font-mono text-[10px] text-[#A4ADB7]/60 uppercase block mb-2">Tech Stack Terpakai</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <span
                                        v-for="skill in project.skills"
                                        :key="skill.id"
                                        class="rounded border border-[#293440] bg-[#101419] px-2.5 py-0.5 font-mono text-[11px] text-[#A4ADB7]"
                                    >
                                        {{ skill.name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </aside>

                    <!-- Right Body: Case Study Breakdown -->
                    <main class="reveal-casestudy-body space-y-12 lg:col-span-8 text-sm leading-relaxed text-[#A4ADB7]">
                        <!-- Overview / Body -->
                        <section v-if="project.body" class="reveal-casestudy-section space-y-4">
                            <h2 class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">
                                01 // Arsitektur & Gambaran Sistem
                            </h2>
                            <div class="text-[#F0F2F3] text-base leading-relaxed whitespace-pre-line">
                                {{ project.body }}
                            </div>
                        </section>

                        <!-- Problem & Solution: Dual Column Grid -->
                        <section v-if="project.problem || project.solution" class="reveal-casestudy-section grid gap-6 md:grid-cols-2">
                            <div v-if="project.problem" class="rounded border border-[#293440] bg-[#151B22] p-6 space-y-3">
                                <div class="flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-[#ffb4ab]" />
                                    <h3 class="font-mono text-xs uppercase tracking-wider text-[#F0F2F3]">Problem Statement</h3>
                                </div>
                                <div class="text-xs sm:text-sm leading-relaxed text-[#A4ADB7] whitespace-pre-line">
                                    {{ project.problem }}
                                </div>
                            </div>

                            <div v-if="project.solution" class="rounded border border-[#293440] bg-[#151B22] p-6 space-y-3">
                                <div class="flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-[#22C55E]" />
                                    <h3 class="font-mono text-xs uppercase tracking-wider text-[#F0F2F3]">Solusi & Rekayasa</h3>
                                </div>
                                <div class="text-xs sm:text-sm leading-relaxed text-[#A4ADB7] whitespace-pre-line">
                                    {{ project.solution }}
                                </div>
                            </div>
                        </section>

                        <!-- Features -->
                        <section v-if="project.features" class="reveal-casestudy-section space-y-4 rounded border border-[#293440] bg-[#151B22] p-6 sm:p-8">
                            <h2 class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">
                                02 // Fitur Utama Terimplementasi
                            </h2>
                            <div class="whitespace-pre-line text-[#F0F2F3] text-sm leading-relaxed">
                                {{ project.features }}
                            </div>
                        </section>

                        <!-- Challenges & Architectural Decisions -->
                        <section v-if="project.challenges" class="reveal-casestudy-section space-y-4">
                            <h2 class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">
                                03 // Kendala Teknis & Keputusan Rekayasa
                            </h2>
                            <div class="whitespace-pre-line text-sm leading-relaxed">
                                {{ project.challenges }}
                            </div>
                        </section>

                        <!-- Outcome / Learnings -->
                        <section v-if="project.outcome_or_learning" class="reveal-casestudy-section space-y-4 rounded border border-[#293440] bg-[#101419] p-6">
                            <h2 class="font-mono text-xs uppercase tracking-wider text-[#22C55E]">
                                04 // Dampak & Pembelajaran
                            </h2>
                            <div class="whitespace-pre-line text-xs sm:text-sm leading-relaxed text-[#A4ADB7]">
                                {{ project.outcome_or_learning }}
                            </div>
                        </section>

                        <!-- Screenshot Gallery -->
                        <section v-if="project.images && project.images.length > 0" class="reveal-casestudy-section space-y-6 pt-6 border-t border-[#293440]">
                            <h2 class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">
                                05 // Galeri Antarmuka & Tangkapan Layar
                            </h2>
                            <div class="grid gap-6 sm:grid-cols-2">
                                <figure
                                    v-for="img in project.images"
                                    :key="img.id"
                                    class="overflow-hidden rounded border border-[#293440] bg-[#151B22]"
                                >
                                    <img
                                        :src="img.path.startsWith('http') ? img.path : `/storage/${img.path}`"
                                        :alt="img.alt_text || project.title"
                                        loading="lazy"
                                        class="w-full object-cover"
                                    />
                                    <figcaption v-if="img.caption" class="p-3 font-mono text-[11px] text-[#A4ADB7] border-t border-[#293440]">
                                        {{ img.caption }}
                                    </figcaption>
                                </figure>
                            </div>
                        </section>
                    </main>
                </div>

                <!-- Footer Navigation -->
                <div class="mt-16 border-t border-[#293440] pt-8 font-mono text-xs">
                    <Link
                        href="/projects"
                        class="text-[#818CF8] hover:underline"
                    >
                        &larr; Kembali ke Semua Proyek
                    </Link>
                </div>
            </div>
        </article>
    </PublicLayout>
</template>
