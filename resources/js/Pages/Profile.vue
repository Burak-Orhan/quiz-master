<template>
    <div class="min-h-screen bg-slate-50 text-slate-800 font-sans selection:bg-slate-100 pb-20">

        <ToastList />

        <header class="bg-white border-b border-slate-200 sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <Link href="/dashboard"
                        class="w-10 h-10 bg-slate-100 hover:bg-slate-200 rounded-xl flex items-center justify-center text-slate-500 transition-colors border border-slate-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </Link>
                    <div>
                        <h1 class="text-xl font-black text-slate-900 tracking-tight leading-none">
                            {{ isOwnProfile ? 'Profilim & Ayarlar' : `${profileUser.name} Profili` }}
                        </h1>
                    </div>
                </div>
                <form method="POST" action="/logout" class="inline">
                    <input type="hidden" name="_token" :value="$page.props.auth.csrf_token">

                    <button type="submit"
                        class="text-xs font-bold text-slate-500 hover:text-red-600 transition-colors uppercase tracking-widest">
                        Çıkış Yap
                    </button>
                </form>
            </div>
        </header>

        <main class="max-w-5xl mx-auto px-6 mt-10">

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 sm:p-10 mb-10">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-8">
                    <div class="flex flex-col sm:flex-row items-center gap-6">
                        <div
                            class="w-28 h-28 rounded-full border border-slate-200 shadow-inner bg-slate-100 overflow-hidden shrink-0">
                            <img :src="`https://ui-avatars.com/api/?name=${profileUser.name}&background=f1f5f9&color=1e293b&size=256&font-size=0.4&bold=true`"
                                alt="Profile Picture" class="w-full h-full object-cover">
                        </div>
                        <div class="text-center sm:text-left">
                            <h2 class="text-3xl font-black text-slate-900 tracking-tight">{{ profileUser.name }}</h2>
                            <p class="text-sm font-medium text-slate-500 mt-1">Üyelik Tarihi: {{ stats.join_date }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3 border border-slate-200 rounded-2xl p-2 bg-slate-50">
                        <div class="text-center px-4 py-2">
                            <span class="block text-2xl font-black text-slate-900">{{ stats.total_solved }}</span>
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">Quiz
                                Çözdü</span>
                        </div>
                        <div class="text-center px-4 py-2 border-x border-slate-200">
                            <span class="block text-2xl font-black text-slate-900">{{ stats.total_quizzes }}</span>
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">Quiz
                                Oluşturdu</span>
                        </div>
                        <div class="text-center px-4 py-2">
                            <span class="block text-2xl font-black text-indigo-600">{{ stats.total_plays }}</span>
                            <span class="text-[10px] uppercase font-bold text-indigo-500 tracking-widest">Oynanma
                                Alındı</span>
                        </div>
                    </div>
                </div>

                <div
                    class="mt-10 bg-slate-50 rounded-2xl p-6 flex flex-col sm:flex-row items-center gap-6 border border-slate-200 shadow-inner relative overflow-hidden">
                    <div
                        class="absolute right-0 top-0 w-40 h-40 bg-indigo-50 rounded-full -translate-y-16 translate-x-16">
                    </div>
                    <div
                        class="w-20 h-20 rounded-xl bg-slate-900 text-white flex flex-col items-center justify-center font-black shrink-0 z-10">
                        <span class="text-[10px] uppercase tracking-widest opacity-70">LEVEL</span>
                        <span class="text-4xl leading-none">{{ levelData.level }}</span>
                    </div>
                    <div class="flex-1 w-full z-10">
                        <div class="flex justify-between items-baseline mb-2">
                            <p class="text-sm font-bold text-slate-700">Deneyim Puanı: <span
                                    class="font-black text-indigo-600">{{ totalXP.toLocaleString() }} XP</span></p>
                            <p class="text-xs font-black text-slate-400">{{ levelData.currentXPInLevel }} / {{
                                levelData.requiredXPForNext }} XP</p>
                        </div>
                        <div
                            class="h-3 w-full bg-slate-200 rounded-full overflow-hidden relative border border-slate-300 shadow-inner">
                            <div class="absolute top-0 left-0 h-full bg-indigo-600 rounded-full transition-all duration-1000 ease-out"
                                :style="`width: ${levelData.progressPercentage}%`">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center bg-slate-100 border border-slate-200 p-1 rounded-2xl w-fit mb-8">
                <button v-for="tab in filteredTabs" :key="tab.id" @click="activeTab = tab.id"
                    :class="activeTab === tab.id ? 'bg-white text-indigo-700 shadow-sm border border-slate-200' : 'text-slate-500 hover:text-slate-700'"
                    class="px-6 py-3 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
                    {{ tab.name }}
                </button>
            </div>

            <div class="relative min-h-[400px]">
                <Transition name="page-fade" mode="out-in">



                    <section v-if="activeTab === 'overview'" key="overview" class="flex flex-col gap-8">

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 w-full">
                            <div class="md:col-span-1">
                                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm h-full">
                                    <div class="flex justify-between items-center mb-6">
                                        <h3 class="text-xs uppercase tracking-widest font-black text-slate-400">En
                                            Değerli Rozetler</h3>
                                        <button @click="activeTab = 'badges'"
                                            class="text-[10px] uppercase font-bold text-indigo-600 hover:text-indigo-800">Tümü</button>
                                    </div>
                                    <div class="space-y-3">
                                        <div v-for="badge in top5Badges" :key="badge.id"
                                            class="flex items-center gap-4 p-3 rounded-xl border border-slate-100 transition-all hover:border-slate-200 hover:bg-slate-50">
                                            <div class="text-3xl shrink-0">{{ badge.icon }}</div>
                                            <div>
                                                <h4 class="font-bold text-sm text-slate-900">{{ badge.name }}</h4>
                                                <p class="text-[11px] font-medium text-slate-500 mt-0.5">{{ badge.desc
                                                }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm h-full">
                                    <h3 class="text-xs uppercase tracking-widest font-black text-slate-400 mb-6">Öne
                                        Çıkan Sınavlar</h3>
                                    <div v-if="top_quizzes.length === 0"
                                        class="text-center py-12 border-2 border-dashed border-slate-200 rounded-2xl">
                                        <span class="text-4xl block mb-3">🎯</span>
                                        <p class="font-bold text-slate-500">Henüz bir sınav bulunmuyor.</p>
                                    </div>
                                    <div v-else class="space-y-3">
                                        <div v-for="(quiz, index) in top_quizzes" :key="quiz.id"
                                            class="group flex items-center justify-between p-4 rounded-xl border border-slate-100 bg-white">
                                            <div class="flex items-center gap-4">
                                                <div
                                                    class="w-9 h-9 rounded-lg font-black text-lg flex items-center justify-center shrink-0 bg-slate-50 text-slate-400">
                                                    {{ index + 1 }}
                                                </div>
                                                <div>
                                                    <h4 class="font-bold text-slate-900">{{ quiz.title }}</h4>
                                                    <p
                                                        class="text-[10px] uppercase font-bold text-slate-400 tracking-widest mt-0.5">
                                                        Oynanma: {{ quiz.plays_count }}</p>
                                                </div>
                                            </div>

                                            <div v-if="!isOwnProfile">
                                                <Link :href="`/?quiz_code=${quiz.code}`"
                                                    class="text-indigo-600 hover:text-indigo-800 text-[10px] font-black uppercase tracking-widest transition-colors flex items-center gap-1">
                                                    Sınava Başla <span>→</span>
                                                </Link>
                                            </div>
                                            <div v-else>
                                                <Link :href="`/quizzes/${quiz.id}/report`"
                                                    class="text-slate-400 hover:text-indigo-600">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                        viewBox="0 0 20 20" fill="currentColor">
                                                        <path
                                                            d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                                                    </svg>
                                                </Link>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="recentAttempts && recentAttempts.length > 0"
                            class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm w-full">
                            <h3 class="text-xs uppercase tracking-widest font-black text-slate-400 mb-6">Son Çözülen
                                Testler</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div v-for="(attempt, index) in recentAttempts" :key="index"
                                    class="group flex flex-col p-5 rounded-2xl border border-slate-100 bg-white hover:border-slate-200 hover:shadow-sm transition-all duration-300">

                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 font-black text-sm flex items-center justify-center shrink-0 border border-slate-100">
                                                {{ attempt.creator.substring(0, 2).toUpperCase() }}
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-slate-900 text-sm line-clamp-1">{{
                                                    attempt.quiz_title }}</h4>
                                                <p
                                                    class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">
                                                    Yazar: {{ attempt.creator }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0 ml-2">
                                            <span class="block text-xl font-black"
                                                :class="attempt.score >= 50 ? 'text-indigo-600' : 'text-slate-700'">
                                                {{ attempt.score }}
                                            </span>
                                            <span
                                                class="text-[9px] uppercase font-bold text-slate-400 tracking-widest block -mt-1">Puan</span>
                                        </div>
                                    </div>

                                    <div
                                        class="flex items-center justify-between mt-auto pt-4 border-t border-slate-50">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{
                                            attempt.date }}</span>
                                        <Link :href="`/?quiz_code=${attempt.quiz_code}`"
                                            class="text-[10px] font-black text-indigo-500 hover:text-indigo-700 uppercase tracking-widest transition-colors flex items-center gap-1">
                                            Sınava Git <span class="text-lg leading-none mb-0.5">→</span>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </section>

                    <section v-else-if="activeTab === 'badges'" key="badges"
                        class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm">
                        <div class="mb-8 border-b border-slate-200 pb-6">
                            <h2 class="text-xl font-black text-slate-900 tracking-tight">Rozet Koleksiyonu</h2>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                            <div v-for="badge in allBadges" :key="badge.id"
                                class="flex items-center gap-5 p-5 rounded-2xl border-2 transition-all relative"
                                :class="badge.isUnlocked ? 'bg-white border-slate-200' : 'bg-slate-50 border-slate-100 grayscale opacity-60'">
                                <div class="text-4xl shrink-0 relative">
                                    {{ badge.icon }}
                                    <div v-if="!badge.isUnlocked"
                                        class="absolute -bottom-1 -right-1 bg-slate-700 text-white rounded-full w-5 h-5 flex items-center justify-center text-[10px] border-2 border-white">
                                        🔒</div>
                                </div>
                                <div class="flex-1 pr-6">
                                    <h4 class="font-bold text-sm text-slate-900">{{ badge.name }}</h4>
                                    <p class="text-xs font-medium text-slate-500 mt-0.5">{{ badge.desc }}</p>
                                </div>
                                <div class="absolute top-2 right-2 text-[9px] font-black"
                                    :class="badge.isUnlocked ? 'text-indigo-400' : 'text-slate-400'">+{{ badge.value }}
                                </div>
                            </div>
                        </div>
                    </section>

                    <section v-else-if="activeTab === 'info' && isOwnProfile" key="info"
                        class="bg-white p-8 sm:p-10 rounded-3xl border border-slate-200 shadow-sm max-w-3xl">
                        <div class="mb-10 border-b border-slate-200 pb-8">
                            <h2 class="text-xl font-black text-slate-900 tracking-tight">Hesap Ayarları</h2>
                        </div>
                        <form @submit.prevent="updateInfo" class="space-y-6 mb-12 border-b border-slate-200 pb-12">
                            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Kişisel
                                Bilgiler</h3>
                            <div>
                                <label
                                    class="block text-xs uppercase tracking-widest font-bold text-slate-400 mb-2 ml-1">İsim
                                    Soyisim</label>
                                <input type="text" v-model="infoForm.name"
                                    class="w-full bg-slate-50 border-2 border-slate-200 rounded-xl px-4 py-3 text-base font-bold text-slate-700 focus:outline-none focus:border-indigo-500 transition-colors">
                            </div>
                            <div>
                                <label
                                    class="block text-xs uppercase tracking-widest font-bold text-slate-400 mb-2 ml-1">E-Posta
                                    Adresi</label>
                                <input type="email" v-model="infoForm.email"
                                    class="w-full bg-slate-50 border-2 border-slate-200 rounded-xl px-4 py-3 text-base font-bold text-slate-700 focus:outline-none focus:border-indigo-500 transition-colors">
                            </div>
                            <button type="submit" :disabled="infoForm.processing"
                                class="bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 px-8 rounded-xl transition-all text-sm uppercase tracking-widest">Bilgileri
                                Kaydet</button>
                        </form>
                        <form @submit.prevent="updatePassword" class="space-y-6">
                            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Güvenlik</h3>
                            <div>
                                <label
                                    class="block text-xs uppercase tracking-widest font-bold text-slate-400 mb-2 ml-1">Mevcut
                                    Şifre</label>
                                <input type="password" v-model="passwordForm.current_password"
                                    class="w-full bg-slate-50 border-2 border-slate-200 rounded-xl px-4 py-3 text-base font-bold text-slate-700 focus:outline-none focus:border-indigo-500 transition-colors">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label
                                        class="block text-xs uppercase tracking-widest font-bold text-slate-400 mb-2 ml-1">Yeni
                                        Şifre</label>
                                    <input type="password" v-model="passwordForm.password"
                                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-xl px-4 py-3 text-base font-bold text-slate-700 focus:outline-none focus:border-indigo-500 transition-colors">
                                </div>
                                <div>
                                    <label
                                        class="block text-xs uppercase tracking-widest font-bold text-slate-400 mb-2 ml-1">Şifre
                                        Tekrar</label>
                                    <input type="password" v-model="passwordForm.password_confirmation"
                                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-xl px-4 py-3 text-base font-bold text-slate-700 focus:outline-none focus:border-indigo-500 transition-colors">
                                </div>
                            </div>
                            <button type="submit" :disabled="passwordForm.processing"
                                class="bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 px-8 rounded-xl transition-all text-sm uppercase tracking-widest">Şifreyi
                                Güncelle</button>
                        </form>
                    </section>
                </Transition>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage, Link } from '@inertiajs/vue3';
import ToastList from '../Components/ToastList.vue';
import { useToast } from '../Composables/useToast';

const props = defineProps({
    profileUser: Object,
    isOwnProfile: Boolean,
    stats: Object,
    top_quizzes: Array,
    recentAttempts: Array,
});

const page = usePage();
const toast = useToast();
const activeTab = ref('overview');

const filteredTabs = computed(() => {
    const tabs = [
        { id: 'overview', name: 'Genel Bakış' },
        { id: 'badges', name: 'Rozet Koleksiyonu' }
    ];
    if (props.isOwnProfile) {
        tabs.push({ id: 'info', name: 'Hesap Ayarları' });
    }
    return tabs;
});

const allBadges = computed(() => {
    return [
        { id: 10, name: 'Efsanevi', icon: '👑', desc: '100+ kez oynandı.', isUnlocked: props.stats.total_plays >= 100, value: 1000 },
        { id: 9, name: 'Popüler', icon: '🔥', desc: '10+ kez oynandı.', isUnlocked: props.stats.total_plays >= 10, value: 500 },
        { id: 8, name: '1 Yıllık Çınar', icon: '🌳', desc: '1 Yıldır aramızda.', isUnlocked: props.stats.months_active >= 12, value: 800 },
        { id: 7, name: '6 Aylık Müdavim', icon: '⭐', desc: '6 Aydır aramızda.', isUnlocked: props.stats.months_active >= 6, value: 400 },
        { id: 6, name: '3 Aylık Dost', icon: '🤝', desc: '3 Aydır aramızda.', isUnlocked: props.stats.months_active >= 3, value: 200 },
        { id: 5, name: '1 Aylık Üye', icon: '🎈', desc: '1 Aydır aramızda.', isUnlocked: props.stats.months_active >= 1, value: 100 },
        { id: 4, name: 'Soru Kurdu', icon: '🧠', desc: '10+ sınav çözdü.', isUnlocked: props.stats.total_solved >= 10, value: 600 },
        { id: 3, name: 'Soru Avcısı', icon: '🔍', desc: 'İlk sınavını çözdü.', isUnlocked: props.stats.total_solved >= 1, value: 150 },
        { id: 2, name: 'Soru Ustası', icon: '✍️', desc: '5+ sınav üretti.', isUnlocked: props.stats.total_quizzes >= 5, value: 600 },
        { id: 1, name: 'İlk Adım', icon: '🎯', desc: 'İlk sınavını oluşturdu.', isUnlocked: props.stats.total_quizzes >= 1, value: 150 },
        { id: 0, name: 'Taze Kan', icon: '🌱', desc: 'Aramıza katıldı.', isUnlocked: true, value: 50 }
    ].sort((a, b) => b.id - a.id);
});

const totalXP = computed(() => {
    let xp = (props.stats.total_quizzes * 100) + (props.stats.total_solved * 50) + (props.stats.total_plays * 10);
    xp += allBadges.value.filter(b => b.isUnlocked).reduce((t, b) => t + b.value, 0);
    return xp;
});

const levelData = computed(() => {
    let level = 1, req = 500, xp = totalXP.value;
    while (xp >= req) { xp -= req; level++; req = Math.floor(req * 1.5); }
    return { level, currentXPInLevel: xp, requiredXPForNext: req, progressPercentage: Math.round((xp / req) * 100) };
});

const top5Badges = computed(() => allBadges.value.filter(b => b.isUnlocked).sort((a, b) => b.value - a.value).slice(0, 5));

const infoForm = useForm({ name: props.profileUser.name, email: props.profileUser.email });
const passwordForm = useForm({ current_password: '', password: '', password_confirmation: '' });
const updateInfo = () => infoForm.patch('/profile', { preserveScroll: true, onSuccess: () => toast.success('Profil güncellendi!') });
const updatePassword = () => passwordForm.put('/password', { preserveScroll: true, onSuccess: () => { passwordForm.reset(); toast.success('Şifre güncellendi!'); } });
</script>

<style scoped>
.page-fade-enter-active,
.page-fade-leave-active {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.page-fade-enter-from {
    opacity: 0;
    transform: translateY(12px);
}

.page-fade-leave-to {
    opacity: 0;
    transform: translateY(-12px);
}
</style>