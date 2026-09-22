<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>@yield('title', config('app.name', 'Dooeed'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body data-page="@yield('page', '')">
    @yield('content')
  </body>
</html>
