export function unformatMoneyInput(value) {
    return String(value ?? '').replace(/[^0-9]/g, '');
}

export function formatMoneyInput(value) {
    const digits = unformatMoneyInput(value);
    return digits ? new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(Number(digits)) : '';
}

export function bindMoneyInput(input) {
    input.type = 'text';
    input.inputMode = 'numeric';
    input.addEventListener('input', () => {
        input.value = formatMoneyInput(input.value);
    });
    input.value = formatMoneyInput(input.value);
}
