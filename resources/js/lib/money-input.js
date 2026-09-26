export function unformatMoneyInput(value) {
    return String(value ?? '').replace(/[^0-9]/g, '');
}

export function formatMoneyInput(value) {
    const serverDecimal = String(value ?? '').match(/^(\d+)\.\d{1,2}$/);
    const digits = serverDecimal ? serverDecimal[1] : unformatMoneyInput(value);
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
