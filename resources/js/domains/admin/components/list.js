import { emptyRow, handleError, message } from './ui.js';
import { applyLocale } from '../../../lib/localization.js';

export function initList(fetchRows, renderRow, columns) {
    const form = document.getElementById('admin-filters');
    const tbody = document.getElementById('admin-rows');
    const pagination = document.getElementById('admin-pagination');
    const loading = document.getElementById('admin-loading');
    const retry = document.getElementById('admin-retry');
    let requestNumber = 0;
    let params = new URLSearchParams(window.location.search);
    for (const input of form.elements) {
        if (input.name && params.has(input.name)) input.value = params.get(input.name);
    }

    async function load() {
        const currentRequest = ++requestNumber;
        loading.hidden = false;
        retry.hidden = true;
        message('');
        tbody.replaceChildren();
        pagination.replaceChildren();
        form.querySelector('button').disabled = true;
        try {
            const body = await fetchRows(params);
            if (currentRequest !== requestNumber) return;
            tbody.replaceChildren(...(body.data.length ? body.data.map(renderRow) : [emptyRow(columns, 'Tidak ada data ditemukan.')]));
            const label = document.createElement('span');
            label.className = 'me-3';
            label.textContent = `Halaman ${body.meta.current_page} / ${body.meta.last_page} · ${body.meta.total} data`;
            pagination.append(label);
            for (const [title, target, disabled] of [
                ['Sebelumnya', body.meta.current_page - 1, body.meta.current_page === 1],
                ['Berikutnya', body.meta.current_page + 1, body.meta.current_page >= body.meta.last_page],
            ]) {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'btn btn-sm me-2';
                button.textContent = title;
                button.disabled = disabled;
                button.addEventListener('click', () => {
                    params.set('page', target);
                    history.replaceState(null, '', `${location.pathname}?${params}`);
                    load();
                });
                pagination.append(button);
            }
            applyLocale(document.documentElement.lang);
        } catch (error) {
            if (currentRequest !== requestNumber) return;
            handleError(error);
            retry.hidden = false;
        } finally {
            if (currentRequest === requestNumber) {
                loading.hidden = true;
                form.querySelector('button').disabled = false;
            }
        }
    }
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        params = new URLSearchParams(new FormData(form));
        params.set('page', '1');
        history.replaceState(null, '', `${location.pathname}?${params}`);
        load();
    });
    retry.addEventListener('click', load);
    load();
}
