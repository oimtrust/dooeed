@extends('layouts.app')

@section('title', 'Masuk — Dooeed')
@section('page', 'login')

@section('content')
<x-auth-shell eyebrow="SENANG MELIHAT ANDA LAGI" heading="Selamat datang kembali." description="Masuk untuk melanjutkan perjalanan keuangan Anda.">
        <form id="login-form" novalidate>
          <div class="mb-3">
            <label class="form-label" for="email">Alamat email</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="nama@email.com" autocomplete="email" required />
          </div>
          <div class="mb-2">
            <label class="form-label" for="password">
              Kata sandi
              <span class="form-label-description">
                <a href="{{ route('password.request') }}">Lupa kata sandi?</a>
              </span>
            </label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan kata sandi" autocomplete="current-password" required />
          </div>
          <div class="form-footer">
            <button type="submit" class="btn btn-primary w-100">Masuk</button>
          </div>
        </form>
  <div class="text-center text-secondary mt-4">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></div>
</x-auth-shell>
@endsection
