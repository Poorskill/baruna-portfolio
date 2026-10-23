<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { SkillGroup } from '@/types';

defineProps<{
    groups: SkillGroup[];
}>();

const editingGroup = ref<SkillGroup | null>(null);

const form = useForm({
    name: '',
    slug: '',
    description: '',
    sort_order: 0,
    is_visible: true,
});

function openCreate() {
    editingGroup.value = null;
    form.reset();
    form.is_visible = true;
    form.sort_order = 0;
}

function openEdit(group: SkillGroup) {
    editingGroup.value = group;
    form.name = group.name;
    form.slug = group.slug;
    form.description = group.description || '';
    form.sort_order = group.sort_order;
    form.is_visible = Boolean(group.is_visible);
}

function submit() {
    if (editingGroup.value) {
        form.put(route('admin.skill-groups.update', editingGroup.value.id), {
            onSuccess: () => {
                editingGroup.value = null;
                form.reset();
            },
        });
    } else {
        form.post(route('admin.skill-groups.store'), {
            onSuccess: () => form.reset(),
        });
    }
}

function deleteGroup(group: SkillGroup) {
    if (confirm(`Hapus grup keahlian "${group.name}"? Skill di dalamnya akan tetap tersimpan tanpa grup.`)) {
        router.delete(route('admin.skill-groups.destroy', group.id));
    }
}
</script>

<template>
    <Head title="Grup Keahlian — Admin" />

    <AdminLayout title="Skill Groups">
        <div class="grid gap-8 lg:grid-cols-12 max-w-6xl">
            <!-- Table List -->
            <div class="space-y-4 lg:col-span-7">
                <div class="border-b border-[#293440] pb-4">
                    <h2 class="text-lg font-semibold text-[#F0F2F3] tracking-tight">Kategori Grup Keahlian</h2>
                    <p class="text-xs font-mono text-[#A4ADB7] mt-0.5">Pengelompokan kompetensi untuk section keahlian di beranda.</p>
                </div>

                <div class="overflow-x-auto rounded border border-[#293440] bg-[#151B22]">
                    <table class="w-full text-left text-xs">
                        <thead class="border-b border-[#293440] bg-[#101419] font-mono text-[11px] uppercase text-[#A4ADB7]">
                            <tr>
                                <th class="p-4">Nama</th>
                                <th class="p-4">Slug</th>
                                <th class="p-4 text-center">Skill</th>
                                <th class="p-4 text-center">Urutan</th>
                                <th class="p-4 text-center">Status</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#293440]">
                            <tr
                                v-for="group in groups"
                                :key="group.id"
                                class="hover:bg-[#1B242E]/50 transition-colors"
                            >
                                <td class="p-4 font-medium text-[#F0F2F3]">
                                    {{ group.name }}
                                </td>
                                <td class="p-4 font-mono text-[11px] text-[#A4ADB7]">
                                    {{ group.slug }}
                                </td>
                                <td class="p-4 text-center font-mono text-[#818CF8]">
                                    {{ group.skills_count ?? 0 }}
                                </td>
                                <td class="p-4 text-center font-mono text-[#A4ADB7]">
                                    {{ group.sort_order }}
                                </td>
                                <td class="p-4 text-center">
                                    <span
                                        :class="[
                                            'rounded px-2 py-0.5 font-mono text-[10px] uppercase',
                                            group.is_visible ? 'bg-[#22C55E]/10 text-[#22C55E]' : 'bg-[#ffb4ab]/10 text-[#ffb4ab]'
                                        ]"
                                    >
                                        {{ group.is_visible ? 'Tampil' : 'Sembunyi' }}
                                    </span>
                                </td>
                                <td class="p-4 text-right space-x-3 font-mono">
                                    <button
                                        type="button"
                                        @click="openEdit(group)"
                                        class="text-xs text-[#818CF8] hover:underline"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        @click="deleteGroup(group)"
                                        class="text-xs text-[#ffb4ab] hover:underline"
                                    >
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!groups || groups.length === 0">
                                <td colspan="6" class="p-8 text-center font-mono text-[#A4ADB7]">
                                    Belum ada grup keahlian terdaftar.
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
                            {{ editingGroup ? `Edit: ${editingGroup.name}` : 'Tambah Grup Baru' }}
                        </h3>
                        <button
                            v-if="editingGroup"
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
                                Nama Grup <span class="text-[#ffb4ab]">*</span>
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                required
                                placeholder="misal: Backend & Databases"
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">Slug</label>
                            <input
                                v-model="form.slug"
                                type="text"
                                placeholder="otomatis jika kosong"
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2 font-mono text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
                            <p v-if="form.errors.slug" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.slug }}</p>
                        </div>

                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">Deskripsi Ringkas</label>
                            <textarea
                                v-model="form.description"
                                rows="2"
                                maxlength="500"
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
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
                                <span v-else>{{ editingGroup ? 'Simpan Perubahan' : 'Buat Grup' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
