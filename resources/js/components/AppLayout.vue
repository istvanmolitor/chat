<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm">
      <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-6">
          <img :src="logo" alt="Chat logo" class="h-8 w-auto" />
          <RouterLink
            :to="{ name: 'home' }"
            class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition"
            active-class="text-indigo-600 border-b-2 border-indigo-600 pb-0.5"
          >
            Kezdőlap
          </RouterLink>
          <RouterLink
            :to="{ name: 'active-users' }"
            class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition"
            active-class="text-indigo-600 border-b-2 border-indigo-600 pb-0.5"
          >
            Aktív felhasználók
          </RouterLink>
        </div>
        <div class="flex items-center gap-4">
          <span class="text-sm text-gray-600">{{ auth.user?.email }}</span>
          <button
            @click="handleLogout"
            class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-1.5 rounded-lg transition"
          >
            Kilépés
          </button>
        </div>
      </div>
    </nav>

    <!-- Page content -->
    <main class="max-w-5xl mx-auto px-4 py-8">
      <slot />
    </main>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth.js';
import logo from '../../images/logo.webp';

const auth = useAuthStore();
const router = useRouter();

async function handleLogout() {
  await auth.logout();
  router.push({ name: 'login' });
}
</script>

