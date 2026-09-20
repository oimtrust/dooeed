@extends('layouts.app')

@section('title', 'Dooeed')
@section('page', 'home')

@section('content')
<main class="page page-center">
  <div class="container container-tight py-4">
    <div class="text-center mb-4">
      <span class="navbar-brand navbar-brand-autodark">Dooeed</span>
    </div>
    <div class="card card-md">
      <div class="card-body text-center">
        <h1 class="h2 mb-3">Finesse Wealth Tracker</h1>
        <p class="text-secondary mb-4">Sign in to continue to your dashboard.</p>
        <div class="d-flex gap-2 justify-content-center">
          <a href="{{ route('login') }}" class="btn btn-primary">Sign in</a>
          <a href="{{ route('register') }}" class="btn">Sign up</a>
        </div>
      </div>
    </div>
  </div>
</main>
@endsection
