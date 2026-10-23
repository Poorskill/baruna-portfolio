<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Experience } from '@/types';

defineProps<{
    experiences: Experience[];
}>();

const editingExp = ref<Experience | null>(null);

const form = useForm({
    organization: '',
    role: '',
    type: '',
    location_mode: '',
    start_date: '',
    end_date: '',
    is_current: false,
    summary: '',
    responsibilities_text: '',
    outcomes_text: '',
    sort_order: 0,
    is_visible: true,
});

function openCreate() {
    editingExp.value = null;
    form.reset();
    form.is_visible = true;
    form.sort_order = 0;
}

function openEdit(exp: Experience) {
    editingExp.value = exp;
    form.organization = exp.organization;
    form.role = exp.role;
    form.type = exp.type || '';
    form.location_mode = exp.location_mode || '';
    form.start_date = exp.start_date ? exp.start_date.substring(0, 10) : '';
    form.end_date = exp.end_date ? exp.end_date.substring(0, 10) : '';
    form.is_current = Boolean(exp.is_current);
    form.summary = exp.summary || '';
    form.responsibilities_text = exp.responsibilities ? exp.responsibilities.join('\n') : '';
    form.outcomes_text = exp.outcomes ? exp.outcomes.join('\n') : '';
    form.sort_order = exp.sort_order;
    form.is_visible = Boolean(exp.is_visible);
}

function submit() {
    const payload = {
        organization: form.organization,
        role: form.role,
        type: form.type || null,
        location_mode: form.location_mode || null,
        start_date: form.start_date,
        end_date: form.is_current ? null : (form.end_date || null),
        is_current: form.is_current,
        summary: form.summary || null,
        responsibilities: form.responsibilities_text ? form.responsibilities_text.split('\n').map((s) => s.trim()).filter(Boolean) : null,
        outcomes: form.outcomes_text ? form.outcomes_text.split('\n').map((s) => s.trim()).filter(Boolean) : null,
        sort_order: form.sort_order,
        is_visible: form.is_visible,
    };

    if (editingExp.value) {
        router.put(route('admin.experience.update', editingExp.value.id), payload, {
            onSuccess: () => {
                editingExp.value = null;
                form.reset();
            },
        });
    } else {
        router.post(route('admin.experience.store'), payload, {
            onSuccess: () => form.reset(),
        });
    }
}

function deleteExperience(exp: Experience) {
    if (confirm(`Hapus pengalaman di "${exp.organization}"?`)) {
        router.delete(route('admin.experience.destroy', exp.id));
    }
}
</script>

<template>
    <Head title="Pengalaman Karir — Admin" />

    <AdminLayout title="Career Experiences">
        <div class="grid gap-8 lg:grid-cols-12 max-w-6xl">
            <!-- Table -->
            <div class="space-y-4 lg:col-span-7">
                <div class="border-b border-[#293440] pb-4">
                    <h2 class="text-lg font-semibold text-[#F0F2F3] tracking-tight">Riwayat Pengalaman Karir</h2>
                    <p class="text-xs font-mono text-[#A4ADB7] mt-0.5">Total {{ experiences.length }} entri riwayat profesional.</p>
                </div>

                <div class="overflow-x-auto rounded border border-[#293440] bg-[#151B22]">
                    <table class="w-full text-left text-xs">
                        <thead class="border-b border-[#293440] bg-[#101419] font-mono text-[11px] uppercase text-[#A4ADB7]">
                            <tr>
                                <th class="p-4">Organisasi & Peran</th>
                                <th class="p-4">Periode</th>
                                <th class="p-4 text-center">Urutan</th>
                                <th class="p-4 text-center">Status</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#293440]">
                            <tr
                                v-for="exp in experiences"
                                :key="exp.id"
                                class="hover:bg-[#1B242E]/50 transition-colors"
                            >
                                <td class="p-4">
                                    <div class="font-medium text-[#F0F2F3]">{{ exp.role }}</div>
                                    <div class="text-[11px] text-[#818CF8] font-mono">{{ exp.organization }}</div>
                                </td>
                                <td class="p-4 font-mono text-[11px] text-[#A4ADB7]">
                                    {{ exp.start_date.substring(0, 7) }} &rarr; {{ exp.is_current ? 'Sekarang' : (exp.end_date ? exp.end_date.substring(0, 7) : '') }}
                                </td>
                                <td class="p-4 text-center font-mono text-[#A4ADB7]">
                                    {{ exp.sort_order }}
                                </td>
                                <td class="p-4 text-center">
                                    <span
                                        :class="[
                                            'rounded px-2 py-0.5 font-mono text-[10px] uppercase',
                                            exp.is_visible ? 'bg-[#22C55E]/10 text-[#22C55E]' : 'bg-[#ffb4ab]/10 text-[#ffb4ab]'
                                        ]"
                                    >
                                        {{ exp.is_visible ? 'Tampil' : 'Sembunyi' }}
                                    </span>
                                </td>
                                <td class="p-4 text-right space-x-3 font-mono">
                                    <button
                                        type="button"
                                        @click="openEdit(exp)"
                                        class="text-xs text-[#818CF8] hover:underline"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        @click="deleteExperience(exp)"
                                        class="text-xs text-[#ffb4ab] hover:underline"
                                    >
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!experiences || experiences.length === 0">
                                <td colspan="5" class="p-8 text-center font-mono text-xs text-[#A4ADB7]">
                                    Belum ada data pengalaman kerja.
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
                            {{ editingExp ? `Edit: ${editingExp.organization}` : 'Tambah Pengalaman Baru' }}
                        </h3>
                        <button
                            v-if="editingExp"
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
                                Organisasi / Perusahaan <span class="text-[#ffb4ab]">*</span>
                            </label>
                            <input
                                v-model="form.organization"
                                type="text"
                                required
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
                            <p v-if="form.errors.organization" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.organization }}</p>
                        </div>

                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                                Posisi / Jabatan <span class="text-[#ffb4ab]">*</span>
                            </label>
                            <input
                                v-model="form.role"
                                type="text"
                                required
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
                            <p v-if="form.errors.role" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.role }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block font-mono text-xs uppercase text-[#A4ADB7]">Tipe Kerja</label>
                                <input
                                    v-model="form.type"
                                    type="text"
                                    placeholder="Full-time / Contract"
                                    class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3 py-2 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                                />
                            </div>
                            <div>
                                <label class="block font-mono text-xs uppercase text-[#A4ADB7]">Lokasi</label>
                                <input
                                    v-model="form.location_mode"
                                    type="text"
                                    placeholder="Remote / Hybrid"
                                    class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3 py-2 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                                    Tanggal Mulai <span class="text-[#ffb4ab]">*</span>
                                </label>
                                <input
                                    v-model="form.start_date"
                                    type="date"
                                    required
                                    class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3 py-2 font-mono text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                                />
                            </div>
                            <div v-if="!form.is_current">
                                <label class="block font-mono text-xs uppercase text-[#A4ADB7]">Tanggal Selesai</label>
                                <input
                                    v-model="form.end_date"
                                    type="date"
                                    class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3 py-2 font-mono text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="flex items-center gap-2 cursor-pointer pt-1">
                                <input
                                    v-model="form.is_current"
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-[#293440] bg-[#101419] text-[#818CF8] focus:ring-0"
                                />
                                <span class="text-xs font-mono text-[#F0F2F3]">Saat ini masih aktif bekerja di sini</span>
                            </label>
                        </div>

                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">Ringkasan Posisi</label>
                            <textarea
                                v-model="form.summary"
                                rows="2"
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
                        </div>

                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                                Tanggung Jawab (1 baris per butir)
                            </label>
                            <textarea
                                v-model="form.responsibilities_text"
                                rows="3"
                                placeholder="Merancang arsitektur database&#10;Mengembangkan REST API&#10;Melakukan code review"
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none font-mono"
                            />
                        </div>

                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                                Capaian Terukur (1 baris per butir)
                            </label>
                            <textarea
                                v-model="form.outcomes_text"
                                rows="2"
                                placeholder="Meningkatkan performa query database 40%"
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none font-mono"
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
                                <span v-else>{{ editingExp ? 'Simpan Perubahan' : 'Buat Pengalaman' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
