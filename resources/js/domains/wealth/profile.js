import { initialWealthApi } from './api.js';
import { authApi } from '../auth/api.js';
import { applyLocale, translate } from '../../lib/localization.js';

const categories = [
    ['cash', 'Kas', 'Nama Rekening', 'Saldo'],
    ['cash_equivalent', 'Aset Setara Kas', 'Nama Aset', 'Nilai Beli'],
    ['non_current_asset', 'Aset Tidak Lancar', 'Nama Aset', 'Nilai Beli'],
    ['receivable', 'Piutang', 'Peminjam', 'Nominal'],
    ['debt', 'Utang', 'Pemberi Utang', 'Nominal'],
];
const assetCategories = new Set(['cash_equivalent', 'non_current_asset']);
let locale = 'id-ID';
let currency = 'IDR';
const money = (value) => new Intl.NumberFormat(locale, { style: 'currency', currency, maximumFractionDigits: 2 }).format(Number(value));

export function initInitialWealthProfile() {
    const root = document.getElementById('initial-wealth-profile');
    const list = document.getElementById('initial-wealth-categories');
    if (!root || !list) return;

    let entries = [];
    let summary = null;
    const render = () => {
        root.querySelectorAll('[data-summary]').forEach((element) => { element.textContent = money(summary?.[element.dataset.summary] ?? 0); });
        list.replaceChildren(...categories.map(([category, title]) => {
            const card = document.createElement('div');
            card.className = 'col-md-6 col-xl-4';
            const items = entries.filter((entry) => entry.category === category);
            card.innerHTML = `<div class="card h-100"><div class="card-header"><h2 class="card-title">${translate(title)}</h2><button class="btn btn-primary btn-sm ms-auto" data-add="${category}">${translate('Tambah')}</button></div><div class="card-body"><div class="h3">${money(summary?.categories?.[category] ?? 0)}</div><div class="text-secondary small mb-2">${items.length ? '' : translate('Belum ada data.')}</div><div class="list-group list-group-flush">${items.map((entry) => `<div class="list-group-item px-0 d-flex align-items-center"><span>${entry.name}<small class="d-block text-secondary">${money(entry.amount)}${entry.debt_type ? ` · ${translate(entry.debt_type === 'other' ? 'Selain kartu kredit & paylater' : 'Kartu kredit & paylater')}` : ''}</small></span><span class="ms-auto"><button class="btn btn-ghost-secondary btn-sm" data-edit="${entry.id}">${translate('Ubah')}</button><button class="btn btn-ghost-danger btn-sm" data-delete="${entry.id}">${translate('Hapus')}</button></span></div>`).join('')}</div></div></div>`;
            return card;
        }));
        applyLocale(document.documentElement.lang);
    };
    const load = async () => { const { data } = await initialWealthApi.index(); entries = data.data; summary = data.summary; render(); };
    const formFor = (entry = null, category = entry?.category) => {
        const [, title, nameLabel, amountLabel] = categories.find(([key]) => key === category);
        const displayTitle = translate(title);
        const displayNameLabel = translate(nameLabel);
        const displayAmountLabel = translate(amountLabel);
        const asset = assetCategories.has(category);
        const form = document.createElement('form');
        form.className = 'card card-body shadow-lg position-fixed top-50 start-50 translate-middle z-3';
        form.style.maxWidth = '440px'; form.style.width = 'calc(100% - 2rem)';
        form.innerHTML = `<h2 class="card-title mb-3">${translate(entry ? 'Ubah' : 'Tambah')} ${displayTitle}</h2><label class="form-label">${displayNameLabel}<input class="form-control" name="name" required value="${entry?.name ?? ''}"></label>${asset ? `<label class="form-label">${translate('Nilai Beli')}<input class="form-control" type="number" min="0" step="0.01" name="unit_price" required value="${entry?.unit_price ?? ''}"></label><label class="form-label">${translate('Jumlah')}<input class="form-control" type="number" min="0.0001" step="0.0001" name="quantity" required value="${entry?.quantity ?? ''}"></label><p class="form-hint">${translate('Total:')} <strong data-total>${money(0)}</strong></p>` : `<label class="form-label">${displayAmountLabel}<input class="form-control" type="number" min="0" step="0.01" name="amount" required value="${entry?.amount ?? ''}"></label>`}${category === 'debt' ? `<label class="form-label">${translate('Jenis Utang')}<select class="form-select" name="debt_type" required><option value="other">${translate('Selain Kartu Kredit & Paylater')}</option><option value="credit_card_paylater">${translate('Kartu Kredit & Paylater')}</option></select></label>` : ''}<div class="d-flex gap-2 justify-content-end mt-2"><button type="button" class="btn btn-link" data-cancel>${translate('Batal')}</button><button class="btn btn-primary">${translate('Simpan')}</button></div>`;
        if (entry?.debt_type) form.debt_type.value = entry.debt_type;
        const refreshTotal = () => { const total = Number(form.unit_price?.value || 0) * Number(form.quantity?.value || 0); form.querySelector('[data-total]')?.replaceChildren(document.createTextNode(money(total))); };
        form.unit_price?.addEventListener('input', refreshTotal); form.quantity?.addEventListener('input', refreshTotal); refreshTotal();
        form.querySelector('[data-cancel]').addEventListener('click', () => form.remove());
        form.addEventListener('submit', async (event) => { event.preventDefault(); const payload = Object.fromEntries(new FormData(form)); payload.category = category; try { entry ? await initialWealthApi.update(entry.id, payload) : await initialWealthApi.store(payload); form.remove(); await load(); } catch (error) { alert(error.response?.data?.message ?? translate('Data gagal disimpan.')); } });
        document.body.append(form);
    };
    list.addEventListener('click', async (event) => {
        const category = event.target.dataset.add; const id = event.target.dataset.edit || event.target.dataset.delete;
        if (category) formFor(null, category);
        if (event.target.dataset.edit) formFor(entries.find((entry) => entry.id === id));
        if (event.target.dataset.delete && confirm(translate('Hapus data ini?'))) { await initialWealthApi.destroy(id); await load(); }
    });
    load().catch(() => { list.textContent = 'Data profil kekayaan tidak dapat dimuat.'; });
    authApi.profile().then(({ data: body }) => {
        const data = body.data;
        locale = data.preferred_locale === 'en' ? 'en-US' : 'id-ID';
        currency = data.preferred_currency;
        if (summary) render();
        applyLocale(data.preferred_locale);
    });
}
