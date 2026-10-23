<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Project, PaginatedResponse } from '@/types';

const props = defineProps<{
    projects: PaginatedResponse<Project>;
    filters: { search?: string; status?: string };
}>();

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');

function applyFilters() {
    router.get(
        route('admin.projects.index'),
        {
            search: search.value || undefined,
            status: status.value || undefined,
        },
        { preserveState: true, replace: true }
    );
}

function deleteProject(project: Project) {
    if (confirm(`Apakah Anda yakin ingin menghapus proyek "${project.title}" secara permanen? Seluruh gambar galeri juga akan dihapus.`)) {
        router.delete(route('admin.projects.destroy', project.id));
    }
}
</script>

<template>
    <Head title="Katalog Proyek — Admin" />

    <AdminLayout title="Projects Catalog">
        <div class="space-y-6 max-w-6xl">
            <!-- Header Toolbar -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-[#293440] pb-6">
                <div>
                    <h2 class="text-xl font-semibold text-[#F0F2F3] tracking-tight">Katalog Proyek & Studi Kasus</h2>
                    <p class="text-xs text-[#A4ADB7] font-mono mt-0.5">Total {{ projects.total }} entri proyek terdaftar dalam database.</p>
                </div>
                <Link
                    :href="route('admin.projects.create')"
                    class="inline-flex items-center justify-center rounded bg-[#4F46E5] px-4 py-2 font-mono text-xs font-semibold text-white hover:bg-[#4338CA] transition-colors"
                >
                    + Tambah Proyek Baru
                </Link>
            </div>

            <!-- Filters Bar -->
            <div class="flex flex-col sm:flex-row gap-3 rounded border border-[#293440] bg-[#151B22] p-4">
                <input
                    v-model="search"
                    @keyup.enter="applyFilters"
                    type="search"
                    placeholder="Cari berdasarkan judul proyek..."
                    class="flex-1 rounded border border-[#293440] bg-[#101419] px-3.5 py-2 font-mono text-xs text-[#F0F2F3] placeholder-[#A4ADB7]/50 focus:border-[#818CF8] focus:outline-none"
                />
                <select
                    v-model="status"
                    @change="applyFilters"
                    class="rounded border border-[#293440] bg-[#101419] px-3 py-2 font-mono text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                >
                    <option value="">Semua Status Publikasi</option>
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                    <option value="archived">Archived</option>
                </select>
                <button
                    type="button"
                    @click="applyFilters"
                    class="rounded border border-[#293440] bg-[#1B242E] px-4 py-2 font-mono text-xs text-[#F0F2F3] hover:border-[#818CF8] transition-colors"
                >
                    Terapkan
                </button>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded border border-[#293440] bg-[#151B22]">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-[#293440] bg-[#101419] font-mono text-[11px] uppercase text-[#A4ADB7]">
                        <tr>
                            <th class="p-4 w-20">Cover</th>
                            <th class="p-4">Judul & Slug</th>
                            <th class="p-4">Kategori</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-center">Urutan</th>
                            <th class="p-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#293440]">
                        <tr
                            v-for="project in projects.data"
                            :key="project.id"
                            class="hover:bg-[#1B242E]/50 transition-colors"
                        >
                            <td class="p-4">
                                <div class="h-12 w-16 overflow-hidden rounded border border-[#293440] bg-[#101419]">
                                    <img
                                        v-if="project.cover_image"
                                        :src="project.cover_image.startsWith('http') ? project.cover_image : `/storage/${project.cover_image}`"
                                        :alt="project.title"
                                        class="h-full w-full object-cover"
                                    />
                                    <div v-else class="flex h-full w-full items-center justify-center font-mono text-[9px] text-[#A4ADB7]/50">
                                        none
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <div class="font-medium text-[#F0F2F3] flex items-center gap-2">
                                    <span>{{ project.title }}</span>
                                    <span v-if="project.is_featured" class="rounded bg-[#101419] border border-[#818CF8]/40 px-1.5 py-0.2 font-mono text-[9px] text-[#818CF8]">
                                        Featured
                                    </span>
                                </div>
                                <div class="font-mono text-[11px] text-[#A4ADB7] mt-0.5">
                                    /projects/{{ project.slug }}
                                </div>
                            </td>
                            <td class="p-4 font-mono text-[#A4ADB7]">
                                {{ project.category || '—' }}
                            </td>
                            <td class="p-4">
                                <span
                                    :class="[
                                        'rounded px-2 py-0.5 font-mono text-[10px] uppercase tracking-wider',
                                        project.status === 'published' ? 'bg-[#22C55E]/10 text-[#22C55E] border border-[#22C55E]/30' :
                                        project.status === 'draft' ? 'bg-[#E5BC72]/10 text-[#E5BC72] border border-[#E5BC72]/30' :
                                        'bg-[#A4ADB7]/10 text-[#A4ADB7] border border-[#293440]'
                                    ]"
                                >
                                    {{ project.status }}
                                </span>
                            </td>
                            <td class="p-4 text-center font-mono text-[#A4ADB7]">
                                {{ project.sort_order }}
                            </td>
                            <td class="p-4 text-right space-x-3 font-mono">
                                <Link
                                    :href="route('admin.projects.edit', project.id)"
                                    class="text-xs text-[#818CF8] hover:underline"
                                >
                                    Edit
                                </Link>
                                <button
                                    type="button"
                                    @click="deleteProject(project)"
                                    class="text-xs text-[#ffb4ab] hover:underline"
                                >
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!projects.data || projects.data.length === 0">
                            <td colspan="6" class="p-10 text-center font-mono text-xs text-[#A4ADB7]">
                                Tidak ada rekaman proyek yang ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="projects.links && projects.links.length > 3" class="flex justify-center gap-1.5 pt-4">
                <template v-for="(link, i) in projects.links" :key="i">
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
