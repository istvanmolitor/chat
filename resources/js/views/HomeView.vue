<template>
  <AppLayout>
    <div class="max-w-2xl mx-auto py-8 sm:py-12 px-4">
      <!-- Friends section -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Ismerőseim</h2>

        <!-- Search -->
        <div class="relative mb-4">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 0 5 11a6 6 0 0 0 12 0z" />
          </svg>
          <input
            v-model="search"
            @input="onSearchInput"
            type="text"
            placeholder="Keresés név alapján…"
            class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition"
          />
        </div>

        <!-- Loading skeleton -->
        <div v-if="friendStore.friendsLoading" class="space-y-3">
          <div v-for="i in 5" :key="i" class="h-14 bg-gray-100 rounded-xl animate-pulse" />
        </div>

        <!-- Empty state -->
        <div v-else-if="friendStore.friendsList.length === 0" class="text-center py-10 text-gray-400 text-sm">
          <svg class="mx-auto mb-3 w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a4 4 0 00-5.356-3.765M9 20H4v-2a4 4 0 015.356-3.765M15 7a4 4 0 11-8 0 4 4 0 018 0zm6 4a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          <p v-if="search">Nincs találat erre a névre.</p>
          <p v-else>
            Még nincs egy ismerősöd sem. Találd meg az ismerőseid az
            <RouterLink :to="{ name: 'active-users' }" class="text-indigo-600 hover:text-indigo-700 underline">
              aktív felhasználók
            </RouterLink>
            között!
          </p>
        </div>

        <!-- Friends list -->
        <ul v-else class="divide-y divide-gray-50 -mx-2">
          <li
            v-for="friend in friendStore.friendsList"
            :key="friend.id"
            class="flex items-center justify-between px-2 py-3 rounded-xl hover:bg-gray-50 transition"
          >
            <div class="flex items-center gap-3">
              <!-- Avatar initials -->
              <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-semibold text-sm select-none shrink-0">
                {{ initials(friend.name) }}
              </div>
              <div>
                <RouterLink
                  :to="{ name: 'user-profile', params: { id: friend.id } }"
                  class="text-sm font-medium text-gray-800 hover:text-indigo-600 transition"
                >{{ friend.name }}</RouterLink>
                <p class="text-xs text-gray-400">{{ friend.email }}</p>
              </div>
            </div>
            <!-- Online indicator -->
            <span
              :class="friend.is_active ? 'bg-green-400' : 'bg-gray-300'"
              class="w-2.5 h-2.5 rounded-full shrink-0"
              :title="friend.is_active ? 'Online' : 'Offline'"
            />
          </li>
        </ul>

        <!-- Pagination -->
        <div
          v-if="friendStore.friendsMeta.last_page > 1"
          class="flex items-center justify-between mt-5 pt-4 border-t border-gray-100"
        >
          <button
            @click="goToPage(friendStore.friendsMeta.current_page - 1)"
            :disabled="friendStore.friendsMeta.current_page <= 1"
            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Előző
          </button>

          <span class="text-xs text-gray-400">
            {{ friendStore.friendsMeta.current_page }} / {{ friendStore.friendsMeta.last_page }}
            &nbsp;·&nbsp; {{ friendStore.friendsMeta.total }} ismerős
          </span>

          <button
            @click="goToPage(friendStore.friendsMeta.current_page + 1)"
            :disabled="friendStore.friendsMeta.current_page >= friendStore.friendsMeta.last_page"
            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition"
          >
            Következő
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import AppLayout from '../components/AppLayout.vue';
import { useAuthStore } from '../stores/auth.js';
import { useFriendStore } from '../stores/friend.js';

const auth = useAuthStore();
const friendStore = useFriendStore();

const search = ref('');
let searchTimer = null;

function initials(name) {
  return name
    .split(' ')
    .filter(Boolean)
    .slice(0, 2)
    .map(w => w[0].toUpperCase())
    .join('');
}

function load(page = 1) {
  friendStore.fetchFriendsPaginated(page, search.value);
}

function onSearchInput() {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => load(1), 350);
}

function goToPage(page) {
  load(page);
}

onMounted(() => load(1));
</script>
