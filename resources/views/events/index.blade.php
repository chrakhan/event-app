@extends('layouts.app')

@section('content')
    <h1>All Events</h1>
    <table>
        <tr>
            <th>Title</th>
            <th>Location</th>
            <th>Date</th>
            <th>Status</th>
            <th>Organizer</th>
            <th>Actions</th>
        </tr>
        @foreach ($events as $event)
            <tr>
                <td>{{ $event->title }}</td>
                <td>{{ $event->location }}</td>
                <td>{{ $event->event_date }}</td>
                <td>{{ $event->status }}</td>
                <td>{{ $event->user->name ?? 'N/A' }}</td>
                <td>
                    <a href="{{ route('events.show', $event) }}">View</a>
                    @auth
                        @if (auth()->id() == $event->user_id || auth()->user()->hasRole('admin'))
                            <a href="{{ route('events.edit', $event) }}">Edit</a>
                            <form action="{{ route('events.destroy', $event) }}" method="POST" style="display:inline">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('Delete?')">Delete</button>
                            </form>
                        @endif
                    @endauth
                </td>
            </tr>
        @endforeach
    </table>
@endsection
