<script setup lang="ts">
import { computed, onMounted, onBeforeUnmount } from 'vue';
import { usePage } from '@inertiajs/vue3';
import TopNavBar from '@/Components/Public/TopNavBar.vue';
import Footer from '@/Components/Public/Footer.vue';
import { NavigationLink, SocialLink } from '@/types';
import { useSmoothScroll } from '@/composables/useSmoothScroll';

defineProps<{
    navLinks?: NavigationLink[];
    socialLinks?: SocialLink[];
    siteName?: string;
    siteDescriptor?: string;
    footerText?: string;
}>();

const page = usePage();
const flashSuccess = computed(() => page.props.flash?.success);
const flashError = computed(() => page.props.flash?.error);

const { init, destroy } = useSmoothScroll();

onMounted(() => {
    init();
});

onBeforeUnmount(() => {
    destroy();
});
</script>

<template>
    <div class="flex min-h-screen flex-col bg-[#101419] text-[#e0e2ea] font-sans antialiased selection:bg-[#4F46E5] selection:text-white">
        <TopNavBar :nav-links="navLinks" :site-name="siteName" />

        <div v-if="flashSuccess" class="border-b border-[#22C55E]/40 bg-[#22C55E]/10 px-4 py-3 text-center text-xs font-mono text-[#22C55E]">
            {{ flashSuccess }}
        </div>
        <div v-if="flashError" class="border-b border-[#ffb4ab]/40 bg-[#ffb4ab]/10 px-4 py-3 text-center text-xs font-mono text-[#ffb4ab]">
            {{ flashError }}
        </div>

        <main class="flex-1">
            <slot />
        </main>

        <Footer
            :social-links="socialLinks"
            :site-name="siteName"
            :site-descriptor="siteDescriptor"
            :footer-text="footerText"
        />
    </div>
</template>
