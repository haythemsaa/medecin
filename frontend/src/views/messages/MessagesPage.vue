<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <router-link to="/dashboard" class="flex items-center space-x-2">
            <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center">
              <span class="text-white font-bold text-xl">S</span>
            </div>
            <span class="text-xl font-bold text-primary-600">Seha Digital</span>
          </router-link>
          <div class="flex items-center space-x-4">
            <router-link to="/dashboard" class="btn btn-secondary text-sm">
              Tableau de bord
            </router-link>
          </div>
        </div>
      </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Conversations List -->
        <div class="lg:col-span-1 card max-h-[calc(100vh-200px)] overflow-y-auto">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold">Messages</h2>
            <span v-if="unreadCount > 0" class="px-2 py-1 bg-red-500 text-white text-xs rounded-full">
              {{ unreadCount }}
            </span>
          </div>

          <div v-if="loading" class="text-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600 mx-auto"></div>
          </div>

          <div v-else-if="conversations.length === 0" class="text-center py-8 text-gray-500">
            <p class="text-4xl mb-2">💬</p>
            <p>Aucune conversation</p>
          </div>

          <div v-else class="space-y-2">
            <div
              v-for="conversation in conversations"
              :key="conversation.id"
              @click="selectConversation(conversation)"
              :class="[
                'p-3 rounded-lg cursor-pointer transition-colors',
                selectedConversation?.id === conversation.id
                  ? 'bg-primary-50 border-2 border-primary-600'
                  : 'hover:bg-gray-50 border-2 border-transparent'
              ]"
            >
              <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0">
                  <span class="text-xl">{{ getParticipantIcon(conversation) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="font-medium text-sm truncate">
                    {{ getParticipantName(conversation) }}
                  </p>
                  <p class="text-xs text-gray-500 truncate">
                    {{ getLastMessagePreview(conversation) }}
                  </p>
                  <p class="text-xs text-gray-400">
                    {{ formatDate(conversation.last_message_at) }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Messages Area -->
        <div class="lg:col-span-2 card flex flex-col" style="height: calc(100vh - 200px)">
          <div v-if="!selectedConversation" class="flex-1 flex items-center justify-center text-gray-500">
            <div class="text-center">
              <p class="text-6xl mb-4">💬</p>
              <p class="text-lg">Sélectionnez une conversation</p>
            </div>
          </div>

          <template v-else>
            <!-- Chat Header -->
            <div class="flex items-center justify-between pb-4 border-b">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                  <span class="text-xl">{{ getParticipantIcon(selectedConversation) }}</span>
                </div>
                <div>
                  <p class="font-semibold">{{ getParticipantName(selectedConversation) }}</p>
                  <p class="text-xs text-gray-500">
                    {{ getParticipantSpeciality(selectedConversation) }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Messages List -->
            <div ref="messagesContainer" class="flex-1 overflow-y-auto py-4 space-y-4">
              <div
                v-for="message in messages"
                :key="message.id"
                :class="[
                  'flex',
                  message.sender_id === authStore.user?.id ? 'justify-end' : 'justify-start'
                ]"
              >
                <div
                  :class="[
                    'max-w-xs md:max-w-md lg:max-w-lg px-4 py-2 rounded-lg',
                    message.sender_id === authStore.user?.id
                      ? 'bg-primary-600 text-white'
                      : 'bg-gray-100 text-gray-900'
                  ]"
                >
                  <p class="text-sm break-words">{{ message.message }}</p>
                  <p
                    :class="[
                      'text-xs mt-1',
                      message.sender_id === authStore.user?.id ? 'text-primary-100' : 'text-gray-500'
                    ]"
                  >
                    {{ formatTime(message.created_at) }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Message Input -->
            <div class="pt-4 border-t">
              <form @submit.prevent="sendMessage" class="flex items-center space-x-2">
                <input
                  v-model="newMessage"
                  type="text"
                  class="input flex-1"
                  placeholder="Écrivez votre message..."
                  maxlength="2000"
                />
                <button
                  type="submit"
                  :disabled="!newMessage.trim() || sending"
                  class="btn btn-primary"
                >
                  {{ sending ? 'Envoi...' : 'Envoyer' }}
                </button>
              </form>
              <p class="text-xs text-gray-500 mt-1">
                ⚠️ Pour les urgences, appelez le 190 ou contactez un service d'urgence
              </p>
            </div>
          </template>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, nextTick, watch } from 'vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const authStore = useAuthStore()

const loading = ref(false)
const sending = ref(false)
const conversations = ref<any[]>([])
const selectedConversation = ref<any>(null)
const messages = ref<any[]>([])
const newMessage = ref('')
const unreadCount = ref(0)
const messagesContainer = ref<HTMLElement>()

async function fetchConversations() {
  loading.value = true
  try {
    const response = await api.get('/messages/conversations')
    conversations.value = response.data
  } catch (error) {
    console.error('Error fetching conversations:', error)
  } finally {
    loading.value = false
  }
}

async function fetchUnreadCount() {
  try {
    const response = await api.get('/messages/unread-count')
    unreadCount.value = response.data.unread_count
  } catch (error) {
    console.error('Error fetching unread count:', error)
  }
}

async function selectConversation(conversation: any) {
  selectedConversation.value = conversation
  await fetchMessages(conversation.id)
}

async function fetchMessages(conversationId: number) {
  try {
    const response = await api.get(`/messages/conversations/${conversationId}/messages`)
    messages.value = response.data.data || response.data
    await nextTick()
    scrollToBottom()
    await fetchUnreadCount()
  } catch (error) {
    console.error('Error fetching messages:', error)
  }
}

async function sendMessage() {
  if (!newMessage.value.trim() || !selectedConversation.value) return

  sending.value = true
  try {
    await api.post(`/messages/conversations/${selectedConversation.value.id}/messages`, {
      message: newMessage.value
    })

    newMessage.value = ''
    await fetchMessages(selectedConversation.value.id)
  } catch (error) {
    console.error('Error sending message:', error)
    alert('Erreur lors de l\'envoi du message')
  } finally {
    sending.value = false
  }
}

function scrollToBottom() {
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}

function getParticipantName(conversation: any): string {
  if (authStore.isPatient) {
    return `Dr. ${conversation.medecin?.first_name} ${conversation.medecin?.last_name}`
  } else {
    return `${conversation.patient?.first_name} ${conversation.patient?.last_name}`
  }
}

function getParticipantIcon(conversation: any): string {
  return authStore.isPatient ? '👨‍⚕️' : '👤'
}

function getParticipantSpeciality(conversation: any): string {
  if (authStore.isPatient) {
    return conversation.medecin?.speciality || ''
  }
  return ''
}

function getLastMessagePreview(conversation: any): string {
  const lastMessage = conversation.messages?.[0]
  return lastMessage?.message || 'Aucun message'
}

function formatDate(dateString: string): string {
  if (!dateString) return ''
  const date = new Date(dateString)
  const now = new Date()
  const diffDays = Math.floor((now.getTime() - date.getTime()) / (1000 * 60 * 60 * 24))

  if (diffDays === 0) {
    return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
  } else if (diffDays === 1) {
    return 'Hier'
  } else if (diffDays < 7) {
    return `${diffDays}j`
  } else {
    return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' })
  }
}

function formatTime(dateString: string): string {
  return new Date(dateString).toLocaleTimeString('fr-FR', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

onMounted(() => {
  fetchConversations()
  fetchUnreadCount()

  // Poll for new messages every 5 seconds
  const interval = setInterval(() => {
    if (selectedConversation.value) {
      fetchMessages(selectedConversation.value.id)
    }
    fetchUnreadCount()
  }, 5000)

  // Cleanup on unmount
  return () => clearInterval(interval)
})
</script>
