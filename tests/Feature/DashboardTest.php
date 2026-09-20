<?php

it('renders the dashboard with all financial menus in the requested order', function (): void {
    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSeeInOrder([
            'Dashboard',
            'Profil Kekayaan Awal',
            'Profil Kemampuan Menabung',
            'Cek Kesehatan Finansial',
            'Level Kekayaan',
            'Saran Budgeting',
            'Laporan Laba Rugi',
            'Atur Budgeting',
            'Mutasi Rekening',
            'Dream Tracker',
            'Pindah Kas/ Tabung',
            'Pendapatan',
            'Pengeluaran',
            'Utang',
            'Piutang',
            'Beli Jual Barang',
            'Investasi',
        ])
        ->assertSee('id="sidebar-menu"', false)
        ->assertSee('id="panel-dashboard"', false)
        ->assertSee('id="panel-investasi"', false)
        ->assertSee('id="logout-button"', false)
        ->assertSee('Ringkasan transaksi belum tersedia');
});
