import { auditUrl, date, emptyRow, money, row, textList } from './ui.js';

export function renderUserDetail(data) {
    const detail = document.getElementById('user-detail');
    detail.hidden = false;
    detail.querySelectorAll('[data-profile]').forEach((node) => {
        const field = node.dataset.profile;
        node.textContent = field === 'created_at' ? `Terdaftar ${date(data.user.created_at)}` : data.user[field];
    });
    document.getElementById('user-audit-link').href = auditUrl({ target_user_id: data.user.id });
    document.getElementById('accounts-count').textContent = `${data.accounts.length} akun`;
    document.getElementById('user-debts').textContent = money(data.outstanding_debts);
    textList('user-balances', data.balances.map((balance) => `${balance.currency} ${money(balance.total)}`), 'p', 'Belum ada saldo.');
    textList('user-accounts', data.accounts.map((account) => `${account.name} — ${account.currency} ${money(account.current_balance)}`), 'li', 'Belum ada akun.');
    textList('user-investments', data.investments.map((investment) => `${investment.name} · ${investment.platform ?? '—'} · ${investment.assets_count} aset`), 'p', 'Belum ada investasi.');
    document.getElementById('user-transactions').replaceChildren(...(data.transactions.length
        ? data.transactions.map((transaction) => row('transaction-row', { ...transaction, amount: `${transaction.currency} ${money(transaction.amount)}` }))
        : [emptyRow(5, 'Belum ada transaksi.')]));
    document.getElementById('user-actions').hidden = !data.user.can_manage;
    detail.querySelectorAll('[data-action]').forEach((button) => {
        button.hidden = (button.dataset.action === 'suspend' && data.user.status !== 'active')
            || (['activate', 'delete'].includes(button.dataset.action) && data.user.status !== 'suspended');
    });
}
