<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-md p-6 sm:p-8 text-center">
      <div class="mb-5 sm:mb-6">
        <svg class="mx-auto h-16 w-16 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
      </div>

      <h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-2">Erősítse meg e-mail-címét</h1>
      <p class="text-gray-500 mb-6">
        Regisztrációja után egy megerősítő linket küldtünk a(z)
        <strong>{{ auth.user?.email }}</strong> e-mail-címre.<br />
        Kérjük, kattintson a levélben lévő linkre a fiók aktiválásához.
      </p>

      <p v-if="sent" class="text-green-600 font-medium mb-4">Az e-mail újraküldésre került!</p>
      <p v-if="sendError" class="text-red-500 text-sm mb-4">{{ sendError }}</p>

      <button
        @click="resend"
        :disabled="resending || countdown > 0"
        class="w-full bg-indigo-600 text-white py-2 rounded-lg font-semibold hover:bg-indigo-700 transition disabled:opacity-60 mb-4"
      >
        <span v-if="countdown > 0">Újraküldés ({{ countdown }}s)</span>
        <span v-else-if="resending">Küldés...</span>
        <span v-else>Megerősítő e-mail újraküldése</span>
      </button>

      <button
        @click="handleLogout"
        class="w-full text-gray-500 hover:text-gray-700 text-sm underline"
      >
        Kilépés
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth.js';

const auth = useAuthStore();
const router = useRouter();

const sent = ref(false);
const resending = ref(false);
const sendError = ref(null);
const countdown = ref(0);

async function resend() {
  resending.value = true;
  sent.value = false;
  sendError.value = null;
  try {
    await auth.resendVerification();
    sent.value = true;
    countdown.value = 60;
    const timer = setInterval(() => {
      countdown.value--;
      if (countdown.value <= 0) clearInterval(timer);
    }, 1000);
  } catch (e) {
    sendError.value = auth.error?.general?.[0] ?? 'Hiba történt.';
  } finally {
    resending.value = false;
  }
}

async function handleLogout() {
  await auth.logout();
  router.push({ name: 'login' });
}
</script>

