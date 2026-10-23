<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

defineProps<{
    title?: string;
}>();

const isMobileSidebarOpen = ref(false);
const page = usePage();

const user = computed(() => page.props.auth?.user);
const flashSuccess = computed(() => page.props.flash?.success);
const flashError = computed(() => page.props.flash?.error);

const navSections = [
    {
        title: 'Core Engine',
        items: [
            { label: 'Overview', routeName: 'admin.dashboard', pattern: '/admin$' },
            { label: 'Projects Catalog', routeName: 'admin.projects.index', pattern: '/admin/projects' },
            { label: 'Messages Inbox', routeName: 'admin.messages.index', pattern: '/admin/messages' },
        ],
    },
    {
        title: 'Knowledge & Bio',
        items: [
            { label: 'Skill Groups', routeName: 'admin.skill-groups.index', pattern: '/admin/skill-groups' },
            { label: 'Skills & Tech', routeName: 'admin.skills.index', pattern: '/admin/skills' },
            { label: 'Experiences', routeName: 'admin.experience.index', pattern: '/admin/experience' },
            { label: 'Education', routeName: 'admin.education.index', pattern: '/admin/education' },
            { label: 'Certificates', routeName: 'admin.certificates.index', pattern: '/admin/certificates' },
        ],
    },
    {
        title: 'Content & System',
        items: [
            { label: 'Home Page Editor', routeName: 'admin.content.home.edit', pattern: '/admin/content/home' },
            { label: 'Profile & CV Editor', routeName: 'admin.content.profile.edit', pattern: '/admin/content/profile' },
            { label: 'Navigation Links', routeName: 'admin.navigation.index', pattern: '/admin/navigation' },
            { label: 'Social Platform Links', routeName: 'admin.social-links.index', pattern: '/admin/social-links' },
            { label: 'Global Site Settings', routeName: 'admin.settings.edit', pattern: '/admin/settings' },
        ],
    },
    {
        title: 'Security & Audit',
        items: [
            { label: 'Audit Activity Log', routeName: 'admin.activity.index', pattern: '/admin/activity' },
            { label: 'Account Credentials', routeName: 'admin.account.edit', pattern: '/admin/account' },
        ],
    },
];

function isNavActive(pattern: string): boolean {
    const url = page.url;
    if (pattern === '/admin$') {
        return url === '/admin' || url === '/admin/';
    }
    return url.startsWith(pattern);
}
</script>

<template>
    <div class="flex min-h-screen bg-[#101419] text-[#F0F2F3] font-sans antialiased selection:bg-[#4F46E5] selection:text-white">
        <!-- Desktop Sidebar: Refined Workbench Sidebar -->
        <aside class="hidden w-64 flex-shrink-0 flex-col border-r border-[#293440] bg-[#151B22] md:flex">
            <!-- Sidebar Header -->
            <div class="flex h-16 items-center border-b border-[#293440] px-6">
                <Link :href="route('admin.dashboard')" class="flex flex-col group focus:outline-none">
                    <span class="font-mono text-xs font-semibold tracking-wider text-[#F0F2F3] group-hover:text-[#818CF8] transition-colors">
                        BARUNA DWI CAHYA
                    </span>
                    <span class="font-mono text-[10px] uppercase text-[#818CF8]">
                        Cockpit Administrator
                    </span>
                </Link>
            </div>

            <!-- Navigation Sections -->
            <nav class="flex-1 space-y-6 overflow-y-auto px-4 py-6" aria-label="Admin Navigation">
                <div v-for="section in navSections" :key="section.title">
                    <h3 class="px-2.5 font-mono text-[10px] uppercase tracking-wider text-[#A4ADB7]/60">
                        {{ section.title }}
                    </h3>
                    <div class="mt-2 space-y-0.5">
                        <Link
                            v-for="item in section.items"
                            :key="item.routeName"
                            :href="route(item.routeName)"
                            :class="[
                                'flex items-center justify-between rounded px-2.5 py-1.5 font-mono text-xs transition-colors duration-150',
                                isNavActive(item.pattern)
                                    ? 'bg-[#1B242E] text-[#818CF8] border-l-2 border-[#818CF8] font-medium'
                                    : 'text-[#A4ADB7] hover:bg-[#1B242E]/60 hover:text-[#F0F2F3]'
                            ]"
                        >
                            <span>{{ item.label }}</span>
                            <span v-if="isNavActive(item.pattern)" class="h-1.5 w-1.5 rounded-full bg-[#818CF8]" />
                        </Link>
                    </div>
                </div>
            </nav>

            <!-- Sidebar User Profile Footer -->
            <div class="border-t border-[#293440] p-4 text-xs font-mono bg-[#101419]/50">
                <div class="flex items-center justify-between">
                    <div class="truncate max-w-[140px]">
                        <span class="text-[#F0F2F3] block truncate font-medium">{{ user?.name || 'Administrator' }}</span>
                        <span class="text-[10px] text-[#22C55E] block">Root Admin</span>
                    </div>
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="text-[11px] text-[#ffb4ab] hover:underline focus:outline-none"
                    >
                        Keluar
                    </Link>
                </div>
            </div>
        </aside>

        <!-- Mobile Drawer Backdrop -->
        <div
            v-if="isMobileSidebarOpen"
            @click="isMobileSidebarOpen = false"
            class="fixed inset-0 z-40 bg-black/70 backdrop-blur-xs md:hidden"
        />

        <!-- Mobile Drawer -->
        <aside
            v-if="isMobileSidebarOpen"
            class="fixed inset-y-0 left-0 z-50 w-64 border-r border-[#293440] bg-[#151B22] p-5 md:hidden overflow-y-auto"
        >
            <div class="flex items-center justify-between border-b border-[#293440] pb-4">
                <span class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">Portfolio Cockpit</span>
                <button
                    type="button"
                    @click="isMobileSidebarOpen = false"
                    class="text-[#A4ADB7] hover:text-[#F0F2F3]"
                    aria-label="Close menu"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <nav class="mt-4 space-y-6">
                <div v-for="section in navSections" :key="'mob-' + section.title">
                    <h3 class="px-2 font-mono text-[10px] uppercase tracking-wider text-[#A4ADB7]/60">
                        {{ section.title }}
                    </h3>
                    <div class="mt-2 space-y-1">
                        <Link
                            v-for="item in section.items"
                            :key="'mob-' + item.routeName"
                            :href="route(item.routeName)"
                            :class="[
                                'block rounded px-2.5 py-1.5 font-mono text-xs',
                                isNavActive(item.pattern)
                                    ? 'bg-[#1B242E] text-[#818CF8] font-semibold'
                                    : 'text-[#A4ADB7] hover:text-[#F0F2F3]'
                            ]"
                            @click="isMobileSidebarOpen = false"
                        >
                            {{ item.label }}
                        </Link>
                    </div>
                </div>
            </nav>
        </aside>

        <!-- Main Body -->
        <div class="flex flex-1 flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="flex h-16 items-center justify-between border-b border-[#293440] bg-[#151B22] px-4 sm:px-6">
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        @click="isMobileSidebarOpen = true"
                        class="text-[#A4ADB7] hover:text-[#F0F2F3] md:hidden focus:outline-none"
                        aria-label="Open sidebar"
                    >
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <div>
                        <span class="font-mono text-[10px] uppercase text-[#A4ADB7] block">WORKSPACE / ADMIN</span>
                        <h1 class="text-sm font-semibold text-[#F0F2F3] tracking-tight">
                            {{ title || 'Dashboard' }}
                        </h1>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a
                        href="/"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-1.5 rounded border border-[#293440] bg-[#101419] px-3 py-1.5 font-mono text-xs text-[#A4ADB7] hover:border-[#818CF8] hover:text-[#818CF8] transition-colors"
                    >
                        <span>Pratinjau Situs</span>
                        <span>&nearr;</span>
                    </a>
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="hidden sm:inline-block rounded border border-[#293440] bg-[#1B242E] px-3 py-1.5 font-mono text-xs text-[#ffb4ab] hover:border-[#ffb4ab] transition-colors"
                    >
                        Logout
                    </Link>
                </div>
            </header>

            <!-- Flash Banners -->
            <div v-if="flashSuccess" class="border-b border-[#22C55E]/40 bg-[#22C55E]/10 px-6 py-2.5 text-xs font-mono text-[#22C55E]">
                &check; {{ flashSuccess }}
            </div>
            <div v-if="flashError" class="border-b border-[#ffb4ab]/40 bg-[#ffb4ab]/10 px-6 py-2.5 text-xs font-mono text-[#ffb4ab]">
                &cross; {{ flashError }}
            </div>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-[#101419] p-4 sm:p-6 lg:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>
