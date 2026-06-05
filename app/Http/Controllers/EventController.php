<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    private function shape(Event $event): array
    {
        return [
            'id'          => $event->id,
            'title'       => $event->title,
            'description' => $event->description,
            'location'    => $event->location,
            'date'        => $event->event_date->format('Y-m-d'),
            'start_time'  => $event->start_time,
            'status'      => $event->status,
            'color'       => $event->color ?? 'blue',
            'user_id'     => $event->user_id,
            'user'        => ['name' => $event->user->name ?? 'Unknown'],
        ];
    }

    public function index()
    {
        $events = Event::with('user')->latest('event_date')->get()->map(fn($e) => $this->shape($e));
        return view('events.index', compact('events'));
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'location'    => 'required|string|max:255',
            'event_date'  => 'required|date',
            'start_time'  => 'nullable|date_format:H:i,H:i:s',
            'status'      => 'required|in:active,cancelled,completed',
            'color'       => 'nullable|in:blue,green,purple,orange,pink,red',
        ]);

        $event = Event::create([
            ...$data,
            'user_id' => Auth::id(),
            'color'   => $data['color'] ?? 'blue',
        ]);
        $event->load('user');

        if ($request->expectsJson()) {
            return response()->json($this->shape($event), 201);
        }

        return redirect()->route('events.index')->with('success', 'Event created!');
    }

    public function show(Event $event)
    {
        return view('events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->id !== $event->user_id && !$user->hasRole('admin')) {
            abort(403);
        }
        return view('events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->id !== $event->user_id && !$user->hasRole('admin')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Not allowed.'], 403);
            }
            abort(403);
        }

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'location'    => 'required|string|max:255',
            'event_date'  => 'required|date',
            'start_time'  => 'nullable|date_format:H:i,H:i:s',
            'status'      => 'required|in:active,cancelled,completed',
            'color'       => 'nullable|in:blue,green,purple,orange,pink,red',
        ]);

        $event->update($data);
        $event->load('user');

        if ($request->expectsJson()) {
            return response()->json($this->shape($event));
        }

        return redirect()->route('events.index')->with('success', 'Event updated!');
    }

    public function destroy(Request $request, Event $event)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->id !== $event->user_id && !$user->hasRole('admin')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Not allowed.'], 403);
            }
            abort(403);
        }

        $event->delete();

        if ($request->expectsJson()) {
            return response()->json(['deleted' => true]);
        }

        return redirect()->route('events.index')->with('success', 'Event deleted!');
    }
}
