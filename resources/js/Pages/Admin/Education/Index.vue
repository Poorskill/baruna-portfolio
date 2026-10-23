<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Education } from '@/types';

defineProps<{
    education: Education[];
}>();

const editingEdu = ref<Education | null>(null);

const form = useForm({
    institution: '',
    qualification: '',
    field_of_study: '',
    start_date: '',
    end_date: '',
    expected_end_date: '',
    status: 'completed' as 'in_progress' | 'completed' | 'dropped',
    description: '',
    sort_order: 0,
    is_visible: true,
});

function openCreate() {
    editingEdu.value = null;
    form.reset();
    form.status = 'completed';
    form.is_visible = true;
    form.sort_order = 0;
}

function openEdit(edu: Education) {
    editingEdu.value = edu;
    form.institution = edu.institution;
    form.qualification = edu.qualification;
    form.field_of_study = edu.field_of_study || '';
    form.start_date = edu.start_date ? edu.start_date.substring(0, 10) : '';
    form.end_date = edu.end_date ? edu.end_date.substring(0, 10) : '';
    form.expected_end_date = edu.expected_end_date ? edu.expected_end_date.substring(0, 10) : '';
    form.status = edu.status;
    form.description = edu.description || '';
    form.sort_order = edu.sort_order;
    form.is_visible = Boolean(edu.is_visible);
}

function submit() {
    const payload = {
        institution: form.institution,
        qualification: form.qualification,
        field_of_study: form.field_of_study || null,
        start_date: form.start_date,
        end_date: form.status === 'in_progress' ? null : (form.end_date || null),
        expected_end_date: form.status === 'in_progress' ? (form.expected_end_date || null) : null,
        status: form.status,
        description: form.description || null,
        sort_order: form.sort_order,
        is_visible: form.is_visible,
    };

    if (editingEdu.value) {
        form.transform(() => payload).put(route('admin.education.update', editingEdu.value.id), {
            onSuccess: () => {
                editingEdu.value = null;
                form.reset();
            },
        });
    } else {
        form.transform(() => payload).post(route('admin.education.store'), {
            onSuccess: () => form.reset(),
        });
    }
}

function deleteEdu(edu: Education) {
    if (confirm(`Hapus riwayat pendidikan di "${edu.institution}"?`)) {
        router.delete(route('admin.education.destroy', edu.id));
    }
}
</script>

<template>
    <Head title="Pendidikan Formal — Admin" />

    <AdminLayout title="Academic Education">
        <div class="grid gap-8 lg:grid-cols-12 max-w-6xl">
            <!-- Table -->
            <div class="space-y-4 lg:col-span-7">
                <div class="border-b border-[#293440] pb-4">
                    <h2 class="text-lg font-semibold text-[#F0F2F3] tracking-tight">Riwayat Pendidikan Formal</h2>
                    <p class="text-xs font-mono text-[#A4ADB7] mt-0.5">Daftar gelar akademik dan institusi pendidikan tinggi.</p>
                </div>

                <div class="overflow-x-auto rounded border border-[#293440] bg-[#151B22]">
                    <table class="w-full text-left text-xs">
                        <thead class="border-b border-[#293440] bg-[#101419] font-mono text-[11px] uppercase text-[#A4ADB7]">
                            <tr>
                                <th class="p-4">Institusi & Kualifikasi</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-center">Urutan</th>
                                <th class="p-4 text-center">Status</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#293440]">
                            <tr
                                v-for="edu in education"
                                :key="edu.id"
                                class="hover:bg-[#1B242E]/50 transition-colors"
                            >
                                <td class="p-4">
                                    <div class="font-medium text-[#F0F2F3]">{{ edu.qualification }}</div>
                                    <div class="text-[11px] text-[#818CF8] font-mono">{{ edu.institution }}</div>
                                    <div v-if="edu.field_of_study" class="font-mono text-[10px] text-[#A4ADB7]">{{ edu.field_of_study }}</div>
                                </td>
                                <td class="p-4 font-mono text-[11px]">
                                    <span
                                        :class="[
                                            'rounded px-2 py-0.5 text-[10px] uppercase font-mono',
                                            edu.status === 'completed' ? 'bg-[#22C55E]/10 text-[#22C55E]' :
                                            edu.status === 'in_progress' ? 'bg-[#E5BC72]/10 text-[#E5BC72]' : 'text-[#A4ADB7]'
                                        ]"
                                    >
                                        {{ edu.status }}
                                    </span>
                                </td>
                                <td class="p-4 text-center font-mono text-[#A4ADB7]">
                                    {{ edu.sort_order }}
                                </td>
                                <td class="p-4 text-center">
                                    <span
                                        :class="[
                                            'rounded px-2 py-0.5 font-mono text-[10px] uppercase',
                                            edu.is_visible ? 'bg-[#22C55E]/10 text-[#22C55E]' : 'bg-[#ffb4ab]/10 text-[#ffb4ab]'
                                        ]"
                                    >
                                        {{ edu.is_visible ? 'Tampil' : 'Sembunyi' }}
                                    </span>
                                </td>
                                <td class="p-4 text-right space-x-3 font-mono">
                                    <button
                                        type="button"
                                        @click="openEdit(edu)"
                                        class="text-xs text-[#818CF8] hover:underline"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        @click="deleteEdu(edu)"
                                        class="text-xs text-[#ffb4ab] hover:underline"
                                    >
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!education || education.length === 0">
                                <td colspan="5" class="p-8 text-center font-mono text-xs text-[#A4ADB7]">
                                    Belum ada data pendidikan.
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
                            {{ editingEdu ? `Edit: ${editingEdu.institution}` : 'Tambah Pendidikan Baru' }}
                        </h3>
                        <button
                            v-if="editingEdu"
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
                                Institusi / Universitas <span class="text-[#ffb4ab]">*</span>
                            </label>
                            <input
                                v-model="form.institution"
                                type="text"
                                required
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
                            <p v-if="form.errors.institution" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.institution }}</p>
                        </div>

                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                                Kualifikasi / Gelar <span class="text-[#ffb4ab]">*</span>
                            </label>
                            <input
                                v-model="form.qualification"
                                type="text"
                                required
                                placeholder="misal: Sarjana Komputer (S.Kom)"
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
                            <p v-if="form.errors.qualification" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.qualification }}</p>
                        </div>

                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">Jurusan / Bidang Studi</label>
                            <input
                                v-model="form.field_of_study"
                                type="text"
                                placeholder="misal: Teknik Informatika"
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
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
                            <div v-if="form.status === 'completed' || form.status === 'dropped'">
                                <label class="block font-mono text-xs uppercase text-[#A4ADB7]">Tanggal Lulus</label>
                                <input
                                    v-model="form.end_date"
                                    type="date"
                                    class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3 py-2 font-mono text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                                />
                            </div>
                            <div v-else>
                                <label class="block font-mono text-xs uppercase text-[#A4ADB7]">Estimasi Lulus</label>
                                <input
                                    v-model="form.expected_end_date"
                                    type="date"
                                    class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3 py-2 font-mono text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                                Status Studi <span class="text-[#ffb4ab]">*</span>
                            </label>
                            <select
                                v-model="form.status"
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3 py-2 font-mono text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            >
                                <option value="completed">Completed (Lulus)</option>
                                <option value="in_progress">In Progress (Sedang Berjalan)</option>
                                <option value="dropped">Dropped</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">Keterangan Akademik</label>
                            <textarea
                                v-model="form.description"
                                rows="2"
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
                                <span v-else>{{ editingEdu ? 'Simpan Perubahan' : 'Buat Pendidikan' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
