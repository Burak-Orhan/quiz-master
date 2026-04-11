<template>
    <div class="fixed top-24 right-6 z-[100] flex flex-col gap-3 pointer-events-none w-full max-w-sm">
        <TransitionGroup name="toast">
            <div v-for="toast in toasts" :key="toast.id"
                class="pointer-events-auto flex items-center gap-4 backdrop-blur-xl transform-gpu will-change-[transform,opacity] p-4 rounded-2xl transition-all overflow-hidden relative group border"
                :class="{
                    'bg-emerald-50/90 border-emerald-200/60 shadow-[0_10px_40px_-10px_rgba(16,185,129,0.2)]': toast.type === 'success',
                    'bg-red-50/90 border-red-200/60 shadow-[0_10px_40px_-10px_rgba(239,68,68,0.2)]': toast.type === 'error',
                    'bg-amber-50/90 border-amber-200/60 shadow-[0_10px_40px_-10px_rgba(245,158,11,0.2)]': toast.type === 'warning',
                    'bg-indigo-50/90 border-indigo-200/60 shadow-[0_10px_40px_-10px_rgba(99,102,241,0.2)]': toast.type === 'info'
                }">

                <div class="w-10 h-10 shrink-0 rounded-xl flex items-center justify-center shadow-inner" :class="{
                    'bg-emerald-100 text-emerald-600': toast.type === 'success',
                    'bg-red-100 text-red-600': toast.type === 'error',
                    'bg-amber-100 text-amber-600': toast.type === 'warning',
                    'bg-indigo-100 text-indigo-600': toast.type === 'info'
                }">

                    <svg v-if="toast.type === 'success'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                    <svg v-else-if="toast.type === 'error'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    <svg v-else-if="toast.type === 'warning'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                </div>

                <div class="flex-1 min-w-0 pr-4">
                    <p class="text-sm font-bold leading-snug" :class="{
                        'text-emerald-900': toast.type === 'success',
                        'text-red-900': toast.type === 'error',
                        'text-amber-900': toast.type === 'warning',
                        'text-indigo-900': toast.type === 'info'
                    }">
                        {{ toast.message }}
                    </p>
                </div>

                <button @click="removeToast(toast.id)"
                    class="absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-lg opacity-0 group-hover:opacity-100 transition-all"
                    :class="{
                        'text-emerald-500 hover:bg-emerald-100': toast.type === 'success',
                        'text-red-500 hover:bg-red-100': toast.type === 'error',
                        'text-amber-500 hover:bg-amber-100': toast.type === 'warning',
                        'text-indigo-500 hover:bg-indigo-100': toast.type === 'info'
                    }">
                    ✕
                </button>
            </div>
        </TransitionGroup>
    </div>
</template>

<script setup>
import { useToast } from '../Composables/useToast'
const { toasts, removeToast } = useToast()
</script>

<style scoped>
.toast-enter-active {
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.toast-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.toast-enter-from {
    opacity: 0;
    transform: translateX(50px) scale(0.9);
}

.toast-leave-to {
    opacity: 0;
    transform: scale(0.95);
}
</style>