@extends('layouts.app')

@section('content')
    <h1>Edit Event</h1>

    @if ($errors->any())
        <ul class="error">
            @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('events.update', $event) }}" method="POST">
        @csrf @method('PUT')
        @include('events.form')
        <button type="submit">Update Event</button>
    </form>
@endsection
