@extends('layouts.app')

@section('title', 'Dooeed — Hari ini terencana. Esok lebih leluasa.')
@section('page', 'home')

@section('content')
@vite('resources/css/landing.css')
<div class="landing" lang="id">
  <a class="visually-hidden-focusable landing-skip" href="#main">Langsung ke konten</a>
  <header class="landing-header">
    <nav class="container-xl d-flex align-items-center justify-content-between gap-3" aria-label="Navigasi utama">
      <a class="landing-brand" href="{{ route('home') }}" aria-label="Dooeed, beranda">dooeed<span>.</span></a>
      <div class="d-none d-md-flex align-items-center gap-4">
        <a href="#tentang">Tentang</a><a href="#fitur">Ruang keuangan</a><a href="#langkah">Cara memulai</a>
      </div>
      <div class="d-flex align-items-center gap-3"><a href="{{ route('login') }}">Masuk</a><a class="btn btn-primary" href="{{ route('register') }}">Buat akun <span aria-hidden="true">↗</span></a></div>
    </nav>
  </header>
  <main id="main">
    <section class="landing-hero container-xl">
      <div class="row align-items-center g-5">
        <div class="col-lg-6">
          <div class="landing-eyebrow"><span class="landing-dot"></span> KEUANGAN PRIBADI, LEBIH TERARAH</div>
          <h1>Uang punya tujuan.<br>Hidup punya<br><em>lebih banyak ruang.</em></h1>
          <p class="landing-lead">Dari memahami kondisi hari ini hingga merencanakan impian esok. Mulai perjalanan finansial Anda bersama Dooeed.</p>
          <div class="d-flex gap-3 flex-wrap mt-4"><a class="btn btn-primary btn-lg" href="{{ route('register') }}">Mulai perjalanan Anda <span aria-hidden="true">↗</span></a><a class="btn btn-outline-secondary btn-lg" href="#tentang">Kenali Dooeed</a></div>
          <div class="landing-hero-note"><span aria-hidden="true">✳</span> Langkah kecil hari ini. Kebiasaan baik untuk nanti.</div>
        </div>
        <div class="col-lg-6">
          <div class="landing-visual">
            <div class="landing-orbit" aria-hidden="true"></div>
            <div class="landing-preview card">
              <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-4"><span class="fw-bold">Ruang keuangan saya</span><span class="badge bg-green-lt">Ilustrasi</span></div>
                <div class="small text-secondary">Selangkah lebih dekat dengan</div><div class="landing-preview-title">masa depan pilihanmu.</div>
                <div class="landing-chart" aria-hidden="true">
                  <svg viewBox="0 0 400 150" xmlns="http://www.w3.org/2000/svg" fill="none" focusable="false">
                    <path d="M0 35H400M0 80H400M0 125H400" stroke="#e7eceb" stroke-dasharray="4 5"/>
                    <path d="M0 132L50 116L100 124L150 81L200 91L250 50L300 64L350 25L400 12V150H0Z" fill="#e9f5ee"/>
                    <path d="M0 132L50 116L100 124L150 81L200 91L250 50L300 64L350 25L400 12" stroke="#427d68" stroke-width="4" stroke-linejoin="round"/>
                    <circle cx="350" cy="25" r="6" fill="#427d68" stroke="#fff" stroke-width="3"/>
                  </svg>
                </div>
                <div class="d-flex justify-content-between small text-secondary mb-4"><span>Hari ini</span><span>Selangkah lagi</span><span>Masa depan</span></div>
                <div class="row g-2"><div class="col-6"><div class="landing-preview-tile"><span class="landing-tile-icon" aria-hidden="true">↗</span><div class="fw-semibold mt-2">Lebih terencana</div><div class="small text-secondary">Kenali arah keuangan</div></div></div><div class="col-6"><div class="landing-preview-tile"><span class="landing-tile-icon" aria-hidden="true">◎</span><div class="fw-semibold mt-2">Lebih bermakna</div><div class="small text-secondary">Dekat dengan impian</div></div></div></div>
              </div>
            </div>
            <div class="landing-goal"><span class="landing-goal-symbol" aria-hidden="true">✦</span><div><strong>Satu mimpi, satu langkah.</strong><span>Mulai dari yang paling berarti.</span></div></div>
            <div class="landing-visual-caption">Gambaran konsep · bukan data atau proyeksi keuangan</div>
          </div>
        </div>
      </div>
    </section>
    <div class="landing-values"><div class="container-xl d-flex flex-wrap justify-content-between gap-3"><span>KENALI KONDISIMU</span><span aria-hidden="true">✳</span><span>TENTUKAN PRIORITAS</span><span aria-hidden="true">✳</span><span>BERI RUANG UNTUK IMPIAN</span></div></div>
    <section class="landing-section container-xl" id="tentang">
      <div class="row g-4 align-items-start"><div class="col-lg-5"><div class="landing-eyebrow">TENTANG DOOEED</div><h2>Bukan sekadar angka.<br>Ini tentang <em>hidup Anda.</em></h2></div><div class="col-lg-6 offset-lg-1"><p class="landing-lead">Rumah pertama. Dana untuk keluarga. Waktu untuk menjelajah. Setiap orang punya alasan sendiri untuk mulai merencanakan keuangan.</p><p class="text-secondary">Dooeed dikembangkan sebagai ruang untuk memahami kekayaan, mengatur arus uang, dan merencanakan tujuan pribadi. Dimulai dari mengenali posisi Anda hari ini, lalu mengambil langkah yang lebih terarah.</p><a class="landing-text-link" href="#fitur">Temukan ruang untuk rencana Anda <span aria-hidden="true">→</span></a></div></div>
    </section>
    <section class="landing-features" id="fitur">
      <div class="container-xl">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-5"><div><div class="landing-eyebrow">SATU RUANG, BANYAK KEMUNGKINAN</div><h2>Keuangan yang lebih utuh.<br>Dari hari ini sampai nanti.</h2></div><span class="landing-status">Modul finansial dalam pengembangan</span></div>
        <div class="row g-4">
          <div class="col-lg-6"><article class="landing-feature landing-feature-dark h-100"><div class="landing-feature-number">01 / KENALI</div><h3>Mulai dari posisi<br>Anda hari ini.</h3><p>Bangun gambaran tentang aset, kewajiban, dan kemampuan menabung. Pahami fondasinya sebelum melangkah lebih jauh.</p><div class="landing-asset-art" aria-hidden="true"><div><span>Aset</span><i></i></div><div><span>Kewajiban</span><i></i></div><div><span>Kekayaan bersih</span><i></i></div></div><div class="landing-feature-tags"><span>Profil kekayaan</span><span>Kesehatan finansial</span></div></article></div>
          <div class="col-lg-6"><div class="d-flex flex-column gap-4 h-100"><article class="landing-feature landing-feature-mint flex-fill"><div class="landing-feature-number">02 / RENCANAKAN</div><h3>Setiap rupiah, punya arah.</h3><p>Ruang untuk merencanakan anggaran, mencatat pendapatan dan pengeluaran, serta memahami pergerakan uang Anda.</p><div class="landing-feature-tags"><span>Budgeting</span><span>Arus kas</span><span>Mutasi rekening</span></div></article><article class="landing-feature landing-feature-peach flex-fill"><div class="landing-feature-number">03 / WUJUDKAN</div><h3>Impian layak punya rencana.</h3><p>Susun tujuan menabung dan kenali aset investasi Anda. Beri setiap impian tempat untuk bertumbuh.</p><div class="landing-feature-tags"><span>Dream Tracker</span><span>Investasi</span></div></article></div></div>
        </div>
      </div>
    </section>
    <section class="landing-section container-xl" id="langkah">
      <div class="text-center mb-5"><div class="landing-eyebrow">MULAI DENGAN SEDERHANA</div><h2>Perjalanan baru.<br>Langkah pertama yang mudah.</h2></div>
      <div class="row g-5">
        <div class="col-md-4"><div class="landing-step">01 <span></span></div><h3>Buat akun Anda</h3><p class="text-secondary">Daftar dengan nama, email, dan kata sandi untuk mulai menggunakan Dooeed.</p></div>
        <div class="col-md-4"><div class="landing-step">02 <span></span></div><h3>Masuk ke ruang pribadi</h3><p class="text-secondary">Akses dashboard melalui akun Anda dan kenali ruang keuangan yang sedang dikembangkan.</p></div>
        <div class="col-md-4"><div class="landing-step">03 <span></span></div><h3>Tumbuh bersama Dooeed</h3><p class="text-secondary">Fitur pencatatan, perencanaan, dan laporan akan melengkapi perjalanan Anda seiring pengembangan aplikasi.</p></div>
      </div>
    </section>
    <section class="container-xl pb-5">
      <div class="landing-cta"><div class="landing-eyebrow">MASA DEPAN DIMULAI DARI HARI INI</div><h2>Rencana besar Anda,<br>dimulai dari <em>satu langkah.</em></h2><p>Buat akun dan mulai kenali Dooeed hari ini.</p><a class="btn btn-lg" href="{{ route('register') }}">Buat akun Dooeed <span aria-hidden="true">↗</span></a><div class="landing-cta-note">Sudah bergabung? <a href="{{ route('login') }}">Masuk ke akun Anda</a></div></div>
    </section>
  </main>
  <footer class="container-xl landing-footer"><div><a class="landing-brand" href="{{ route('home') }}">dooeed<span>.</span></a><p>Keuangan pribadi, lebih terarah.</p></div><div class="d-flex gap-4"><a href="#tentang">Tentang</a><a href="{{ route('login') }}">Masuk</a><a href="{{ route('register') }}">Daftar</a></div><span class="small text-secondary">© {{ date('Y') }} Dooeed</span></footer>
</div>
@endsection
