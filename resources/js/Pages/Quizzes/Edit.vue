<template>
    <div class="min-h-screen bg-slate-50 p-6 md:p-10 font-sans text-slate-800 pb-32">
        <div class="max-w-4xl mx-auto space-y-8">

            <div
                class="flex flex-col md:flex-row md:items-end justify-between gap-6 sticky top-0 bg-slate-50/90 backdrop-blur-md py-4 z-40 border-b border-slate-200/50">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <Link href="/dashboard" class="text-slate-400 hover:text-slate-900 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </Link>
                        <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight">
                            Sınavı Düzenle
                        </h2>
                    </div>
                    <p class="text-slate-500 font-medium ml-9">Sınav sorularını ve şıklarını güncelleyin.</p>
                </div>

                <button @click="submit" :disabled="form.processing"
                    class="bg-indigo-600 text-white px-8 py-3.5 rounded-2xl font-bold uppercase tracking-widest shadow-xl shadow-indigo-500/20 hover:bg-indigo-700 active:scale-95 transition-all disabled:opacity-50">
                    Değişiklikleri Kaydet
                </button>
            </div>

            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2 md:col-span-2">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1 block">Sınav
                            Başlığı</label>
                        <input v-model="form.title" type="text"
                            class="w-full bg-slate-50 border-0 rounded-2xl px-5 py-4 text-lg font-bold text-slate-900 focus:ring-2 focus:ring-indigo-500 transition-all outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1 block">Sınav
                            Kodu (Değiştirilemez)</label>
                        <div
                            class="w-full bg-slate-100 border border-slate-200 rounded-2xl px-5 py-4 flex items-center justify-between opacity-70">
                            <span class="font-mono font-black text-slate-500 tracking-widest text-lg">{{ quiz.code
                            }}</span>
                            <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <h3 class="text-lg font-black text-slate-900 ml-2">Sorular ({{ form.questions.length }})</h3>

                <div v-for="(q, index) in form.questions" :key="index"
                    class="bg-white p-6 md:p-8 rounded-3xl border border-slate-200 shadow-sm relative transition-all hover:shadow-md">

                    <button v-if="form.questions.length > 1" @click="removeQuestion(index)"
                        class="absolute top-3 right-3 w-8 h-8 flex items-center justify-center rounded-xl bg-red-50 text-red-400 hover:text-red-600 hover:bg-red-100 transition-colors"
                        title="Soruyu Sil">
                        ✕
                    </button>

                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <span
                                class="w-10 h-10 shrink-0 bg-slate-900 text-white rounded-xl flex items-center justify-center font-black text-sm shadow-md mt-1">
                                {{ index + 1 }}
                            </span>
                            <div class="flex-1">
                                <label
                                    class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1 block">Soru
                                    Metni</label>
                                <textarea v-model="q.text" rows="2" placeholder="Soruyu buraya yazın..."
                                    class="w-full bg-slate-50/50 border border-slate-200 focus:border-indigo-500 focus:bg-white rounded-xl p-4 text-lg font-semibold text-slate-800 outline-none transition-all resize-none"></textarea>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:ml-14">
                            <div v-for="opt in ['A', 'B', 'C', 'D']" :key="opt"
                                class="flex items-center gap-3 p-3 rounded-2xl border-2 transition-all cursor-pointer group"
                                :class="q.correct === opt ? 'border-emerald-500 bg-emerald-50/30' : 'border-slate-100 bg-white hover:border-slate-300'"
                                @click="q.correct = opt">

                                <div class="w-8 h-8 shrink-0 rounded-lg flex items-center justify-center font-black text-sm transition-colors"
                                    :class="q.correct === opt ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/30' : 'bg-slate-100 text-slate-400 group-hover:bg-slate-200'">
                                    {{ opt }}
                                </div>

                                <input v-model="q.options[opt]" @click.stop placeholder="Şıkkı yazın..."
                                    class="flex-1 bg-transparent border-0 focus:ring-0 font-medium text-slate-700 w-full p-0 outline-none">

                                <div v-if="q.correct === opt"
                                    class="w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0">
                                    ✓
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end mt-4" v-if="!q.showImageArea && !q.imagePreview">
                        <button @click.prevent="q.showImageArea = true"
                            class="text-[11px] uppercase tracking-widest font-black text-indigo-500 hover:text-indigo-700 transition-colors flex items-center gap-1.5 bg-indigo-50 hover:bg-indigo-100 px-4 py-2 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Görsel Ekle
                        </button>
                    </div>

                    <Transition enter-active-class="transition-all duration-300 ease-out"
                        enter-from-class="opacity-0 translate-y-1" enter-to-class="opacity-100 translate-y-0"
                        leave-active-class="" leave-from-class="" leave-to-class="">

                        <div v-if="q.showImageArea || q.imagePreview"
                            class="my-6 bg-slate-50 p-6 rounded-2xl border border-slate-200 shadow-inner">

                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-xs uppercase tracking-widest font-black text-slate-400">Soru Görseli
                                </h4>
                                <button @click.prevent="removeQuestionImage(index)"
                                    class="text-[10px] uppercase font-black text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-colors">
                                    {{ q.imagePreview ? 'Görseli Sil' : 'Vazgeç' }}
                                </button>
                            </div>

                            <div v-if="!q.imagePreview" class="relative group">
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
                                <input type="file" @change="(e) => handleQuestionImageUpload(e, index)" accept="image/*"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            </div>

                            <div v-else class="relative w-full max-w-sm mx-auto">
                                <img :src="q.imagePreview"
                                    class="w-full h-48 object-contain rounded-xl border border-slate-200 bg-white shadow-sm p-2"
                                    alt="Soru Önizleme">
                            </div>
                        </div>
                    </Transition>
                </div>

                <button @click="addQuestion"
                    class="w-full py-6 border-2 border-dashed border-slate-300 rounded-3xl text-slate-400 font-bold uppercase tracking-widest hover:bg-slate-100 hover:border-slate-400 hover:text-slate-600 transition-all">
                    + Yeni Soru Ekle
                </button>
            </div>

        </div>
    </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'

const props = defineProps({
    quiz: {
        type: Object,
        required: true
    }
})

const initialQuestions = (props.quiz.questions && props.quiz.questions.length > 0)
    ? props.quiz.questions.map(q => ({
        ...q,
        existing_image: q.image || null,
        image: null,
        imagePreview: q.image ? `/storage/${q.image}` : null,
        showImageArea: !!q.image
    }))
    : [{ text: '', options: { A: '', B: '', C: '', D: '' }, correct: 'A', existing_image: null, image: null, imagePreview: null, showImageArea: false }];

const form = useForm({
    _method: 'put',
    title: props.quiz.title,
    questions: initialQuestions
})

const addQuestion = () => {
    form.questions.push({
        text: '',
        options: { A: '', B: '', C: '', D: '' },
        correct: 'A',
        existing_image: null,
        image: null,
        imagePreview: null,
        showImageArea: false,
    })
}

const removeQuestion = (index) => {
    if (form.questions.length > 1) {
        if (confirm(`${index + 1}. soruyu silmek istediğinize emin misiniz?`)) {
            form.questions.splice(index, 1)
        }
    } else {
        alert("Sınavda en az 1 soru bulunmalıdır.");
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
    form.questions[index].existing_image = null;
    form.questions[index].imagePreview = null;
    form.questions[index].showImageArea = false;
};

const submit = () => {
    if (!form.title.trim()) {
        alert("Lütfen sınav başlığını girin.");
        return;
    }

    form.post(`/quizzes/${props.quiz.id}`, {
        preserveScroll: true
    })
}
</script>