<label>Title</label>
<input type="text" name="title" value="{{ old('title', $event->title ?? '') }}">

<label>Description</label>
<textarea name="description" rows="4">
{{ old('description', $event->description ?? '') }}</textarea>

<label>Location</label>
<input type="text" name="location" value="{{ old('location', $event->location ?? '') }}">

<label>Event Date</label>
<input type="date" name="event_date" value="{{ old('event_date', $event->event_date ?? '') }}">

<label>Status</label>
<select name="status">
    @foreach (['active', 'cancelled', 'completed'] as $s)
        <option value="{{ $s }}" {{ old('status', $event->status ?? '') == $s ? 'selected' : '' }}>
            {{ ucfirst($s) }}
        </option>
    @endforeach
</select>
