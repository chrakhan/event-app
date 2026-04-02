@extends('layouts.app')

@section('content')
    <h1>Login</h1>

    @if ($errors->any())
        <ul class="error">
            @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    @endif

    <form action="/login" method="POST">
        @csrf
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}">

        <label>Password</label>
        <input type="password" name="password">

        <button type="submit">Login</button>
    </form>
    <p>No account? <a href="/register">Register</a></p>
@endsection
