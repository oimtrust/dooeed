@extends('layouts.app')

@section('title', 'Reset password')
@section('page', 'reset-password')

@section('content')
<main class="page page-center">
  <div class="container container-tight py-4">
    <div class="text-center mb-4">
      <a href="{{ route('home') }}" class="navbar-brand navbar-brand-autodark">Dooeed</a>
    </div>
    <div class="card card-md">
      <div class="card-body">
        <h1 class="h2 text-center mb-4">Reset password</h1>
        <form id="reset-password-form" novalidate>
          <input type="hidden" name="token" value="{{ $token }}" />
          <div class="mb-3">
            <label class="form-label" for="email">Email address</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="your@email.com" value="{{ request('email') }}" autocomplete="email" required />
          </div>
          <div class="mb-3">
            <label class="form-label" for="password">New password</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Min. 8 characters" autocomplete="new-password" required />
          </div>
          <div class="mb-3">
            <label class="form-label" for="password_confirmation">Confirm new password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Repeat password" autocomplete="new-password" required />
          </div>
          <div class="form-footer">
            <button type="submit" class="btn btn-primary w-100">Reset password</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</main>
@endsection
