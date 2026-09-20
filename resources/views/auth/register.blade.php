@extends('layouts.app')

@section('title', 'Daftar — Dooeed')
@section('page', 'register')

@section('content')
<x-auth-shell eyebrow="LANGKAH PERTAMA ANDA" heading="Mulai cerita finansial Anda." description="Buat akun dan ambil langkah pertama menuju keuangan yang lebih terencana.">
        <form id="register-form" novalidate>
          <div class="mb-3">
            <label class="form-label" for="name">Nama lengkap</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Nama lengkap Anda" autocomplete="name" maxlength="255" required />
          </div>
          <div class="mb-3">
            <label class="form-label" for="email">Alamat email</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="nama@email.com" autocomplete="email" required />
          </div>
          <div class="mb-3">
            <label class="form-label" for="password">Kata sandi</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 8 karakter" autocomplete="new-password" minlength="8" required />
          </div>
          <div class="mb-3">
            <label class="form-label" for="password_confirmation">Konfirmasi kata sandi</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ulangi kata sandi" autocomplete="new-password" minlength="8" required />
          </div>
          <div class="form-footer">
            <button type="submit" class="btn btn-primary w-100">Buat akun</button>
          </div>
        </form>
  <div class="text-center text-secondary mt-4">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></div>
</x-auth-shell>
@endsection
