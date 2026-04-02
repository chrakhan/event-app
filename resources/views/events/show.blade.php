@extends('layouts.app')

@section('content')
    <h1>{{ $event->title }}</h1>
    <p><strong>Location:</strong> {{ $event->location }}</p>
    <p><strong>Date:</strong> {{ $event->event_date }}</p>
    <p><strong>Status:</strong> {{ $event->status }}</p>
    <p><strong>Organizer:</strong> {{ $event->user->name ?? 'N/A' }}</p>
    <p><strong>Description:</strong><br>{{ $event->description }}</p>
    <a href="{{ route('events.index') }}">← Back</a>
@endsection
