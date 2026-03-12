<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm">
      <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
        <span class="text-xl font-bold text-indigo-600">Chat</span>
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

    <!-- Content -->
    <div class="max-w-5xl mx-auto px-4 py-12 text-center">
      <h1 class="text-3xl font-bold text-gray-800 mb-3">Üdvözöljük, {{ auth.user?.name }}!</h1>
      <p class="text-gray-500 text-lg">Az e-mail-címe sikeresen megerősítésre került. ✅</p>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth.js';

const auth = useAuthStore();
const router = useRouter();

async function handleLogout() {
  await auth.logout();
  router.push({ name: 'login' });
}
</script>

