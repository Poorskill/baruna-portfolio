<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps<{
    stats: {
        totalProjects: number;
        publishedProjects: number;
        draftProjects: number;
        totalSkills: number;
        unreadMessages: number;
    };
}>();
</script>

<template>
    <Head title="Dashboard Overview — Admin" />

    <AdminLayout title="System Overview">
        <div class="space-y-8 max-w-6xl">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 border-b border-[#293440] pb-6">
                <div>
                    <span class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">Ringkasan Operasional</span>
                    <h2 class="mt-1 text-2xl font-semibold text-[#F0F2F3] tracking-tight">Status Portofolio & Konten</h2>
                    <p class="mt-1 text-xs text-[#A4ADB7]">
                        Metrik riil yang dihimpun langsung dari database produksi tanpa angka dekoratif.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <Link
                        :href="route('admin.projects.create')"
                        class="inline-flex items-center gap-1.5 rounded bg-[#4F46E5] px-4 py-2 font-mono text-xs font-semibold text-white hover:bg-[#4338CA] transition-colors"
                    >
                        <span>+ Tambah Proyek Baru</span>
                    </Link>
                </div>
            </div>

            <!-- KPI Row: Dense & Precise -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded border border-[#293440] bg-[#151B22] p-5 space-y-2">
                    <span class="font-mono text-[10px] uppercase tracking-wider text-[#A4ADB7]">Total Proyek</span>
                    <p class="text-3xl font-semibold text-[#F0F2F3] tracking-tight">{{ stats.totalProjects }}</p>
                    <div class="flex items-center gap-3 font-mono text-xs pt-1 border-t border-[#293440]/60">
                        <span class="text-[#22C55E]">{{ stats.publishedProjects }} terbit</span>
                        <span class="text-[#A4ADB7]/40">&bull;</span>
                        <span class="text-[#E5BC72]">{{ stats.draftProjects }} draft</span>
                    </div>
                </div>

                <div class="rounded border border-[#293440] bg-[#151B22] p-5 space-y-2">
                    <span class="font-mono text-[10px] uppercase tracking-wider text-[#A4ADB7]">Keahlian Terdaftar</span>
                    <p class="text-3xl font-semibold text-[#F0F2F3] tracking-tight">{{ stats.totalSkills }}</p>
                    <div class="font-mono text-xs text-[#A4ADB7] pt-1 border-t border-[#293440]/60">
                        <span>Aktif di katalog teknologi</span>
                    </div>
                </div>

                <div class="rounded border border-[#293440] bg-[#151B22] p-5 space-y-2">
                    <span class="font-mono text-[10px] uppercase tracking-wider text-[#A4ADB7]">Pesan Masuk Baru</span>
                    <p class="text-3xl font-semibold tracking-tight" :class="stats.unreadMessages > 0 ? 'text-[#818CF8]' : 'text-[#F0F2F3]'">
                        {{ stats.unreadMessages }}
                    </p>
                    <div class="pt-1 border-t border-[#293440]/60">
                        <Link
                            :href="route('admin.messages.index')"
                            class="font-mono text-xs text-[#818CF8] hover:underline"
                        >
                            Buka Kotak Masuk &rarr;
                        </Link>
                    </div>
                </div>

                <div class="rounded border border-[#293440] bg-[#151B22] p-5 space-y-2 flex flex-col justify-between">
                    <div>
                        <span class="font-mono text-[10px] uppercase tracking-wider text-[#A4ADB7]">Integritas Data</span>
                        <p class="text-xs text-[#22C55E] font-mono mt-1">&check; Database sinkron</p>
                    </div>
                    <div class="pt-1 border-t border-[#293440]/60">
                        <Link
                            :href="route('admin.activity.index')"
                            class="font-mono text-xs text-[#A4ADB7] hover:text-[#F0F2F3]"
                        >
                            Lihat Audit Log &rarr;
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Quick Navigation Modules -->
            <div class="grid gap-6 md:grid-cols-2">
                <!-- Content Management -->
                <div class="rounded border border-[#293440] bg-[#151B22] p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-[#293440] pb-3">
                        <h3 class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">
                            Manajemen Konten Publik
                        </h3>
                        <span class="font-mono text-[10px] text-[#A4ADB7]">01</span>
                    </div>

                    <div class="space-y-2 text-xs">
                        <Link
                            :href="route('admin.projects.index')"
                            class="flex items-center justify-between rounded border border-[#293440] bg-[#101419] p-3 text-[#F0F2F3] hover:border-[#818CF8] transition-colors"
                        >
                            <div>
                                <span class="font-medium block">Katalog Proyek & Studi Kasus</span>
                                <span class="font-mono text-[11px] text-[#A4ADB7]">Kelola ringkasan, peran, solusi, galeri, dan slug</span>
                            </div>
                            <span class="font-mono text-[#818CF8]">&rarr;</span>
                        </Link>

                        <Link
                            :href="route('admin.content.home.edit')"
                            class="flex items-center justify-between rounded border border-[#293440] bg-[#101419] p-3 text-[#F0F2F3] hover:border-[#818CF8] transition-colors"
                        >
                            <div>
                                <span class="font-medium block">Editor Halaman Utama (Home)</span>
                                <span class="font-mono text-[11px] text-[#A4ADB7]">Headline hero, deskripsi, dan tombol CTA</span>
                            </div>
                            <span class="font-mono text-[#818CF8]">&rarr;</span>
                        </Link>

                        <Link
                            :href="route('admin.content.profile.edit')"
                            class="flex items-center justify-between rounded border border-[#293440] bg-[#101419] p-3 text-[#F0F2F3] hover:border-[#818CF8] transition-colors"
                        >
                            <div>
                                <span class="font-medium block">Editor Profil & Berkas CV</span>
                                <span class="font-mono text-[11px] text-[#A4ADB7]">Biografi lengkap dan upload berkas PDF resmi</span>
                            </div>
                            <span class="font-mono text-[#818CF8]">&rarr;</span>
                        </Link>
                    </div>
                </div>

                <!-- Structured Bio & Credentials -->
                <div class="rounded border border-[#293440] bg-[#151B22] p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-[#293440] pb-3">
                        <h3 class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">
                            Riwayat & Konfigurasi Sistem
                        </h3>
                        <span class="font-mono text-[10px] text-[#A4ADB7]">02</span>
                    </div>

                    <div class="space-y-2 text-xs">
                        <Link
                            :href="route('admin.experience.index')"
                            class="flex items-center justify-between rounded border border-[#293440] bg-[#101419] p-3 text-[#F0F2F3] hover:border-[#818CF8] transition-colors"
                        >
                            <div>
                                <span class="font-medium block">Pengalaman Karir</span>
                                <span class="font-mono text-[11px] text-[#A4ADB7]">Organisasi, tanggal, tanggung jawab, dan capaian terukur</span>
                            </div>
                            <span class="font-mono text-[#818CF8]">&rarr;</span>
                        </Link>

                        <Link
                            :href="route('admin.education.index')"
                            class="flex items-center justify-between rounded border border-[#293440] bg-[#101419] p-3 text-[#F0F2F3] hover:border-[#818CF8] transition-colors"
                        >
                            <div>
                                <span class="font-medium block">Riwayat Pendidikan</span>
                                <span class="font-mono text-[11px] text-[#A4ADB7]">Gelar, institusi formal, dan status kelulusan</span>
                            </div>
                            <span class="font-mono text-[#818CF8]">&rarr;</span>
                        </Link>

                        <Link
                            :href="route('admin.settings.edit')"
                            class="flex items-center justify-between rounded border border-[#293440] bg-[#101419] p-3 text-[#F0F2F3] hover:border-[#818CF8] transition-colors"
                        >
                            <div>
                                <span class="font-medium block">Pengaturan Global & SEO</span>
                                <span class="font-mono text-[11px] text-[#A4ADB7]">Identitas situs, meta description default, dan kontak</span>
                            </div>
                            <span class="font-mono text-[#818CF8]">&rarr;</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
