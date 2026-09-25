import { authApi } from '../auth/api.js';
import { applyLocale } from '../../lib/localization.js';

export async function withAdminLocale(callback) {
    try {
        const { data: body } = await authApi.profile();
        applyLocale(body.data.preferred_locale);
    } finally {
        callback();
    }
}
