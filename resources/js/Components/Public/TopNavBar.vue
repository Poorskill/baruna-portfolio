<script setup lang="ts">
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { NavigationLink } from '@/types';
import { useSmoothScroll } from '@/composables/useSmoothScroll';

defineProps<{
    navLinks?: NavigationLink[];
    siteName?: string;
}>();

const isMobileOpen = ref(false);
const page = usePage();
const { scrollTo } = useSmoothScroll();

const defaultLinks = [
    { label: 'Home', url: '/' },
    { label: 'About', url: '/about' },
    { label: 'Projects', url: '/projects' },
    { label: 'Contact', url: '/#contact' },
];

function isContactUrl(url: string): boolean {
    return url === '/contact' || url === '/#contact' || url === '#contact';
}

function resolveUrl(url: string): string {
    if (isContactUrl(url)) {
        return page.url === '/' || page.url.startsWith('/#') ? '#contact' : '/#contact';
    }
    return url;
}

function isActive(url: string): boolean {
    const current = page.url;
    if (isContactUrl(url)) {
        return current.includes('#contact');
    }
    if (url === '/') return current === '/' || current.startsWith('/?');
    return current.startsWith(url);
}

function handleLinkClick(url: string, e: MouseEvent) {
    isMobileOpen.value = false;
    if (isContactUrl(url)) {
        if (page.url === '/' || page.url.startsWith('/#')) {
            e.preventDefault();
            const el = document.getElementById('contact');
            if (el) {
                scrollTo(el, { offset: -70 });
                window.history.pushState(null, '', '/#contact');
            }
        }
    }
}
</script>

<template>
    <header class="sticky top-0 z-40 w-full border-b border-[#293440] bg-[#101419]/90 backdrop-blur-md">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <!-- Brand Wordmark -->
            <Link href="/" class="group flex items-center gap-3 focus:outline-none">
                <span class="font-mono text-sm font-semibold tracking-wider text-[#F0F2F3] group-hover:text-[#818CF8] transition-colors">
                    {{ siteName ? siteName.toUpperCase() : 'BARUNA DWI CAHYA' }}
                </span>
                <span class="hidden sm:inline-flex items-center gap-1.5 rounded border border-[#293440] bg-[#151B22] px-2 py-0.5 font-mono text-[10px] text-[#A4ADB7]">
                    <span class="h-1.5 w-1.5 rounded-full bg-[#818CF8]" />
                    <span>ENGINEERING</span>
                </span>
            </Link>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex md:items-center md:gap-8" aria-label="Main Navigation">
                <template v-if="navLinks && navLinks.length > 0">
                    <template v-for="item in navLinks" :key="item.id">
                        <a
                            v-if="item.is_external"
                            :href="item.url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="font-mono text-xs text-[#A4ADB7] hover:text-[#F0F2F3] transition-colors"
                        >
                            {{ item.label }} &nearr;
                        </a>
                        <Link
                            v-else
                            :href="resolveUrl(item.url)"
                            @click="(e) => handleLinkClick(item.url, e)"
                            :class="[
                                'relative font-mono text-xs transition-colors py-1',
                                isActive(item.url)
                                    ? 'text-[#818CF8] font-medium'
                                    : 'text-[#A4ADB7] hover:text-[#F0F2F3]'
                            ]"
                        >
                            {{ item.label }}
                            <span
                                v-if="isActive(item.url)"
                                class="absolute -bottom-2 left-0 right-0 h-[2px] bg-[#818CF8]"
                            />
                        </Link>
                    </template>
                </template>
                <template v-else>
                    <Link
                        v-for="item in defaultLinks"
                        :key="item.url"
                        :href="resolveUrl(item.url)"
                        @click="(e) => handleLinkClick(item.url, e)"
                        :class="[
                            'relative font-mono text-xs transition-colors py-1',
                            isActive(item.url)
                                ? 'text-[#818CF8] font-medium'
                                : 'text-[#A4ADB7] hover:text-[#F0F2F3]'
                        ]"
                    >
                        {{ item.label }}
                        <span
                            v-if="isActive(item.url)"
                            class="absolute -bottom-2 left-0 right-0 h-[2px] bg-[#818CF8]"
                        />
                    </Link>
                </template>
            </nav>

            <!-- Mobile Hamburger -->
            <button
                type="button"
                @click="isMobileOpen = !isMobileOpen"
                class="inline-flex items-center justify-center rounded border border-[#293440] p-2 text-[#A4ADB7] hover:text-[#F0F2F3] md:hidden focus:outline-none focus:border-[#818CF8]"
                :aria-expanded="isMobileOpen"
                aria-label="Toggle navigation menu"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path v-if="!isMobileOpen" stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    <path v-else stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Mobile Drawer -->
        <div v-show="isMobileOpen" class="border-t border-[#293440] bg-[#151B22] md:hidden px-4 py-5 space-y-3">
            <template v-if="navLinks && navLinks.length > 0">
                <template v-for="item in navLinks" :key="'mob-' + item.id">
                    <a
                        v-if="item.is_external"
                        :href="item.url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="block py-1.5 font-mono text-xs text-[#A4ADB7] hover:text-[#F0F2F3]"
                        @click="isMobileOpen = false"
                    >
                        {{ item.label }} &nearr;
                    </a>
                    <Link
                        v-else
                        :href="resolveUrl(item.url)"
                        @click="(e) => handleLinkClick(item.url, e)"
                        :class="[
                            'block py-1.5 font-mono text-xs',
                            isActive(item.url) ? 'text-[#818CF8] font-semibold' : 'text-[#A4ADB7] hover:text-[#F0F2F3]'
                        ]"
                    >
                        {{ item.label }}
                    </Link>
                </template>
            </template>
            <template v-else>
                <Link
                    v-for="item in defaultLinks"
                    :key="'mob-' + item.url"
                    :href="resolveUrl(item.url)"
                    @click="(e) => handleLinkClick(item.url, e)"
                    :class="[
                        'block py-1.5 font-mono text-xs',
                        isActive(item.url) ? 'text-[#818CF8] font-semibold' : 'text-[#A4ADB7] hover:text-[#F0F2F3]'
                    ]"
                >
                    {{ item.label }}
                </Link>
            </template>
        </div>
    </header>
</template>
