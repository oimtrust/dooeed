import './bootstrap';
import '@tabler/core';

import { initDashboardPage } from './domains/auth/pages/dashboard.js';
import { initForgotPasswordPage } from './domains/auth/pages/forgot-password.js';
import { initLoginPage } from './domains/auth/pages/login.js';
import { initRegisterPage } from './domains/auth/pages/register.js';
import { initResetPasswordPage } from './domains/auth/pages/reset-password.js';

import { initAdminUsers } from './domains/admin/pages/users.js';
import { initAdminAudit } from './domains/admin/pages/audit.js';
import { initAdminUserDetail } from './domains/admin/pages/user-detail.js';

const pages = {
    'admin-users': initAdminUsers,
    'admin-audit': initAdminAudit,
    'admin-user-detail': initAdminUserDetail,
    login: initLoginPage,
    register: initRegisterPage,
    'forgot-password': initForgotPasswordPage,
    'reset-password': initResetPasswordPage,
    dashboard: initDashboardPage,
};

pages[document.body.dataset.page]?.();
