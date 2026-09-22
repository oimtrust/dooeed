import { Modal } from '@tabler/core';
import { adminApi } from '../api.js';
import { handleError, message, page } from './ui.js';

const labels = { suspend: 'Suspend', activate: 'Activate', reset_password: 'Reset Password', change_role: 'Ubah role', delete: 'Hapus user' };

export function bindUserActions(getUser, reload) {
    const modal = document.getElementById('user-action-modal');
    const form = document.getElementById('user-action-form');
    const errorBox = document.getElementById('action-error');
    let action;
    let saving = false;

    function clearErrors() {
        errorBox.hidden = true;
        form.querySelectorAll('.invalid-feedback').forEach((node) => node.remove());
        form.querySelectorAll('.is-invalid').forEach((node) => node.classList.remove('is-invalid'));
    }
    modal.addEventListener('hide.bs.modal', (event) => {
        if (saving) event.preventDefault();
    });
    modal.addEventListener('show.bs.modal', (event) => {
        action = event.relatedTarget.dataset.action;
        form.reset();
        clearErrors();
        const user = getUser();
        document.getElementById('action-title').textContent = labels[action];
        const explanation = action === 'reset_password' ? ' Password dan token lama langsung dibatalkan. Link reset dikirim ke email user.'
            : action === 'delete' ? ' User disembunyikan dari daftar; data keuangan dan audit tetap tersimpan.' : '';
        document.getElementById('action-description').textContent = `Konfirmasi ${labels[action]} untuk ${user.email}?${explanation}`;
        document.getElementById('role-field').hidden = action !== 'change_role';
        form.elements.role.value = user.role;
        form.elements.reason.required = action === 'suspend';
        document.getElementById('reason-label').textContent = action === 'suspend' ? 'Alasan (wajib)' : 'Alasan (opsional)';
    });
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (saving) return;
        clearErrors();
        const payload = { action, reason: form.elements.reason.value.trim() || null, confirmed: true };
        if (action === 'change_role') payload.role = form.elements.role.value;
        saving = true;
        form.querySelectorAll('button').forEach((button) => { button.disabled = true; });
        const submit = form.querySelector('[type="submit"]');
        submit.classList.add('btn-loading');
        try {
            const result = await adminApi.updateUser(getUser().id, payload);
            saving = false;
            Modal.getInstance(modal)?.hide();
            if (action === 'delete') {
                window.location.assign(page().dataset.usersUrl);
                return;
            }
            if (await reload()) message(result.message, 'success');
        } catch (error) {
            if ([401, 403].includes(error.response?.status)) handleError(error);
            errorBox.textContent = error.response?.data?.message || 'Tindakan gagal. Silakan coba lagi.';
            errorBox.hidden = false;
            for (const [field, errors] of Object.entries(error.response?.data?.errors || {})) {
                const input = form.elements.namedItem(field);
                if (!input) continue;
                input.classList.add('is-invalid');
                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = errors.join(' ');
                input.after(feedback);
            }
        } finally {
            saving = false;
            submit.classList.remove('btn-loading');
            form.querySelectorAll('button').forEach((button) => { button.disabled = false; });
        }
    });
}
