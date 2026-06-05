<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Event App</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
  <div class="app-nav">
    <div class="wrap">
      <div class="app-nav-inner">
        <a href="{{ route('events.index') }}" class="nav-brand">Event App</a>
        <div class="nav-links">
          @auth
            <a href="{{ route('events.index') }}" class="btn btn-ghost btn-sm">Events</a>
            <a href="{{ route('dashboard') }}" class="btn btn-ghost btn-sm">Dashboard</a>
            <form action="/logout" method="POST" style="display:inline">
              @csrf
              <button type="submit" class="btn btn-outline btn-sm">Logout</button>
            </form>
          @else
            <a href="/login"    class="btn btn-ghost btn-sm">Login</a>
            <a href="/register" class="btn btn-default btn-sm">Register</a>
          @endauth
        </div>
      </div>
    </div>
  </div>

  <div class="wrap">
    @if (session('success'))
      <div class="flash-ok">{{ session('success') }}</div>
    @endif

    @yield('content')
  </div>
</body>
</html>
