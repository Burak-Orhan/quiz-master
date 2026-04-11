<template>
    <div class="min-h-screen flex items-center justify-center bg-slate-50 p-4 font-sans">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Quiz Master</h1>
                <p class="text-slate-500 text-sm mt-2">Sınavlarını yönetmek için giriş yap.</p>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8">
                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label
                            class="block text-[10px] uppercase tracking-[0.2em] font-bold text-slate-400 mb-1.5 ml-1">E-Posta
                            Adresi</label>
                        <input type="email" v-model="form.email"
                            :class="{ 'border-red-400 ring-red-100': form.errors.email }"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition-all">
                        <p v-if="form.errors.email" class="text-[10px] text-red-500 font-bold mt-1 ml-1">{{
                            form.errors.email }}</p>
                    </div>

                    <div>
                        <label
                            class="block text-[10px] uppercase tracking-[0.2em] font-bold text-slate-400 mb-1.5 ml-1">Şifre</label>
                        <input type="password" v-model="form.password"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition-all">
                    </div>

                    <button type="submit" :disabled="form.processing"
                        class="w-full bg-slate-900 hover:bg-slate-800 text-white py-4 rounded-2xl font-bold text-sm transition-all active:scale-[0.98] disabled:opacity-50">
                        {{ form.processing ? 'GİRİŞ YAPILIYOR...' : 'SİSTEME GİR' }}
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                    <p class="text-xs text-slate-500 font-medium">
                        Henüz bir hesabın yok mu?
                        <a href="/register" class="text-slate-900 font-bold hover:underline ml-1">Hemen Kayıt Ol</a>
                    </p>
                </div>
            </div>

            <div class="text-center mt-6">
                <a href="/"
                    class="text-[10px] uppercase tracking-widest font-bold text-slate-400 hover:text-slate-600 transition-colors">
                    ← Ana Sayfaya Dön
                </a>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

const form = useForm({
    email: '',
    password: '',
})

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    })
}
</script>