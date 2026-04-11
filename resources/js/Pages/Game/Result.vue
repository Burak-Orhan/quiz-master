<template>
    <div class="min-h-screen bg-slate-50 text-slate-800 py-12 px-4 font-sans selection:bg-slate-200">
        <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

            <div v-if="isSinglePlayer" :class="singlePlayerStats.bgColor" class="py-12 text-center transition-all">
                <span :class="singlePlayerStats.textColor"
                    class="font-semibold tracking-widest text-sm uppercase mb-2 block">
                    {{ singlePlayerStats.subtitle }}
                </span>
                <h1 class="text-4xl md:text-5xl font-black text-white">{{ singlePlayerStats.title }}</h1>
            </div>

            <template v-else>
                <div v-if="winner" class="bg-green-500 py-12 text-center transition-all">
                    <span
                        class="text-green-100 font-semibold tracking-widest text-sm uppercase mb-2 block">Şampiyon</span>
                    <h1 class="text-4xl md:text-5xl font-black text-white">{{ winner.name }}</h1>
                </div>
                <div v-else class="bg-blue-500 py-12 text-center transition-all">
                    <span class="text-blue-100 font-semibold tracking-widest text-sm uppercase mb-2 block">Dostluk
                        Kazandı</span>
                    <h1 class="text-4xl md:text-5xl font-black text-white">Beraberlik!</h1>
                </div>
            </template>

            <div class="p-8">
                <div class="grid gap-6 mb-12"
                    :class="isSinglePlayer ? 'grid-cols-1 max-w-xl mx-auto' : 'grid-cols-1 md:grid-cols-2'">
                    <div v-for="(team, index) in sortedTeams" :key="team.id"
                        class="bg-slate-50 border border-slate-100 rounded-xl p-6 text-center relative overflow-hidden group shadow-sm">
                        <div class="absolute top-0 left-0 w-full h-2" :style="{ backgroundColor: team.color }"></div>

                        <h3 class="text-xl font-bold text-slate-700 mt-2">{{ team.name }}</h3>
                        <div class="text-6xl font-black my-4" :style="{ color: team.color }">
                            {{ team.score }}
                        </div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Toplam Puan</span>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-8">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                        <h2 class="text-lg font-bold text-slate-900">📝 Soru Analizi</h2>

                        <div class="flex flex-wrap gap-2">
                            <select v-if="!isSinglePlayer" v-model="filterTeam"
                                class="text-xs font-bold border-slate-200 rounded-lg bg-slate-50 px-2 py-1.5 focus:ring-0 focus:border-slate-400">
                                <option value="all">Tüm Takımlar</option>
                                <option v-for="team in teams" :key="team.id" :value="team.name">{{ team.name }}</option>
                            </select>

                            <div class="flex bg-slate-100 p-1 rounded-lg border border-slate-200">
                                <button @click="filterStatus = 'all'"
                                    :class="filterStatus === 'all' ? 'bg-white shadow-sm text-slate-900' : 'text-slate-500'"
                                    class="px-3 py-1 text-[10px] uppercase tracking-wider font-bold rounded-md transition-all">
                                    Hepsi
                                </button>
                                <button @click="filterStatus = 'correct'"
                                    :class="filterStatus === 'correct' ? 'bg-white shadow-sm text-green-600' : 'text-slate-500'"
                                    class="px-3 py-1 text-[10px] uppercase tracking-wider font-bold rounded-md transition-all">
                                    Doğru
                                </button>
                                <button @click="filterStatus = 'wrong'"
                                    :class="filterStatus === 'wrong' ? 'bg-white shadow-sm text-red-600' : 'text-slate-500'"
                                    class="px-3 py-1 text-[10px] uppercase tracking-wider font-bold rounded-md transition-all">
                                    Yanlış
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div v-for="(item, index) in filteredHistory" :key="index"
                            class="p-5 rounded-xl border transition-all duration-300"
                            :class="item.isCorrect ? 'bg-green-50/50 border-green-100' : 'bg-red-50/50 border-red-100'">

                            <div class="flex justify-between items-start mb-3">
                                <span class="text-sm font-bold text-slate-900">Soru {{ item.questionNumber }}</span>
                                <span v-if="!isSinglePlayer"
                                    class="text-xs font-bold px-2 py-1 rounded bg-white border border-slate-200 text-slate-500">
                                    {{ item.teamName }}
                                </span>
                            </div>
                            <p class="text-slate-700 font-medium mb-4">{{ item.questionText }}</p>

                            <div class="space-y-2 text-sm">
                                <div v-if="item.isCorrect"
                                    class="flex items-center gap-2 text-green-700 font-semibold bg-green-100/50 p-2 rounded-lg">
                                    <span>✅ Doğru:</span> <span>{{ item.options[item.correctKey] }}</span>
                                </div>
                                <div v-else class="flex flex-col gap-2">
                                    <div
                                        class="flex items-center gap-2 text-red-600 font-semibold bg-red-100/50 p-2 rounded-lg">
                                        <span v-if="item.selectedKey === 'TIMEOUT'">⏰ Süre Bitti</span>
                                        <span v-else>❌ Cevap: {{ item.options[item.selectedKey] }}</span>
                                    </div>
                                    <div
                                        class="flex items-center gap-2 text-green-700 font-semibold bg-green-100/50 p-2 rounded-lg">
                                        <span>👉 Doğru:</span> <span>{{ item.options[item.correctKey] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="filteredHistory.length === 0"
                            class="text-center py-10 bg-slate-50 rounded-xl border border-dashed border-slate-200 text-slate-400 text-sm">
                            Bu filtreye uygun soru bulunamadı.
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-8 border-t border-slate-100">
                    <button @click="goHome"
                        class="w-full bg-slate-900 hover:bg-slate-800 text-white py-4 rounded-xl font-medium transition-colors uppercase tracking-widest text-sm">
                        Ana Menüye Dön ve Yeni Oyun Kur
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    teams: Array,
    history: Array
})

const isSinglePlayer = computed(() => {
    return props.teams && props.teams.length === 1;
})

const singlePlayerStats = computed(() => {
    if (!isSinglePlayer.value || !props.history || props.history.length === 0) return null;

    const correctCount = props.history.filter(h => h.isCorrect).length;
    const totalQuestions = props.history.length;
    const successRate = correctCount / totalQuestions;

    if (successRate >= 0.7) {
        return {
            title: 'Başarılı!',
            subtitle: 'Harika bir iş çıkardın',
            bgColor: 'bg-emerald-500',
            textColor: 'text-emerald-100'
        };
    } else if (successRate >= 0.4) {
        return {
            title: 'Fena Değil',
            subtitle: 'Biraz daha pratikle mükemmel olabilirsin',
            bgColor: 'bg-amber-500',
            textColor: 'text-amber-100'
        };
    } else {
        return {
            title: 'Gelişmen Lazım',
            subtitle: 'Daha çok çalışmalısın, pes etme!',
            bgColor: 'bg-rose-500',
            textColor: 'text-rose-100'
        };
    }
})

const filterTeam = ref('all')
const filterStatus = ref('all')

const filteredHistory = computed(() => {
    if (!props.history) return [];

    return props.history.filter(item => {
        const teamMatch = isSinglePlayer.value || filterTeam.value === 'all' || item.teamName === filterTeam.value;

        let statusMatch = false;
        if (filterStatus.value === 'all') {
            statusMatch = true;
        } else if (filterStatus.value === 'correct') {
            statusMatch = item.isCorrect === true;
        } else if (filterStatus.value === 'wrong') {
            statusMatch = item.isCorrect === false;
        }

        return teamMatch && statusMatch;
    });
});

const sortedTeams = computed(() => {
    return [...(props.teams || [])].sort((a, b) => b.score - a.score)
})

const winner = computed(() => {
    if (isSinglePlayer.value) return null;
    if (sortedTeams.value.length < 2) return null;
    if (sortedTeams.value[0].score === sortedTeams.value[1].score) return null;
    return sortedTeams.value[0];
})

const goHome = () => {
    router.visit('/')
}
</script>