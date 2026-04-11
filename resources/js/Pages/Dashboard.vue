<template>
    <div class="min-h-screen bg-slate-50 text-slate-800 font-sans selection:bg-slate-200 pb-20">

        <ToastList />
        <ConfirmModal />

        <header class="bg-white/80 backdrop-blur-xl border-b border-slate-200/50 sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-slate-900 rounded-xl flex items-center justify-center text-white font-black text-xl shadow-md">
                        Q
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-slate-900 tracking-tight leading-none">Quiz Master</h1>
                        <p class="text-[9px] text-slate-400 uppercase tracking-widest font-bold mt-1">Kontrol Merkezi
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-6">
                    <Link :href="`/profile/${$page.props.auth.user.slug}`"
                        class="text-right hidden sm:block group cursor-pointer">
                        <p
                            class="text-[10px] text-slate-400 uppercase tracking-widest font-bold mb-0.5 group-hover:text-indigo-500 transition-colors">
                            Profili Düzenle</p>
                        <p class="text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">{{
                            $page.props.auth.user.name }}</p>
                    </Link>
                    <div class="w-px h-8 bg-slate-200 hidden sm:block"></div>
                    <form method="POST" action="/logout" class="inline">
                        <input type="hidden" name="_token" :value="$page.props.auth.csrf_token">

                        <button type="submit"
                            class="text-xs font-bold text-slate-500 hover:text-red-600 transition-colors uppercase tracking-widest">
                            Çıkış Yap
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-6 mt-10 space-y-12">

            <section>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div
                        class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden group">
                        <div
                            class="absolute -right-6 -top-6 w-24 h-24 bg-indigo-50 rounded-full transition-transform group-hover:scale-150 duration-500">
                        </div>
                        <div class="relative z-10">
                            <p class="text-[11px] font-black uppercase tracking-widest text-indigo-500 mb-2">Toplam
                                Sınav</p>
                            <p class="text-4xl font-black text-slate-900">{{ quizzes.length }}</p>
                        </div>
                    </div>

                    <div
                        class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden group">
                        <div
                            class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-50 rounded-full transition-transform group-hover:scale-150 duration-500">
                        </div>
                        <div class="relative z-10">
                            <p class="text-[11px] font-black uppercase tracking-widest text-emerald-500 mb-2">Toplam
                                Oynanma</p>
                            <p class="text-4xl font-black text-slate-900">{{ totalPlays }}</p>
                        </div>
                    </div>

                    <div
                        class="bg-slate-900 p-6 rounded-3xl border border-slate-800 shadow-xl relative overflow-hidden">
                        <div
                            class="absolute right-0 top-0 w-32 h-32 bg-gradient-to-br from-white/10 to-transparent rounded-full -translate-y-10 translate-x-10">
                        </div>
                        <div class="relative z-10">
                            <p class="text-[11px] font-black uppercase tracking-widest text-slate-400 mb-2">Yeni Sınav
                                Oluştur</p>
                            <Link href="/quizzes/create"
                                class="w-full mt-2 bg-white text-slate-900 font-bold py-3 rounded-xl hover:bg-slate-100 transition-colors shadow-sm active:scale-95 flex items-center justify-center">
                                + Oluştur
                            </Link>
                        </div>
                    </div>
                </div>
            </section>

            <section>
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                    <div>
                        <h2 class="text-2xl font-black text-slate-900">Sınavlarım</h2>
                        <p class="text-sm font-medium text-slate-500 mt-1">Oluşturduğun tüm sınavlar ve yönetim paneli
                        </p>
                    </div>

                    <div class="flex items-center bg-slate-200/50 p-1.5 rounded-2xl w-fit">
                        <button @click="activeTab = 'active'"
                            :class="activeTab === 'active' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                            class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
                            Aktif Sınavlar
                            <span v-if="activeCount > 0"
                                class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md text-[10px]">{{ activeCount
                                }}</span>
                        </button>
                        <button @click="activeTab = 'archived'"
                            :class="activeTab === 'archived' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                            class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
                            Arşiv
                            <span v-if="archivedCount > 0"
                                class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md text-[10px]">{{ archivedCount
                                }}</span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">

                    <div v-if="filteredQuizzes.length === 0"
                        class="col-span-full bg-white border-2 border-dashed border-slate-200 rounded-3xl p-12 text-center">
                        <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <span class="text-3xl">{{ activeTab === 'active' ? '👻' : '📦' }}</span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">
                            {{ activeTab === 'active' ? 'Henüz Aktif Sınavın Yok' : 'Arşivlenmiş Sınav Bulunmuyor' }}
                        </h3>
                        <p class="text-sm text-slate-500 mt-2">
                            {{ activeTab === 'active' ? 'Yukarıdaki butonu kullanarak ilk sınavını oluştur.' :
                                'Sınavlarını arşivleyerek burada saklayabilirsin.' }}
                        </p>
                    </div>

                    <div v-for="quiz in filteredQuizzes" :key="quiz.id"
                        class="group bg-white border border-slate-200 rounded-3xl p-6 transition-all duration-300 hover:shadow-xl hover:shadow-slate-200/50 flex flex-col"
                        :class="quiz.status === 'archived' ? 'opacity-80' : ''">

                        <div class="flex justify-between items-start mb-6">
                            <div class="pr-4">
                                <div class="mb-3">
                                    <span :class="[
                                        'px-2.5 py-1 rounded-md text-[9px] font-black uppercase tracking-widest border',
                                        (!quiz.status || quiz.status === 'active')
                                            ? 'bg-emerald-50 border-emerald-100 text-emerald-600'
                                            : 'bg-amber-50 border-amber-100 text-amber-600'
                                    ]">
                                        {{ (!quiz.status || quiz.status === 'active') ? 'Aktif Sınav' : 'Arşivlenmiş' }}
                                    </span>
                                </div>
                                <h3 class="text-xl font-bold text-slate-900 leading-tight mb-1 line-clamp-2"
                                    :title="quiz.title">
                                    {{ quiz.title }}
                                </h3>
                                <p class="text-[11px] font-black uppercase tracking-widest text-slate-400">Soru Sayısı:
                                    {{ quiz.question_count || 0 }}</p>
                            </div>
                            <div class="shrink-0 bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-lg flex items-center gap-2 cursor-pointer hover:bg-slate-100 transition-colors"
                                @click="copyCode(quiz.code)" title="Kodu Kopyala">
                                <span v-if="!quiz.status || quiz.status === 'active'"
                                    class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span v-else class="w-2 h-2 rounded-full bg-amber-400"></span>
                                <span class="text-xs font-black text-slate-700 uppercase tracking-widest">{{ quiz.code
                                }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-8 mt-auto">
                            <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100">
                                <span
                                    class="text-[9px] font-black uppercase tracking-widest text-slate-400 block mb-0.5">Oynanma</span>
                                <span class="text-lg font-black text-slate-800">{{ quiz.plays_count }} Kez</span>
                            </div>
                            <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100">
                                <span
                                    class="text-[9px] font-black uppercase tracking-widest text-slate-400 block mb-0.5">Başarı
                                    Oranı</span>
                                <span class="text-lg font-black text-emerald-600">%{{ quiz.success_rate }}</span>
                            </div>
                        </div>

                        <!-- <div class="flex items-center gap-2">
                            <Link v-if="!quiz.status || quiz.status === 'active'" 
                                  :href="`/?quiz_code=${quiz.code}`"
                                  class="flex-1 bg-slate-900 hover:bg-slate-800 text-white py-3.5 rounded-xl text-xs font-bold uppercase tracking-widest transition-all shadow-md flex items-center justify-center gap-2 active:scale-95">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                </svg>
                                Başlat
                            </Link>
                            <button v-else disabled
                                    class="flex-1 bg-slate-300 cursor-not-allowed text-white py-3.5 rounded-xl text-xs font-bold uppercase tracking-widest transition-all shadow-md flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                </svg>
                                Arşivde
                            </button>

                            <div class="flex items-center gap-1">
                                <Link :href="`/quizzes/${quiz.id}/edit`" class="w-11 h-11 flex items-center justify-center rounded-xl bg-slate-50 border border-slate-100 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 hover:border-indigo-100 transition-colors" title="Düzenle">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </Link>
                                
                                <button @click="toggleArchive(quiz.id, quiz.status)" 
                                        class="w-11 h-11 flex items-center justify-center rounded-xl bg-slate-50 border border-slate-100 transition-colors"
                                        :class="quiz.status === 'archived' ? 'text-amber-600 bg-amber-50 border-amber-100 hover:bg-slate-100 hover:text-slate-500' : 'text-slate-400 hover:text-amber-600 hover:bg-amber-50 hover:border-amber-100'"
                                        :title="quiz.status === 'archived' ? 'Arşivden Çıkar' : 'Arşivle'">
                                    
                                    <svg v-if="!quiz.status || quiz.status === 'active'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                    </svg>
                                    
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </button>

                                <button @click="deleteQuiz(quiz.id)" class="w-11 h-11 flex items-center justify-center rounded-xl bg-slate-50 border border-slate-100 text-slate-400 hover:text-red-600 hover:bg-red-50 hover:border-red-100 transition-colors" title="Sil">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div> -->

                        <div class="flex items-center gap-2">
                            <Link v-if="!quiz.status || quiz.status === 'active'" :href="`/?quiz_code=${quiz.code}`"
                                class="flex-1 bg-slate-900 hover:bg-slate-800 text-white py-3.5 rounded-xl text-xs font-bold uppercase tracking-widest transition-all shadow-md flex items-center justify-center gap-2 active:scale-95">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                                        clip-rule="evenodd" />
                                </svg>
                                Başlat
                            </Link>
                            <button v-else disabled
                                class="flex-1 bg-slate-300 cursor-not-allowed text-white py-3.5 rounded-xl text-xs font-bold uppercase tracking-widest transition-all shadow-md flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                                        clip-rule="evenodd" />
                                </svg>
                                Arşivde
                            </button>

                            <div class="flex items-center gap-1">
                                <Link :href="`/quizzes/${quiz.id}/report`"
                                    class="w-11 h-11 flex items-center justify-center rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-500 hover:text-white hover:bg-indigo-500 transition-colors"
                                    title="Sınav Raporu (Karneler)">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </Link>

                                <Link :href="`/quizzes/${quiz.id}/edit`"
                                    class="w-11 h-11 flex items-center justify-center rounded-xl bg-slate-50 border border-slate-100 text-slate-400 hover:text-blue-600 hover:bg-blue-50 hover:border-blue-100 transition-colors"
                                    title="Düzenle">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </Link>

                                <button @click="toggleArchive(quiz.id, quiz.status)"
                                    class="w-11 h-11 flex items-center justify-center rounded-xl bg-slate-50 border border-slate-100 transition-colors"
                                    :class="quiz.status === 'archived' ? 'text-amber-600 bg-amber-50 border-amber-100 hover:bg-slate-100 hover:text-slate-500' : 'text-slate-400 hover:text-amber-600 hover:bg-amber-50 hover:border-amber-100'"
                                    :title="quiz.status === 'archived' ? 'Arşivden Çıkar' : 'Arşivle'">
                                    <svg v-if="!quiz.status || quiz.status === 'active'"
                                        xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                    </svg>
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </button>

                                <button @click="deleteQuiz(quiz.id)"
                                    class="w-11 h-11 flex items-center justify-center rounded-xl bg-slate-50 border border-slate-100 text-slate-400 hover:text-red-600 hover:bg-red-50 hover:border-red-100 transition-colors"
                                    title="Sil">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'

import ToastList from '../Components/ToastList.vue'
import ConfirmModal from '../Components/ConfirmModal.vue'
import { useToast } from '../Composables/useToast'
import { useConfirm } from '../Composables/useConfirm'

const props = defineProps({
    quizzes: {
        type: Array,
        default: () => []
    }
})

const activeTab = ref('active')
const toast = useToast()
const confirm = useConfirm()

const activeCount = computed(() => {
    return props.quizzes.filter(q => !q.status || q.status === 'active').length
})

const archivedCount = computed(() => {
    return props.quizzes.filter(q => q.status === 'archived').length
})

const filteredQuizzes = computed(() => {
    if (activeTab.value === 'active') {
        return props.quizzes.filter(q => !q.status || q.status === 'active')
    } else {
        return props.quizzes.filter(q => q.status === 'archived')
    }
})

const totalPlays = computed(() => {
    return props.quizzes.reduce((toplam, quiz) => toplam + quiz.plays_count, 0);
})

const copyCode = (code) => {
    navigator.clipboard.writeText(code);
    toast.success(`Sınav kodu (${code}) başarıyla kopyalandı!`);
}

const toggleArchive = (id, currentStatus) => {
    const isCurrentlyActive = !currentStatus || currentStatus === 'active';
    confirm.requireConfirm({
        title: isCurrentlyActive ? 'Sınavı Arşivle' : 'Arşivden Çıkar',
        message: isCurrentlyActive
            ? 'Sınavı arşive aldığınızda ana listede görünmez. İşleme devam edilsin mi?'
            : 'Sınav tekrar aktifler listesine alınacaktır. Onaylıyor musunuz?',
        type: 'warning',
        confirmText: isCurrentlyActive ? 'Evet, Arşivle' : 'Evet, Geri Çıkar',
        onConfirm: () => {
            const newStatus = isCurrentlyActive ? 'archived' : 'active';
            router.patch(`/quizzes/${id}/toggle-status`, { status: newStatus }, {
                preserveScroll: true,
                onSuccess: () => toast.info(`Sınav durumu güncellendi.`)
            });
        }
    });
}

const deleteQuiz = (id) => {
    confirm.requireConfirm({
        title: 'Kalıcı Olarak Sil',
        message: 'DİKKAT: Bu sınavı kalıcı olarak silmek üzeresiniz. Sınava ait tüm soru ve kayıtlar yok edilecek. Bu işlem geri alınamaz!',
        type: 'danger',
        confirmText: 'Sınavı Sil',
        onConfirm: () => {
            router.delete(`/quizzes/${id}`, {
                preserveScroll: true,
                onSuccess: () => toast.success(`Sınav kalıcı olarak silindi.`)
            });
        }
    });
}
</script>