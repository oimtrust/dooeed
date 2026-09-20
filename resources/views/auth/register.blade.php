@extends('layouts.app')

@section('title', 'Sign up')
@section('page', 'register')

@section('content')
<main class="page page-center">
  <div class="container container-tight py-4">
    <div class="text-center mb-4">
      <a href="{{ route('home') }}" class="navbar-brand navbar-brand-autodark">Dooeed</a>
    </div>
    <div class="card card-md">
      <div class="card-body">
        <h1 class="h2 text-center mb-4">Create new account</h1>
        <form id="register-form" novalidate>
          <div class="mb-3">
            <label class="form-label" for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Enter name" autocomplete="name" required />
          </div>
          <div class="mb-3">
            <label class="form-label" for="email">Email address</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="your@email.com" autocomplete="email" required />
          </div>
          <div class="mb-3">
            <label class="form-label" for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Min. 8 characters" autocomplete="new-password" required />
          </div>
          <div class="mb-3">
            <label class="form-label" for="password_confirmation">Confirm password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Repeat password" autocomplete="new-password" required />
          </div>
          <div class="form-footer">
            <button type="submit" class="btn btn-primary w-100">Create account</button>
          </div>
        </form>
      </div>
    </div>
    <div class="text-center text-secondary mt-3">Already have an account? <a href="{{ route('login') }}">Sign in</a></div>
  </div>
</main>
@endsection
