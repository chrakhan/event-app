@extends('layouts.app')

@section('content')
    <h1>Register</h1>

    @if ($errors->any())
        <ul class="error">
            @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    @endif

    <form action="/register" method="POST">
        @csrf
        <label>Name</label>
        <input type="text" name="name" value="{{ old('name') }}">

        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}">

        <label>Password</label>
        <input type="password" name="password">

        <label>Confirm Password</label>
        <input type="password" name="password_confirmation">

        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="/login">Login</a></p>
@endsection
