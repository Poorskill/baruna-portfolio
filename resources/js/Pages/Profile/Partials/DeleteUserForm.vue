<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';
import Modal from '@/Components/Modal.vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value?.focus());
};

const deleteUser = () => {
    form.delete(route('admin.account.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => {
            form.reset();
        },
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header class="border-b border-[#293440] pb-3">
            <h3 class="font-mono text-xs uppercase tracking-wider text-[#ffb4ab]">
                03 // Penghapusan Akun
            </h3>
            <p class="mt-1 text-xs text-[#A4ADB7]">
                Setelah akun dihapus, seluruh sumber daya dan data terkait akan dihapus secara permanen.
            </p>
        </header>

        <div>
            <button
                type="button"
                @click="confirmUserDeletion"
                class="rounded border border-[#ffb4ab]/40 bg-[#1B242E] px-4 py-2 font-mono text-xs text-[#ffb4ab] hover:border-[#ffb4ab] transition-colors"
            >
                Hapus Akun Administrator
            </button>
        </div>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6 bg-[#151B22] border border-[#293440] rounded text-[#F0F2F3] space-y-4">
                <h2 class="text-base font-semibold text-[#ffb4ab]">
                    Konfirmasi Penghapusan Akun
                </h2>

                <p class="text-xs text-[#A4ADB7] leading-relaxed">
                    Tindakan ini tidak dapat dibatalkan. Masukkan kata sandi administrator Anda untuk mengonfirmasi penghapusan permanen akun ini.
                </p>

                <div class="space-y-2">
                    <label for="delete_password" class="sr-only">Kata Sandi</label>
                    <input
                        id="delete_password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="w-full rounded border border-[#293440] bg-[#101419] px-3.5 py-2 font-mono text-xs text-[#F0F2F3] focus:border-[#ffb4ab] focus:outline-none"
                        placeholder="Kata Sandi Administrator"
                        @keyup.enter="deleteUser"
                    />
                    <p v-if="form.errors.password" class="text-xs text-[#ffb4ab] font-mono">
                        {{ form.errors.password }}
                    </p>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-[#293440]">
                    <button
                        type="button"
                        @click="closeModal"
                        class="rounded border border-[#293440] bg-[#101419] px-4 py-2 font-mono text-xs text-[#A4ADB7] hover:text-[#F0F2F3]"
                    >
                        Batalkan
                    </button>

                    <button
                        type="button"
                        @click="deleteUser"
                        :disabled="form.processing"
                        class="rounded bg-[#ffb4ab] px-4 py-2 font-mono text-xs font-semibold text-[#0B0F14] hover:bg-[#ffdad6] transition-colors disabled:opacity-50"
                    >
                        Ya, Hapus Akun Ini
                    </button>
                </div>
            </div>
        </Modal>
    </section>
</template>
