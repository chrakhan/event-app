<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    // Show all events
    public function index()
    {
        $events = Event::with('user')->latest()->get();
        return view('events.index', compact('events'));
    }

    // Show the create form
    public function create()
    {
        return view('events.create');
    }

    // Save new event to database
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'location'    => 'required|string|max:255',
            'event_date'  => 'required|date',
            'status'      => 'required|in:active,cancelled,completed',
        ]);

        Event::create([
            'title'       => $request->title,
            'description' => $request->description,
            'location'    => $request->location,
            'event_date'  => $request->event_date,
            'status'      => $request->status,
            'user_id'     => Auth::id(),
        ]);

        return redirect()->route('events.index')
            ->with('success', 'Event created!');
    }

    // Show one event
    public function show(Event $event)
    {
        return view('events.show', compact('event'));
    }

    // Show edit form
    public function edit(Event $event)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->id !== $event->user_id && !$user->hasRole('admin')) {
            abort(403, 'Not allowed.');
        }

        return view('events.edit', compact('event'));
    }

    // Save updated event
    public function update(Request $request, Event $event)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->id !== $event->user_id && !$user->hasRole('admin')) {
            abort(403, 'Not allowed.');
        }

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'location'    => 'required|string|max:255',
            'event_date'  => 'required|date',
            'status'      => 'required|in:active,cancelled,completed',
        ]);

        $event->update($request->only([
            'title',
            'description',
            'location',
            'event_date',
            'status'
        ]));

        return redirect()->route('events.index')
            ->with('success', 'Event updated!');
    }

    // Delete event
    public function destroy(Event $event)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->id !== $event->user_id && !$user->hasRole('admin')) {
            abort(403, 'Not allowed.');
        }

        $event->delete();

        return redirect()->route('events.index')
            ->with('success', 'Event deleted!');
    }
}
