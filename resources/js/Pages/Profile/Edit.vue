<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
}>();
</script>

<template>
    <Head title="Kredensial Akun — Admin" />

    <AdminLayout title="Account & Security">
        <div class="max-w-4xl space-y-8">
            <div class="border-b border-[#293440] pb-6">
                <h2 class="text-xl font-semibold text-[#F0F2F3] tracking-tight">Kredensial & Keamanan Akun</h2>
                <p class="text-xs font-mono text-[#A4ADB7] mt-0.5">
                    Pengaturan identitas otentikasi administrator dan pembaruan kata sandi.
                </p>
            </div>

            <!-- Profile Info Form -->
            <div class="rounded border border-[#293440] bg-[#151B22] p-6 sm:p-8">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="max-w-xl"
                />
            </div>

            <!-- Password Form -->
            <div class="rounded border border-[#293440] bg-[#151B22] p-6 sm:p-8">
                <UpdatePasswordForm class="max-w-xl" />
            </div>

            <!-- Delete Form -->
            <div class="rounded border border-[#293440] bg-[#151B22] p-6 sm:p-8">
                <DeleteUserForm class="max-w-xl" />
            </div>
        </div>
    </AdminLayout>
</template>
