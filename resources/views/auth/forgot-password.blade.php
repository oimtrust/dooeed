@extends('layouts.app')

@section('title', 'Forgot password')
@section('page', 'forgot-password')

@section('content')
<main class="page page-center">
  <div class="container container-tight py-4">
    <div class="text-center mb-4">
      <a href="{{ route('home') }}" class="navbar-brand navbar-brand-autodark">Dooeed</a>
    </div>
    <div class="card card-md">
      <div class="card-body">
        <h1 class="h2 text-center mb-4">Forgot password</h1>
        <p class="text-secondary mb-4">Enter your email address and we will send you a password reset link.</p>
        <form id="forgot-password-form" novalidate>
          <div class="mb-3">
            <label class="form-label" for="email">Email address</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="your@email.com" autocomplete="email" required />
          </div>
          <div class="form-footer">
            <button type="submit" class="btn btn-primary w-100">Send reset link</button>
          </div>
        </form>
      </div>
    </div>
    <div class="text-center text-secondary mt-3">Remember your password? <a href="{{ route('login') }}">Sign in</a></div>
  </div>
</main>
@endsection
