<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { SocialLink } from '@/types';

defineProps<{
    links: SocialLink[];
}>();

const editingLink = ref<SocialLink | null>(null);

const form = useForm({
    label: '',
    platform: '',
    url: '',
    sort_order: 0,
    is_visible: true,
});

function openCreate() {
    editingLink.value = null;
    form.reset();
    form.is_visible = true;
    form.sort_order = 0;
}

function openEdit(link: SocialLink) {
    editingLink.value = link;
    form.label = link.label;
    form.platform = link.platform;
    form.url = link.url;
    form.sort_order = link.sort_order;
    form.is_visible = Boolean(link.is_visible);
}

function submit() {
    if (editingLink.value) {
        form.put(route('admin.social-links.update', editingLink.value.id), {
            onSuccess: () => {
                editingLink.value = null;
                form.reset();
            },
        });
    } else {
        form.post(route('admin.social-links.store'), {
            onSuccess: () => form.reset(),
        });
    }
}

function deleteLink(link: SocialLink) {
    if (confirm(`Hapus tautan sosial "${link.label}"?`)) {
        router.delete(route('admin.social-links.destroy', link.id));
    }
}
</script>

<template>
    <Head title="Media Sosial & Platform — Admin" />

    <AdminLayout title="Social Platforms">
        <div class="grid gap-8 lg:grid-cols-12 max-w-6xl">
            <!-- Table -->
            <div class="space-y-4 lg:col-span-7">
                <div class="border-b border-[#293440] pb-4">
                    <h2 class="text-lg font-semibold text-[#F0F2F3] tracking-tight">Tautan Platform Profesional</h2>
                    <p class="text-xs font-mono text-[#A4ADB7] mt-0.5">Tautan publik untuk GitHub, LinkedIn, email, dan kanal profesional.</p>
                </div>

                <div class="overflow-x-auto rounded border border-[#293440] bg-[#151B22]">
                    <table class="w-full text-left text-xs">
                        <thead class="border-b border-[#293440] bg-[#101419] font-mono text-[11px] uppercase text-[#A4ADB7]">
                            <tr>
                                <th class="p-4">Label</th>
                                <th class="p-4">Platform</th>
                                <th class="p-4">URL</th>
                                <th class="p-4 text-center">Urutan</th>
                                <th class="p-4 text-center">Status</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#293440]">
                            <tr
                                v-for="link in links"
                                :key="link.id"
                                class="hover:bg-[#1B242E]/50 transition-colors"
                            >
                                <td class="p-4 font-medium text-[#F0F2F3]">
                                    {{ link.label }}
                                </td>
                                <td class="p-4 font-mono text-[11px] text-[#818CF8]">
                                    {{ link.platform }}
                                </td>
                                <td class="p-4 font-mono text-[11px] text-[#A4ADB7] truncate max-w-xs">
                                    {{ link.url }}
                                </td>
                                <td class="p-4 text-center font-mono text-[#A4ADB7]">
                                    {{ link.sort_order }}
                                </td>
                                <td class="p-4 text-center">
                                    <span
                                        :class="[
                                            'rounded px-2 py-0.5 font-mono text-[10px] uppercase',
                                            link.is_visible ? 'bg-[#22C55E]/10 text-[#22C55E]' : 'bg-[#ffb4ab]/10 text-[#ffb4ab]'
                                        ]"
                                    >
                                        {{ link.is_visible ? 'Tampil' : 'Sembunyi' }}
                                    </span>
                                </td>
                                <td class="p-4 text-right space-x-3 font-mono">
                                    <button
                                        type="button"
                                        @click="openEdit(link)"
                                        class="text-xs text-[#818CF8] hover:underline"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        @click="deleteLink(link)"
                                        class="text-xs text-[#ffb4ab] hover:underline"
                                    >
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!links || links.length === 0">
                                <td colspan="6" class="p-8 text-center font-mono text-xs text-[#A4ADB7]">
                                    Belum ada tautan platform sosial.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Form -->
            <div class="lg:col-span-5">
                <div class="rounded border border-[#293440] bg-[#151B22] p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-[#293440] pb-3">
                        <h3 class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">
                            {{ editingLink ? `Edit: ${editingLink.label}` : 'Tambah Tautan Baru' }}
                        </h3>
                        <button
                            v-if="editingLink"
                            type="button"
                            @click="openCreate"
                            class="font-mono text-xs text-[#A4ADB7] hover:underline"
                        >
                            + Batal Edit
                        </button>
                    </div>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                                Label Tampilan <span class="text-[#ffb4ab]">*</span>
                            </label>
                            <input
                                v-model="form.label"
                                type="text"
                                required
                                placeholder="misal: GitHub, LinkedIn, Mail"
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
                            <p v-if="form.errors.label" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.label }}</p>
                        </div>

                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                                Nama Platform <span class="text-[#ffb4ab]">*</span>
                            </label>
                            <input
                                v-model="form.platform"
                                type="text"
                                required
                                placeholder="misal: github, linkedin, email"
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
                            <p v-if="form.errors.platform" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.platform }}</p>
                        </div>

                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                                URL Tautan <span class="text-[#ffb4ab]">*</span>
                            </label>
                            <input
                                v-model="form.url"
                                type="text"
                                required
                                placeholder="https://github.com/... atau mailto:..."
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2 font-mono text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
                            <p v-if="form.errors.url" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.url }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block font-mono text-xs uppercase text-[#A4ADB7]">Urutan</label>
                                <input
                                    v-model.number="form.sort_order"
                                    type="number"
                                    min="0"
                                    class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3 py-2 font-mono text-xs text-[#F0F2F3]"
                                />
                            </div>
                            <div class="flex items-center pt-6">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input
                                        v-model="form.is_visible"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-[#293440] bg-[#101419] text-[#818CF8] focus:ring-0"
                                    />
                                    <span class="text-xs font-mono text-[#F0F2F3]">Tampilkan</span>
                                </label>
                            </div>
                        </div>

                        <div class="pt-2">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full rounded bg-[#4F46E5] px-4 py-2 font-mono text-xs font-semibold text-white hover:bg-[#4338CA] transition-colors disabled:opacity-50"
                            >
                                <span v-if="form.processing">Menyimpan...</span>
                                <span v-else>{{ editingLink ? 'Simpan Perubahan' : 'Buat Tautan' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
