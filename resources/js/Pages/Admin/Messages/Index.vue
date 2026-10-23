<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ContactMessage, PaginatedResponse } from '@/types';

const props = defineProps<{
    messages: PaginatedResponse<ContactMessage>;
    filters: { status?: string };
}>();

const currentStatus = ref(props.filters.status || '');

function filterStatus() {
    router.get(
        route('admin.messages.index'),
        { status: currentStatus.value || undefined },
        { preserveState: true, replace: true }
    );
}

function deleteMessage(msg: ContactMessage) {
    if (confirm(`Hapus pesan dari "${msg.name}"?`)) {
        router.delete(route('admin.messages.destroy', msg.id));
    }
}

function formatDate(dateStr: string): string {
    try {
        const d = new Date(dateStr);
        return d.toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
    } catch {
        return dateStr;
    }
}
</script>

<template>
    <Head title="Kotak Masuk Pesan — Admin" />

    <AdminLayout title="Messages Inbox">
        <div class="space-y-6 max-w-6xl">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-[#293440] pb-6">
                <div>
                    <h2 class="text-xl font-semibold text-[#F0F2F3] tracking-tight">Kotak Masuk Pesan Pengunjung</h2>
                    <p class="text-xs font-mono text-[#A4ADB7] mt-0.5">Total {{ messages.total }} pesan kontak tercatat dalam database.</p>
                </div>

                <div class="flex items-center gap-2">
                    <select
                        v-model="currentStatus"
                        @change="filterStatus"
                        class="rounded border border-[#293440] bg-[#151B22] px-3 py-1.5 font-mono text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                    >
                        <option value="">Semua Status</option>
                        <option value="unread">Belum Dibaca (Unread)</option>
                        <option value="read">Sudah Dibaca (Read)</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded border border-[#293440] bg-[#151B22]">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-[#293440] bg-[#101419] font-mono text-[11px] uppercase text-[#A4ADB7]">
                        <tr>
                            <th class="p-4 w-28">Status</th>
                            <th class="p-4">Pengirim</th>
                            <th class="p-4">Subjek & Cuplikan Pesan</th>
                            <th class="p-4 w-44">Waktu Masuk</th>
                            <th class="p-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#293440]">
                        <tr
                            v-for="msg in messages.data"
                            :key="msg.id"
                            :class="[
                                'hover:bg-[#1B242E]/50 transition-colors',
                                msg.status === 'unread' ? 'bg-[#18212B]/50 font-medium' : ''
                            ]"
                        >
                            <td class="p-4">
                                <span
                                    :class="[
                                        'rounded px-2 py-0.5 font-mono text-[10px] uppercase',
                                        msg.status === 'unread'
                                            ? 'bg-[#818CF8]/10 text-[#818CF8] border border-[#818CF8]/30'
                                            : 'bg-[#A4ADB7]/10 text-[#A4ADB7]'
                                    ]"
                                >
                                    {{ msg.status === 'unread' ? 'UNREAD' : 'READ' }}
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="text-[#F0F2F3] font-medium">{{ msg.name }}</div>
                                <div class="font-mono text-[11px] text-[#A4ADB7]">{{ msg.email }}</div>
                            </td>
                            <td class="p-4">
                                <Link
                                    :href="route('admin.messages.show', msg.id)"
                                    class="text-[#F0F2F3] hover:text-[#818CF8] transition-colors block"
                                >
                                    <div class="font-semibold">{{ msg.subject || '(Tanpa Subjek)' }}</div>
                                    <div class="mt-0.5 text-[#A4ADB7] truncate max-w-md font-normal font-mono text-[11px]">
                                        {{ msg.message }}
                                    </div>
                                </Link>
                            </td>
                            <td class="p-4 font-mono text-[11px] text-[#A4ADB7]">
                                {{ formatDate(msg.created_at) }}
                            </td>
                            <td class="p-4 text-right space-x-3 font-mono">
                                <Link
                                    :href="route('admin.messages.show', msg.id)"
                                    class="text-xs text-[#818CF8] hover:underline"
                                >
                                    Buka &rarr;
                                </Link>
                                <button
                                    type="button"
                                    @click="deleteMessage(msg)"
                                    class="text-xs text-[#ffb4ab] hover:underline"
                                >
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!messages.data || messages.data.length === 0">
                            <td colspan="5" class="p-10 text-center font-mono text-[#A4ADB7]">
                                Tidak ada pesan kontak yang ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="messages.links && messages.links.length > 3" class="flex justify-center gap-1.5 pt-4">
                <template v-for="(link, i) in messages.links" :key="i">
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
                                : 'border border-[#293440] bg-[#151B22] text-[#A4ADB7] hover:border-[#818CF8] hover:text-[#F0F2F3]'
                        ]"
                        v-html="link.label"
                    />
                </template>
            </div>
        </div>
    </AdminLayout>
</template>
