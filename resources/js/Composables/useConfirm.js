import { reactive } from 'vue'

const confirmState = reactive({
    isOpen: false,
    title: '',
    message: '',
    type: 'danger', //danger, warning, info
    confirmText: 'Onayla',
    cancelText: 'Vazgeç',
    onConfirm: null,
    onCancel: null
})

export function useConfirm() {
    
    const requireConfirm = (options) => {
        confirmState.title = options.title || 'Emin misiniz?'
        confirmState.message = options.message || 'Bu işlemi gerçekleştirmek istediğinize emin misiniz?'
        confirmState.type = options.type || 'danger'
        confirmState.confirmText = options.confirmText || 'Evet, Onayla'
        confirmState.cancelText = options.cancelText || 'İptal'
        confirmState.onConfirm = options.onConfirm || null
        confirmState.onCancel = options.onCancel || null
        
        confirmState.isOpen = true
    }

    const accept = () => {
        if (confirmState.onConfirm) confirmState.onConfirm()
        close()
    }

    const reject = () => {
        if (confirmState.onCancel) confirmState.onCancel()
        close()
    }

    const close = () => {
        confirmState.isOpen = false
    }

    return {
        confirmState,
        requireConfirm,
        accept,
        reject,
        close
    }
}