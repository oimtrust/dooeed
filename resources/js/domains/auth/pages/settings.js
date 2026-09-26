import { authApi } from '../api.js';
import { authStore } from '../store.js';
import { clearErrors, showAlert } from '../components/form.js';
import { applyLocale } from '../../../lib/localization.js';

export function initSettingsPage() {
    if (!authStore.isAuthenticated) { window.location.href = '/login'; return; }
    const form = document.getElementById('preferences-form');
    const apply = ({ data: body }) => { const data = body.data; form.preferred_locale.value = data.preferred_locale; form.preferred_currency.value = data.preferred_currency; applyLocale(data.preferred_locale); document.getElementById('currency-example').textContent = `Contoh: ${data.currency_example}`; };
    authApi.profile().then(apply);
    form?.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearErrors(form);

        apply(await authApi.updatePreferences(Object.fromEntries(new FormData(form))));
        showAlert(form, 'success', form.preferred_locale.value === 'en' ? 'Settings saved successfully.' : 'Pengaturan berhasil disimpan.');

        window.setTimeout(() => document.getElementById('form-alert')?.remove(), 4000);
    });
}
