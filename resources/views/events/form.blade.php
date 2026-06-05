<div class="form-group" style="margin-bottom:1rem">
    <label class="label" for="title">Title</label>
    <input id="title" class="input" type="text" name="title" value="{{ old('title', $event->title ?? '') }}" required>
</div>

<div class="form-group" style="margin-bottom:1rem">
    <label class="label" for="description">Description</label>
    <textarea id="description" class="textarea" name="description" rows="4">{{ old('description', $event->description ?? '') }}</textarea>
</div>

<div class="form-group" style="margin-bottom:1rem">
    <label class="label" for="location">Location</label>
    <input id="location" class="input" type="text" name="location" value="{{ old('location', $event->location ?? '') }}" required>
</div>

<div class="form-row form-row-2" style="margin-bottom:1rem">
    <div class="form-group">
        <label class="label" for="event_date">Date</label>
        <input id="event_date" class="input" type="date" name="event_date" value="{{ old('event_date', isset($event->event_date) ? $event->event_date->format('Y-m-d') : '') }}" required>
    </div>
    <div class="form-group">
        <label class="label" for="start_time">Start Time</label>
        <input id="start_time" class="input" type="time" name="start_time" value="{{ old('start_time', isset($event->start_time) ? substr($event->start_time, 0, 5) : '') }}">
    </div>
</div>

<div class="form-row form-row-2" style="margin-bottom:1rem">
    <div class="form-group">
        <label class="label" for="status">Status</label>
        <select id="status" class="select" name="status">
            @foreach (['active', 'cancelled', 'completed'] as $s)
                <option value="{{ $s }}" {{ old('status', $event->status ?? 'active') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label class="label" for="color">Color</label>
        <select id="color" class="select" name="color">
            @foreach (['blue', 'green', 'purple', 'orange', 'pink', 'red'] as $c)
                <option value="{{ $c }}" {{ old('color', $event->color ?? 'blue') === $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>
            @endforeach
        </select>
    </div>
</div>
