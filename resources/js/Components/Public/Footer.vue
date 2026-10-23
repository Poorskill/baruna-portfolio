<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { SocialLink } from '@/types';

const props = defineProps<{
    socialLinks?: SocialLink[];
    siteName?: string;
    siteDescriptor?: string;
    footerText?: string;
}>();

const currentYear = new Date().getFullYear();

const validSocialLinks = computed(() => {
    if (!props.socialLinks) return [];
    return props.socialLinks.filter((item) => {
        if (!item.url) return false;
        const u = item.url.trim().toLowerCase();
        return u !== '' && u !== 'https://' && u !== 'http://' && !u.includes('example.com') && u !== 'https://wa.me/';
    });
});
</script>

<template>
    <footer class="mt-auto border-t border-[#293440] bg-[#0B0F14] text-[#A4ADB7]">
        <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
            <!-- Top Row: Identity (Left) & Nav/Socials (Right) -->
            <div class="flex flex-col gap-10 md:flex-row md:items-start md:justify-between">
                <!-- Left: Identity -->
                <div class="space-y-2 max-w-sm">
                    <Link href="/" class="inline-block focus:outline-none">
                        <span class="font-mono text-sm font-semibold tracking-wider text-[#F0F2F3] hover:text-[#818CF8] transition-colors">
                            {{ siteName ? siteName.toUpperCase() : 'BARUNA DWI CAHYA' }}
                        </span>
                    </Link>
                    <p class="font-mono text-xs leading-relaxed text-[#A4ADB7]">
                        {{ siteDescriptor || 'Web Development & Software Systems' }}
                    </p>
                </div>

                <!-- Right: Navigation & Social Links -->
                <div class="flex flex-wrap gap-12 sm:gap-16">
                    <!-- Navigasi -->
                    <div class="space-y-3">
                        <h4 class="font-mono text-[11px] uppercase tracking-wider text-[#F0F2F3]">
                            Navigasi
                        </h4>
                        <ul class="space-y-2 font-mono text-xs">
                            <li>
                                <Link href="/" class="text-[#A4ADB7] hover:text-[#818CF8] transition-colors">Home</Link>
                            </li>
                            <li>
                                <Link href="/about" class="text-[#A4ADB7] hover:text-[#818CF8] transition-colors">About</Link>
                            </li>
                            <li>
                                <Link href="/projects" class="text-[#A4ADB7] hover:text-[#818CF8] transition-colors">Projects</Link>
                            </li>
                            <li>
                                <Link href="/contact" class="text-[#A4ADB7] hover:text-[#818CF8] transition-colors">Contact</Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Social Links (Only Valid CMS Configured Links) -->
                    <div v-if="validSocialLinks.length > 0" class="space-y-3">
                        <h4 class="font-mono text-[11px] uppercase tracking-wider text-[#F0F2F3]">
                            Terhubung
                        </h4>
                        <ul class="space-y-2 font-mono text-xs">
                            <li v-for="item in validSocialLinks" :key="item.id">
                                <a
                                    :href="item.url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1 text-[#A4ADB7] hover:text-[#818CF8] transition-colors"
                                    :aria-label="`Buka profil ${item.label}`"
                                >
                                    <span>{{ item.label }}</span>
                                    <span class="text-[10px] text-[#A4ADB7]/60">&nearr;</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Bottom Row: Copyright -->
            <div class="mt-12 border-t border-[#293440] pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 font-mono text-[11px] text-[#A4ADB7]/70">
                <p>
                    {{ (footerText && !footerText.includes('2025')) ? footerText : `© ${currentYear} Baruna Dwi Cahya. All rights reserved.` }}
                </p>
            </div>
        </div>
    </footer>
</template>
