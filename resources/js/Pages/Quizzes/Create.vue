<template>
    <div class="min-h-screen bg-slate-50 p-6 md:p-10 font-sans text-slate-800 pb-32">

        <ToastList />
        <ConfirmModal />

        <div class="max-w-4xl mx-auto space-y-8">

            <div
                class="flex flex-col md:flex-row md:items-end justify-between gap-6 sticky top-0 bg-slate-50/90 backdrop-blur-md py-4 z-40 border-b border-slate-200/50">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <Link href="/dashboard"
                            class="w-10 h-10 flex items-center justify-center rounded-full bg-white border border-slate-200 text-slate-400 hover:text-slate-900 hover:shadow-md transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </Link>
                        <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight">
                            Yeni Sınav Oluştur
                        </h2>
                    </div>
                    <p class="text-slate-500 font-medium ml-12">Öğrencilerin için yeni bir meydan okuma hazırla.</p>
                </div>

                <button @click="submit" :disabled="form.processing"
                    class="bg-slate-900 text-white px-8 py-3.5 rounded-2xl font-bold uppercase tracking-widest shadow-xl shadow-slate-900/20 hover:bg-slate-800 active:scale-95 transition-all disabled:opacity-50 flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                    Sınavı Kaydet
                </button>
            </div>

            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm mt-8 relative overflow-hidden group">
                <div
                    class="absolute -right-6 -top-6 w-32 h-32 bg-indigo-50/50 rounded-full transition-transform group-hover:scale-150 duration-700 pointer-events-none">
                </div>

                <div class="relative z-10">
                    <label
                        class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1 block mb-2">Sınav
                        Başlığı</label>
                    <input type="text" v-model="form.title" placeholder="Örn: PHP Temelleri Final Sınavı"
                        class="w-full bg-slate-50 border-0 rounded-2xl px-6 py-5 text-xl font-bold text-slate-900 focus:ring-2 focus:ring-indigo-500 transition-all placeholder-slate-300 outline-none">

                    <p v-if="form.errors.title"
                        class="text-xs text-red-500 mt-3 ml-2 font-bold flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ form.errors.title }}
                    </p>
                </div>
            </div>

            <div class="space-y-6 pt-4">
                <div class="flex items-center justify-between ml-2 mb-4">
                    <h3 class="text-lg font-black text-slate-900">Sorular</h3>
                    <span class="bg-slate-200 text-slate-600 px-3 py-1 rounded-lg text-xs font-bold">{{
                        form.questions.length }} Soru</span>
                </div>

                <div v-for="(question, qIndex) in form.questions" :key="qIndex"
                    class="bg-white p-6 md:p-8 rounded-3xl border border-slate-200 shadow-sm relative group transition-all duration-300 hover:shadow-md">

                    <button v-if="form.questions.length > 1" @click="removeQuestion(qIndex)"
                        class="absolute top-3 right-3 w-8 h-8 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors"
                        title="Soruyu Sil">
                        ✕
                    </button>

                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <span
                                class="w-10 h-10 shrink-0 bg-slate-900 text-white rounded-xl flex items-center justify-center font-black text-sm shadow-md mt-1">
                                {{ qIndex + 1 }}
                            </span>
                            <div class="flex-1">
                                <label
                                    class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1 block">Soru
                                    Metni</label>
                                <textarea v-model="question.text" rows="2" placeholder="Sorunuzu buraya yazın..."
                                    class="w-full bg-slate-50/50 border border-slate-200 focus:border-indigo-500 focus:bg-white rounded-xl p-4 text-lg font-semibold text-slate-800 outline-none transition-all resize-none"></textarea>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:ml-14">
                            <div v-for="(optText, optKey) in ['A', 'B', 'C', 'D']" :key="optKey"
                                class="flex items-center gap-3 p-3 rounded-2xl border-2 transition-all cursor-pointer group"
                                :class="question.correct === optText ? 'border-emerald-500 bg-emerald-50/40' : 'border-slate-100 bg-white hover:border-slate-300'"
                                @click="question.correct = optText">

                                <div class="w-8 h-8 shrink-0 rounded-lg flex items-center justify-center font-black text-sm transition-colors"
                                    :class="question.correct === optText ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/30' : 'bg-slate-100 text-slate-400 group-hover:bg-slate-200'">
                                    {{ optText }}
                                </div>

                                <input type="text" v-model="question.options[optText]" @click.stop
                                    :placeholder="optText + ' Şıkkı'"
                                    class="flex-1 bg-transparent border-0 focus:ring-0 font-medium text-slate-700 w-full p-0 outline-none placeholder-slate-300">

                                <div v-if="question.correct === optText"
                                    class="w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end mt-4" v-if="!question.showImageArea && !question.imagePreview">
                        <button @click.prevent="question.showImageArea = true"
                            class="text-[11px] uppercase tracking-widest font-black text-indigo-500 hover:text-indigo-700 transition-colors flex items-center gap-1.5 bg-indigo-50 hover:bg-indigo-100 px-4 py-2 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Görsel Ekle
                        </button>
                    </div>

                    <Transition enter-active-class="transition-all duration-500 ease-out"
                        enter-from-class="opacity-0 translate-y-1" enter-to-class="opacity-100 translate-y-0"
                        leave-active-class="" leave-from-class="" leave-to-class="">
                        <div v-if="question.showImageArea || question.imagePreview"
                            class="my-6 bg-slate-50 p-6 rounded-2xl border border-slate-200 shadow-inner">

                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-xs uppercase tracking-widest font-black text-slate-400">Soru Görseli
                                </h4>
                                <button @click.prevent="removeQuestionImage(qIndex)"
                                    class="text-[10px] uppercase font-black text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-colors">
                                    {{ question.imagePreview ? 'Görseli Sil' : 'Vazgeç' }}
                                </button>
                            </div>

                            <div v-if="!question.imagePreview" class="relative group">
                                <div
                                    class="w-full h-32 bg-white border-2 border-dashed border-slate-300 rounded-xl flex flex-col items-center justify-center transition-all group-hover:border-indigo-400 group-hover:bg-indigo-50/50 cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-6 w-6 text-slate-300 group-hover:text-indigo-500 mb-2 transition-colors"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    <span class="text-xs font-bold text-slate-500 group-hover:text-indigo-600">Tıkla
                                        veya Sürükle</span>
                                </div>
                                <input type="file" @change="(e) => handleQuestionImageUpload(e, qIndex)"
                                    accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            </div>

                            <div v-else class="relative w-full max-w-sm mx-auto">
                                <img :src="question.imagePreview"
                                    class="w-full h-48 object-contain rounded-xl border border-slate-200 bg-white shadow-sm p-2"
                                    alt="Soru Önizleme">
                            </div>
                        </div>
                    </Transition>

                </div>

                <button @click="addQuestion"
                    class="w-full py-6 border-2 border-dashed border-slate-300 rounded-3xl text-slate-400 font-bold uppercase tracking-widest hover:bg-slate-100 hover:border-slate-400 hover:text-slate-600 transition-all active:scale-95">
                    + Yeni Soru Ekle
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'

import ToastList from '../../Components/ToastList.vue'
import ConfirmModal from '../../Components/ConfirmModal.vue'
import { useToast } from '../../Composables/useToast'
import { useConfirm } from '../../Composables/useConfirm'

const toast = useToast()
const confirm = useConfirm()

const form = useForm({
    title: '',
    questions: [
        {
            text: '',
            options: { A: '', B: '', C: '', D: '' },
            correct: 'A'
        }
    ]
})

const addQuestion = () => {
    form.questions.push({
        text: '',
        options: { A: '', B: '', C: '', D: '' },
        correct: 'A',
        image: null,
        imagePreview: null,
        showImageArea: false,
    })

    setTimeout(() => {
        window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' })
    }, 100)
}

const removeQuestion = (index) => {
    if (form.questions.length > 1) {
        confirm.requireConfirm({
            title: 'Soruyu Sil',
            message: `${index + 1}. soruyu silmek istediğinize emin misiniz? Bu işlem geri alınamaz.`,
            type: 'danger',
            confirmText: 'Evet, Sil',
            onConfirm: () => {
                form.questions.splice(index, 1)
                toast.info('Soru başarıyla kaldırıldı.')
            }
        })
    }
}

const handleQuestionImageUpload = (event, index) => {
    const file = event.target.files[0];
    if (file) {
        form.questions[index].image = file;
        form.questions[index].imagePreview = URL.createObjectURL(file);
    }
};

const removeQuestionImage = (index) => {
    form.questions[index].image = null;
    form.questions[index].imagePreview = null;
    form.questions[index].showImageArea = false;
};

const submit = () => {
    if (!form.title.trim()) {
        toast.warning('Lütfen sınav için bir başlık belirleyin.');
        return;
    }

    const hasEmptyQuestion = form.questions.some(q => !q.text.trim());
    if (hasEmptyQuestion) {
        toast.warning('Lütfen tüm soru metinlerini eksiksiz doldurun.');
        return;
    }

    const hasEmptyOption = form.questions.some(q => {
        return Object.values(q.options).some(optText => !optText.trim());
    });

    if (hasEmptyOption) {
        toast.warning('Lütfen tüm soruların şıklarını (A, B, C, D) eksiksiz doldurun.');
        return;
    }

    confirm.requireConfirm({
        title: 'Sınavı Kaydet ve Yayınla',
        message: 'Tüm sorularınız hazır mı? Onayladığınızda sınav yayınlanacak ve oynamaya hazır hale gelecektir.',
        type: 'info',
        confirmText: 'Sınavı Yayınla',
        onConfirm: () => {
            form.post('/quizzes', {
                preserveScroll: true,
                onSuccess: () => {
                }
            })
        }
    })
}
</script>