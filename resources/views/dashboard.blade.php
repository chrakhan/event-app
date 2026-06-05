@extends('layouts.app')

@section('content')
    <div class="card" style="padding:1.5rem;max-width:24rem">
        <h1 style="font-size:1.25rem;font-weight:600;margin-bottom:1rem">Welcome, {{ auth()->user()->name }}</h1>
        <dl style="display:grid;gap:.5rem;font-size:.9rem;margin-bottom:1.25rem">
            <div><dt style="font-weight:500;color:var(--muted-fg)">Email</dt><dd>{{ auth()->user()->email }}</dd></div>
            <div><dt style="font-weight:500;color:var(--muted-fg)">Role</dt><dd>{{ auth()->user()->getRoleNames()->first() ?? '—' }}</dd></div>
        </dl>
        <div style="display:flex;gap:.5rem">
            <a href="{{ route('events.index') }}" class="btn btn-default btn-sm">View Events</a>
            <form action="/logout" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline btn-sm">Logout</button>
            </form>
        </div>
    </div>
@endsection
