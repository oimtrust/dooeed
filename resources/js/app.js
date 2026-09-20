import './bootstrap';
import '@tabler/core/dist/js/tabler.min.js';

import { initDashboardPage } from './domains/auth/pages/dashboard.js';
import { initForgotPasswordPage } from './domains/auth/pages/forgot-password.js';
import { initLoginPage } from './domains/auth/pages/login.js';
import { initRegisterPage } from './domains/auth/pages/register.js';
import { initResetPasswordPage } from './domains/auth/pages/reset-password.js';

const pages = {
    login: initLoginPage,
    register: initRegisterPage,
    'forgot-password': initForgotPasswordPage,
    'reset-password': initResetPasswordPage,
    dashboard: initDashboardPage,
};

pages[document.body.dataset.page]?.();
