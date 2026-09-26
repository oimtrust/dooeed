@extends('layouts.app')

@section('title', 'Dashboard — Dooeed')
@section('page', 'dashboard')

@section('content')
<div class="page">
  <x-dashboard-menu />
  <div class="page-wrapper">
    <header class="navbar navbar-expand-md d-print-none">
      <div class="container-xl">
        <span class="navbar-brand fs-4">Ruang keuangan Anda</span>
        <div class="d-flex align-items-center gap-3 ms-auto">
          <button type="button" id="admin-panel-button" class="btn btn-primary btn-sm" data-session-url="{{ route('admin.session') }}" hidden>Admin panel</button>
          <span class="text-secondary small d-none d-md-block" id="user-email">Memuat akun…</span>
          <div class="dropdown"><button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Akun</button><div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow"><a class="dropdown-item" href="{{ route('profile') }}"><svg class="icon dropdown-item-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="7" r="4"/><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/></svg>Profile</a><a class="dropdown-item" href="{{ route('settings') }}"><svg class="icon dropdown-item-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3a2 2 0 1 0 0 4a2 2 0 0 0 0 -4"/><path d="M12 17a2 2 0 1 0 0 4a2 2 0 0 0 0 -4"/><path d="M3 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/><path d="M17 12h4"/><path d="M7 12h10"/></svg>Settings &amp; Privacy</a><div class="dropdown-divider"></div><button type="button" id="logout-button" class="dropdown-item text-danger">Logout</button></div></div>
        </div>
      </div>
    </header>
    <main class="page-body">
      <div class="container-xl tab-content">
        <section class="tab-pane fade show active" id="panel-dashboard" role="tabpanel" aria-labelledby="menu-dashboard" tabindex="0">
          <div class="row align-items-center mb-4 g-3">
            <div class="col">
              <div class="page-pretitle">Ringkasan keuangan</div>
              <h1 class="page-title mt-1">Dashboard</h1>
            </div>
            <div class="col-auto"><span class="badge bg-blue-lt">Perjalanan finansial Anda</span></div>
          </div>
          <div class="card bg-primary-lt mb-4">
            <div class="card-body p-4">
              <div class="row align-items-center g-3">
                <div class="col-lg-8">
                  <div class="subheader text-primary mb-2">Selangkah lebih terencana</div>
                  <h2>Halo, <span id="user-name">…</span>!</h2>
                  <p class="text-secondary mb-0">Kenali kondisi keuangan, susun anggaran, dan mulai wujudkan impian Anda. Semuanya dimulai dari mencatat apa yang Anda miliki hari ini.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                  <button class="btn btn-primary" type="button" data-dashboard-menu="profil-kekayaan-awal">Lengkapi profil kekayaan</button>
                </div>
              </div>
            </div>
          </div>
          <div class="row row-cards mb-4">
            @foreach (['Total aset' => 'Seluruh kekayaan yang Anda miliki', 'Total kewajiban' => 'Utang dan kewajiban yang berjalan', 'Kekayaan bersih' => 'Total aset dikurangi kewajiban', 'Kemampuan menabung' => 'Dana yang dapat disisihkan per bulan'] as $label => $description)
              <div class="col-sm-6 col-xl-3">
                <div class="card h-100"><div class="card-body">
                  <div class="subheader">{{ $label }}</div>
                  <div class="h1 my-3 text-secondary" aria-label="Data belum tersedia">—</div>
                  <div class="text-secondary small">{{ $description }}</div>
                </div></div>
              </div>
            @endforeach
          </div>
          <div class="row row-cards">
            <div class="col-lg-7">
              <div class="card h-100">
                <div class="card-header"><h2 class="card-title">Mulai dari sini</h2></div>
                <div class="list-group list-group-flush">
                  @foreach ([['profil-kekayaan-awal', 'Catat kekayaan awal', 'Kenali aset, rekening, dan kewajiban Anda.'], ['profil-kemampuan-menabung', 'Kenali kemampuan menabung', 'Petakan pendapatan dan kebutuhan rutin.'], ['atur-budgeting', 'Susun anggaran', 'Siapkan alokasi untuk kebutuhan dan tujuan Anda.']] as [$menu, $label, $description])
                    <button type="button" class="list-group-item list-group-item-action d-flex gap-3 align-items-center py-3" data-dashboard-menu="{{ $menu }}">
                      <span class="avatar bg-blue-lt">{{ $loop->iteration }}</span>
                      <span><span class="d-block fw-medium">{{ $label }}</span><span class="d-block text-secondary small mt-1">{{ $description }}</span></span>
                      <span class="ms-auto text-secondary" aria-hidden="true">→</span>
                    </button>
                  @endforeach
                </div>
              </div>
            </div>
            <div class="col-lg-5">
              <div class="card h-100">
                <div class="card-header"><h2 class="card-title">Dream Tracker</h2><span class="badge bg-purple-lt ms-auto">Tujuan Anda</span></div>
                <div class="card-body">
                  <h3>Impian besar, langkah kecil.</h3>
                  <p class="text-secondary">Siapkan tempat untuk merencanakan dana darurat, rumah pertama, atau perjalanan impian Anda.</p>
                  <button class="btn btn-outline-primary" type="button" data-dashboard-menu="dream-tracker">Jelajahi Dream Tracker</button>
                </div>
              </div>
            </div>
            <div class="col-12">
              <div class="card">
                <div class="card-header"><h2 class="card-title">Aktivitas keuangan</h2></div>
                <div class="empty py-5">
                  <p class="empty-title">Ringkasan transaksi belum tersedia</p>
                  <p class="empty-subtitle text-secondary">Pendapatan, pengeluaran, dan mutasi rekening akan ditampilkan setelah fitur pencatatan tersedia.</p>
                </div>
              </div>
            </div>
          </div>
        </section>
        <section class="tab-pane fade" id="panel-profil-kekayaan-awal" role="tabpanel" aria-labelledby="menu-profil-kekayaan-awal" tabindex="0">
          <div class="page-pretitle">Keuangan pribadi</div>
          <h1 class="page-title mt-1 mb-4">Profil Kekayaan Awal</h1>
          <div id="initial-wealth-profile">
            <div class="row row-cards mb-4">
              <div class="col-sm-4"><div class="card"><div class="card-body d-flex align-items-center gap-3"><span class="avatar avatar-lg bg-green-lt text-green" aria-hidden="true"><svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21v-16a2 2 0 0 1 2 -2h9a2 2 0 0 1 2 2v16"/><path d="M16 7h3a2 2 0 0 1 2 2v12"/><path d="M3 21h18"/><path d="M9 7v.01"/><path d="M9 11v.01"/><path d="M9 15v.01"/><path d="M13 7v.01"/><path d="M13 11v.01"/><path d="M13 15v.01"/></svg></span><div><div class="subheader">Total aset</div><div class="h2 mb-0" data-summary="assets">Rp0</div></div></div></div></div>
              <div class="col-sm-4"><div class="card"><div class="card-body d-flex align-items-center gap-3"><span class="avatar avatar-lg bg-red-lt text-red" aria-hidden="true"><svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="M3 10h18"/><path d="M7 15h.01"/><path d="M11 15h2"/></svg></span><div><div class="subheader">Total kewajiban</div><div class="h2 mb-0" data-summary="liabilities">Rp0</div></div></div></div></div>
              <div class="col-sm-4"><div class="card"><div class="card-body d-flex align-items-center gap-3"><span class="avatar avatar-lg bg-blue-lt text-blue" aria-hidden="true"><svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17l6 -6l4 4l8 -8"/><path d="M14 7h7v7"/><path d="M3 21h18"/></svg></span><div><div class="subheader">Kekayaan bersih awal</div><div class="h2 mb-0" data-summary="net_worth">Rp0</div></div></div></div></div>
            </div>
            <div class="row row-cards" id="initial-wealth-categories"></div>
          </div>
        </section>
        <section class="tab-pane fade" id="panel-profil-kemampuan-menabung" role="tabpanel" aria-labelledby="menu-profil-kemampuan-menabung" tabindex="0">
          <div class="page-pretitle">Keuangan pribadi</div>
          <h1 class="page-title mt-1 mb-4">Profil Kemampuan Menabung</h1>
          <div class="card"><div class="empty py-5">
            <span class="badge bg-blue-lt mb-3">Segera tersedia</span>
            <h2 class="empty-title">Profil Kemampuan Menabung</h2>
            <p class="empty-subtitle text-secondary">Fitur ini sedang disiapkan. Pencatatan dan pengelolaan data belum tersedia.</p>
            <div class="empty-action"><button type="button" class="btn btn-primary" data-dashboard-menu="dashboard">Kembali ke Dashboard</button></div>
          </div></div>
        </section>
        <section class="tab-pane fade" id="panel-cek-kesehatan-finansial" role="tabpanel" aria-labelledby="menu-cek-kesehatan-finansial" tabindex="0">
          <div class="page-pretitle">Keuangan pribadi</div>
          <h1 class="page-title mt-1 mb-4">Cek Kesehatan Finansial</h1>
          <div class="card"><div class="empty py-5">
            <span class="badge bg-blue-lt mb-3">Segera tersedia</span>
            <h2 class="empty-title">Cek Kesehatan Finansial</h2>
            <p class="empty-subtitle text-secondary">Fitur ini sedang disiapkan. Pencatatan dan pengelolaan data belum tersedia.</p>
            <div class="empty-action"><button type="button" class="btn btn-primary" data-dashboard-menu="dashboard">Kembali ke Dashboard</button></div>
          </div></div>
        </section>
        <section class="tab-pane fade" id="panel-level-kekayaan" role="tabpanel" aria-labelledby="menu-level-kekayaan" tabindex="0">
          <div class="page-pretitle">Keuangan pribadi</div>
          <h1 class="page-title mt-1 mb-4">Level Kekayaan</h1>
          <div class="card"><div class="empty py-5">
            <span class="badge bg-blue-lt mb-3">Segera tersedia</span>
            <h2 class="empty-title">Level Kekayaan</h2>
            <p class="empty-subtitle text-secondary">Fitur ini sedang disiapkan. Pencatatan dan pengelolaan data belum tersedia.</p>
            <div class="empty-action"><button type="button" class="btn btn-primary" data-dashboard-menu="dashboard">Kembali ke Dashboard</button></div>
          </div></div>
        </section>
        <section class="tab-pane fade" id="panel-saran-budgeting" role="tabpanel" aria-labelledby="menu-saran-budgeting" tabindex="0">
          <div class="page-pretitle">Keuangan pribadi</div>
          <h1 class="page-title mt-1 mb-4">Saran Budgeting</h1>
          <div class="card"><div class="empty py-5">
            <span class="badge bg-blue-lt mb-3">Segera tersedia</span>
            <h2 class="empty-title">Saran Budgeting</h2>
            <p class="empty-subtitle text-secondary">Fitur ini sedang disiapkan. Pencatatan dan pengelolaan data belum tersedia.</p>
            <div class="empty-action"><button type="button" class="btn btn-primary" data-dashboard-menu="dashboard">Kembali ke Dashboard</button></div>
          </div></div>
        </section>
        <section class="tab-pane fade" id="panel-laporan-laba-rugi" role="tabpanel" aria-labelledby="menu-laporan-laba-rugi" tabindex="0">
          <div class="page-pretitle">Keuangan pribadi</div>
          <h1 class="page-title mt-1 mb-4">Laporan Laba Rugi</h1>
          <div class="card"><div class="empty py-5">
            <span class="badge bg-blue-lt mb-3">Segera tersedia</span>
            <h2 class="empty-title">Laporan Laba Rugi</h2>
            <p class="empty-subtitle text-secondary">Fitur ini sedang disiapkan. Pencatatan dan pengelolaan data belum tersedia.</p>
            <div class="empty-action"><button type="button" class="btn btn-primary" data-dashboard-menu="dashboard">Kembali ke Dashboard</button></div>
          </div></div>
        </section>
        <section class="tab-pane fade" id="panel-atur-budgeting" role="tabpanel" aria-labelledby="menu-atur-budgeting" tabindex="0">
          <div class="page-pretitle">Keuangan pribadi</div>
          <h1 class="page-title mt-1 mb-4">Atur Budgeting</h1>
          <div class="card"><div class="empty py-5">
            <span class="badge bg-blue-lt mb-3">Segera tersedia</span>
            <h2 class="empty-title">Atur Budgeting</h2>
            <p class="empty-subtitle text-secondary">Fitur ini sedang disiapkan. Pencatatan dan pengelolaan data belum tersedia.</p>
            <div class="empty-action"><button type="button" class="btn btn-primary" data-dashboard-menu="dashboard">Kembali ke Dashboard</button></div>
          </div></div>
        </section>
        <section class="tab-pane fade" id="panel-mutasi-rekening" role="tabpanel" aria-labelledby="menu-mutasi-rekening" tabindex="0">
          <div class="page-pretitle">Keuangan pribadi</div>
          <h1 class="page-title mt-1 mb-4">Mutasi Rekening</h1>
          <div class="card"><div class="empty py-5">
            <span class="badge bg-blue-lt mb-3">Segera tersedia</span>
            <h2 class="empty-title">Mutasi Rekening</h2>
            <p class="empty-subtitle text-secondary">Fitur ini sedang disiapkan. Pencatatan dan pengelolaan data belum tersedia.</p>
            <div class="empty-action"><button type="button" class="btn btn-primary" data-dashboard-menu="dashboard">Kembali ke Dashboard</button></div>
          </div></div>
        </section>
        <section class="tab-pane fade" id="panel-dream-tracker" role="tabpanel" aria-labelledby="menu-dream-tracker" tabindex="0">
          <div class="page-pretitle">Keuangan pribadi</div>
          <h1 class="page-title mt-1 mb-4">Dream Tracker</h1>
          <div class="card"><div class="empty py-5">
            <span class="badge bg-blue-lt mb-3">Segera tersedia</span>
            <h2 class="empty-title">Dream Tracker</h2>
            <p class="empty-subtitle text-secondary">Fitur ini sedang disiapkan. Pencatatan dan pengelolaan data belum tersedia.</p>
            <div class="empty-action"><button type="button" class="btn btn-primary" data-dashboard-menu="dashboard">Kembali ke Dashboard</button></div>
          </div></div>
        </section>
        <section class="tab-pane fade" id="panel-pindah-kas-tabung" role="tabpanel" aria-labelledby="menu-pindah-kas-tabung" tabindex="0">
          <div class="page-pretitle">Keuangan pribadi</div>
          <h1 class="page-title mt-1 mb-4">Pindah Kas/ Tabung</h1>
          <div class="card"><div class="empty py-5">
            <span class="badge bg-blue-lt mb-3">Segera tersedia</span>
            <h2 class="empty-title">Pindah Kas/ Tabung</h2>
            <p class="empty-subtitle text-secondary">Fitur ini sedang disiapkan. Pencatatan dan pengelolaan data belum tersedia.</p>
            <div class="empty-action"><button type="button" class="btn btn-primary" data-dashboard-menu="dashboard">Kembali ke Dashboard</button></div>
          </div></div>
        </section>
        <section class="tab-pane fade" id="panel-pendapatan" role="tabpanel" aria-labelledby="menu-pendapatan" tabindex="0">
          <div class="page-pretitle">Keuangan pribadi</div>
          <h1 class="page-title mt-1 mb-4">Pendapatan</h1>
          <div class="card"><div class="empty py-5">
            <span class="badge bg-blue-lt mb-3">Segera tersedia</span>
            <h2 class="empty-title">Pendapatan</h2>
            <p class="empty-subtitle text-secondary">Fitur ini sedang disiapkan. Pencatatan dan pengelolaan data belum tersedia.</p>
            <div class="empty-action"><button type="button" class="btn btn-primary" data-dashboard-menu="dashboard">Kembali ke Dashboard</button></div>
          </div></div>
        </section>
        <section class="tab-pane fade" id="panel-pengeluaran" role="tabpanel" aria-labelledby="menu-pengeluaran" tabindex="0">
          <div class="page-pretitle">Keuangan pribadi</div>
          <h1 class="page-title mt-1 mb-4">Pengeluaran</h1>
          <div class="card"><div class="empty py-5">
            <span class="badge bg-blue-lt mb-3">Segera tersedia</span>
            <h2 class="empty-title">Pengeluaran</h2>
            <p class="empty-subtitle text-secondary">Fitur ini sedang disiapkan. Pencatatan dan pengelolaan data belum tersedia.</p>
            <div class="empty-action"><button type="button" class="btn btn-primary" data-dashboard-menu="dashboard">Kembali ke Dashboard</button></div>
          </div></div>
        </section>
        <section class="tab-pane fade" id="panel-utang" role="tabpanel" aria-labelledby="menu-utang" tabindex="0">
          <div class="page-pretitle">Keuangan pribadi</div>
          <h1 class="page-title mt-1 mb-4">Utang</h1>
          <div class="card"><div class="empty py-5">
            <span class="badge bg-blue-lt mb-3">Segera tersedia</span>
            <h2 class="empty-title">Utang</h2>
            <p class="empty-subtitle text-secondary">Fitur ini sedang disiapkan. Pencatatan dan pengelolaan data belum tersedia.</p>
            <div class="empty-action"><button type="button" class="btn btn-primary" data-dashboard-menu="dashboard">Kembali ke Dashboard</button></div>
          </div></div>
        </section>
        <section class="tab-pane fade" id="panel-piutang" role="tabpanel" aria-labelledby="menu-piutang" tabindex="0">
          <div class="page-pretitle">Keuangan pribadi</div>
          <h1 class="page-title mt-1 mb-4">Piutang</h1>
          <div class="card"><div class="empty py-5">
            <span class="badge bg-blue-lt mb-3">Segera tersedia</span>
            <h2 class="empty-title">Piutang</h2>
            <p class="empty-subtitle text-secondary">Fitur ini sedang disiapkan. Pencatatan dan pengelolaan data belum tersedia.</p>
            <div class="empty-action"><button type="button" class="btn btn-primary" data-dashboard-menu="dashboard">Kembali ke Dashboard</button></div>
          </div></div>
        </section>
        <section class="tab-pane fade" id="panel-beli-jual-barang" role="tabpanel" aria-labelledby="menu-beli-jual-barang" tabindex="0">
          <div class="page-pretitle">Keuangan pribadi</div>
          <h1 class="page-title mt-1 mb-4">Beli Jual Barang</h1>
          <div class="card"><div class="empty py-5">
            <span class="badge bg-blue-lt mb-3">Segera tersedia</span>
            <h2 class="empty-title">Beli Jual Barang</h2>
            <p class="empty-subtitle text-secondary">Fitur ini sedang disiapkan. Pencatatan dan pengelolaan data belum tersedia.</p>
            <div class="empty-action"><button type="button" class="btn btn-primary" data-dashboard-menu="dashboard">Kembali ke Dashboard</button></div>
          </div></div>
        </section>
        <section class="tab-pane fade" id="panel-investasi" role="tabpanel" aria-labelledby="menu-investasi" tabindex="0">
          <div class="page-pretitle">Keuangan pribadi</div>
          <h1 class="page-title mt-1 mb-4">Investasi</h1>
          <div class="card"><div class="empty py-5">
            <span class="badge bg-blue-lt mb-3">Segera tersedia</span>
            <h2 class="empty-title">Investasi</h2>
            <p class="empty-subtitle text-secondary">Fitur ini sedang disiapkan. Pencatatan dan pengelolaan data belum tersedia.</p>
            <div class="empty-action"><button type="button" class="btn btn-primary" data-dashboard-menu="dashboard">Kembali ke Dashboard</button></div>
          </div></div>
        </section>
      </div>
    </main>
    <footer class="footer footer-transparent d-print-none"><div class="container-xl text-secondary small">Dooeed · Kelola hari ini, rencanakan masa depan.</div></footer>
  </div>
</div>
@endsection
