<template>
  <AppLayout>
    <div>
      <h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6">Aktív felhasználók</h1>

      <!-- Search input -->
      <div class="mb-4 w-full sm:max-w-sm">
        <input
          v-model="searchQuery"
          type="search"
          placeholder="Keresés név szerint…"
          class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition"
        />
      </div>

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

      <!-- Empty state -->
      <div v-else-if="users.length === 0" class="text-center py-16 text-gray-400 text-lg">
        {{ searchQuery ? 'Nincs találat erre a névre.' : 'Jelenleg rajtad kívül nincs aktív felhasználó.' }}
      </div>

      <!-- User list -->
      <template v-else>
        <div class="bg-white rounded-xl shadow-sm divide-y divide-gray-100">
          <RouterLink
            v-for="user in users"
            :key="user.id"
            :to="{ name: 'user-profile', params: { id: user.id } }"
            class="flex items-center gap-4 px-5 py-4 hover:bg-indigo-50 transition"
          >
            <!-- Avatar initials -->
            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-semibold text-sm shrink-0">
              {{ initials(user.name) }}
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-800 truncate">{{ user.name }}</p>
              <p class="text-xs text-gray-400 truncate">{{ user.email }}</p>
            </div>
            <span class="text-xs text-gray-500 shrink-0">
              Utoljára aktív: {{ user.last_active_at ? formatDate(user.last_active_at) : 'Soha' }}
            </span>
            <span v-if="auth.user?.id !== user.id" @click.prevent class="shrink-0">
              <FriendButton :user-id="user.id" />
            </span>
          </RouterLink>
        </div>

        <!-- Pagination -->
        <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <p class="text-sm text-gray-500">
            {{ from }}–{{ to }} / {{ total }} felhasználó
          </p>
          <div class="flex flex-wrap gap-2">
            <button
              :disabled="currentPage <= 1"
              @click="fetchPage(currentPage - 1)"
              class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 bg-white hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition"
            >
              ← Előző
            </button>
            <template v-for="page in pageNumbers" :key="page">
              <button
                v-if="page !== '...'"
                @click="fetchPage(page)"
                :class="[
                  'px-3 py-1.5 text-sm rounded-lg border transition',
                  page === currentPage
                    ? 'bg-indigo-600 text-white border-indigo-600'
                    : 'border-gray-200 bg-white hover:bg-gray-50 text-gray-700'
                ]"
              >
                {{ page }}
              </button>
              <span v-else class="px-2 py-1.5 text-sm text-gray-400">…</span>
            </template>
            <button
              :disabled="currentPage >= lastPage"
              @click="fetchPage(currentPage + 1)"
              class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 bg-white hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition"
            >
              Következő →
            </button>
          </div>
        </div>
      </template>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import AppLayout from '../components/AppLayout.vue';
import FriendButton from '../components/FriendButton.vue';
import { useAuthStore } from '../stores/auth.js';
import api from '../api/axios.js';

const auth = useAuthStore();

const PER_PAGE = 10;

const users = ref([]);
const loading = ref(false);
const error = ref(null);
const currentPage = ref(1);
const lastPage = ref(1);
const total = ref(0);
const from = ref(0);
const to = ref(0);
const searchQuery = ref('');

let debounceTimer = null;

watch(searchQuery, () => {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => fetchPage(1), 350);
});

async function fetchPage(page = 1) {
  loading.value = true;
  error.value = null;
  try {
    const params = { page, per_page: PER_PAGE };
    if (searchQuery.value.trim()) params.search = searchQuery.value.trim();
    const { data } = await api.get('/users/active', { params });
    users.value = data.data;
    currentPage.value = data.current_page;
    lastPage.value = data.last_page;
    total.value = data.total;
    from.value = data.from ?? 0;
    to.value = data.to ?? 0;
  } catch {
    error.value = 'Nem sikerült betölteni az aktív felhasználókat.';
  } finally {
    loading.value = false;
  }
}

const pageNumbers = computed(() => {
  const pages = [];
  const delta = 2;
  const left = currentPage.value - delta;
  const right = currentPage.value + delta;

  let last = 0;
  for (let p = 1; p <= lastPage.value; p++) {
    if (p === 1 || p === lastPage.value || (p >= left && p <= right)) {
      if (last && p - last > 1) pages.push('...');
      pages.push(p);
      last = p;
    }
  }
  return pages;
});

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

onMounted(() => fetchPage(1));
</script>

