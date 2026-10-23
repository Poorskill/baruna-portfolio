<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ContactMessage } from '@/types';

const props = defineProps<{
    message: ContactMessage;
}>();

function deleteMessage() {
    if (confirm(`Hapus pesan dari "${props.message.name}" secara permanen?`)) {
        router.delete(route('admin.messages.destroy', props.message.id));
    }
}

function formatDate(dateStr: string): string {
    try {
        const d = new Date(dateStr);
        return d.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });
    } catch {
        return dateStr;
    }
}
</script>

<template>
    <Head :title="`Pesan: ${message.subject || message.name} — Admin`" />

    <AdminLayout title="Message Detail">
        <div class="max-w-3xl space-y-6">
            <nav>
                <Link
                    :href="route('admin.messages.index')"
                    class="font-mono text-xs text-[#A4ADB7] hover:text-[#818CF8] transition-colors"
                >
                    &larr; Kembali ke Kotak Masuk
                </Link>
            </nav>

            <article class="rounded border border-[#293440] bg-[#151B22] p-6 sm:p-8 space-y-6">
                <!-- Message Header -->
                <header class="border-b border-[#293440] pb-6">
                    <div class="flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-2">
                        <h2 class="text-xl font-semibold text-[#F0F2F3] tracking-tight">
                            {{ message.subject || '(Tanpa Subjek)' }}
                        </h2>
                        <span class="font-mono text-xs text-[#A4ADB7]">
                            {{ formatDate(message.created_at) }}
                        </span>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center justify-between gap-4 text-xs">
                        <div class="space-y-0.5">
                            <div>
                                <span class="font-mono text-[#A4ADB7]">Pengirim:</span>
                                <span class="ml-1.5 font-medium text-[#F0F2F3]">{{ message.name }}</span>
                            </div>
                            <div>
                                <span class="font-mono text-[#A4ADB7]">Email:</span>
                                <a :href="`mailto:${message.email}`" class="ml-1.5 font-mono text-[#818CF8] hover:underline">
                                    {{ message.email }}
                                </a>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <a
                                :href="`mailto:${message.email}?subject=Re: ${encodeURIComponent(message.subject || 'Portfolio Inquiry')}`"
                                class="inline-flex items-center gap-1.5 rounded bg-[#4F46E5] px-4 py-2 font-mono text-xs font-semibold text-white hover:bg-[#4338CA] transition-colors"
                            >
                                <span>Balas Surel</span>
                                <span>&nearr;</span>
                            </a>
                            <button
                                type="button"
                                @click="deleteMessage"
                                class="rounded border border-[#293440] bg-[#101419] px-3.5 py-2 font-mono text-xs text-[#ffb4ab] hover:border-[#ffb4ab] transition-colors"
                            >
                                Hapus
                            </button>
                        </div>
                    </div>
                </header>

                <!-- Escaped Message Body (strictly escaped, NO v-html) -->
                <div class="text-sm leading-relaxed text-[#F0F2F3] whitespace-pre-wrap font-sans bg-[#101419] rounded border border-[#293440] p-6">
                    {{ message.message }}
                </div>
            </article>
        </div>
    </AdminLayout>
</template>
