@extends('layouts.app')

@section('title', 'Sign in')
@section('page', 'login')

@section('content')
<main class="page page-center">
  <div class="container container-tight py-4">
    <div class="text-center mb-4">
      <a href="{{ route('home') }}" class="navbar-brand navbar-brand-autodark">Dooeed</a>
    </div>
    <div class="card card-md">
      <div class="card-body">
        <h1 class="h2 text-center mb-4">Login to your account</h1>
        <form id="login-form" novalidate>
          <div class="mb-3">
            <label class="form-label" for="email">Email address</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="your@email.com" autocomplete="email" required />
          </div>
          <div class="mb-2">
            <label class="form-label" for="password">
              Password
              <span class="form-label-description">
                <a href="{{ route('password.request') }}">I forgot password</a>
              </span>
            </label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Your password" autocomplete="current-password" required />
          </div>
          <div class="form-footer">
            <button type="submit" class="btn btn-primary w-100">Sign in</button>
          </div>
        </form>
      </div>
    </div>
    <div class="text-center text-secondary mt-3">Don't have account yet? <a href="{{ route('register') }}">Sign up</a></div>
  </div>
</main>
@endsection
