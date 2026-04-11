<template>
    <div class="min-h-screen bg-slate-50 py-12 px-4 sm:px-6 lg:px-8 font-sans text-slate-800 pb-20">
        <div class="max-w-4xl mx-auto space-y-8">

            <div class="flex items-center justify-between bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
                <div>
                    <p class="text-slate-400 font-black uppercase tracking-widest text-[10px] mb-1">Sınav Analiz Raporu
                    </p>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ quiz.title }}</h1>
                </div>
                <button @click="goToDashboard"
                    class="px-6 py-3 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition-colors text-sm uppercase tracking-widest">
                    Panele Dön
                </button>
            </div>

            <div v-if="processedAttempts.length === 0"
                class="text-center py-20 bg-white rounded-3xl border border-slate-200 border-dashed">
                <span class="text-4xl mb-4 block">📭</span>
                <h3 class="text-xl font-bold text-slate-700">Henüz Rapor Yok</h3>
                <p class="text-slate-500 mt-2">Bu sınav henüz kimse tarafından çözülmemiş.</p>
            </div>

            <div class="space-y-6">
                <div v-for="(stats, index) in processedAttempts" :key="stats.id"
                    class="bg-white rounded-3xl p-8 shadow-sm border-2 transition-all relative overflow-hidden"
                    :style="{ borderColor: index === 0 ? '#3b82f6' : '#e2e8f0' }">

                    <div class="absolute top-0 right-0 px-6 py-2 rounded-bl-3xl text-white font-black text-sm tracking-widest uppercase shadow-sm"
                        :style="{ backgroundColor: index === 0 ? '#3b82f6' : '#94a3b8' }">
                        {{ index === 0 ? '🏆 1. SIRA (REKOR)' : `${index + 1}. SIRA` }}
                    </div>

                    <div class="flex flex-col md:flex-row gap-8 items-center mt-4">

                        <div class="w-40 h-40 rounded-full flex flex-col items-center justify-center border-8 shrink-0"
                            :class="getPercentageColorClass(stats.successRate)">
                            <span class="text-4xl font-black text-slate-800">%{{ stats.successRate }}</span>
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-widest mt-1">Başarı
                                Oranı</span>
                        </div>

                        <div class="flex-1 w-full space-y-6">

                            <div class="flex items-center gap-2">
                                <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tight">
                                    {{ stats.participant_name }}
                                </h2>

                                <a v-if="stats.user_slug" :href="`/profile/${stats.user_slug}`" target="_blank"
                                    class="group relative inline-flex items-center justify-center text-slate-400 hover:text-indigo-600 transition-all duration-300"
                                    title="Profiline Git">

                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path
                                            d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" />
                                        <path
                                            d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z" />
                                    </svg>

                                    <span
                                        class="absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap">
                                        Profili Gör
                                    </span>
                                </a>
                            </div>

                            <p class="text-slate-500 font-bold text-sm mt-1"
                                :class="getStatusTextColor(stats.successRate)">
                                Değerlendirme: {{ getStatusMessage(stats.successRate) }}
                            </p>

                            <div class="grid grid-cols-3 gap-4">
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-center">
                                    <span class="block text-3xl font-black text-slate-800">{{ stats.score }}</span>
                                    <span
                                        class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">Puan</span>
                                </div>
                                <div class="bg-emerald-50 p-4 rounded-2xl border border-emerald-100 text-center">
                                    <span class="block text-3xl font-black text-emerald-600">{{ stats.correct_answers
                                        }}</span>
                                    <span
                                        class="text-[10px] uppercase font-bold text-emerald-500 tracking-widest">Doğru</span>
                                </div>
                                <div class="bg-red-50 p-4 rounded-2xl border border-red-100 text-center">
                                    <span class="block text-3xl font-black text-red-600">{{ stats.wrong_answers
                                        }}</span>
                                    <span
                                        class="text-[10px] uppercase font-bold text-red-500 tracking-widest">Yanlış</span>
                                </div>
                            </div>

                            <div class="text-right text-xs font-bold text-slate-400 uppercase tracking-widest">
                                Oynanma Tarihi: {{ new Date(stats.created_at).toLocaleDateString('tr-TR') }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    quiz: Object,
    attempts: Array
})

const processedAttempts = computed(() => {
    return props.attempts.map(attempt => {
        const total = attempt.correct_answers + attempt.wrong_answers;
        const successRate = total > 0 ? Math.round((attempt.correct_answers / total) * 100) : 0;

        return {
            ...attempt,
            successRate
        };
    });
})

const getPercentageColorClass = (rate) => {
    if (rate >= 80) return 'border-emerald-500 bg-emerald-50';
    if (rate >= 50) return 'border-amber-400 bg-amber-50';
    return 'border-red-500 bg-red-50';
}

const getStatusTextColor = (rate) => {
    if (rate >= 80) return 'text-emerald-600';
    if (rate >= 50) return 'text-amber-600';
    return 'text-red-600';
}

const getStatusMessage = (rate) => {
    if (rate >= 90) return 'Mükemmel bir performans! Konuya tamamen hakim.';
    if (rate >= 75) return 'Çok başarılı. Ufak tefek eksikler dışında harika.';
    if (rate >= 50) return 'Geçer not. Biraz daha pratik yapması faydalı olur.';
    return 'Geliştirilmeli. Konuyu tekrar gözden geçirmeli.';
}

const goToDashboard = () => {
    router.get('/dashboard');
}
</script>