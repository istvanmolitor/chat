<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-md p-8 text-center">

      <!-- Loading -->
      <div v-if="status === 'loading'">
        <svg class="mx-auto h-12 w-12 text-indigo-400 animate-spin mb-4" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
        </svg>
        <p class="text-gray-500">E-mail cím megerősítése folyamatban...</p>
      </div>

      <!-- Success -->
      <div v-else-if="status === 'success'">
        <svg class="mx-auto h-16 w-16 text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">E-mail cím megerősítve!</h1>
        <p class="text-gray-500 mb-6">Fiókja sikeresen aktiválva lett.</p>
        <button
          @click="router.push({ name: 'home' })"
          class="w-full bg-indigo-600 text-white py-2 rounded-lg font-semibold hover:bg-indigo-700 transition"
        >
          Tovább a főoldalra
        </button>
      </div>

      <!-- Error -->
      <div v-else>
        <svg class="mx-auto h-16 w-16 text-red-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 8v4m0 4h.01M21 12A9 9 0 113 12a9 9 0 0118 0z" />
        </svg>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Megerősítés sikertelen</h1>
        <p class="text-gray-500 mb-6">{{ errorMessage }}</p>
        <button
          @click="router.push({ name: 'login' })"
          class="w-full bg-indigo-600 text-white py-2 rounded-lg font-semibold hover:bg-indigo-700 transition"
        >
          Bejelentkezés
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../api/axios.js';
import { useAuthStore } from '../stores/auth.js';

const route  = useRoute();
const router = useRouter();
const auth   = useAuthStore();

const status       = ref('loading');
const errorMessage = ref('Az ellenőrző link érvénytelen vagy lejárt.');

onMounted(async () => {
    const { id, hash } = route.params;
    const { expires, signature } = route.query;

    try {
        await api.get(`/email/verify/${id}/${hash}`, {
            params: { expires, signature },
        });

        // Refresh user state so the app knows the email is now verified
        await auth.fetchUser();

        status.value = 'success';
    } catch (error) {
        errorMessage.value =
            error?.response?.data?.message ?? 'Az ellenőrző link érvénytelen vagy lejárt.';
        status.value = 'error';
    }
});
</script>

