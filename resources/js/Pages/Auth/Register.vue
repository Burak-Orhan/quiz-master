<template>
    <div class="min-h-screen flex items-center justify-center bg-slate-50 p-4 font-sans">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Quiz Master</h1>
                <p class="text-slate-500 text-sm mt-2">Kendi sınavlarını oluşturmaya başla.</p>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8">
                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label
                            class="block text-[10px] uppercase tracking-[0.2em] font-bold text-slate-400 mb-1.5 ml-1">Ad
                            Soyad</label>
                        <input type="text" v-model="form.name"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition-all">
                        <p v-if="form.errors.name" class="text-[10px] text-red-500 font-bold mt-1 ml-1">{{
                            form.errors.name }}</p>
                    </div>

                    <div>
                        <label
                            class="block text-[10px] uppercase tracking-[0.2em] font-bold text-slate-400 mb-1.5 ml-1">E-Posta</label>
                        <input type="email" v-model="form.email"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition-all">
                        <p v-if="form.errors.email" class="text-[10px] text-red-500 font-bold mt-1 ml-1">{{
                            form.errors.email }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-[10px] uppercase tracking-[0.2em] font-bold text-slate-400 mb-1.5 ml-1">Şifre</label>
                            <input type="password" v-model="form.password"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition-all">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] uppercase tracking-[0.2em] font-bold text-slate-400 mb-1.5 ml-1">Şifre
                                Tekrarı</label>
                            <input type="password" v-model="form.password_confirmation"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition-all">
                        </div>
                    </div>
                    <p v-if="form.errors.password" class="text-[10px] text-red-500 font-bold mt-1 ml-1">{{
                        form.errors.password }}</p>

                    <button type="submit" :disabled="form.processing"
                        class="w-full bg-slate-900 hover:bg-slate-800 text-white py-4 rounded-2xl font-bold text-sm transition-all active:scale-[0.98]">
                        {{ form.processing ? 'HESAP OLUŞTURULUYOR...' : 'HEMEN KAYIT OL' }}
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                    <p class="text-xs text-slate-500 font-medium">
                        Zaten üye misin?
                        <a href="/login" class="text-slate-900 font-bold hover:underline ml-1">Giriş Yap</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}
</script>