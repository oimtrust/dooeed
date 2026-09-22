export const page = () => document.getElementById('admin-page');
export const money = (value) => Number(value).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
export const date = (value) => value ? new Date(value).toLocaleString('id-ID') : '—';
export const userUrl = (id) => page().dataset.userUrl.replace('__USER__', encodeURIComponent(id));
export function auditUrl(filters) {
    const url = new URL(page().dataset.auditUrl, window.location.origin);
    url.search = new URLSearchParams(filters).toString();
    return url.href;
}
export function message(text, kind = 'danger') {
    const box = document.getElementById('admin-message');
    box.textContent = text;
    box.className = `alert alert-${kind}`;
    box.hidden = !text;
}
export function handleError(error) {
    const status = error.response?.status;
    if (status === 401) {
        window.location.assign(page().dataset.loginUrl);
        return;
    }
    if (status === 403) {
        message(error.response?.data?.message || 'Akses ditolak.');
        window.setTimeout(() => window.location.assign(page().dataset.dashboardUrl), 1500);
        return;
    }
    const errors = error.response?.data?.errors;
    message(status === 404 ? 'User tidak ditemukan.' : errors ? Object.values(errors).flat().join(' ') : 'Data gagal dimuat. Silakan coba lagi.');
}
export function row(template, values) {
    const node = document.getElementById(template).content.firstElementChild.cloneNode(true);
    node.querySelectorAll('[data-field]').forEach((cell) => {
        cell.textContent = values[cell.dataset.field] ?? '—';
    });
    return node;
}
export function emptyRow(columns, text) {
    const node = document.createElement('tr');
    const cell = document.createElement('td');
    cell.colSpan = columns;
    cell.className = 'text-center py-4';
    cell.textContent = text;
    node.append(cell);
    return node;
}
export function textList(id, items, tag, empty) {
    const container = document.getElementById(id);
    container.replaceChildren();
    (items.length ? items : [empty]).forEach((text) => {
        const node = document.createElement(tag);
        node.textContent = text;
        container.append(node);
    });
}
