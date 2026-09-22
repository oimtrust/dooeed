import { http } from '../../lib/http.js';

export const adminApi = {
    async users(params) {
        return (await http.get('/admin/users', { params })).data;
    },
    async user(id) {
        return (await http.get(`/admin/users/${encodeURIComponent(id)}`)).data.data;
    },
    async updateUser(id, payload) {
        return (await http.patch(`/admin/users/${encodeURIComponent(id)}`, payload)).data;
    },
    async audit(params) {
        return (await http.get('/admin/audit-logs', { params })).data;
    },
};
