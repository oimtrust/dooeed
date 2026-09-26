import { authApi } from '../api.js';
import { authStore } from '../store.js';
import { clearErrors, handleApiError, setLoading } from '../components/form.js';
import { applyLocale } from '../../../lib/localization.js';

function showSuccessToast(message) {
    document.getElementById('profile-success-toast')?.remove();

    const toast = document.createElement('div');
    toast.id = 'profile-success-toast';
    toast.className = 'toast show align-items-center border-0 position-fixed bottom-0 end-0 m-4 shadow-lg';
    toast.setAttribute('role', 'status');
    toast.innerHTML = `<div class="d-flex"><div class="toast-body d-flex align-items-center gap-2"><span class="avatar avatar-sm bg-green-lt text-green">✓</span><span>${message}</span></div><button type="button" class="btn-close me-2 m-auto" aria-label="Close"></button></div>`;
    toast.querySelector('.btn-close').addEventListener('click', () => toast.remove());
    document.body.append(toast);

    window.setTimeout(() => toast.remove(), 4000);
}

export function initProfilePage() {
    if (!authStore.isAuthenticated) { window.location.href = '/login'; return; }
    const form = document.getElementById('profile-password-form');
    authApi.profile().then(({ data: body }) => {
        document.getElementById('profile-email').textContent = body.data.email;
        applyLocale(body.data.preferred_locale);
    });
    form?.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearErrors(form);
        setLoading(form, true);

        try {
            await authApi.updatePassword(Object.fromEntries(new FormData(form)));
            form.reset();
            showSuccessToast(document.documentElement.lang === 'en' ? 'Password updated successfully.' : 'Password berhasil diubah.');
        } catch (error) {
            handleApiError(form, error);
        } finally {
            setLoading(form, false);
        }
    });
}
