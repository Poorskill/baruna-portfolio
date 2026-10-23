<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Login Administrator" />

        <div v-if="status" class="mb-4 text-xs font-mono text-[#22C55E]">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <label for="email" class="block font-mono text-xs uppercase text-[#A4ADB7]">
                    Email Administrator <span class="text-[#ffb4ab]">*</span>
                </label>
                <input
                    id="email"
                    type="email"
                    class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 font-mono text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />
                <p v-if="form.errors.email" class="mt-1 text-xs text-[#ffb4ab] font-mono">
                    {{ form.errors.email }}
                </p>
            </div>

            <div>
                <label for="password" class="block font-mono text-xs uppercase text-[#A4ADB7]">
                    Kata Sandi <span class="text-[#ffb4ab]">*</span>
                </label>
                <input
                    id="password"
                    type="password"
                    class="mt-1.5 w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2.5 font-mono text-xs text-[#F0F2F3] focus:border-[#818CF8] focus:outline-none"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />
                <p v-if="form.errors.password" class="mt-1 text-xs text-[#ffb4ab] font-mono">
                    {{ form.errors.password }}
                </p>
            </div>

            <div class="flex items-center justify-between text-xs font-mono pt-1">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input
                        type="checkbox"
                        v-model="form.remember"
                        class="h-4 w-4 rounded border-[#293440] bg-[#101419] text-[#818CF8] focus:ring-0"
                    />
                    <span class="text-[#A4ADB7]">Ingat sesi ini</span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-[#A4ADB7] hover:text-[#818CF8] underline transition-colors"
                >
                    Lupa password?
                </Link>
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded bg-[#4F46E5] py-2.5 font-mono text-xs font-semibold text-white hover:bg-[#4338CA] transition-colors disabled:opacity-50 focus:outline-none"
                >
                    <span v-if="form.processing">Mengautentikasi...</span>
                    <span v-else>Masuk ke Cockpit Admin &rarr;</span>
                </button>
            </div>
        </form>
    </GuestLayout>
</template>
