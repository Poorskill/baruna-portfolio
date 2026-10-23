<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref<HTMLInputElement | null>(null);
const currentPasswordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value?.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value?.focus();
            }
        },
    });
};
</script>

<template>
    <section class="space-y-6">
        <header class="border-b border-[#293440] pb-3">
            <h3 class="font-mono text-xs uppercase tracking-wider text-[#818CF8]">
                02 // Perbarui Kata Sandi
            </h3>
            <p class="mt-1 text-xs text-[#A4ADB7]">
                Gunakan kata sandi yang panjang dan unik untuk menjaga keamanan akun portofolio.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="space-y-5">
            <div>
                <label for="current_password" class="block font-mono text-xs uppercase text-[#A4ADB7]">
                    Kata Sandi Saat Ini <span class="text-[#ffb4ab]">*</span>
                </label>
                <input
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 font-mono text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                    autocomplete="current-password"
                />
                <p v-if="form.errors.current_password" class="mt-1 text-xs text-[#ffb4ab]">
                    {{ form.errors.current_password }}
                </p>
            </div>

            <div>
                <label for="password" class="block font-mono text-xs uppercase text-[#A4ADB7]">
                    Kata Sandi Baru <span class="text-[#ffb4ab]">*</span>
                </label>
                <input
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 font-mono text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                    autocomplete="new-password"
                />
                <p v-if="form.errors.password" class="mt-1 text-xs text-[#ffb4ab]">
                    {{ form.errors.password }}
                </p>
            </div>

            <div>
                <label for="password_confirmation" class="block font-mono text-xs uppercase text-[#A4ADB7]">
                    Konfirmasi Kata Sandi Baru <span class="text-[#ffb4ab]">*</span>
                </label>
                <input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 font-mono text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                    autocomplete="new-password"
                />
                <p v-if="form.errors.password_confirmation" class="mt-1 text-xs text-[#ffb4ab]">
                    {{ form.errors.password_confirmation }}
                </p>
            </div>

            <div class="flex items-center gap-4 pt-2">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded bg-[#4F46E5] px-5 py-2 font-mono text-xs font-semibold text-white hover:bg-[#4338CA] transition-colors disabled:opacity-50"
                >
                    <span v-if="form.processing">Menyimpan...</span>
                    <span v-else>Perbarui Kata Sandi</span>
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
                        Kata sandi berhasil diperbarui.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
