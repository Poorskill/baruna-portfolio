<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import gsap from 'gsap';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import ProjectCard from '@/Components/Public/ProjectCard.vue';
import { Project, PaginatedResponse, NavigationLink, SocialLink } from '@/types';

const props = defineProps<{
    projects: PaginatedResponse<Project>;
    categories: string[];
    filters: { search?: string; category?: string };
    navLinks: NavigationLink[];
    socialLinks: SocialLink[];
    siteName: string;
}>();

const pageRef = ref<HTMLElement | null>(null);
let gsapContext: gsap.Context | null = null;

const search = ref(props.filters.search || '');
const activeCategory = ref(props.filters.category || '');

function applyFilters() {
    router.get(
        route('projects.index'),
        {
            search: search.value || undefined,
            category: activeCategory.value || undefined,
        },
        { preserveState: true, replace: true }
    );
}

function selectCategory(cat: string) {
    activeCategory.value = cat === activeCategory.value ? '' : cat;
    applyFilters();
}

function resetFilters() {
    search.value = '';
    activeCategory.value = '';
    applyFilters();
}

const showFeaturedTop = computed(() => {
    return !props.filters.search && !props.filters.category && props.projects.current_page === 1;
});
const firstProject = computed(() => props.projects.data[0] || null);
const restProjects = computed(() => showFeaturedTop.value ? props.projects.data.slice(1) : props.projects.data);

onMounted(() => {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion || !pageRef.value) return;

    gsapContext = gsap.context(() => {
        gsap.from('.reveal-projects-card', {
            scrollTrigger: {
                trigger: '.reveal-projects-container',
                start: 'top 85%',
                toggleActions: 'play none none none',
                once: true,
            },
            opacity: 0,
            y: 20,
            duration: 0.6,
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
        <title>Projects — {{ siteName }}</title>
        <meta name="description" content="Katalog proyek perangkat lunak, sistem web full-stack, dan arsitektur backend yang dirancang oleh Baruna Dwi Cahya." />
    </Head>

    <PublicLayout
        :nav-links="navLinks"
        :social-links="socialLinks"
        :site-name="siteName"
    >
        <div ref="pageRef">
        <!-- Header -->
        <section class="border-b border-[#293440] bg-[#101419] py-20 sm:py-24">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
                    <div class="max-w-2xl space-y-3">
                        <div class="inline-flex items-center gap-2 rounded border border-[#293440] bg-[#151B22] px-3 py-1 font-mono text-xs text-[#818CF8]">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#22C55E]" />
                            <span>ARSIP KARYA & STUDI KASUS</span>
                        </div>

                        <h1 class="text-3xl font-semibold tracking-[-0.03em] text-[#F0F2F3] sm:text-5xl">
                            Daftar Proyek Sistem
                        </h1>

                        <p class="text-sm sm:text-base leading-relaxed text-[#A4ADB7]">
                            Koleksi aplikasi web nyata dan studi kasus rekayasa. Setiap entri mencakup analisis tantangan, arsitektur solusi, dan fitur yang berfungsi.
                        </p>
                    </div>

                    <div class="font-mono text-xs text-[#A4ADB7]">
                        <span class="text-[#F0F2F3] font-semibold">{{ projects.total }}</span> Proyek Terbit
                    </div>
                </div>

                <!-- Filter Controls -->
                <div class="mt-10 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between pt-6 border-t border-[#293440]">
                    <!-- Search Input -->
                    <form @submit.prevent="applyFilters" class="relative max-w-md w-full">
                        <input
                            v-model="search"
                            type="search"
                            placeholder="Cari berdasarkan judul, fitur, atau ringkasan..."
                            class="w-full rounded border border-[#293440] bg-[#151B22] px-3.5 py-2 text-xs text-[#F0F2F3] placeholder-[#A4ADB7]/50 focus:border-[#818CF8] focus:outline-none font-mono"
                        />
                    </form>

                    <!-- Category Pills -->
                    <div v-if="categories && categories.length > 0" class="flex flex-wrap items-center gap-2">
                        <button
                            type="button"
                            @click="selectCategory('')"
                            :class="[
                                'rounded px-3 py-1 font-mono text-xs transition-colors',
                                !activeCategory
                                    ? 'bg-[#4F46E5] text-white font-semibold'
                                    : 'border border-[#293440] bg-[#151B22] text-[#A4ADB7] hover:text-[#F0F2F3]'
                            ]"
                        >
                            Semua
                        </button>
                        <button
                            v-for="cat in categories"
                            :key="cat"
                            type="button"
                            @click="selectCategory(cat)"
                            :class="[
                                'rounded px-3 py-1 font-mono text-xs transition-colors',
                                activeCategory === cat
                                    ? 'bg-[#4F46E5] text-white font-semibold'
                                    : 'border border-[#293440] bg-[#151B22] text-[#A4ADB7] hover:text-[#F0F2F3]'
                            ]"
                        >
                            {{ cat }}
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Projects Grid Section -->
        <section class="py-16 sm:py-24 bg-[#101419]">
            <div class="reveal-projects-container mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 space-y-12">
                <div v-if="projects.data && projects.data.length > 0" class="space-y-10">
                    <!-- Featured Top Item when browsing page 1 with no filter -->
                    <div v-if="showFeaturedTop && firstProject" class="reveal-projects-card">
                        <ProjectCard
                            :project="firstProject"
                            variant="featured"
                            :index="0"
                        />
                    </div>

                    <!-- Grid of remaining projects -->
                    <div v-if="restProjects.length > 0" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="(project, idx) in restProjects"
                            :key="project.id"
                            class="reveal-projects-card"
                        >
                            <ProjectCard
                                :project="project"
                                variant="default"
                                :index="showFeaturedTop ? idx + 1 : idx"
                            />
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="projects.links && projects.links.length > 3" class="flex items-center justify-center gap-1.5 pt-8 border-t border-[#293440]">
                        <template v-for="(link, idx) in projects.links" :key="idx">
                            <span
                                v-if="!link.url"
                                class="rounded border border-[#293440]/50 px-3 py-1 font-mono text-xs text-[#A4ADB7]/40"
                                v-html="link.label"
                            />
                            <Link
                                v-else
                                :href="link.url"
                                :class="[
                                    'rounded px-3 py-1 font-mono text-xs transition-colors',
                                    link.active
                                        ? 'bg-[#4F46E5] text-white font-semibold'
                                        : 'border border-[#293440] bg-[#151B22] text-[#A4ADB7] hover:border-[#364453] hover:text-[#F0F2F3]'
                                ]"
                                v-html="link.label"
                            />
                        </template>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="rounded border border-[#293440] bg-[#151B22] p-16 text-center space-y-3">
                    <p class="text-sm font-mono text-[#A4ADB7]">
                        Tidak ada proyek yang sesuai dengan kriteria filter saat ini.
                    </p>
                    <button
                        v-if="filters.search || filters.category"
                        type="button"
                        @click="resetFilters"
                        class="inline-block font-mono text-xs text-[#818CF8] hover:underline"
                    >
                        Reset Filter &rarr;
                    </button>
                </div>
            </div>
        </section>
        </div>
    </PublicLayout>
</template>
