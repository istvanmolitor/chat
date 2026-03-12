import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '../api/axios.js';

export const useFriendStore = defineStore('friend', () => {
    // Cache: userId -> { status, friendship_id }
    const statusCache = ref({});
    const loading = ref({});

    async function fetchStatus(userId) {
        loading.value[userId] = true;
        try {
            const { data } = await api.get(`/friends/status/${userId}`);
            statusCache.value[userId] = data;
            return data;
        } finally {
            loading.value[userId] = false;
        }
    }

    async function sendRequest(userId) {
        loading.value[userId] = true;
        try {
            const { data } = await api.post(`/friends/request/${userId}`);
            statusCache.value[userId] = { status: data.status, friendship_id: data.friendship_id };
            return data;
        } finally {
            loading.value[userId] = false;
        }
    }

    async function acceptRequest(userId, friendshipId) {
        loading.value[userId] = true;
        try {
            const { data } = await api.post(`/friends/accept/${friendshipId}`);
            statusCache.value[userId] = { status: data.status, friendship_id: friendshipId };
            return data;
        } finally {
            loading.value[userId] = false;
        }
    }

    async function declineRequest(userId, friendshipId) {
        loading.value[userId] = true;
        try {
            await api.post(`/friends/decline/${friendshipId}`);
            statusCache.value[userId] = { status: 'none' };
        } finally {
            loading.value[userId] = false;
        }
    }

    async function removeFriend(userId) {
        loading.value[userId] = true;
        try {
            await api.delete(`/friends/${userId}`);
            statusCache.value[userId] = { status: 'none' };
        } finally {
            loading.value[userId] = false;
        }
    }

    function getStatus(userId) {
        return statusCache.value[userId] ?? null;
    }

    function isLoading(userId) {
        return loading.value[userId] ?? false;
    }

    return {
        statusCache,
        fetchStatus,
        sendRequest,
        acceptRequest,
        declineRequest,
        removeFriend,
        getStatus,
        isLoading,
    };
});

