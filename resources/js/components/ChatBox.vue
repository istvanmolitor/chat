<template>
  <div class="bg-white rounded-xl shadow-sm w-full flex flex-col">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 16c0 1.1-.9 2-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2v11z" />
      </svg>
      <h2 class="text-base font-semibold text-gray-800">Üzenetküldés</h2>
    </div>

    <!-- Message list -->
    <div
      ref="scrollEl"
      class="flex-1 overflow-y-auto px-6 py-4 space-y-3 min-h-55 max-h-90"
    >
      <!-- Loading -->
      <div v-if="loadingMessages" class="flex justify-center py-8">
        <svg class="animate-spin h-6 w-6 text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
        </svg>
      </div>

      <!-- Empty -->
      <div v-else-if="messages.length === 0" class="flex flex-col items-center justify-center h-full py-8 text-gray-400 text-sm gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 16c0 1.1-.9 2-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2v11z" />
        </svg>
        Még nincs üzenet. Kezdj el írni!
      </div>

      <!-- Messages -->
      <template v-else>
        <div
          v-for="msg in messages"
          :key="msg.id"
          class="flex"
          :class="isOwnMessage(msg) ? 'justify-end' : 'justify-start'"
        >
          <div
            class="max-w-[75%] px-4 py-2 rounded-2xl text-sm leading-relaxed wrap-break-word"
            :class="isOwnMessage(msg)
              ? 'bg-indigo-600 text-white rounded-br-sm'
              : 'bg-gray-100 text-gray-800 rounded-bl-sm'"
          >
            {{ msg.body }}
            <div
              class="text-[10px] mt-1 text-right"
              :class="isOwnMessage(msg) ? 'text-indigo-200' : 'text-gray-400'"
            >
              {{ formatTime(msg.created_at) }}
            </div>
          </div>
        </div>
      </template>
    </div>

    <!-- Input -->
    <form @submit.prevent="sendMessage" class="px-4 py-3 border-t border-gray-100 flex gap-2 items-end">
      <textarea
        v-model="newBody"
        rows="2"
        placeholder="Üzenet írása..."
        class="flex-1 resize-none rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition"
        @keydown.enter.exact.prevent="sendMessage"
      />
      <button
        type="submit"
        :disabled="sending || !newBody.trim()"
        class="shrink-0 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-lg px-4 py-2 text-sm font-medium transition flex items-center gap-1.5"
      >
        <svg v-if="sending" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M22 2L11 13M22 2L15 22l-4-9-9-4 20-7z" />
        </svg>
        Küldés
      </button>
    </form>

    <!-- Send error -->
    <p v-if="sendError" class="px-6 pb-3 text-xs text-red-500">{{ sendError }}</p>
  </div>
</template>

<script setup>
import { ref, watch, nextTick, onMounted, onUnmounted } from 'vue';
import { storeToRefs } from 'pinia';
import api from '../api/axios.js';
import { useAuthStore } from '../stores/auth.js';

const props = defineProps({
  userId: {
    type: Number,
    required: true,
  },
});

const authStore = useAuthStore();
const { user: authUser } = storeToRefs(authStore);

const messages = ref([]);
const loadingMessages = ref(false);
const newBody = ref('');
const sending = ref(false);
const sendError = ref(null);
const scrollEl = ref(null);

let pollTimer = null;

async function fetchMessages() {
  loadingMessages.value = true;
  try {
    const { data } = await api.get(`/messages/${props.userId}`);
    messages.value = data.data;
    await scrollToBottom();
  } catch {
    // silently fail on background poll
  } finally {
    loadingMessages.value = false;
  }
}

async function sendMessage() {
  const body = newBody.value.trim();
  if (!body || sending.value) return;

  sending.value = true;
  sendError.value = null;
  try {
    const { data } = await api.post(`/messages/${props.userId}`, { body });
    messages.value.push(data.data);
    newBody.value = '';
    await scrollToBottom();
  } catch (e) {
    sendError.value = e?.response?.data?.message ?? 'Nem sikerült elküldeni az üzenetet.';
  } finally {
    sending.value = false;
  }
}

async function scrollToBottom() {
  await nextTick();
  if (scrollEl.value) {
    scrollEl.value.scrollTop = scrollEl.value.scrollHeight;
  }
}

async function pollMessages() {
  try {
    const { data } = await api.get(`/messages/${props.userId}/unread`);
    const unreadMessages = data.data ?? [];

    if (unreadMessages.length > 0) {
      const existingIds = new Set(messages.value.map((message) => Number(message.id)));
      const newMessages = unreadMessages.filter((message) => !existingIds.has(Number(message.id)));

      if (newMessages.length > 0) {
        messages.value.push(...newMessages);
      }

      await scrollToBottom();
    }
  } catch {
    // ignore poll errors
  }
}

watch(() => props.userId, () => {
  messages.value = [];
  fetchMessages();
});

watch(
  () => messages.value.length,
  async (newLength, oldLength) => {
    if (newLength === oldLength) {
      return;
    }

    await scrollToBottom();
  }
);

onMounted(() => {
  fetchMessages();
  pollTimer = setInterval(pollMessages, 5000);
});

onUnmounted(() => {
  clearInterval(pollTimer);
});

function formatTime(iso) {
  return new Intl.DateTimeFormat('hu-HU', {
    hour: '2-digit',
    minute: '2-digit',
    month: 'short',
    day: 'numeric',
  }).format(new Date(iso));
}

function isOwnMessage(message) {
  if (!authUser.value?.id) {
    return false;
  }

  return Number(message.sender_id) === Number(authUser.value.id);
}
</script>
