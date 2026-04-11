<template>
    <div
        class="min-h-screen flex items-center justify-center bg-slate-50 text-slate-800 p-6 font-sans selection:bg-slate-200">

        <div class="w-full max-w-3xl bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">

            <div class="px-10 py-8 border-b border-slate-100 flex justify-between items-center bg-white">
                <div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Quiz Master</h1>
                    <p class="text-[11px] text-slate-400 uppercase tracking-widest font-bold mt-1">Oyun Kurulumu</p>
                </div>

                <div class="flex items-center gap-4">
                    <template v-if="!$page.props.auth.user">
                        <a href="/login"
                            class="text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors uppercase tracking-widest">Giriş</a>
                        <span class="w-1.5 h-1.5 bg-slate-300 rounded-full"></span>
                        <a href="/register"
                            class="text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors uppercase tracking-widest">Kayıt
                            Ol</a>
                    </template>
                    <template v-else>
                        <a href="/dashboard"
                            class="text-xs font-bold text-indigo-600 hover:text-indigo-700 bg-indigo-50 px-4 py-2 rounded-lg transition-colors">Panelim</a>
                    </template>
                </div>
            </div>

            <div class="p-10 space-y-10">

                <section>
                    <h2 class="text-xs uppercase tracking-widest text-slate-400 font-bold mb-4 ml-1">Sınav Referans Kodu
                    </h2>
                    <div class="relative">
                        <input type="text" v-model="quizCode" @input="handleCodeInput" placeholder="ÖRN: QM-XJ2"
                            class="w-full bg-slate-50 border-2 rounded-2xl px-6 py-4 text-xl font-black text-slate-700 tracking-widest focus:outline-none uppercase transition-colors duration-300"
                            :class="{
                                'border-slate-200 focus:border-slate-400': status === 'idle',
                                'border-indigo-300 focus:border-indigo-500 text-indigo-700': status === 'loading',
                                'border-emerald-400 focus:border-emerald-500 text-emerald-700 bg-emerald-50/30': status === 'success',
                                'border-red-300 focus:border-red-400 text-red-600 bg-red-50/30': status === 'error'
                            }">
                    </div>
                    <div class="mt-4 p-4 border rounded-xl flex items-center gap-3 transition-colors duration-300"
                        :class="{
                            'bg-slate-50 border-slate-100 text-slate-500': status === 'idle',
                            'bg-indigo-50 border-indigo-100 text-indigo-600': status === 'loading',
                            'bg-emerald-50 border-emerald-100 text-emerald-700': status === 'success',
                            'bg-red-50 border-red-100 text-red-600': status === 'error'
                        }">
                        <div class="w-2 h-2 rounded-full shrink-0"
                            :class="{ 'bg-slate-300': status === 'idle', 'bg-indigo-500 animate-pulse': status === 'loading', 'bg-emerald-500': status === 'success', 'bg-red-500': status === 'error' }">
                        </div>
                        <p class="text-sm font-medium">{{ statusMessage }}</p>
                    </div>
                </section>

                <section>
                    <h2 class="text-xs uppercase tracking-widest text-slate-400 font-bold mb-4 ml-1">1. Oyun Modu</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <button @click="setMode('single')"
                            :class="form.mode === 'single' ? 'border-slate-900 ring-2 ring-slate-900 bg-slate-50 text-slate-900' : 'border-slate-200 text-slate-500 hover:border-slate-300'"
                            class="py-4 border rounded-xl font-bold text-base transition-all duration-200">
                            Tek Kişi
                        </button>
                        <button @click="setMode('group')"
                            :class="form.mode === 'group' ? 'border-slate-900 ring-2 ring-slate-900 bg-slate-50 text-slate-900' : 'border-slate-200 text-slate-500 hover:border-slate-300'"
                            class="py-4 border rounded-xl font-bold text-base transition-all duration-200">
                            Grup Oyunu
                        </button>
                    </div>
                </section>

                <section v-if="form.mode === 'single'">
                    <h2 class="text-xs uppercase tracking-widest text-slate-400 font-bold mb-4 ml-1">2. Oyuncu Bilgisi
                    </h2>
                    <div
                        class="flex items-center gap-4 bg-slate-50 p-3 rounded-xl border border-slate-200 transition-all opacity-90">
                        <div class="w-10 h-10 rounded-lg overflow-hidden shrink-0 border-2 border-slate-200">
                            <input type="color" v-model="form.teams[0].color"
                                class="w-16 h-16 -mt-3 -ml-3 cursor-pointer bg-transparent border-0 p-0">
                        </div>

                        <input type="text" v-model="form.teams[0].name" disabled
                            class="flex-1 bg-transparent border-0 text-base font-black text-slate-500 focus:ring-0 p-0 uppercase cursor-not-allowed"
                            title="Tek kişilik modda isim otomatik atanır">

                        <div class="shrink-0 mr-2 text-slate-400" title="İsim değiştirilemez">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </section>

                <section v-if="form.mode === 'group'">
                    <div class="flex justify-between items-center mb-4 ml-1">
                        <h2 class="text-xs uppercase tracking-widest text-slate-400 font-bold">2. Takımlar</h2>
                        <button @click="addTeam"
                            class="text-slate-500 hover:text-slate-900 text-xs uppercase tracking-widest font-bold transition-colors">
                            + Yeni Ekle
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div v-for="(team, index) in form.teams" :key="team.id"
                            class="flex items-center gap-4 bg-slate-50/50 p-3 rounded-xl border border-slate-100 transition-all">
                            <div class="w-10 h-10 rounded-lg overflow-hidden shrink-0 border-2 border-slate-200">
                                <input type="color" v-model="team.color"
                                    class="w-16 h-16 -mt-3 -ml-3 cursor-pointer bg-transparent border-0 p-0">
                            </div>
                            <input type="text" v-model="team.name" placeholder="Takım İsmi"
                                class="flex-1 bg-transparent border-0 text-base font-bold text-slate-700 placeholder-slate-400 focus:ring-0 p-0 uppercase">
                            <button v-if="form.teams.length > 2" @click="removeTeam(team.id)"
                                class="bg-red-50 hover:bg-red-100 text-red-500 w-8 h-8 flex items-center justify-center rounded-lg transition-colors text-sm font-bold"
                                title="Sil">
                                ✕
                            </button>
                        </div>
                    </div>
                </section>

                <section>
                    <h2 class="text-xs uppercase tracking-widest text-slate-400 font-bold mb-4 ml-1">3. Kurallar</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label
                                class="block text-xs uppercase tracking-widest font-bold text-blue-500 mb-2 ml-1">Süre
                                (Sn)</label>
                            <input type="number" v-model="form.timeLimit" min="5" max="120"
                                class="w-full bg-white border-2 border-slate-100 rounded-xl px-4 py-3 text-base font-bold text-slate-700 focus:outline-none focus:border-blue-500 transition-colors">
                        </div>
                        <div>
                            <label
                                class="block text-xs uppercase tracking-widest font-bold text-green-500 mb-2 ml-1">Doğru
                                Puanı</label>
                            <input type="number" v-model="form.correctPoints" min="1"
                                class="w-full bg-white border-2 border-slate-100 rounded-xl px-4 py-3 text-base font-bold text-slate-700 focus:outline-none focus:border-green-500 transition-colors">
                        </div>
                        <div>
                            <label
                                class="block text-xs uppercase tracking-widest font-bold text-red-500 mb-2 ml-1">Yanlış
                                Cezası</label>
                            <input type="number" v-model="form.wrongPoints" min="0"
                                class="w-full bg-white border-2 border-slate-100 rounded-xl px-4 py-3 text-base font-bold text-slate-700 focus:outline-none focus:border-red-500 transition-colors">
                        </div>
                    </div>
                </section>

                <section>
                    <h2 class="text-xs uppercase tracking-widest text-slate-400 font-bold mb-4 ml-1">4. Akış Ayarı</h2>
                    <div class="bg-slate-50 border border-slate-200 p-5 rounded-2xl flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-slate-900">Cevap Sonrası Otomatik Geçiş</p>
                            <p class="text-xs text-slate-500">Cevap verildiğinde 2 saniye sonra otomatik diğer soruya
                                geçer.</p>
                        </div>
                        <button @click="form.autoPass = !form.autoPass"
                            class="w-14 h-8 rounded-full transition-colors relative"
                            :class="form.autoPass ? 'bg-emerald-500' : 'bg-slate-300'">
                            <div class="absolute top-1 w-6 h-6 bg-white rounded-full transition-all"
                                :class="form.autoPass ? 'left-7' : 'left-1'"></div>
                        </button>
                    </div>
                </section>

                <div class="pt-8 border-t border-slate-100">
                    <div class="flex justify-center mb-5">
                        <button @click="showRulesModal = true"
                            class="text-[11px] font-bold text-slate-400 hover:text-indigo-500 uppercase tracking-widest transition-colors flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Bu mod nasıl oynanır?
                        </button>
                    </div>

                    <button @click="startGame" :disabled="status !== 'success'"
                        class="w-full bg-slate-900 hover:bg-slate-800 disabled:bg-slate-200 disabled:text-slate-400 text-white text-base font-bold py-5 rounded-2xl transition-colors">
                        {{ status === 'success' ? 'Oyunu Başlat' : 'Lütfen Geçerli Bir Kod Girin' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <Transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0"
        enter-to-class="opacity-100" leave-active-class="transition-all duration-200 ease-in"
        leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="showRulesModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40"
            @click="showRulesModal = false">
        </div>
    </Transition>

    <Transition enter-active-class="transition-all duration-300 ease-out"
        enter-from-class="opacity-0 scale-95 translate-y-4" enter-to-class="opacity-100 scale-100 translate-y-0"
        leave-active-class="transition-all duration-200 ease-in" leave-from-class="opacity-100 scale-100 translate-y-0"
        leave-to-class="opacity-0 scale-95 translate-y-4">
        <div v-if="showRulesModal" class="fixed inset-0 z-50 flex items-center justify-center px-4 pointer-events-none">

            <div class="bg-white rounded-3xl p-8 max-w-md w-full relative shadow-2xl pointer-events-auto">

                <button @click="showRulesModal = false"
                    class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 bg-slate-50 hover:bg-slate-100 p-2 rounded-full transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>

                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 tracking-tight">Oyun Kuralları</h3>
                </div>

                <ul class="space-y-4 text-sm font-medium text-slate-600">
                    <li class="flex items-start gap-3">
                        <span class="text-blue-500 text-lg leading-none">⏱️</span>
                        <span>Her soru için <strong>{{ form.timeLimit }} saniye</strong> süreniz var. Süre dolduğunda
                            yanlış
                            cevaplamış sayılırsınız.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-emerald-500 text-lg leading-none">✅</span>
                        <span>Doğru cevaplar takıma/sana <strong>+{{ form.correctPoints }} puan</strong>
                            kazandırır.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-red-500 text-lg leading-none">❌</span>
                        <span>Yanlış cevaplarda veya süre bittiğinde <strong>{{ form.wrongPoints }} puan</strong>
                            silinir.
                            Puan eksiye düşebilir.</span>
                    </li>

                    <div class="pt-4 mt-2 border-t border-slate-100">
                        <li v-if="form.mode === 'single'"
                            class="flex items-start gap-3 bg-indigo-50/50 p-3 rounded-xl border border-indigo-100/50">
                            <span class="text-indigo-500 text-lg leading-none">👤</span>
                            <span class="text-indigo-900"><strong>Tek Kişilik Mod:</strong> Sıra beklemeden tüm soruları
                                kendi hızında cevaplarsın. Amacın oyun sonunda kendi rekorunu kırmaktır.</span>
                        </li>
                        <li v-else
                            class="flex items-start gap-3 bg-amber-50/50 p-3 rounded-xl border border-amber-100/50">
                            <span class="text-amber-500 text-lg leading-none">👥</span>
                            <span class="text-amber-900"><strong>Grup Modu:</strong> Takımlar sırayla yarışır. Yukarıda
                                ismi
                                yazan takımın sırasıdır ve soruyu sadece o takım cevaplamalıdır. En yüksek puanı
                                toplayan
                                kazanır.</span>
                        </li>
                    </div>
                </ul>

                <button @click="showRulesModal = false"
                    class="w-full mt-8 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-3.5 rounded-xl transition-colors">
                    Anladım, Kapat
                </button>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
    quiz: {
        type: Object,
        default: () => ({ id: null, questions: [] })
    }
})

const page = usePage()

const showRulesModal = ref(false)
const quizCode = ref('')
const status = ref('idle')
const statusMessage = ref('Sınav kodunu girdiğinizde otomatik olarak doğrulanacaktır.')
let debounceTimer = null

const form = useForm({
    quiz_id: props.quiz?.id || null,
    mode: 'group',
    teams: [
        { id: 1, name: 'A Grubu', color: '#1e293b' },
        { id: 2, name: 'B Grubu', color: '#64748b' }
    ],
    timeLimit: 30,
    correctPoints: 10,
    wrongPoints: 5,
    questions: props.quiz?.questions || [],
    autoPass: true,
})

const setMode = (mode) => {
    form.mode = mode
    if (mode === 'single') {
        const playerName = page.props.auth?.user?.name || 'Oyuncu 1'
        form.teams = [{ id: 1, name: playerName, color: '#8770f0' }] //3b82f6
    } else {
        form.teams = [
            { id: 1, name: 'A Grubu', color: '#1e293b' },
            { id: 2, name: 'B Grubu', color: '#64748b' }
        ]
    }
}

const handleCodeInput = () => {
    form.questions = []

    if (quizCode.value.trim().length < 3) {
        status.value = 'idle'
        statusMessage.value = 'Sınav kodunu girdiğinizde otomatik olarak doğrulanacaktır.'
        clearTimeout(debounceTimer)
        return
    }

    status.value = 'loading'
    statusMessage.value = 'Sınav kodu kontrol ediliyor...'
    clearTimeout(debounceTimer)

    debounceTimer = setTimeout(() => {
        fetchQuiz()
    }, 600)
}

const fetchQuiz = async (codeOverride = null) => {
    const codeToFetch = (codeOverride || quizCode.value).trim()
    if (!codeToFetch) return

    try {
        const response = await axios.get(`/api/quiz/${codeToFetch.toUpperCase()}`)

        form.questions = response.data.questions
        form.quiz_id = response.data.id

        status.value = 'success'
        statusMessage.value = `Sınav Hazır: ${response.data.title}`
    } catch (error) {
        status.value = 'error'
        if (error.response && error.response.status === 404) {
            statusMessage.value = "Sınav bulunamadı. Lütfen kodu kontrol edin."
        } else {
            statusMessage.value = "Bağlantı hatası oluştu."
        }
    }
}

onMounted(() => {
    if (props.quiz && props.quiz.id) {
        quizCode.value = props.quiz.code || '';
        status.value = 'success';
        statusMessage.value = `Sınav Hazır: ${props.quiz.title || 'Sınav Yüklendi'}`;

        if (typeof form.questions === 'string') {
            try { form.questions = JSON.parse(form.questions); } catch (e) { form.questions = []; }
        }
    }

    const urlParams = new URLSearchParams(window.location.search)
    const codeFromUrl = urlParams.get('quiz_code')
    if (codeFromUrl) {
        quizCode.value = codeFromUrl
        status.value = 'loading'
        statusMessage.value = 'Sınav kodu kontrol ediliyor...'
        fetchQuiz(codeFromUrl)
    }
})

const startGame = () => {
    if (status.value !== 'success') {
        alert("Lütfen önce geçerli bir sınav referans kodu girin.");
        return;
    }

    if (!form.questions || form.questions.length === 0) {
        alert("DİKKAT: Bu sınavın içinde hiç soru yok! Lütfen soruları olan geçerli bir sınav seçin.");
        return;
    }

    if (!form.quiz_id) {
        alert("DİKKAT: Sınavın ID numarası bulunamadı. Lütfen kodu silip tekrar yazın.");
        return;
    }

    form.post('/start', {
        preserveScroll: true,
        onError: (errors) => {
            console.error("Laravel'den Gelen Hatalar:", errors);
            alert("Oyunu başlatamadık çünkü şu veri eksik veya hatalı: " + Object.keys(errors).join(', '));
        }
    });
}

const addTeam = () => {
    if (form.teams.length >= 8) return;
    const newId = Math.max(...form.teams.map(t => t.id)) + 1;
    form.teams.push({ id: newId, name: `Takım ${newId}`, color: '#94a3b8' });
}

const removeTeam = (id) => {
    if (form.teams.length > 2) {
        form.teams = form.teams.filter(t => t.id !== id);
    }
}
</script>