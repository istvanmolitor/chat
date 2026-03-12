import axios from 'axios';

const api = axios.create({
    baseURL: '/api',
    withCredentials: true,
    withXSRFToken: true,
    headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
    },
});

/**
 * Get the CSRF cookie before state-changing requests.
 * Call this once before login/register.
 */
export async function getCsrfCookie() {
    await axios.get('/sanctum/csrf-cookie', { withCredentials: true });
}

export default api;

