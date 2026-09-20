import { http } from '../../lib/http.js';
import { authStore } from './store.js';

export const authApi = {
    async register(data) {
        const { data: body } = await http.post('/auth/register', data);
        authStore.save(body.data);

        return body.data;
    },

    async login(credentials) {
        const { data: body } = await http.post('/auth/login', credentials);
        authStore.save(body.data);

        return body.data;
    },

    async logout() {
        try {
            await http.post('/auth/logout');
        } finally {
            authStore.clear();
        }
    },

    async me() {
        const { data: body } = await http.get('/auth/me');
        authStore.saveUser(body.data);

        return body.data;
    },

    forgotPassword(email) {
        return http.post('/auth/forgot-password', { email });
    },

    resetPassword(payload) {
        return http.post('/auth/reset-password', payload);
    },
};
