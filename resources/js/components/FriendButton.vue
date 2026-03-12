<template>
  <!-- self -->
  <span v-if="status === 'self'" />

  <!-- barát -->
  <button
    v-else-if="status === 'accepted'"
    @click.prevent="handleRemove"
    :disabled="busy"
    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium border border-green-300 bg-green-50 text-green-700 hover:bg-red-50 hover:border-red-300 hover:text-red-600 transition disabled:opacity-50"
  >
    <svg v-if="!busy" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
      <path d="M9 12l-4-4 1.41-1.41L9 9.17l8.59-8.58L19 2l-10 10z" />
    </svg>
    <Spinner v-else />
    <span class="hidden sm:inline">Ismerős</span>
  </button>

  <!-- küldött, várakozik -->
  <button
    v-else-if="status === 'pending_sent'"
    @click.prevent="handleRemove"
    :disabled="busy"
    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium border border-yellow-300 bg-yellow-50 text-yellow-700 hover:bg-red-50 hover:border-red-300 hover:text-red-600 transition disabled:opacity-50"
  >
    <Spinner v-if="busy" />
    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <span class="hidden sm:inline">Visszavonás</span>
  </button>

  <!-- bejövő kérés -->
  <span v-else-if="status === 'pending_received'" class="inline-flex items-center gap-1.5">
    <button
      @click.prevent="handleAccept"
      :disabled="busy"
      class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium border border-indigo-300 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition disabled:opacity-50"
    >
      <Spinner v-if="busy" />
      <span v-else>Elfogad</span>
    </button>
    <button
      @click.prevent="handleDecline"
      :disabled="busy"
      class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 transition disabled:opacity-50"
    >
      <span>Elutasít</span>
    </button>
  </span>

  <!-- nincs kapcsolat / betöltés -->
  <button
    v-else
    @click.prevent="handleSend"
    :disabled="busy || status === null"
    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium border border-indigo-300 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition disabled:opacity-50"
  >
    <Spinner v-if="busy || status === null" />
    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
    </svg>
    <span class="hidden sm:inline">Ismerős jelölés</span>
  </button>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useFriendStore } from '../stores/friend.js';

const Spinner = {
  template: `<svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
  </svg>`,
};

const props = defineProps({
  userId: { type: Number, required: true },
});

const friendStore = useFriendStore();

const status = computed(() => friendStore.getStatus(props.userId)?.status ?? null);
const friendshipId = computed(() => friendStore.getStatus(props.userId)?.friendship_id ?? null);
const busy = computed(() => friendStore.isLoading(props.userId));

onMounted(() => friendStore.fetchStatus(props.userId));

async function handleSend() {
  try {
    await friendStore.sendRequest(props.userId);
  } catch (e) {
    alert(e?.response?.data?.message ?? 'Hiba történt.');
  }
}

async function handleAccept() {
  try {
    await friendStore.acceptRequest(props.userId, friendshipId.value);
  } catch (e) {
    alert(e?.response?.data?.message ?? 'Hiba történt.');
  }
}

async function handleDecline() {
  try {
    await friendStore.declineRequest(props.userId, friendshipId.value);
  } catch (e) {
    alert(e?.response?.data?.message ?? 'Hiba történt.');
  }
}

async function handleRemove() {
  try {
    await friendStore.removeFriend(props.userId);
  } catch (e) {
    alert(e?.response?.data?.message ?? 'Hiba történt.');
  }
}
</script>

