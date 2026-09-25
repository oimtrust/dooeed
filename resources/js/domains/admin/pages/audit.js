import { adminApi } from '../api.js';
import { initList } from '../components/list.js';
import { auditUrl, date, row } from '../components/ui.js';
import { withAdminLocale } from '../localization.js';

export function initAdminAudit() {
    withAdminLocale(() => initList(adminApi.audit, (log) => {
        const node = row('audit-row', { ...log, created_at: date(log.created_at) });
        node.querySelector('[data-field="admin_email"]').href = auditUrl({ admin_id: log.admin_id });
        node.querySelector('[data-field="target_user_email"]').href = auditUrl({ target_user_id: log.target_user_id });
        return node;
    }, 5));
}
