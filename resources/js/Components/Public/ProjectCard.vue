<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Project } from '@/types';

withDefaults(
    defineProps<{
        project: Project;
        variant?: 'featured' | 'default' | 'compact';
        index?: number;
    }>(),
    {
        variant: 'default',
        index: undefined,
    }
);
</script>

<template>
    <!-- FEATURED VARIANT: Large Asymmetric Showcase -->
    <article
        v-if="variant === 'featured'"
        class="group relative overflow-hidden rounded border border-[#293440] bg-[#151B22] transition-colors duration-200 hover:border-[#364453]"
    >
        <div class="grid gap-0 lg:grid-cols-12 items-stretch">
            <!-- Left: Big Media -->
            <div class="relative aspect-video lg:aspect-auto lg:col-span-7 overflow-hidden border-b lg:border-b-0 lg:border-r border-[#293440] bg-[#101419]">
                <img
                    v-if="project.cover_image"
                    :src="project.cover_image.startsWith('http') ? project.cover_image : `/storage/${project.cover_image}`"
                    :alt="project.title"
                    loading="lazy"
                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.015]"
                />
                <div v-else class="flex h-full min-h-[260px] w-full items-center justify-center font-mono text-xs text-[#A4ADB7]/60">
                    [ Tangkapan Layar Sistem ]
                </div>

                <div class="absolute top-3 left-3 flex items-center gap-2">
                    <span class="rounded bg-[#101419]/90 border border-[#818CF8]/40 px-2 py-0.5 font-mono text-[10px] uppercase tracking-wider text-[#818CF8]">
                        Featured Case Study
                    </span>
                    <span v-if="index !== undefined" class="rounded bg-[#101419]/90 border border-[#293440] px-2 py-0.5 font-mono text-[10px] text-[#A4ADB7]">
                        #0{{ index + 1 }}
                    </span>
                </div>
            </div>

            <!-- Right: Editorial Details -->
            <div class="flex flex-col justify-between p-6 sm:p-8 lg:col-span-5 bg-[#151B22]">
                <div class="space-y-4">
                    <div class="flex items-center justify-between text-xs font-mono">
                        <span class="uppercase tracking-wider text-[#818CF8]">
                            {{ project.category || 'Engineering' }}
                        </span>
                        <span v-if="project.role" class="text-[#A4ADB7]">
                            {{ project.role }}
                        </span>
                    </div>

                    <h3 class="text-2xl font-semibold tracking-tight text-[#F0F2F3] group-hover:text-[#818CF8] transition-colors">
                        <Link :href="route('projects.show', project.slug)" class="focus:outline-none">
                            {{ project.title }}
                        </Link>
                    </h3>

                    <p v-if="project.summary" class="text-sm leading-relaxed text-[#A4ADB7]">
                        {{ project.summary }}
                    </p>

                    <div v-if="project.skills && project.skills.length > 0" class="pt-2 flex flex-wrap gap-1.5">
                        <span
                            v-for="skill in project.skills"
                            :key="skill.id"
                            class="rounded border border-[#293440] bg-[#101419] px-2.5 py-0.5 font-mono text-[11px] text-[#A4ADB7]"
                        >
                            {{ skill.name }}
                        </span>
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-between border-t border-[#293440] pt-4">
                    <Link
                        :href="route('projects.show', project.slug)"
                        class="inline-flex items-center gap-1.5 font-mono text-xs font-semibold text-[#818CF8] hover:underline"
                    >
                        <span>Baca Studi Kasus</span>
                        <span>&rarr;</span>
                    </Link>

                    <div class="flex items-center gap-3 font-mono text-xs text-[#A4ADB7]">
                        <a
                            v-if="project.demo_url"
                            :href="project.demo_url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="hover:text-[#818CF8] transition-colors"
                        >
                            Demo &nearr;
                        </a>
                        <a
                            v-if="project.source_url"
                            :href="project.source_url"
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
    </article>

    <!-- COMPACT LIST VARIANT -->
    <article
        v-else-if="variant === 'compact'"
        class="group flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-[#293440] py-4 transition-colors hover:bg-[#151B22]/50 px-2"
    >
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="font-mono text-[10px] uppercase text-[#818CF8]">
                    {{ project.category || 'Project' }}
                </span>
                <span v-if="project.is_featured" class="rounded bg-[#101419] border border-[#818CF8]/30 px-1.5 py-0.2 font-mono text-[9px] text-[#818CF8]">
                    Featured
                </span>
            </div>
            <h4 class="text-base font-semibold text-[#F0F2F3] group-hover:text-[#818CF8] transition-colors">
                <Link :href="route('projects.show', project.slug)">
                    {{ project.title }}
                </Link>
            </h4>
        </div>

        <div class="flex items-center gap-4">
            <div v-if="project.skills && project.skills.length > 0" class="hidden md:flex flex-wrap gap-1">
                <span
                    v-for="skill in project.skills.slice(0, 3)"
                    :key="skill.id"
                    class="rounded border border-[#293440] bg-[#101419] px-2 py-0.5 font-mono text-[10px] text-[#A4ADB7]"
                >
                    {{ skill.name }}
                </span>
            </div>

            <Link
                :href="route('projects.show', project.slug)"
                class="font-mono text-xs text-[#818CF8] hover:underline"
            >
                Detail &rarr;
            </Link>
        </div>
    </article>

    <!-- DEFAULT GRID CARD VARIANT -->
    <article
        v-else
        class="group relative flex flex-col justify-between overflow-hidden rounded border border-[#293440] bg-[#151B22] transition-colors duration-200 hover:border-[#364453]"
    >
        <div class="relative aspect-[16/10] w-full overflow-hidden border-b border-[#293440] bg-[#101419]">
            <img
                v-if="project.cover_image"
                :src="project.cover_image.startsWith('http') ? project.cover_image : `/storage/${project.cover_image}`"
                :alt="project.title"
                loading="lazy"
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.02]"
            />
            <div v-else class="flex h-full w-full items-center justify-center font-mono text-xs text-[#A4ADB7]/50">
                [ Screenshot Belum Tersedia ]
            </div>

            <div v-if="project.is_featured" class="absolute top-2.5 right-2.5">
                <span class="rounded bg-[#101419]/90 border border-[#818CF8]/50 px-2 py-0.5 font-mono text-[10px] uppercase tracking-wider text-[#818CF8]">
                    Featured
                </span>
            </div>
        </div>

        <div class="flex flex-1 flex-col p-6">
            <div v-if="project.category" class="mb-2">
                <span class="font-mono text-[11px] uppercase tracking-wider text-[#818CF8]">
                    {{ project.category }}
                </span>
            </div>

            <h3 class="text-lg font-semibold tracking-tight text-[#F0F2F3] group-hover:text-[#818CF8] transition-colors">
                <Link :href="route('projects.show', project.slug)" class="focus:outline-none">
                    {{ project.title }}
                </Link>
            </h3>

            <p v-if="project.summary" class="mt-2.5 text-xs leading-relaxed text-[#A4ADB7] line-clamp-3">
                {{ project.summary }}
            </p>

            <div v-if="project.skills && project.skills.length > 0" class="mt-5 flex flex-wrap gap-1.5 pt-3 border-t border-[#293440]/60">
                <span
                    v-for="skill in project.skills"
                    :key="skill.id"
                    class="rounded border border-[#293440] bg-[#101419] px-2 py-0.5 font-mono text-[10px] text-[#A4ADB7]"
                >
                    {{ skill.name }}
                </span>
            </div>
        </div>

        <div class="flex items-center justify-between border-t border-[#293440] bg-[#101419]/60 px-6 py-3 text-xs">
            <Link
                :href="route('projects.show', project.slug)"
                class="font-mono font-medium text-[#F0F2F3] hover:text-[#818CF8] transition-colors"
            >
                Studi Kasus &rarr;
            </Link>

            <div class="flex items-center gap-3 font-mono text-[11px] text-[#A4ADB7]">
                <a
                    v-if="project.demo_url"
                    :href="project.demo_url"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="hover:text-[#818CF8] transition-colors"
                >
                    Demo
                </a>
                <a
                    v-if="project.source_url"
                    :href="project.source_url"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="hover:text-[#818CF8] transition-colors"
                >
                    Source
                </a>
            </div>
        </div>
    </article>
</template>
