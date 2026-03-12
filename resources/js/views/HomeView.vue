<template>
  <AppLayout>
    <div class="text-center py-12">
      <h1 class="text-3xl font-bold text-gray-800 mb-3">Üdvözöljük, {{ auth.user?.name }}!</h1>
      <p class="text-gray-500 text-lg">Az e-mail-címe sikeresen megerősítésre került. ✅</p>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '../components/AppLayout.vue';
import { useAuthStore } from '../stores/auth.js';

const auth = useAuthStore();
</script>

