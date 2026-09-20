@props(['eyebrow', 'heading', 'description'])
@vite('resources/css/auth.css')
<main class="page auth-page" lang="id">
  <div class="auth-shell">
    <aside class="auth-story">
      <a href="{{ route('home') }}" class="auth-brand" aria-label="Dooeed, beranda">dooeed<span>.</span></a>
      <div class="auth-story-content">
        <div class="auth-kicker">RUANG UNTUK MASA DEPAN ANDA</div>
        <h2>Hari ini terencana.<br><span>Esok lebih leluasa.</span></h2>
        <p>Kenali keuangan Anda, bangun kebiasaan baik,<br class="d-none d-xl-block"> dan beri ruang untuk setiap impian.</p>
        <svg class="auth-illustration" viewBox="0 0 520 340" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
          <circle cx="269" cy="166" r="145" fill="#213c59"/>
          <circle cx="269" cy="166" r="114" fill="none" stroke="#38516c" stroke-dasharray="4 9"/>
          <path d="M48 291H481" stroke="#58718a" stroke-width="2"/>
          <rect x="92" y="104" width="291" height="174" rx="18" fill="#f6f9fc"/>
          <path d="M110 104h255q18 0 18 18v20H92v-20q0 -18 18 -18" fill="#e5edf4"/>
          <circle cx="112" cy="124" r="4" fill="#f6ad85"/><circle cx="126" cy="124" r="4" fill="#e0c470"/><circle cx="140" cy="124" r="4" fill="#7cbaad"/>
          <rect x="114" y="162" width="70" height="8" rx="4" fill="#b4c5d5"/>
          <rect x="114" y="181" width="101" height="13" rx="6" fill="#294761"/>
          <path d="M115 248h243" stroke="#dce5ee"/>
          <path d="M126 231l41 -13l42 9l42 -35l41 9l51 -42" fill="none" stroke="#238978" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
          <circle cx="343" cy="159" r="7" fill="#238978" stroke="#fff" stroke-width="3"/>
          <g transform="rotate(-8 303 216)"><rect x="303" y="216" width="133" height="73" rx="14" fill="#a9dec9"/>
          <circle cx="331" cy="248" r="13" fill="#28765f"/><path d="m325 248 4 4 8 -9" fill="none" stroke="#fff" stroke-width="2.5"/>
          <path d="M354 241h54m-54 12h34" stroke="#367e68" stroke-width="6" stroke-linecap="round"/></g>
          <g transform="rotate(-7 66 62)"><rect x="66" y="62" width="140" height="66" rx="13" fill="#fff"/>
          <circle cx="94" cy="94" r="17" fill="#fff0dd"/><path d="m94 82 3 8 9 1 -7 5 2 9 -7 -5 -7 5 2 -9 -7 -5 9 -1z" fill="#d3a047"/>
          <path d="M121 87h58m-58 13h37" stroke="#b0c3d4" stroke-width="6" stroke-linecap="round"/></g>
          <path d="M429 198v-55m0 28c-32 0 -33 -26 -33 -26c28 -3 33 26 33 26m0 -12c0 -24 26 -31 26 -31c5 25 -26 31 -26 31" fill="#81b5a7" stroke="#81b5a7" stroke-width="3"/>
          <path d="M414 193h30l-5 27h-20z" fill="#dfa777"/>
          <path d="M366 68v16m-8 -8h16M63 198v12m-6 -6h12" stroke="#a9dec9" stroke-width="3" stroke-linecap="round"/>
        </svg>
        <div class="auth-story-notes"><span>01 / Kenali</span><span>02 / Rencanakan</span><span>03 / Wujudkan</span></div>
      </div>
      <div class="auth-story-footer">Langkah kecil. Arah yang lebih jelas.</div>
    </aside>
    <section class="auth-form-panel">
      <div class="auth-form-content">
        <div class="auth-kicker text-secondary mb-3">{{ $eyebrow }}</div>
        <h1>{{ $heading }}</h1>
        <p class="text-secondary mb-4">{{ $description }}</p>
        {{ $slot }}
      </div>
      <div class="auth-form-footer">Dooeed · Keuangan pribadi, lebih terarah.</div>
    </section>
  </div>
</main>
