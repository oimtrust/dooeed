@extends('layouts.app')
@section('title', 'Admin — Dooeed')
@section('content')
<div class="page"><div class="page-wrapper">
  <header class="navbar"><nav class="container-xl gap-3" aria-label="Navigasi admin">
    <a class="navbar-brand" href="{{ route('admin.users.index') }}">Dooeed Admin</a>
    <a href="{{ route('admin.users.index') }}">Users</a>
    <a href="{{ route('admin.audit.index') }}">Audit log</a>
    <a class="ms-auto" href="{{ route('dashboard') }}">Dashboard</a>
  </nav></header>
  <main class="page-body"><div class="container-xl" id="admin-page" data-dashboard-url="{{ route('dashboard') }}" data-login-url="{{ route('login') }}" data-users-url="{{ route('admin.users.index') }}" data-user-url="{{ route('admin.users.show', ['user' => '__USER__']) }}" data-audit-url="{{ route('admin.audit.index') }}">
    <div id="admin-message" class="alert" role="alert" hidden></div>
    <div id="admin-loading" class="text-secondary mb-3" role="status">Memuat data…</div>
    <button id="admin-retry" class="btn btn-outline-primary mb-3" type="button" hidden>Coba lagi</button>
    @yield('admin-content')
  </div></main>
</div></div>
@endsection
