@extends('layouts.app')

@section('content')
    <h2 style="font-size:1.25rem;font-weight:600;margin-bottom:1.25rem">Edit Event</h2>

    @if ($errors->any())
        <div class="err-box" style="margin-bottom:1rem">
            <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card" style="padding:1.5rem;max-width:36rem">
        <form action="{{ route('events.update', $event) }}" method="POST">
            @csrf @method('PUT')
            @include('events.form')
            <div style="display:flex;gap:.5rem;margin-top:.5rem">
                <button type="submit" class="btn btn-default">Save Changes</button>
                <a href="{{ route('events.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
@endsection
