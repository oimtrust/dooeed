@extends('layouts.app')

@section('title', 'Verifikasi Email — Dooeed')
@section('page', 'verify-email')

@section('content')
<x-auth-shell eyebrow="KONFIRMASI EMAIL" heading="Masukkan kode verifikasi." description="Kami telah mengirim kode OTP enam digit ke alamat email Anda.">
  <form id="verify-email-form" novalidate>
    <div class="mb-3">
      <label class="form-label" for="email">Alamat email</label>
      <input type="email" name="email" id="email" class="form-control" autocomplete="email" required readonly />
    </div>
    <div class="mb-3">
      <label class="form-label" for="code">Kode OTP</label>
      <input type="text" name="code" id="code" class="form-control" inputmode="numeric" autocomplete="one-time-code" maxlength="6" pattern="[0-9]{6}" placeholder="123456" required autofocus />
      <div class="form-hint">Kode berlaku selama 10 menit dan hanya dapat digunakan satu kali.</div>
    </div>
    <div class="form-footer">
      <button type="submit" class="btn btn-primary w-100">Verifikasi dan masuk</button>
    </div>
  </form>
  <div class="text-center text-secondary mt-4">Belum menerima kode? <button id="resend-otp" type="button" class="btn btn-link p-0 align-baseline">Kirim ulang</button></div>
</x-auth-shell>
@endsection
