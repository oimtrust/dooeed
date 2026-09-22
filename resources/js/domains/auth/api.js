import { http } from '../../lib/http.js';
import { authStore } from './store.js';

export const authApi = {
    async register(data) {
        const { data: body } = await http.post('/session/register', data, {
            baseURL: '',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        });
        authStore.save(body.data);

        return body.data;
    },

    async login(credentials) {
        const { data: body } = await http.post('/session/login', credentials, {
            baseURL: '',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        });
        authStore.save(body.data);

        return body.data;
    },

    async openAdminPanel(url) {
        const { data } = await http.post(url, {}, {
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        });
        window.location.href = data.redirect;
    },

    async logout() {
        try {
            await http.post('/auth/logout');
        } finally {
            authStore.clear();
            await http.delete('/admin/session', {
                baseURL: '',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            });
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
