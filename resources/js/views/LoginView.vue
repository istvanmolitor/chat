<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-md p-8">
      <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Belépés</h1>

      <form @submit.prevent="handleLogin" class="space-y-4">
        <!-- Email -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">E-mail cím</label>
          <input
            v-model="form.email"
            type="email"
            required
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            placeholder="pelda@email.hu"
          />
          <p v-if="fieldError('email')" class="text-red-500 text-xs mt-1">{{ fieldError('email') }}</p>
        </div>

        <!-- Password -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Jelszó</label>
          <input
            v-model="form.password"
            type="password"
            required
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            placeholder="Jelszava"
          />
          <p v-if="fieldError('password')" class="text-red-500 text-xs mt-1">{{ fieldError('password') }}</p>
        </div>

        <!-- Remember me -->
        <div class="flex items-center gap-2">
          <input v-model="form.remember" type="checkbox" id="remember" class="rounded" />
          <label for="remember" class="text-sm text-gray-600">Emlékezz rám</label>
        </div>

        <!-- General error -->
        <p v-if="fieldError('general')" class="text-red-500 text-sm">{{ fieldError('general') }}</p>

        <button
          type="submit"
          :disabled="auth.loading"
          class="w-full bg-indigo-600 text-white py-2 rounded-lg font-semibold hover:bg-indigo-700 transition disabled:opacity-60"
        >
          {{ auth.loading ? 'Belépés...' : 'Belépek' }}
        </button>
      </form>

      <p class="mt-4 text-center text-sm text-gray-600">
        Nincs még fiókja?
        <RouterLink to="/register" class="text-indigo-600 hover:underline font-medium">Regisztráció</RouterLink>
      </p>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth.js';

const auth = useAuthStore();
const router = useRouter();

const form = reactive({
  email: '',
  password: '',
  remember: false,
});

function fieldError(field) {
  return auth.error?.[field]?.[0] ?? null;
}

async function handleLogin() {
  try {
    await auth.login(form.email, form.password, form.remember);
    if (!auth.isVerified) {
      router.push({ name: 'verify-email' });
    } else {
      router.push({ name: 'home' });
    }
  } catch {
    // errors displayed via fieldError()
  }
}
</script>

