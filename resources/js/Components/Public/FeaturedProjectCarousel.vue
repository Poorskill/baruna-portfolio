<script setup lang="ts">
import { computed, ref, onMounted, onBeforeUnmount } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Project } from '@/types';

const props = defineProps<{
    projects: Project[];
}>();

const currentIndex = ref(0);
const isPaused = ref(false);
const isHovered = ref(false);
const isFocused = ref(false);
const isTabHidden = ref(false);
const prefersReducedMotion = ref(false);

const touchStartX = ref(0);
const touchStartY = ref(0);

let autoplayTimer: ReturnType<typeof setInterval> | null = null;

const totalProjects = computed(() => props.projects?.length || 0);

const currentProject = computed<Project | null>(() => {
    if (totalProjects.value === 0) return null;
    return props.projects[currentIndex.value] || null;
});

function formatNumber(num: number): string {
    return num < 10 ? `0${num}` : `${num}`;
}

function next() {
    if (totalProjects.value <= 1) return;
    currentIndex.value = (currentIndex.value + 1) % totalProjects.value;
    resetAutoplayTimer();
}

function prev() {
    if (totalProjects.value <= 1) return;
    currentIndex.value = (currentIndex.value - 1 + totalProjects.value) % totalProjects.value;
    resetAutoplayTimer();
}

function goTo(index: number) {
    if (index >= 0 && index < totalProjects.value) {
        currentIndex.value = index;
        resetAutoplayTimer();
    }
}

function togglePause() {
    isPaused.value = !isPaused.value;
    if (isPaused.value) {
        stopAutoplay();
    } else {
        startAutoplay();
    }
}

function startAutoplay() {
    stopAutoplay();
    if (totalProjects.value <= 1 || isPaused.value || prefersReducedMotion.value) return;

    autoplayTimer = setInterval(() => {
        if (!isHovered.value && !isFocused.value && !isTabHidden.value) {
            currentIndex.value = (currentIndex.value + 1) % totalProjects.value;
        }
    }, 7000);
}

function stopAutoplay() {
    if (autoplayTimer) {
        clearInterval(autoplayTimer);
        autoplayTimer = null;
    }
}

function resetAutoplayTimer() {
    if (!isPaused.value && !prefersReducedMotion.value && totalProjects.value > 1) {
        startAutoplay();
    }
}

function handleMouseEnter() {
    isHovered.value = true;
}

function handleMouseLeave() {
    isHovered.value = false;
}

function handleFocusIn() {
    isFocused.value = true;
}

function handleFocusOut(e: FocusEvent) {
    const currentTarget = e.currentTarget as HTMLElement | null;
    if (!currentTarget?.contains(e.relatedTarget as Node)) {
        isFocused.value = false;
    }
}

function handleKeydown(e: KeyboardEvent) {
    if (totalProjects.value <= 1) return;
    if (e.key === 'ArrowLeft') {
        e.preventDefault();
        prev();
    } else if (e.key === 'ArrowRight') {
        e.preventDefault();
        next();
    }
}

function handleTouchStart(e: TouchEvent) {
    if (e.touches.length !== 1) return;
    touchStartX.value = e.touches[0].clientX;
    touchStartY.value = e.touches[0].clientY;
}

function handleTouchEnd(e: TouchEvent) {
    if (e.changedTouches.length !== 1) return;
    const diffX = e.changedTouches[0].clientX - touchStartX.value;
    const diffY = e.changedTouches[0].clientY - touchStartY.value;

    // Detect intentional horizontal swipe (>45px and more horizontal than vertical)
    if (Math.abs(diffX) > 45 && Math.abs(diffX) > Math.abs(diffY)) {
        if (diffX < 0) {
            next();
        } else {
            prev();
        }
    }
}

function handleVisibilityChange() {
    isTabHidden.value = document.hidden;
}

onMounted(() => {
    if (typeof window !== 'undefined') {
        if (window.matchMedia) {
            prefersReducedMotion.value = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        }
        document.addEventListener('visibilitychange', handleVisibilityChange);
    }
    startAutoplay();
});

onBeforeUnmount(() => {
    stopAutoplay();
    if (typeof document !== 'undefined') {
        document.removeEventListener('visibilitychange', handleVisibilityChange);
    }
});
</script>

<template>
    <!-- Empty State -->
    <div
        v-if="totalProjects === 0"
        class="rounded-xl border border-[#293440] bg-[#151B22] p-12 text-center text-sm font-mono text-[#A4ADB7]"
    >
        Belum ada proyek unggulan yang dipublikasikan.
    </div>

    <!-- Single Project View (No Carousel Overhead) -->
    <div
        v-else-if="totalProjects === 1 && currentProject"
        class="overflow-hidden rounded-xl border border-[#293440] bg-[#151B22] transition-colors duration-200 hover:border-[#364453]"
    >
        <div class="grid gap-0 lg:grid-cols-12 items-stretch">
            <!-- Media -->
            <div class="relative aspect-[16/10] lg:aspect-auto lg:col-span-7 overflow-hidden border-b lg:border-b-0 lg:border-r border-[#293440] bg-[#101419]">
                <img
                    v-if="currentProject.cover_image"
                    :src="currentProject.cover_image.startsWith('http') ? currentProject.cover_image : `/storage/${currentProject.cover_image}`"
                    :alt="currentProject.title"
                    loading="lazy"
                    class="h-full w-full object-cover"
                />
                <div v-else class="flex h-full min-h-[280px] w-full items-center justify-center font-mono text-xs text-[#A4ADB7]/60">
                    [ Tangkapan Layar Sistem ]
                </div>
                <div class="absolute top-4 left-4 flex items-center gap-2">
                    <span class="rounded bg-[#101419]/90 border border-[#818CF8]/40 px-2.5 py-1 font-mono text-[10px] uppercase tracking-wider text-[#818CF8]">
                        {{ currentProject.category || 'Featured' }}
                    </span>
                </div>
            </div>

            <!-- Content -->
            <div class="flex flex-col justify-between p-6 sm:p-8 lg:col-span-5 bg-[#151B22]">
                <div class="space-y-4">
                    <span class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">
                        {{ currentProject.category || 'Case Study' }}
                    </span>
                    <h3 class="text-2xl font-bold tracking-tight text-[#F0F2F3] hover:text-[#818CF8] transition-colors">
                        <Link :href="route('projects.show', currentProject.slug)">
                            {{ currentProject.title }}
                        </Link>
                    </h3>
                    <p v-if="currentProject.summary" class="text-sm leading-relaxed text-[#A4ADB7]">
                        {{ currentProject.summary }}
                    </p>
                    <div v-if="currentProject.skills && currentProject.skills.length > 0" class="pt-2 flex flex-wrap gap-1.5">
                        <span
                            v-for="skill in currentProject.skills"
                            :key="skill.id"
                            class="rounded border border-[#293440] bg-[#101419] px-2.5 py-0.5 font-mono text-[11px] text-[#A4ADB7]"
                        >
                            {{ skill.name }}
                        </span>
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-between border-t border-[#293440] pt-4">
                    <Link
                        :href="route('projects.show', currentProject.slug)"
                        class="inline-flex items-center gap-1.5 font-mono text-xs font-semibold text-[#818CF8] hover:underline"
                    >
                        <span>Lihat Studi Kasus</span>
                        <span>&rarr;</span>
                    </Link>
                    <div class="flex items-center gap-3 font-mono text-xs text-[#A4ADB7]">
                        <a
                            v-if="currentProject.demo_url"
                            :href="currentProject.demo_url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="hover:text-[#818CF8] transition-colors"
                        >
                            Demo &nearr;
                        </a>
                        <a
                            v-if="currentProject.source_url"
                            :href="currentProject.source_url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="hover:text-[#818CF8] transition-colors"
                        >
                            Source &nearr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Full Interactive Carousel (2+ Projects) -->
    <div
        v-else-if="currentProject"
        class="space-y-5"
        role="region"
        aria-roledescription="carousel"
        aria-label="Karya Terpilih Carousel"
        tabindex="0"
        @keydown="handleKeydown"
        @mouseenter="handleMouseEnter"
        @mouseleave="handleMouseLeave"
        @focusin="handleFocusIn"
        @focusout="handleFocusOut"
        @touchstart.passive="handleTouchStart"
        @touchend="handleTouchEnd"
    >
        <!-- Top Carousel Utility & Navigation Rail -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 rounded-lg border border-[#293440] bg-[#151B22] px-4 py-3">
            <!-- Slide Counter & Autoplay Status -->
            <div class="flex items-center gap-3 font-mono text-xs">
                <span class="text-[#818CF8] font-bold text-sm tracking-wider">
                    {{ formatNumber(currentIndex + 1) }}
                </span>
                <span class="text-[#A4ADB7]/40">/</span>
                <span class="text-[#A4ADB7] font-medium">
                    {{ formatNumber(totalProjects) }}
                </span>
                <span class="h-3 w-[1px] bg-[#293440] mx-1" />
                <span class="text-[11px] text-[#A4ADB7]/70 hidden md:inline">
                    {{ isPaused ? 'Mode Manual' : 'Peralihan Otomatis' }}
                </span>
            </div>

            <!-- Slide Indicators & Controls -->
            <div class="flex items-center justify-between sm:justify-end gap-3 w-full sm:w-auto">
                <!-- Dots / Bar Indicators -->
                <div class="flex items-center gap-1.5" role="tablist" aria-label="Indikator slide proyek">
                    <button
                        v-for="(p, idx) in projects"
                        :key="'dot-' + p.id"
                        type="button"
                        @click="goTo(idx)"
                        :class="[
                            'h-1.5 transition-all duration-300 rounded-full focus:outline-none focus:ring-1 focus:ring-[#818CF8]',
                            currentIndex === idx ? 'w-7 bg-[#818CF8]' : 'w-2 bg-[#293440] hover:bg-[#A4ADB7]'
                        ]"
                        :aria-label="`Buka slide ${idx + 1}: ${p.title}`"
                        :aria-selected="currentIndex === idx"
                        role="tab"
                    />
                </div>

                <div class="h-4 w-[1px] bg-[#293440]" />

                <!-- Action Buttons: Pause/Play, Prev, Next -->
                <div class="flex items-center gap-1.5">
                    <!-- Pause/Play Toggle Button -->
                    <button
                        type="button"
                        @click="togglePause"
                        class="inline-flex h-8 w-8 items-center justify-center rounded border border-[#293440] bg-[#101419] text-[#A4ADB7] hover:border-[#818CF8] hover:text-[#F0F2F3] focus:outline-none focus:ring-1 focus:ring-[#818CF8] transition-colors"
                        :aria-label="isPaused ? 'Lanjutkan peralihan otomatis' : 'Jeda peralihan otomatis'"
                        :title="isPaused ? 'Putar otomatis (7s)' : 'Jeda otomatis'"
                    >
                        <svg v-if="!isPaused" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 9v6m4-6v6" />
                        </svg>
                        <svg v-else class="h-3.5 w-3.5 text-[#818CF8]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        </svg>
                    </button>

                    <!-- Previous Button -->
                    <button
                        type="button"
                        @click="prev"
                        class="inline-flex h-8 w-8 items-center justify-center rounded border border-[#293440] bg-[#101419] text-[#A4ADB7] hover:border-[#818CF8] hover:text-[#F0F2F3] focus:outline-none focus:ring-1 focus:ring-[#818CF8] transition-colors"
                        aria-label="Proyek sebelumnya"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    <!-- Next Button -->
                    <button
                        type="button"
                        @click="next"
                        class="inline-flex h-8 w-8 items-center justify-center rounded border border-[#293440] bg-[#101419] text-[#A4ADB7] hover:border-[#818CF8] hover:text-[#F0F2F3] focus:outline-none focus:ring-1 focus:ring-[#818CF8] transition-colors"
                        aria-label="Proyek berikutnya"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Carousel Slide Frame with Transition -->
        <div class="relative overflow-hidden rounded-xl border border-[#293440] bg-[#151B22] shadow-2xl transition-colors duration-200 hover:border-[#364453]">
            <Transition name="carousel-fade" mode="out-in">
                <article
                    :key="currentProject.id"
                    class="grid gap-0 lg:grid-cols-12 items-stretch"
                    :aria-label="`Slide ${currentIndex + 1}: ${currentProject.title}`"
                >
                    <!-- Left: Large Project Media Showcase -->
                    <div class="relative aspect-[16/10] lg:aspect-auto lg:col-span-7 overflow-hidden border-b lg:border-b-0 lg:border-r border-[#293440] bg-[#101419]">
                        <img
                            v-if="currentProject.cover_image"
                            :src="currentProject.cover_image.startsWith('http') ? currentProject.cover_image : `/storage/${currentProject.cover_image}`"
                            :alt="currentProject.title"
                            loading="lazy"
                            class="h-full w-full object-cover transition-transform duration-500 hover:scale-[1.01]"
                        />
                        <div v-else class="flex h-full min-h-[300px] w-full items-center justify-center font-mono text-xs text-[#A4ADB7]/60">
                            [ Tangkapan Layar Sistem ]
                        </div>

                        <!-- Overlay Tags -->
                        <div class="absolute top-4 left-4 flex items-center gap-2">
                            <span class="rounded bg-[#101419]/90 border border-[#818CF8]/40 px-2.5 py-1 font-mono text-[10px] uppercase tracking-wider text-[#818CF8] shadow-sm">
                                {{ currentProject.category || 'Featured' }}
                            </span>
                            <span class="rounded bg-[#101419]/90 border border-[#293440] px-2 py-1 font-mono text-[10px] text-[#A4ADB7] shadow-sm">
                                #{{ formatNumber(currentIndex + 1) }}
                            </span>
                        </div>
                    </div>

                    <!-- Right: Curated Editorial Details -->
                    <div class="flex flex-col justify-between p-6 sm:p-8 lg:p-10 lg:col-span-5 bg-[#151B22]">
                        <div class="space-y-4">
                            <!-- Category & Subtitle -->
                            <div class="flex items-center justify-between text-xs font-mono">
                                <span class="uppercase tracking-wider text-[#818CF8] font-medium">
                                    {{ currentProject.category || 'Software Engineering' }}
                                </span>
                                <span v-if="currentProject.role" class="text-[#A4ADB7] text-[11px]">
                                    {{ currentProject.role }}
                                </span>
                            </div>

                            <!-- Main Title -->
                            <h3 class="text-2xl sm:text-3xl font-bold tracking-tight text-[#F0F2F3] hover:text-[#818CF8] transition-colors">
                                <Link :href="route('projects.show', currentProject.slug)" class="focus:outline-none">
                                    {{ currentProject.title }}
                                </Link>
                            </h3>

                            <!-- Narrative Summary -->
                            <p v-if="currentProject.summary" class="text-sm leading-relaxed text-[#A4ADB7] line-clamp-4">
                                {{ currentProject.summary }}
                            </p>

                            <!-- Technology Tags -->
                            <div v-if="currentProject.skills && currentProject.skills.length > 0" class="pt-2 flex flex-wrap gap-1.5">
                                <span
                                    v-for="skill in currentProject.skills"
                                    :key="skill.id"
                                    class="rounded border border-[#293440] bg-[#101419] px-2.5 py-0.5 font-mono text-[11px] text-[#A4ADB7]"
                                >
                                    {{ skill.name }}
                                </span>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="mt-8 flex flex-wrap items-center justify-between gap-4 border-t border-[#293440] pt-5">
                            <Link
                                :href="route('projects.show', currentProject.slug)"
                                class="inline-flex items-center gap-2 font-mono text-xs font-semibold text-[#818CF8] hover:text-[#A5B4FC] transition-colors"
                            >
                                <span>Lihat Studi Kasus</span>
                                <span>&rarr;</span>
                            </Link>

                            <div class="flex items-center gap-4 font-mono text-xs text-[#A4ADB7]">
                                <a
                                    v-if="currentProject.demo_url"
                                    :href="currentProject.demo_url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="hover:text-[#818CF8] transition-colors"
                                >
                                    Demo &nearr;
                                </a>
                                <a
                                    v-if="currentProject.source_url"
                                    :href="currentProject.source_url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="hover:text-[#818CF8] transition-colors"
                                >
                                    Source &nearr;
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
            </Transition>
        </div>
    </div>
</template>

<style scoped>
.carousel-fade-enter-active,
.carousel-fade-leave-active {
    transition: opacity 0.28s ease, transform 0.28s ease;
}

.carousel-fade-enter-from {
    opacity: 0;
    transform: translateY(4px);
}

.carousel-fade-leave-to {
    opacity: 0;
    transform: translateY(-4px);
}

@media (prefers-reduced-motion: reduce) {
    .carousel-fade-enter-active,
    .carousel-fade-leave-active {
        transition: none !important;
    }
}
</style>
