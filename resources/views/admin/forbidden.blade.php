@extends('layouts.app')
@section('title', '403 — Akses ditolak')
@section('content')
<meta http-equiv="refresh" content="3;url={{ route('dashboard') }}">
<main class="page page-center"><div class="container-tight text-center py-5">
  <h1>403 — Akses ditolak</h1><p>Panel ini hanya untuk admin. Anda akan diarahkan ke dashboard.</p>
  <a class="btn btn-primary" href="{{ route('dashboard') }}">Kembali ke dashboard</a>
</div></main>
@endsection
