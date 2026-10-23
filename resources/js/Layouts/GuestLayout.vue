<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen flex-col items-center justify-center bg-[#101419] p-4 text-[#F0F2F3] font-sans antialiased">
        <div class="mb-6 text-center">
            <Link href="/" class="group focus:outline-none">
                <span class="font-mono text-sm font-semibold tracking-wider text-[#F0F2F3] group-hover:text-[#818CF8] transition-colors">
                    BARUNA DWI CAHYA
                </span>
                <span class="block font-mono text-[10px] uppercase text-[#A4ADB7] mt-0.5">
                    Portfolio Authentication
                </span>
            </Link>
        </div>

        <div class="w-full overflow-hidden rounded border border-[#293440] bg-[#151B22] p-8 shadow-2xl sm:max-w-md">
            <slot />
        </div>

        <div class="mt-8 font-mono text-[11px] text-[#A4ADB7]/60">
            &larr; <Link href="/" class="hover:text-[#818CF8]">Kembali ke Beranda Publik</Link>
        </div>
    </div>
</template>
