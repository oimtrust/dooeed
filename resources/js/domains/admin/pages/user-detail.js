import { adminApi } from '../api.js';
import { bindUserActions } from '../components/user-actions.js';
import { renderUserDetail } from '../components/user-detail.js';
import { handleError, message } from '../components/ui.js';
import { withAdminLocale } from '../localization.js';

export function initAdminUserDetail() {
    const detail = document.getElementById('user-detail');
    const retry = document.getElementById('admin-retry');
    const loading = document.getElementById('admin-loading');
    let user;
    async function load() {
        loading.hidden = false;
        retry.hidden = true;
        detail.hidden = true;
        message('');
        try {
            const data = await adminApi.user(detail.dataset.userId);
            user = data.user;
            renderUserDetail(data);
            return true;
        } catch (error) {
            handleError(error);
            retry.hidden = error.response?.status === 404;
            return false;
        } finally {
            loading.hidden = true;
        }
    }
    withAdminLocale(() => {
        bindUserActions(() => user, load);
        retry.addEventListener('click', load);
        load();
    });
}
