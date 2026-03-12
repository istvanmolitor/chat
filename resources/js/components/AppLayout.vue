<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm">
      <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
        <!-- Logo + desktop nav links -->
        <div class="flex items-center gap-6">
          <img :src="logo" alt="Chat logo" class="h-8 w-auto" />
          <div class="hidden sm:flex items-center gap-6">
            <RouterLink
              :to="{ name: 'home' }"
              class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition"
              active-class="text-indigo-600 border-b-2 border-indigo-600 pb-0.5"
            >
              Ismerősöeim
            </RouterLink>
            <RouterLink
              :to="{ name: 'active-users' }"
              class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition"
              active-class="text-indigo-600 border-b-2 border-indigo-600 pb-0.5"
            >
              Aktív felhasználók
            </RouterLink>
          </div>
        </div>

        <!-- Desktop: email + logout -->
        <div class="hidden sm:flex items-center gap-4">
          <span class="text-sm text-gray-600 truncate max-w-[200px]">{{ auth.user?.email }}</span>
          <button
            @click="handleLogout"
            class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-1.5 rounded-lg transition"
          >
            Kilépés
          </button>
        </div>

        <!-- Mobile: hamburger button -->
        <button
          class="sm:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition"
          @click="mobileOpen = !mobileOpen"
          aria-label="Menü"
        >
          <svg v-if="!mobileOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Mobile dropdown menu -->
      <div v-if="mobileOpen" class="sm:hidden border-t border-gray-100 px-4 py-3 space-y-3">
        <RouterLink
          :to="{ name: 'home' }"
          class="block text-sm font-medium text-gray-600 hover:text-indigo-600 transition py-1"
          active-class="text-indigo-600"
          @click="mobileOpen = false"
        >
          Kezdőlap
        </RouterLink>
        <RouterLink
          :to="{ name: 'active-users' }"
          class="block text-sm font-medium text-gray-600 hover:text-indigo-600 transition py-1"
          active-class="text-indigo-600"
          @click="mobileOpen = false"
        >
          Aktív felhasználók
        </RouterLink>
        <div class="pt-2 border-t border-gray-100">
          <p class="text-xs text-gray-400 truncate mb-2">{{ auth.user?.email }}</p>
          <button
            @click="handleLogout"
            class="w-full text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition text-left"
          >
            Kilépés
          </button>
        </div>
      </div>
    </nav>

    <!-- Page content -->
    <main :class="props.fullWidth ? 'w-full px-4 py-6 sm:py-8' : 'max-w-5xl mx-auto px-4 py-6 sm:py-8'">
      <slot />
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth.js';
import logo from '../../images/logo.webp';

const props = defineProps({
  fullWidth: {
    type: Boolean,
    default: false,
  },
});

const auth = useAuthStore();
const router = useRouter();
const mobileOpen = ref(false);

async function handleLogout() {
  await auth.logout();
  router.push({ name: 'login' });
}
</script>
