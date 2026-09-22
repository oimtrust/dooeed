@extends('admin.layout')
@section('page', 'admin-user-detail')
@section('admin-content')
<section id="user-detail" data-user-id="{{ $userId }}" hidden>
  <div class="d-flex flex-wrap gap-3 align-items-center mb-4"><h1 class="page-title" data-profile="name"></h1><span class="badge bg-blue-lt" data-profile="role"></span><span class="badge bg-blue-lt" data-profile="status"></span><a id="user-audit-link" class="ms-auto">Riwayat tindakan</a></div>
  <div class="card card-body mb-4"><div data-profile="email"></div><div class="text-secondary" data-profile="created_at"></div><div class="text-secondary" data-profile="id"></div></div>
  <div class="row row-cards mb-4">
    <div class="col-md-4"><div class="card card-body h-100"><h2 class="card-title">Accounts & current balance</h2><p id="accounts-count"></p><div id="user-balances"></div><ul id="user-accounts" class="list-unstyled mb-0"></ul></div></div>
    <div class="col-md-4"><div class="card card-body h-100"><h2 class="card-title">Outstanding debts</h2><div class="h2" id="user-debts"></div><p class="text-secondary mb-0">Utang berstatus active / overdue. Mata uang utang belum tersedia pada data aplikasi.</p></div></div>
    <div class="col-md-4"><div class="card card-body h-100"><h2 class="card-title">Investasi</h2><div id="user-investments"></div><p class="text-secondary mb-0">Ringkasan akun dan jumlah aset; valuasi pasar belum tersedia.</p></div></div>
  </div>
  <div class="card mb-4" id="user-actions" hidden><div class="card-header"><h2 class="card-title">Tindakan admin</h2></div><div class="card-body d-flex flex-wrap gap-2">
    <button type="button" class="btn btn-outline-danger" data-action="suspend" data-bs-toggle="modal" data-bs-target="#user-action-modal">Suspend</button>
    <button type="button" class="btn btn-outline-primary" data-action="activate" data-bs-toggle="modal" data-bs-target="#user-action-modal">Activate</button>
    <button type="button" class="btn btn-outline-primary" data-action="reset_password" data-bs-toggle="modal" data-bs-target="#user-action-modal">Reset Password</button>
    <button type="button" class="btn btn-outline-primary" data-action="change_role" data-bs-toggle="modal" data-bs-target="#user-action-modal">Ubah role</button>
    <button type="button" class="btn btn-outline-danger" data-action="delete" data-bs-toggle="modal" data-bs-target="#user-action-modal">Hapus user</button>
  </div></div>
  <div class="card"><div class="card-header"><h2 class="card-title">20 transaksi terakhir</h2></div><div class="table-responsive"><table class="table table-vcenter card-table"><thead><tr><th>Tanggal</th><th>Akun</th><th>Jenis</th><th>Jumlah</th><th>Deskripsi</th></tr></thead><tbody id="user-transactions"></tbody></table></div></div>
</section>
<div class="modal fade" id="user-action-modal" tabindex="-1" aria-labelledby="action-title" aria-hidden="true"><div class="modal-dialog"><form id="user-action-form" class="modal-content">
  <div class="modal-header"><h3 class="modal-title" id="action-title"></h3><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
  <div class="modal-body"><div class="alert alert-danger" id="action-error" role="alert" hidden></div><p id="action-description"></p>
    <div id="role-field" hidden><label class="form-label" for="role">Role</label><select class="form-select mb-3" name="role" id="role"><option>user</option><option>admin</option></select></div>
    <label class="form-label" id="reason-label" for="reason">Alasan</label><textarea class="form-control" id="reason" name="reason" maxlength="2000"></textarea>
  </div><div class="modal-footer"><button type="button" class="btn" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Konfirmasi</button></div>
</form></div></div>
<template id="transaction-row"><tr><td data-field="transaction_date"></td><td data-field="account_name"></td><td data-field="type"></td><td data-field="amount"></td><td data-field="description"></td></tr></template>
@endsection
