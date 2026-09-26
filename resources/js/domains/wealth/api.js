import { http } from '../../lib/http.js';

export const initialWealthApi = {
    index: () => http.get('/initial-wealth-entries'),
    store: (payload) => http.post('/initial-wealth-entries', payload),
    update: (id, payload) => http.put(`/initial-wealth-entries/${id}`, payload),
    destroy: (id) => http.delete(`/initial-wealth-entries/${id}`),
    savingsCapacityProfile: () => http.get('/savings-capacity-profile'),
    saveSavingsCapacityProfile: (payload) => http.put('/savings-capacity-profile', payload),
};
