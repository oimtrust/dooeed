import { adminApi } from '../api.js';
import { initList } from '../components/list.js';
import { date, row, userUrl } from '../components/ui.js';
import { withAdminLocale } from '../localization.js';

export function initAdminUsers() {
    withAdminLocale(() => initList(adminApi.users, (user) => {
        const node = row('user-row', { ...user, created_at: date(user.created_at) });
        node.querySelector('a').href = userUrl(user.id);
        node.querySelector('[data-field="status"]').classList.add(user.status === 'active' ? 'bg-green-lt' : 'bg-red-lt');
        return node;
    }, 7));
}
