<template>
  <AppLayout>
    <div>
      <!-- Back button -->
      <button
        @click="$router.back()"
        class="mb-6 flex items-center gap-1.5 text-sm text-gray-500 hover:text-indigo-600 transition"
      >
        ← Vissza
      </button>

      <!-- Loading state -->
      <div v-if="loading" class="flex justify-center py-16">
        <svg class="animate-spin h-8 w-8 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
        </svg>
      </div>

      <!-- Error state -->
      <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3">
        {{ error }}
      </div>

      <!-- Profile card -->
      <div v-else-if="user" class="bg-white rounded-xl shadow-sm p-5 sm:p-8 w-full sm:max-w-lg">
        <!-- Avatar -->
        <div class="flex items-center gap-4 sm:gap-5 mb-6 sm:mb-8">
          <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xl sm:text-2xl shrink-0">
            {{ initials(user.name) }}
          </div>
          <div class="min-w-0">
            <h1 class="text-lg sm:text-xl font-bold text-gray-800 truncate">{{ user.name }}</h1>
            <p class="text-sm text-gray-400 truncate">{{ user.email }}</p>
          </div>
        </div>

        <!-- Details -->
        <dl class="divide-y divide-gray-100">
          <div class="py-3 flex justify-between items-center">
            <dt class="text-sm text-gray-500">Státusz</dt>
            <dd>
              <span
                v-if="user.is_active"
                class="flex items-center gap-1.5 text-sm text-green-600 font-medium"
              >
                <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                Aktív
              </span>
              <span v-else class="flex items-center gap-1.5 text-sm text-gray-400 font-medium">
                <span class="w-2 h-2 rounded-full bg-gray-300 inline-block"></span>
                Inaktív
              </span>
            </dd>
          </div>
          <div class="py-3 flex justify-between items-center">
            <dt class="text-sm text-gray-500">Utoljára aktív</dt>
            <dd class="text-sm text-gray-700">
              {{ user.last_active_at ? formatDate(user.last_active_at) : 'Soha' }}
            </dd>
          </div>
        </dl>
      </div>

      <!-- Chat -->
      <ChatBox v-if="user" :user-id="user.id" />
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import AppLayout from '../components/AppLayout.vue';
import ChatBox from '../components/ChatBox.vue';
import api from '../api/axios.js';

const route = useRoute();

const user = ref(null);
const loading = ref(false);
const error = ref(null);

async function fetchUser() {
  loading.value = true;
  error.value = null;
  try {
    const { data } = await api.get(`/users/${route.params.id}`);
    user.value = data;
  } catch (e) {
    error.value = e?.response?.status === 404
      ? 'A felhasználó nem található.'
      : 'Nem sikerült betölteni a profilt.';
  } finally {
    loading.value = false;
  }
}

function initials(name) {
  if (!name) return '?';
  return name
    .split(' ')
    .slice(0, 2)
    .map((w) => w[0])
    .join('')
    .toUpperCase();
}

function formatDate(iso) {
  return new Intl.DateTimeFormat('hu-HU', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }).format(new Date(iso));
}

onMounted(fetchUser);
</script>

