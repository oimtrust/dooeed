@extends('layouts.app')
@section('title', 'Profile — Dooeed')
@section('page', 'profile')
@section('content')
<main class="page page-center"><div class="container container-tight py-4"><div class="card"><div class="card-body"><a href="{{ route('dashboard') }}" class="btn btn-link px-0">← Dashboard</a><h1 class="card-title">Profile</h1><p id="profile-email" class="text-secondary"></p><hr><h2 class="h3">Ubah password</h2><form id="profile-password-form"><label class="form-label">Password saat ini<input name="current_password" class="form-control" type="password" required></label><label class="form-label">Password baru<input name="password" class="form-control" type="password" minlength="8" required></label><label class="form-label">Konfirmasi password baru<input name="password_confirmation" class="form-control" type="password" required></label><button class="btn btn-primary">Simpan password</button></form></div></div></div></main>
@endsection
