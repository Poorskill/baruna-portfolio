<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Certificate } from '@/types';

defineProps<{
    certificates: Certificate[];
}>();

const editingCert = ref<Certificate | null>(null);

const form = useForm({
    name: '',
    issuer: '',
    issued_at: '',
    credential_url: '',
    credential_id: '',
    image: null as File | null,
    sort_order: 0,
    is_visible: true,
});

function openCreate() {
    editingCert.value = null;
    form.reset();
    form.is_visible = true;
    form.sort_order = 0;
}

function openEdit(cert: Certificate) {
    editingCert.value = cert;
    form.name = cert.name;
    form.issuer = cert.issuer;
    form.issued_at = cert.issued_at ? cert.issued_at.substring(0, 10) : '';
    form.credential_url = cert.credential_url || '';
    form.credential_id = cert.credential_id || '';
    form.image = null;
    form.sort_order = cert.sort_order;
    form.is_visible = Boolean(cert.is_visible);
}

function handleImageChange(e: Event) {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.image = target.files[0];
    }
}

function submit() {
    if (editingCert.value) {
        form.transform((data) => ({
            ...data,
            _method: 'PUT',
        })).post(route('admin.certificates.update', editingCert.value.id), {
            forceFormData: true,
            onSuccess: () => {
                editingCert.value = null;
                form.reset();
            },
        });
    } else {
        form.post(route('admin.certificates.store'), {
            forceFormData: true,
            onSuccess: () => form.reset(),
        });
    }
}

function deleteCert(cert: Certificate) {
    if (confirm(`Hapus sertifikat "${cert.name}"?`)) {
        router.delete(route('admin.certificates.destroy', cert.id));
    }
}
</script>

<template>
    <Head title="Sertifikasi Terverifikasi — Admin" />

    <AdminLayout title="Verified Certificates">
        <div class="grid gap-8 lg:grid-cols-12 max-w-6xl">
            <!-- Table -->
            <div class="space-y-4 lg:col-span-7">
                <div class="border-b border-[#293440] pb-4">
                    <h2 class="text-lg font-semibold text-[#F0F2F3] tracking-tight">Sertifikat & Kredensial</h2>
                    <p class="text-xs font-mono text-[#A4ADB7] mt-0.5">Daftar sertifikasi resmi dengan verifikasi URL.</p>
                </div>

                <div class="overflow-x-auto rounded border border-[#293440] bg-[#151B22]">
                    <table class="w-full text-left text-xs">
                        <thead class="border-b border-[#293440] bg-[#101419] font-mono text-[11px] uppercase text-[#A4ADB7]">
                            <tr>
                                <th class="p-4">Nama Sertifikat</th>
                                <th class="p-4">Penerbit</th>
                                <th class="p-4 text-center">Urutan</th>
                                <th class="p-4 text-center">Status</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#293440]">
                            <tr
                                v-for="cert in certificates"
                                :key="cert.id"
                                class="hover:bg-[#1B242E]/50 transition-colors"
                            >
                                <td class="p-4">
                                    <div class="font-medium text-[#F0F2F3]">{{ cert.name }}</div>
                                    <div v-if="cert.credential_id" class="font-mono text-[10px] text-[#A4ADB7]">ID: {{ cert.credential_id }}</div>
                                </td>
                                <td class="p-4 font-mono text-[11px] text-[#818CF8]">
                                    {{ cert.issuer }}
                                </td>
                                <td class="p-4 text-center font-mono text-[#A4ADB7]">
                                    {{ cert.sort_order }}
                                </td>
                                <td class="p-4 text-center">
                                    <span
                                        :class="[
                                            'rounded px-2 py-0.5 font-mono text-[10px] uppercase',
                                            cert.is_visible ? 'bg-[#22C55E]/10 text-[#22C55E]' : 'bg-[#ffb4ab]/10 text-[#ffb4ab]'
                                        ]"
                                    >
                                        {{ cert.is_visible ? 'Tampil' : 'Sembunyi' }}
                                    </span>
                                </td>
                                <td class="p-4 text-right space-x-3 font-mono">
                                    <button
                                        type="button"
                                        @click="openEdit(cert)"
                                        class="text-xs text-[#818CF8] hover:underline"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        @click="deleteCert(cert)"
                                        class="text-xs text-[#ffb4ab] hover:underline"
                                    >
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!certificates || certificates.length === 0">
                                <td colspan="5" class="p-8 text-center font-mono text-xs text-[#A4ADB7]">
                                    Belum ada sertifikat terverifikasi.
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
                            {{ editingCert ? `Edit: ${editingCert.name}` : 'Tambah Sertifikat Baru' }}
                        </h3>
                        <button
                            v-if="editingCert"
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
                                Nama Sertifikat / Ujian <span class="text-[#ffb4ab]">*</span>
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                required
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">
                                Lembaga Penerbit <span class="text-[#ffb4ab]">*</span>
                            </label>
                            <input
                                v-model="form.issuer"
                                type="text"
                                required
                                placeholder="misal: AWS, Google, Oracle"
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
                            <p v-if="form.errors.issuer" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.issuer }}</p>
                        </div>

                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">Tanggal Penerbitan</label>
                            <input
                                v-model="form.issued_at"
                                type="date"
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2 font-mono text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
                        </div>

                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">Credential ID</label>
                            <input
                                v-model="form.credential_id"
                                type="text"
                                placeholder="Nomor seri sertifikat"
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2 font-mono text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
                        </div>

                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">Tautan Verifikasi (URL)</label>
                            <input
                                v-model="form.credential_url"
                                type="url"
                                placeholder="https://..."
                                class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2 font-mono text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                            />
                        </div>

                        <div>
                            <label class="block font-mono text-xs uppercase text-[#A4ADB7]">Berkas Gambar Sertifikat</label>
                            <input
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                                @change="handleImageChange"
                                class="mt-1.5 block w-full text-xs text-[#A4ADB7] file:mr-4 file:rounded file:border-0 file:bg-[#101419] file:px-3 file:py-1.5 file:font-mono file:text-xs file:text-[#818CF8]"
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
                                <span v-else>{{ editingCert ? 'Simpan Perubahan' : 'Buat Sertifikat' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
