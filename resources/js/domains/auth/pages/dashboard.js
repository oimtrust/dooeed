import { authApi } from '../api.js';
import { authStore } from '../store.js';

export function initDashboardPage() {
    if (!authStore.isAuthenticated) {
        window.location.href = '/login';
        return;
    }

    const nameEl = document.getElementById('user-name');
    const emailEl = document.getElementById('user-email');
    const logoutBtn = document.getElementById('logout-button');

    const cached = authStore.user;
    if (cached) {
        if (nameEl) nameEl.textContent = cached.name;
        if (emailEl) emailEl.textContent = cached.email;
    }

    authApi
        .me()
        .then((user) => {
            if (nameEl) nameEl.textContent = user.name;
            if (emailEl) emailEl.textContent = user.email;
        })
        .catch(() => {
            window.location.href = '/login';
        });

    logoutBtn?.addEventListener('click', async () => {
        logoutBtn.classList.add('btn-loading');
        logoutBtn.disabled = true;

        try {
            await authApi.logout();
        } finally {
            window.location.href = '/login';
        }
    });
}
