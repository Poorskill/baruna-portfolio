<script setup lang="ts">
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
}>();

const user = usePage().props.auth.user;

const form = useForm({
    name: user?.name || '',
    email: user?.email || '',
});
</script>

<template>
    <section class="space-y-6">
        <header class="border-b border-[#293440] pb-3">
            <h3 class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">
                01 // Informasi Akun Administrator
            </h3>
            <p class="mt-1 text-xs text-[#A4ADB7]">
                Perbarui nama lengkap dan alamat email yang digunakan untuk autentikasi.
            </p>
        </header>

        <form
            @submit.prevent="form.patch(route('admin.account.update'))"
            class="space-y-5"
        >
            <div>
                <label for="name" class="block font-mono text-xs uppercase text-[#A4ADB7]">
                    Nama Lengkap <span class="text-[#ffb4ab]">*</span>
                </label>
                <input
                    id="name"
                    type="text"
                    class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <p v-if="form.errors.name" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.name }}</p>
            </div>

            <div>
                <label for="email" class="block font-mono text-xs uppercase text-[#A4ADB7]">
                    Alamat Email <span class="text-[#ffb4ab]">*</span>
                </label>
                <input
                    id="email"
                    type="email"
                    class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 font-mono text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <p v-if="form.errors.email" class="mt-1 text-xs text-[#ffb4ab]">{{ form.errors.email }}</p>
            </div>

            <div v-if="mustVerifyEmail && user && user.email_verified_at === null">
                <p class="text-xs text-[#A4ADB7]">
                    Alamat email Anda belum terverifikasi.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="text-xs font-mono text-[#818CF8] underline hover:text-[#c7d2fe]"
                    >
                        Klik di sini untuk mengirim ulang tautan verifikasi.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-xs font-mono text-[#22C55E]"
                >
                    Tautan verifikasi baru telah dikirimkan ke email Anda.
                </div>
            </div>

            <div class="flex items-center gap-4 pt-2">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded bg-[#4F46E5] px-5 py-2 font-mono text-xs font-semibold text-white hover:bg-[#4338CA] transition-colors disabled:opacity-50"
                >
                    <span v-if="form.processing">Menyimpan...</span>
                    <span v-else>Simpan Informasi Akun</span>
                </button>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-xs font-mono text-[#22C55E]"
                    >
                        Tersimpan.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
