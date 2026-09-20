export function clearErrors(form) {
    form.querySelectorAll('.is-invalid').forEach((el) => el.classList.remove('is-invalid'));
    form.querySelectorAll('.invalid-feedback').forEach((el) => el.remove());
    const alert = document.getElementById('form-alert');
    if (alert) alert.remove();
}

export function applyErrors(form, errors) {
    Object.entries(errors).forEach(([field, messages]) => {
        const input = form.querySelector(`[name="${field}"]`);
        if (!input) return;

        input.classList.add('is-invalid');

        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        feedback.textContent = messages[0];
        input.closest('.mb-3, .mb-2, div')?.appendChild(feedback);
    });
}

export function showAlert(form, type, message) {
    const alert = document.createElement('div');
    alert.id = 'form-alert';
    alert.className = `alert alert-${type} alert-dismissible`;
    alert.setAttribute('role', 'alert');
    alert.innerHTML = `<div>${message}</div><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`;
    form.prepend(alert);
}

export function setLoading(form, loading) {
    const button = form.querySelector('button[type="submit"]');
    if (!button) return;

    button.classList.toggle('btn-loading', loading);
    button.disabled = loading;
}

export function handleApiError(form, error, fallback = 'Something went wrong. Please try again.') {
    const errors = error.response?.data?.errors;

    if (errors) {
        applyErrors(form, errors);
    }

    showAlert(form, 'danger', error.response?.data?.message ?? fallback);
}
