<template>
    <Teleport to="body">
        <Transition name="fade">
            <div v-if="confirmState.isOpen"
                class="fixed inset-0 z-[100] bg-slate-900/30 backdrop-blur-md transform-gpu will-change-opacity"
                style="transform: translateZ(0); backface-visibility: hidden" @click="reject"></div>
        </Transition>

        <Transition name="zoom">
            <div v-if="confirmState.isOpen"
                class="fixed inset-0 z-[101] flex items-center justify-center p-4 sm:p-6 pointer-events-none">
                <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 sm:p-8 pointer-events-auto">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center mb-5 shadow-inner" :class="{
                            'bg-red-50 text-red-500':
                                confirmState.type === 'danger',
                            'bg-amber-50 text-amber-500':
                                confirmState.type === 'warning',
                            'bg-indigo-50 text-indigo-500':
                                confirmState.type === 'info',
                        }">
                            <svg v-if="confirmState.type === 'danger'" xmlns="http://www.w3.org/2000/svg"
                                class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>

                            <svg v-else-if="confirmState.type === 'warning'" xmlns="http://www.w3.org/2000/svg"
                                class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>

                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>

                        <h3 class="text-xl font-black text-slate-900 mb-2">
                            {{ confirmState.title }}
                        </h3>
                        <p class="text-slate-500 font-medium text-sm">
                            {{ confirmState.message }}
                        </p>
                    </div>

                    <div class="mt-8 flex gap-3">
                        <button @click="reject"
                            class="flex-1 py-3.5 px-4 bg-slate-50 text-slate-600 font-bold rounded-xl hover:bg-slate-100 transition-colors">
                            {{ confirmState.cancelText }}
                        </button>

                        <button @click="accept"
                            class="flex-1 py-3.5 px-4 text-white font-bold rounded-xl shadow-md transition-all active:scale-95"
                            :class="{
                                'bg-red-500 hover:bg-red-600 shadow-red-500/20':
                                    confirmState.type === 'danger',
                                'bg-amber-500 hover:bg-amber-600 shadow-amber-500/20':
                                    confirmState.type === 'warning',
                                'bg-indigo-600 hover:bg-indigo-700 shadow-indigo-500/20':
                                    confirmState.type === 'info',
                            }">
                            {{ confirmState.confirmText }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { useConfirm } from "../Composables/useConfirm";
const { confirmState, accept, reject } = useConfirm();
</script>

<style scoped>
.fade-enter-active {
    transition: opacity 0.15s ease-out;
}

.fade-leave-active {
    transition: opacity 0.2s ease-in;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.zoom-enter-active {
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) 0.08s;
}

.zoom-leave-active {
    transition: all 0.15s ease-in;
}

.zoom-enter-from,
.zoom-leave-to {
    opacity: 0;
    transform: scale(0.9) translateY(15px);
}
</style>
