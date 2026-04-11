import { ref } from 'vue'

const toasts = ref([])
let toastId = 0

export function useToast() {
    const addToast = (message, type = 'success', duration = 3000) => {
        const id = toastId++
        toasts.value.push({ id, message, type })

        setTimeout(() => {
            removeToast(id)
        }, duration)
    }

    const removeToast = (id) => {
        toasts.value = toasts.value.filter(t => t.id !== id)
    }

    const success = (message, duration) => addToast(message, 'success', duration)
    const error = (message, duration) => addToast(message, 'error', duration)
    const warning = (message, duration) => addToast(message, 'warning', duration)
    const info = (message, duration) => addToast(message, 'info', duration)

    return {
        toasts,
        success,
        error,
        warning,
        info,
        removeToast
    }
}