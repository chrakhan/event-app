@extends('layouts.app')

@section('content')
    <h1>Create Event</h1>

    @if ($errors->any())
        <ul class="error">
            @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('events.store') }}" method="POST">
        @csrf
        @include('events.form')
        <button type="submit">Create Event</button>
    </form>
@endsection
