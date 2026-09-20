import { authApi } from '../api.js';
import { authStore } from '../store.js';
import { clearErrors, handleApiError, setLoading, showAlert } from '../components/form.js';

export function initResetPasswordPage() {
    if (authStore.isAuthenticated) {
        window.location.href = '/dashboard';
        return;
    }

    const form = document.getElementById('reset-password-form');
    if (!form) return;

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearErrors(form);
        setLoading(form, true);

        try {
            const { data } = await authApi.resetPassword({
                token: form.token.value,
                email: form.email.value.trim(),
                password: form.password.value,
                password_confirmation: form.password_confirmation.value,
            });
            showAlert(form, 'success', `${data.message ?? 'Password reset.'} You can now sign in.`);
            form.reset();
            setTimeout(() => {
                window.location.href = '/login';
            }, 1500);
        } catch (error) {
            handleApiError(form, error);
        } finally {
            setLoading(form, false);
        }
    });
}
