<aside class="navbar navbar-vertical navbar-expand-lg" aria-label="Navigasi utama">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold fs-2 text-primary" href="{{ route('dashboard') }}">dooeed<span class="text-secondary">.</span></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Buka menu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="sidebar-menu">
      <div class="text-secondary small text-uppercase px-3 pt-3 pb-2">Keuangan pribadi</div>
      <div class="navbar-nav" role="tablist" aria-label="Menu keuangan" aria-orientation="vertical">
        <div class="nav-item" role="presentation">
          <button class="nav-link w-100 text-start active" id="menu-dashboard" data-bs-toggle="tab" data-bs-target="#panel-dashboard" type="button" role="tab" aria-controls="panel-dashboard" aria-selected="true">
            <span class="nav-link-icon text-secondary small" aria-hidden="true">01</span>
            <span class="nav-link-title">Dashboard</span>
          </button>
        </div>
        <div class="nav-item" role="presentation">
          <button class="nav-link w-100 text-start" id="menu-profil-kekayaan-awal" data-bs-toggle="tab" data-bs-target="#panel-profil-kekayaan-awal" type="button" role="tab" aria-controls="panel-profil-kekayaan-awal" aria-selected="false">
            <span class="nav-link-icon text-secondary small" aria-hidden="true">02</span>
            <span class="nav-link-title">Profil Kekayaan Awal</span>
          </button>
        </div>
        <div class="nav-item" role="presentation">
          <button class="nav-link w-100 text-start" id="menu-profil-kemampuan-menabung" data-bs-toggle="tab" data-bs-target="#panel-profil-kemampuan-menabung" type="button" role="tab" aria-controls="panel-profil-kemampuan-menabung" aria-selected="false">
            <span class="nav-link-icon text-secondary small" aria-hidden="true">03</span>
            <span class="nav-link-title">Profil Kemampuan Menabung</span>
          </button>
        </div>
        <div class="nav-item" role="presentation">
          <button class="nav-link w-100 text-start" id="menu-cek-kesehatan-finansial" data-bs-toggle="tab" data-bs-target="#panel-cek-kesehatan-finansial" type="button" role="tab" aria-controls="panel-cek-kesehatan-finansial" aria-selected="false">
            <span class="nav-link-icon text-secondary small" aria-hidden="true">04</span>
            <span class="nav-link-title">Cek Kesehatan Finansial</span>
          </button>
        </div>
        <div class="nav-item" role="presentation">
          <button class="nav-link w-100 text-start" id="menu-level-kekayaan" data-bs-toggle="tab" data-bs-target="#panel-level-kekayaan" type="button" role="tab" aria-controls="panel-level-kekayaan" aria-selected="false">
            <span class="nav-link-icon text-secondary small" aria-hidden="true">05</span>
            <span class="nav-link-title">Level Kekayaan</span>
          </button>
        </div>
        <div class="nav-item" role="presentation">
          <button class="nav-link w-100 text-start" id="menu-saran-budgeting" data-bs-toggle="tab" data-bs-target="#panel-saran-budgeting" type="button" role="tab" aria-controls="panel-saran-budgeting" aria-selected="false">
            <span class="nav-link-icon text-secondary small" aria-hidden="true">06</span>
            <span class="nav-link-title">Saran Budgeting</span>
          </button>
        </div>
        <div class="nav-item" role="presentation">
          <button class="nav-link w-100 text-start" id="menu-laporan-laba-rugi" data-bs-toggle="tab" data-bs-target="#panel-laporan-laba-rugi" type="button" role="tab" aria-controls="panel-laporan-laba-rugi" aria-selected="false">
            <span class="nav-link-icon text-secondary small" aria-hidden="true">07</span>
            <span class="nav-link-title">Laporan Laba Rugi</span>
          </button>
        </div>
        <div class="nav-item" role="presentation">
          <button class="nav-link w-100 text-start" id="menu-atur-budgeting" data-bs-toggle="tab" data-bs-target="#panel-atur-budgeting" type="button" role="tab" aria-controls="panel-atur-budgeting" aria-selected="false">
            <span class="nav-link-icon text-secondary small" aria-hidden="true">08</span>
            <span class="nav-link-title">Atur Budgeting</span>
          </button>
        </div>
        <div class="nav-item" role="presentation">
          <button class="nav-link w-100 text-start" id="menu-mutasi-rekening" data-bs-toggle="tab" data-bs-target="#panel-mutasi-rekening" type="button" role="tab" aria-controls="panel-mutasi-rekening" aria-selected="false">
            <span class="nav-link-icon text-secondary small" aria-hidden="true">09</span>
            <span class="nav-link-title">Mutasi Rekening</span>
          </button>
        </div>
        <div class="nav-item" role="presentation">
          <button class="nav-link w-100 text-start" id="menu-dream-tracker" data-bs-toggle="tab" data-bs-target="#panel-dream-tracker" type="button" role="tab" aria-controls="panel-dream-tracker" aria-selected="false">
            <span class="nav-link-icon text-secondary small" aria-hidden="true">10</span>
            <span class="nav-link-title">Dream Tracker</span>
          </button>
        </div>
        <div class="nav-item" role="presentation">
          <button class="nav-link w-100 text-start" id="menu-pindah-kas-tabung" data-bs-toggle="tab" data-bs-target="#panel-pindah-kas-tabung" type="button" role="tab" aria-controls="panel-pindah-kas-tabung" aria-selected="false">
            <span class="nav-link-icon text-secondary small" aria-hidden="true">11</span>
            <span class="nav-link-title">Pindah Kas/ Tabung</span>
          </button>
        </div>
        <div class="nav-item" role="presentation">
          <button class="nav-link w-100 text-start" id="menu-pendapatan" data-bs-toggle="tab" data-bs-target="#panel-pendapatan" type="button" role="tab" aria-controls="panel-pendapatan" aria-selected="false">
            <span class="nav-link-icon text-secondary small" aria-hidden="true">12</span>
            <span class="nav-link-title">Pendapatan</span>
          </button>
        </div>
        <div class="nav-item" role="presentation">
          <button class="nav-link w-100 text-start" id="menu-pengeluaran" data-bs-toggle="tab" data-bs-target="#panel-pengeluaran" type="button" role="tab" aria-controls="panel-pengeluaran" aria-selected="false">
            <span class="nav-link-icon text-secondary small" aria-hidden="true">13</span>
            <span class="nav-link-title">Pengeluaran</span>
          </button>
        </div>
        <div class="nav-item" role="presentation">
          <button class="nav-link w-100 text-start" id="menu-utang" data-bs-toggle="tab" data-bs-target="#panel-utang" type="button" role="tab" aria-controls="panel-utang" aria-selected="false">
            <span class="nav-link-icon text-secondary small" aria-hidden="true">14</span>
            <span class="nav-link-title">Utang</span>
          </button>
        </div>
        <div class="nav-item" role="presentation">
          <button class="nav-link w-100 text-start" id="menu-piutang" data-bs-toggle="tab" data-bs-target="#panel-piutang" type="button" role="tab" aria-controls="panel-piutang" aria-selected="false">
            <span class="nav-link-icon text-secondary small" aria-hidden="true">15</span>
            <span class="nav-link-title">Piutang</span>
          </button>
        </div>
        <div class="nav-item" role="presentation">
          <button class="nav-link w-100 text-start" id="menu-beli-jual-barang" data-bs-toggle="tab" data-bs-target="#panel-beli-jual-barang" type="button" role="tab" aria-controls="panel-beli-jual-barang" aria-selected="false">
            <span class="nav-link-icon text-secondary small" aria-hidden="true">16</span>
            <span class="nav-link-title">Beli Jual Barang</span>
          </button>
        </div>
        <div class="nav-item" role="presentation">
          <button class="nav-link w-100 text-start" id="menu-investasi" data-bs-toggle="tab" data-bs-target="#panel-investasi" type="button" role="tab" aria-controls="panel-investasi" aria-selected="false">
            <span class="nav-link-icon text-secondary small" aria-hidden="true">17</span>
            <span class="nav-link-title">Investasi</span>
          </button>
        </div>
      </div>
      <div class="px-3 py-3 text-secondary small">Langkah kecil hari ini,<br>masa depan lebih terencana.</div>
    </div>
  </div>
</aside>
