@extends('admin.layout')
@section('page', 'admin-users')
@section('admin-content')
<h1 class="page-title mb-4">Monitoring user</h1>
<form id="admin-filters" class="card card-body mb-4">
  <div class="row g-3 align-items-end">
    <div class="col-md-4"><label for="search" class="form-label">Nama / email</label><input id="search" name="search" class="form-control" placeholder="Cari user"></div>
    <div class="col-md-2"><label for="status" class="form-label">Status</label><select id="status" name="status" class="form-select"><option value="">Semua</option><option>active</option><option>suspended</option></select></div>
    <div class="col-md-3"><label for="sort" class="form-label">Urutkan</label><select id="sort" name="sort" class="form-select"><option value="name">Nama</option><option value="email">Email</option><option value="status">Status</option><option value="role">Role</option><option value="accounts_count">Total akun</option><option value="transactions_count">Total transaksi</option><option value="created_at" selected>Created at</option></select></div>
    <div class="col-md-2"><label for="direction" class="form-label">Arah</label><select id="direction" name="direction" class="form-select"><option value="desc">Menurun</option><option value="asc">Menaik</option></select></div>
    <div class="col-md-1"><button class="btn btn-primary">Cari</button></div>
  </div>
</form>
<div class="card"><div class="table-responsive"><table class="table table-vcenter card-table">
  <thead><tr><th>Nama</th><th>Email</th><th>Status</th><th>Role</th><th>Total akun</th><th>Total transaksi</th><th>Created at</th></tr></thead>
  <tbody id="admin-rows"></tbody>
</table></div><div class="card-footer" id="admin-pagination"></div></div>
<template id="user-row"><tr><td><a data-field="name"></a></td><td data-field="email"></td><td><span class="badge" data-field="status"></span></td><td data-field="role"></td><td data-field="accounts_count"></td><td data-field="transactions_count"></td><td data-field="created_at"></td></tr></template>
@endsection
