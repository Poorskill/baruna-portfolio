<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ActivityLog, PaginatedResponse } from '@/types';

defineProps<{
    logs: PaginatedResponse<ActivityLog>;
}>();

function formatDate(dateStr: string): string {
    try {
        const d = new Date(dateStr);
        return d.toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' });
    } catch {
        return dateStr;
    }
}
</script>

<template>
    <Head title="Log Aktivitas Sistem — Admin" />

    <AdminLayout title="System Audit Logs">
        <div class="space-y-6 max-w-6xl">
            <div class="border-b border-[#293440] pb-6">
                <h2 class="text-xl font-semibold text-[#F0F2F3] tracking-tight">Audit Log Aktivitas Sistem</h2>
                <p class="text-xs font-mono text-[#A4ADB7] mt-0.5">
                    Catatan peristiwa administratif mutasi data untuk kebutuhan integritas dan pelacakan audit.
                </p>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded border border-[#293440] bg-[#151B22]">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-[#293440] bg-[#101419] font-mono text-[11px] uppercase text-[#A4ADB7]">
                        <tr>
                            <th class="p-4 w-48">Waktu Kejadian</th>
                            <th class="p-4 w-36">Pelaku</th>
                            <th class="p-4 w-28">Aksi</th>
                            <th class="p-4 w-40">Entitas</th>
                            <th class="p-4">Ringkasan Mutasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#293440]">
                        <tr
                            v-for="log in logs.data"
                            :key="log.id"
                            class="hover:bg-[#1B242E]/50 transition-colors"
                        >
                            <td class="p-4 font-mono text-[11px] text-[#A4ADB7]">
                                {{ formatDate(log.created_at) }}
                            </td>
                            <td class="p-4 font-mono text-[11px] text-[#F0F2F3]">
                                {{ log.user?.name || 'Sistem / CLI' }}
                            </td>
                            <td class="p-4">
                                <span
                                    :class="[
                                        'rounded px-2 py-0.5 font-mono text-[10px] uppercase tracking-wider',
                                        log.action === 'created' ? 'bg-[#22C55E]/10 text-[#22C55E]' :
                                        log.action === 'updated' ? 'bg-[#E5BC72]/10 text-[#E5BC72]' :
                                        log.action === 'deleted' ? 'bg-[#ffb4ab]/10 text-[#ffb4ab]' :
                                        'bg-[#A4ADB7]/10 text-[#A4ADB7]'
                                    ]"
                                >
                                    {{ log.action }}
                                </span>
                            </td>
                            <td class="p-4 font-mono text-[11px] text-[#818CF8]">
                                {{ log.entity_type || '—' }} <span v-if="log.entity_id" class="text-[#A4ADB7]">#{{ log.entity_id }}</span>
                            </td>
                            <td class="p-4 text-[#F0F2F3] font-mono text-[11px]">
                                {{ log.summary }}
                            </td>
                        </tr>
                        <tr v-if="!logs.data || logs.data.length === 0">
                            <td colspan="5" class="p-10 text-center font-mono text-xs text-[#A4ADB7]">
                                Belum ada catatan aktivitas administratif.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="logs.links && logs.links.length > 3" class="flex justify-center gap-1.5 pt-4">
                <template v-for="(link, i) in logs.links" :key="i">
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
