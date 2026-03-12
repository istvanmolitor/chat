import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api, { getCsrfCookie } from '../api/axios.js';

export const useAuthStore = defineStore('auth', () => {
    const user = ref(null);
    const loading = ref(false);
    const error = ref(null);

    const isLoggedIn = computed(() => user.value !== null);
    const isVerified = computed(() => user.value?.email_verified_at !== null);

    async function fetchUser() {
        try {
            const { data } = await api.get('/user');
            user.value = data;
        } catch {
            user.value = null;
        }
    }

    async function register(name, email, password, passwordConfirmation) {
        error.value = null;
        loading.value = true;
        try {
            await getCsrfCookie();
            const { data } = await api.post('/register', {
                name,
                email,
                password,
                password_confirmation: passwordConfirmation,
            });
            user.value = data.user;
        } catch (e) {
            error.value = e.response?.data?.errors ?? { general: [e.response?.data?.message ?? 'Registration failed.'] };
            throw e;
        } finally {
            loading.value = false;
        }
    }

    async function login(email, password, remember = false) {
        error.value = null;
        loading.value = true;
        try {
            await getCsrfCookie();
            const { data } = await api.post('/login', { email, password, remember });
            user.value = data.user;
        } catch (e) {
            error.value = e.response?.data?.errors ?? { general: [e.response?.data?.message ?? 'Login failed.'] };
            throw e;
        } finally {
            loading.value = false;
        }
    }

    async function logout() {
        loading.value = true;
        try {
            await api.post('/logout');
        } finally {
            user.value = null;
            loading.value = false;
        }
    }

    async function resendVerification() {
        error.value = null;
        try {
            await api.post('/email/verification-notification');
        } catch (e) {
            error.value = { general: [e.response?.data?.message ?? 'Could not send verification email.'] };
            throw e;
        }
    }

    return { user, loading, error, isLoggedIn, isVerified, fetchUser, register, login, logout, resendVerification };
});

