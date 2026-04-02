<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Event App</title>
    <style>
        body {
            font-family: Arial;
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }

        nav {
            margin-bottom: 20px;
        }

        nav a {
            margin-right: 15px;
            text-decoration: none;
            color: #333;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 8px;
            margin: 5px 0 15px;
            box-sizing: border-box;
        }

        button,
        .btn {
            padding: 8px 16px;
            cursor: pointer;
            background: #333;
            color: white;
            border: none;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <nav>
        <a href="{{ route('events.index') }}">All Events</a>
        @auth
            <a href="{{ route('events.create') }}">+ New Event</a>
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <form action="/logout" method="POST" style="display:inline">
                @csrf
                <button type="submit">Logout</button>
            </form>
        @else
            <a href="/login">Login</a>
            <a href="/register">Register</a>
        @endauth
    </nav>

    @if (session('success'))
        <p class="success">✅ {{ session('success') }}</p>
    @endif

    @yield('content')
</body>

</html>
