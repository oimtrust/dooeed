@extends('admin.layout')
@section('page', 'admin-audit')
@section('admin-content')
<h1 class="page-title mb-4">Audit log</h1>
<form id="admin-filters" class="card card-body mb-4"><div class="row g-3 align-items-end">
<div class="col-md-5"><label class="form-label" for="admin_id">Admin ID</label><input class="form-control" id="admin_id" name="admin_id" placeholder="UUID admin" data-i18n-placeholder></div>
<div class="col-md-5"><label class="form-label" for="target_user_id">User ID</label><input class="form-control" id="target_user_id" name="target_user_id" placeholder="UUID user" data-i18n-placeholder></div>
<div class="col-md-2"><button class="btn btn-primary">Filter</button> <a href="{{ route('admin.audit.index') }}" class="btn">Reset</a></div>
</div></form>
<div class="card"><div class="table-responsive"><table class="table table-vcenter card-table"><thead><tr><th>Timestamp</th><th>Admin</th><th>User</th><th>Action</th><th>Reason</th></tr></thead><tbody id="admin-rows"></tbody></table></div><div class="card-footer" id="admin-pagination"></div></div>
<template id="audit-row"><tr><td data-field="created_at"></td><td><a data-field="admin_email"></a></td><td><a data-field="target_user_email"></a></td><td data-field="action"></td><td data-field="reason"></td></tr></template>
@endsection
