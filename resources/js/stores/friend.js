import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '../api/axios.js';

export const useFriendStore = defineStore('friend', () => {
    // Cache: userId -> { status, friendship_id }
    const statusCache = ref({});
    const loading = ref({});

    // Friends list (paginated)
    const friendsList = ref([]);
    const friendsLoading = ref(false);
    const friendsMeta = ref({ current_page: 1, last_page: 1, per_page: 10, total: 0 });

    async function fetchFriendsPaginated(page = 1, search = '', perPage = 10) {
        friendsLoading.value = true;
        try {
            const params = { page, per_page: perPage };
            if (search) params.search = search;
            const { data } = await api.get('/friends/paginated', { params });
            friendsList.value = data.data;
            friendsMeta.value = {
                current_page: data.current_page,
                last_page: data.last_page,
                per_page: data.per_page,
                total: data.total,
            };
            return data;
        } finally {
            friendsLoading.value = false;
        }
    }

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
        friendsList,
        friendsLoading,
        friendsMeta,
        fetchFriendsPaginated,
    };
});

