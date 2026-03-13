import axios from 'axios';

const AUTH_TOKEN_STORAGE_KEY = 'auth_token';

const api = axios.create({
    baseURL: '/api',
    withCredentials: true,
    withXSRFToken: true,
    headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
    },
});

function setAuthorizationHeader(token) {
    if (token) {
        api.defaults.headers.common.Authorization = `Bearer ${token}`;

        return;
    }

    delete api.defaults.headers.common.Authorization;
}

export function getStoredAuthToken() {
    if (typeof window === 'undefined') {
        return null;
    }

    return window.localStorage.getItem(AUTH_TOKEN_STORAGE_KEY);
}

export function storeAuthToken(token) {
    if (typeof window !== 'undefined') {
        window.localStorage.setItem(AUTH_TOKEN_STORAGE_KEY, token);
    }

    setAuthorizationHeader(token);
}

export function clearAuthToken() {
    if (typeof window !== 'undefined') {
        window.localStorage.removeItem(AUTH_TOKEN_STORAGE_KEY);
    }

    setAuthorizationHeader(null);
}

const initialToken = getStoredAuthToken();
if (initialToken) {
    setAuthorizationHeader(initialToken);
}

/**
 * Get the CSRF cookie before state-changing requests.
 * Call this once before login/register.
 */
export async function getCsrfCookie() {
    await axios.get('/sanctum/csrf-cookie', { withCredentials: true });
}

export default api;
