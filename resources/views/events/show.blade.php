@extends('layouts.app')

@section('content')
    <div class="card" style="padding:1.5rem;max-width:36rem">
        <h1 style="font-size:1.25rem;font-weight:600;margin-bottom:1rem">{{ $event->title }}</h1>
        <dl style="display:grid;gap:.625rem;font-size:.9rem">
            <div><dt style="font-weight:500;color:var(--muted-fg);margin-bottom:.125rem">Location</dt><dd>{{ $event->location }}</dd></div>
            <div><dt style="font-weight:500;color:var(--muted-fg);margin-bottom:.125rem">Date</dt><dd>{{ $event->event_date->format('D, M j, Y') }}</dd></div>
            @if($event->start_time)
            <div><dt style="font-weight:500;color:var(--muted-fg);margin-bottom:.125rem">Time</dt><dd>{{ substr($event->start_time,0,5) }}</dd></div>
            @endif
            <div><dt style="font-weight:500;color:var(--muted-fg);margin-bottom:.125rem">Status</dt><dd><span class="badge badge-{{ $event->status }}">{{ ucfirst($event->status) }}</span></dd></div>
            <div><dt style="font-weight:500;color:var(--muted-fg);margin-bottom:.125rem">Organizer</dt><dd>{{ $event->user->name ?? 'N/A' }}</dd></div>
            @if($event->description)
            <div><dt style="font-weight:500;color:var(--muted-fg);margin-bottom:.125rem">Description</dt><dd style="white-space:pre-wrap">{{ $event->description }}</dd></div>
            @endif
        </dl>
        <div style="margin-top:1.25rem;display:flex;gap:.5rem">
            <a href="{{ route('events.index') }}" class="btn btn-outline btn-sm">← Back</a>
            @auth
                @if (auth()->id() == $event->user_id || auth()->user()->hasRole('admin'))
                    <a href="{{ route('events.edit', $event) }}" class="btn btn-secondary btn-sm">Edit</a>
                    <form action="{{ route('events.destroy', $event) }}" method="POST" style="display:inline">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Delete?')" class="btn btn-destructive btn-sm">Delete</button>
                    </form>
                @endif
            @endauth
        </div>
    </div>
@endsection
