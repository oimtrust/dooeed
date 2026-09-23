import { authApi } from '../api.js';
import { authStore } from '../store.js';
import { clearErrors, handleApiError, setLoading, showAlert } from '../components/form.js';

export function initVerifyEmailPage() {
    if (authStore.isAuthenticated) {
        window.location.href = '/dashboard';
        return;
    }

    const form = document.getElementById('verify-email-form');
    const resendButton = document.getElementById('resend-otp');
    const email = new URLSearchParams(window.location.search).get('email');

    if (!form || !resendButton || !email) {
        window.location.href = '/register';
        return;
    }

    form.email.value = email;

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearErrors(form);
        setLoading(form, true);

        try {
            await authApi.verifyEmailOtp({ email, code: form.code.value.trim() });
            window.location.href = '/dashboard';
        } catch (error) {
            handleApiError(form, error);
        } finally {
            setLoading(form, false);
        }
    });

    resendButton.addEventListener('click', async () => {
        resendButton.disabled = true;

        try {
            await authApi.requestEmailOtp(email);
            showAlert(form, 'success', 'Kode OTP baru telah dikirim.');
        } catch (error) {
            handleApiError(form, error);
        } finally {
            resendButton.disabled = false;
        }
    });
}
