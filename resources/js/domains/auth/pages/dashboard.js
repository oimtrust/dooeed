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

    document.querySelectorAll('[data-dashboard-menu]').forEach((button) => {
        button.addEventListener('click', () => {
            document.getElementById(`menu-${button.dataset.dashboardMenu}`)?.click();
        });
    });

    document.querySelectorAll('#sidebar-menu [data-bs-toggle="tab"]').forEach((tab) => {
        tab.addEventListener('shown.bs.tab', () => {
            const panel = document.querySelector(tab.dataset.bsTarget);
            document.title = `${tab.querySelector('.nav-link-title').textContent} — Dooeed`;
            panel?.focus({ preventScroll: true });
            window.scrollTo({ top: 0, behavior: 'instant' });

            const sidebar = document.getElementById('sidebar-menu');
            const toggle = document.querySelector('[data-bs-target="#sidebar-menu"]');

            if (sidebar?.classList.contains('show') && toggle?.getClientRects().length) {
                toggle.click();
            }
        });
    });

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
