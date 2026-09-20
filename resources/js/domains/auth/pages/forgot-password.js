import { authApi } from '../api.js';
import { authStore } from '../store.js';
import { clearErrors, handleApiError, setLoading, showAlert } from '../components/form.js';

export function initForgotPasswordPage() {
    if (authStore.isAuthenticated) {
        window.location.href = '/dashboard';
        return;
    }

    const form = document.getElementById('forgot-password-form');
    if (!form) return;

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearErrors(form);
        setLoading(form, true);

        try {
            const { data } = await authApi.forgotPassword(form.email.value.trim());
            showAlert(form, 'success', data.message ?? 'Reset link sent. Check your email.');
            form.reset();
        } catch (error) {
            handleApiError(form, error);
        } finally {
            setLoading(form, false);
        }
    });
}
