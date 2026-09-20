@extends('layouts.app')

@section('title', 'Dashboard')
@section('page', 'dashboard')

@section('content')
<div class="page">
  <div class="page-wrapper">
    <div class="page-header d-print-none">
      <div class="container-xl">
        <div class="row g-2 align-items-center">
          <div class="col">
            <h2 class="page-title">Dashboard</h2>
          </div>
          <div class="col-auto ms-auto">
            <button type="button" id="logout-button" class="btn btn-outline-danger">Logout</button>
          </div>
        </div>
      </div>
    </div>
    <div class="page-body">
      <div class="container-xl">
        <div class="card">
          <div class="card-body">
            <h3 class="card-title">Welcome, <span id="user-name">…</span></h3>
            <p class="text-secondary mb-0" id="user-email">…</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
