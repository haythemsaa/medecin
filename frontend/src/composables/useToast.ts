import { getCurrentInstance } from 'vue'
import type { Toast } from '@/components/ToastNotification.vue'

export function useToast() {
  const instance = getCurrentInstance()
  const toastComponent = instance?.appContext.config.globalProperties.$toast

  function showToast(toast: Omit<Toast, 'id'>) {
    if (toastComponent) {
      toastComponent.addToast(toast)
    } else {
      // Fallback to console if toast component is not available
      console.log(`[${toast.type.toUpperCase()}]`, toast.title || toast.message)
    }
  }

  function success(message: string, title?: string) {
    showToast({ type: 'success', message, title })
  }

  function error(message: string, title?: string) {
    showToast({ type: 'error', message, title })
  }

  function warning(message: string, title?: string) {
    showToast({ type: 'warning', message, title })
  }

  function info(message: string, title?: string) {
    showToast({ type: 'info', message, title })
  }

  return {
    showToast,
    success,
    error,
    warning,
    info
  }
}
