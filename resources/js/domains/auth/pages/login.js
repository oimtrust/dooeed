import { authApi } from '../api.js';
import { authStore } from '../store.js';
import { clearErrors, handleApiError, setLoading } from '../components/form.js';

export function initLoginPage() {
    if (authStore.isAuthenticated) {
        window.location.href = '/dashboard';
        return;
    }

    const form = document.getElementById('login-form');
    if (!form) return;

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearErrors(form);
        setLoading(form, true);

        try {
            await authApi.login({
                email: form.email.value.trim(),
                password: form.password.value,
            });
            window.location.href = '/dashboard';
        } catch (error) {
            handleApiError(form, error);
        } finally {
            setLoading(form, false);
        }
    });
}
