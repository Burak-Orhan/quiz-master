<template>
    <div class="min-h-screen bg-slate-50 text-slate-800 font-sans flex flex-col selection:bg-slate-200 pb-40">

        <ConfirmModal />

        <header
            class="bg-white border-b border-slate-200 px-8 py-6 flex justify-between items-center shrink-0 shadow-sm sticky top-0 z-20">
            <div class="flex items-center gap-4">
                <div
                    class="w-14 h-14 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-black text-xl shadow-md">
                    {{ currentQuestionIndex + 1 }}
                </div>
                <div>
                    <span
                        class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 block mb-1">İlerleme</span>
                    <span class="text-lg font-bold text-slate-900">Soru {{ currentQuestionIndex + 1 }} / {{
                        questions.length }}</span>
                </div>
            </div>

            <div class="hidden md:flex flex-col items-center px-10 py-3 bg-slate-50 rounded-2xl border-2 transition-colors duration-500"
                :style="{ borderColor: teams[currentTurn].color + '40', backgroundColor: teams[currentTurn].color + '05' }">
                <span class="text-[11px] text-slate-400 font-black uppercase tracking-[0.2em] mb-1">Hamle Sırası</span>
                <span class="text-xl font-black tracking-widest uppercase" :style="{ color: teams[currentTurn].color }">
                    {{ teams[currentTurn].name }}
                </span>
            </div>

            <div class="flex items-center gap-6">
                <div class="flex flex-col items-end">
                    <span class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 mb-1">Kalan
                        Süre</span>
                    <span class="text-4xl font-black tabular-nums transition-colors"
                        :class="timeLeft <= 5 ? 'text-red-500 animate-pulse' : 'text-slate-900'">
                        {{ timeLeft }}<span class="text-xl text-slate-400 ml-1">s</span>
                    </span>
                </div>

                <div class="w-px h-12 bg-slate-200 hidden sm:block"></div>

                <button @click="exitToLobby" class="group flex flex-col items-center" title="Lobiye Dön">
                    <div
                        class="w-14 h-14 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center group-hover:bg-red-500 group-hover:text-white transition-all shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </div>
                </button>
            </div>
        </header>

        <div class="w-full h-2 bg-slate-100">
            <div class="h-full transition-all duration-1000 ease-linear shadow-sm"
                :style="{ width: timeProgress + '%', backgroundColor: teams[currentTurn].color }"></div>
        </div>

        <main class="flex-1 flex flex-col items-center justify-center p-8 max-w-5xl mx-auto w-full mt-4">

            <div v-if="currentQuestion.image" class="w-full flex justify-center mb-8">
                <div
                    class="relative max-w-3xl w-full h-64 md:h-96 bg-slate-50/50 rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                    <img :src="`/storage/${currentQuestion.image}`" class="w-full h-full object-contain p-3"
                        alt="Soru Görseli">
                </div>
            </div>

            <div
                class="w-full bg-white p-12 rounded-3xl border border-slate-200 mb-10 text-center shadow-sm relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1.5 opacity-30 transition-colors duration-500"
                    :style="{ backgroundColor: teams[currentTurn].color }"></div>

                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 leading-tight">
                    {{ currentQuestion.text }}
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full">
                <button v-for="(option, key) in currentQuestion.options" :key="key" @click="selectOption(key)"
                    :disabled="isAnswered" :class="[
                        'p-8 text-left border-2 rounded-3xl text-xl font-bold transition-all duration-300 flex items-center gap-6 group',
                        getOptionClass(key)
                    ]">
                    <span
                        class="w-14 h-14 flex items-center justify-center rounded-2xl font-black text-xl shrink-0 transition-colors shadow-sm"
                        :class="isAnswered ? 'bg-white/20' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-900 group-hover:text-white'">
                        {{ key }}
                    </span>
                    <span class="flex-1 leading-snug">{{ option }}</span>
                </button>
            </div>

            <div class="mt-10 h-20 flex items-center justify-center">
                <button v-if="isAnswered && !settings.autoPass" @click="executeNextTurn"
                    class="group flex items-center gap-4 px-10 py-5 bg-slate-900 text-white rounded-3xl text-lg font-black uppercase tracking-widest shadow-xl shadow-slate-900/20 transition-all hover:bg-slate-800 active:scale-95">
                    <span>Sonraki Soruya Geç</span>
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                            d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

        </main>

        <footer
            class="fixed bottom-0 left-0 right-0 bg-slate-100 border-t border-slate-300 py-6 px-4 z-30 shadow-[0_-10px_30px_rgba(0,0,0,0.05)]">

            <div class="max-w-7xl mx-auto flex items-end justify-center gap-2 sm:gap-4 px-2">

                <div v-for="(team, index) in teams" :key="team.id"
                    class="flex flex-col bg-white rounded-2xl transition-all duration-300 relative flex-1 min-w-[80px] max-w-[220px]"
                    :class="currentTurn === index ? 'scale-110 shadow-2xl z-10 border-4 -translate-y-2' : 'scale-95 opacity-70 border-2 border-slate-200 shadow-sm'"
                    :style="{
                        borderColor: currentTurn === index ? team.color : '#e2e8f0'
                    }">

                    <div class="h-3 w-full rounded-t-lg transition-colors duration-300"
                        :style="{ backgroundColor: team.color }"></div>

                    <div class="p-3 sm:p-5 flex flex-col items-center text-center gap-1 sm:gap-2">
                        <div class="text-3xl sm:text-5xl font-black tabular-nums leading-none transition-colors duration-300"
                            :style="{ color: currentTurn === index ? team.color : '#1e293b' }">
                            {{ team.score }}
                        </div>

                        <div class="w-full mt-1">
                            <p
                                class="hidden sm:block text-[9px] sm:text-[11px] font-black uppercase tracking-widest text-slate-400 mb-0.5">
                                Takım</p>
                            <p
                                class="text-xs sm:text-base font-bold text-slate-900 leading-tight line-clamp-2 break-words">
                                {{ team.name }}
                            </p>
                        </div>
                    </div>

                    <div class="px-3 pb-3 flex items-center justify-center gap-1 w-full">
                        <div v-for="i in 5" :key="i" class="h-1.5 flex-1 rounded-full transition-colors duration-300"
                            :class="getHistoryColor(team.recentAnswers[i - 1])">
                        </div>
                    </div>

                </div>

            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import ConfirmModal from '../../Components/ConfirmModal.vue'
import { useConfirm } from '../../Composables/useConfirm'

const confirm = useConfirm()

const props = defineProps({
    quiz_id: Number,
    settings: Object,
    teams: Array,
    questions: Array
})

const settings = props.settings
const questions = props.questions

const teams = ref(props.teams.map(t => ({
    ...t,
    recentAnswers: []
})))

const currentQuestionIndex = ref(0)
const currentTurn = ref(0)
const timeLeft = ref(settings.timeLimit)
const isAnswered = ref(false)
const selectedAnswer = ref(null)

const matchHistory = ref([])
let timer = null
let nextTurnTimeout = null

const currentQuestion = computed(() => questions[currentQuestionIndex.value])
const timeProgress = computed(() => (timeLeft.value / settings.timeLimit) * 100)

const startTimer = (resume = false) => {
    if (!resume) {
        timeLeft.value = settings.timeLimit;
    }

    clearInterval(timer);
    timer = setInterval(() => {
        if (timeLeft.value > 0) {
            timeLeft.value--;
        } else {
            handleTimeUp();
        }
    }, 1000);
};

const selectOption = (key) => {
    if (isAnswered.value) return
    clearInterval(timer)
    isAnswered.value = true
    selectedAnswer.value = key

    const isCorrect = key === currentQuestion.value.correct
    if (isCorrect) {
        teams.value[currentTurn.value].score += settings.correctPoints
    } else {
        teams.value[currentTurn.value].score -= settings.wrongPoints
    }

    updateTeamHistory(isCorrect)
    recordHistory(isCorrect)

    if (settings.autoPass) {
        moveToNextWithDelay()
    }
}

const handleTimeUp = () => {
    clearInterval(timer)
    isAnswered.value = true
    selectedAnswer.value = 'TIMEOUT'
    teams.value[currentTurn.value].score -= settings.wrongPoints
    updateTeamHistory(false)
    recordHistory(false)

    if (settings.autoPass) {
        moveToNextWithDelay()
    }
}

const exitToLobby = () => {

    clearInterval(timer);

    confirm.requireConfirm({
        title: 'Oyundan Çıkış',
        message: `Oyunu yarıda (${currentQuestionIndex.value + 1}. soruda) kesip lobiye dönmek istediğine emin misin? Mevcut puanların kaydedilmeyecek.`,
        type: 'info',
        confirmText: 'Evet, Çık',
        cancelText: "Oyuna Dön",
        onConfirm: () => {
            clearTimeout(nextTurnTimeout)
            router.get('/')
        },
        onCancel: () => {
            startTimer(true);
        }
    })
}

const updateTeamHistory = (result) => {
    const team = teams.value[currentTurn.value]
    team.recentAnswers.push(result)
    if (team.recentAnswers.length > 5) {
        team.recentAnswers.shift()
    }
}

const getHistoryColor = (result) => {
    if (result === true) return 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]'
    if (result === false) return 'bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.5)]'
    if (result === null) return 'bg-slate-300'
    return 'bg-slate-100'
}

const recordHistory = (isCorrect) => {
    matchHistory.value.push({
        questionNumber: currentQuestionIndex.value + 1,
        questionText: currentQuestion.value.text,
        options: currentQuestion.value.options,
        teamName: teams.value[currentTurn.value].name,
        selectedKey: selectedAnswer.value,
        correctKey: currentQuestion.value.correct,
        isCorrect: isCorrect
    })
}

const moveToNextWithDelay = () => {
    nextTurnTimeout = setTimeout(() => {
        executeNextTurn()
    }, 2000)
}

const executeNextTurn = () => {
    if (currentQuestionIndex.value < questions.length - 1) {
        currentQuestionIndex.value++
        currentTurn.value = (currentTurn.value + 1) % teams.value.length

        isAnswered.value = false
        selectedAnswer.value = null
        startTimer()
    } else {
        router.post('/finish-game', {
            quiz_id: props.quiz_id,
            teams: teams.value,
            history: matchHistory.value
        })
    }
}

const getOptionClass = (key) => {
    if (!isAnswered.value) return 'border-slate-200 bg-white hover:border-slate-900 text-slate-700'

    if (key === currentQuestion.value.correct) {
        return 'border-emerald-500 bg-emerald-500 text-white shadow-lg shadow-emerald-500/30'
    }

    if (key === selectedAnswer.value) {
        return 'border-red-500 bg-red-500 text-white shadow-lg shadow-red-500/30'
    }

    return 'border-slate-100 bg-white text-slate-300 opacity-40'
}

onMounted(() => {
    startTimer()
})

onUnmounted(() => {
    clearInterval(timer)
    clearTimeout(nextTurnTimeout)
})
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}

.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>