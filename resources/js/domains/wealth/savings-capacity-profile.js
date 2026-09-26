import { initialWealthApi } from './api.js';
import { translate } from '../../lib/localization.js';
import { authApi } from '../auth/api.js';
import { bindMoneyInput, unformatMoneyInput } from '../../lib/money-input.js';

export function initSavingsCapacityProfile() {
    const form = document.getElementById('savings-capacity-form');
    if (!form) return;
    const number = (name) => Number(unformatMoneyInput(form.elements[name].value));
    const years = (value) => Number.isFinite(value) ? value.toFixed(2) : '—';
    const money = (value) => new Intl.NumberFormat(document.documentElement.lang === 'en' ? 'en-US' : 'id-ID', { style: 'currency', currency: form.dataset.currency || 'IDR', maximumFractionDigits: 0 }).format(value);
    const age = () => form.date_of_birth.value ? (Date.now() - new Date(form.date_of_birth.value).getTime()) / 31557600000 : NaN;
    const render = () => {
        const currentAge = age(); const remaining = number('retirement_age') - currentAge; const duration = number('inheritance_age') - number('retirement_age');
        form.elements.savings_capacity.value = money(number('monthly_income') - number('monthly_expenses'));
        form.elements.current_age.value = years(currentAge);
        form.elements.remaining_years.value = years(remaining);
        form.elements.retirement_duration.value = years(duration);
        form.elements.pre_retirement_fund.value = Number.isFinite(remaining) ? money(number('monthly_expenses') * 12 * remaining) : '—';
        form.elements.retirement_fund.value = Number.isFinite(duration) ? money(number('monthly_expenses') * 12 * duration) : '—';
    };
    authApi.profile().then(({ data: body }) => { form.dataset.currency = body.data.preferred_currency; render(); });
    initialWealthApi.savingsCapacityProfile().then(({ data }) => { if (data.data) Object.entries(data.data).forEach(([key, value]) => { if (form.elements[key]) form.elements[key].value = key === 'date_of_birth' && value ? String(value).slice(0, 10) : (value ?? '');
        ['monthly_income', 'monthly_expenses'].forEach((name) => bindMoneyInput(form.elements[name])); }); render(); });
    form.addEventListener('input', render);
    form.addEventListener('submit', async (event) => { event.preventDefault(); form.querySelectorAll('.invalid-feedback').forEach((el) => el.remove()); form.querySelectorAll('.is-invalid').forEach((el) => el.classList.remove('is-invalid')); try { const payload = Object.fromEntries(new FormData(form));
            payload.monthly_income = unformatMoneyInput(payload.monthly_income);
            payload.monthly_expenses = unformatMoneyInput(payload.monthly_expenses);
            await initialWealthApi.saveSavingsCapacityProfile(payload); const alert = document.getElementById('savings-success'); alert.hidden = false; setTimeout(() => { alert.hidden = true; }, 4000); } catch (error) { Object.entries(error.response?.data?.errors || {}).forEach(([field, messages]) => { const input = form.elements[field]; input.classList.add('is-invalid'); input.insertAdjacentHTML('afterend', `<div class="invalid-feedback">${messages[0]}</div>`); }); } });
    render();
}
