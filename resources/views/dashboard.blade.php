@extends('layouts.app')

@section('content')
    <h1>Welcome, {{ auth()->user()->name }}!</h1>
    <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
    <p><strong>Role:</strong> {{ auth()->user()->getRoleNames()->first() }}</p>

    <form action="/logout" method="POST">
        @csrf
        <button type="submit">Logout</button>
    </form>
@endsection
