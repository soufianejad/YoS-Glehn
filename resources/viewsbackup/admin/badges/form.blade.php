@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="mb-3">
    <label for="name" class="form-label">{{ __('Badge Name') }}</label>
    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $badge->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="description" class="form-label">{{ __('Description') }}</label>
    <textarea class="form-control" id="description" name="description" rows="3" required>{{ old('description', $badge->description ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label for="points" class="form-label">{{ __('Points') }}</label>
    <input type="number" class="form-control" id="points" name="points" value="{{ old('points', $badge->points ?? 0) }}" required>
</div>

<div class="mb-3">
    <label for="books_required" class="form-label">{{ __('Books Required') }}</label>
    <input type="number" class="form-control" id="books_required" name="books_required" value="{{ old('books_required', $badge->books_required ?? 0) }}">
</div>

<div class="mb-3">
    <label for="minutes_required" class="form-label">{{ __('Minutes Required') }}</label>
    <input type="number" class="form-control" id="minutes_required" name="minutes_required" value="{{ old('minutes_required', $badge->minutes_required ?? 0) }}">
</div>

<div class="mb-3">
    <label for="quizzes_required" class="form-label">{{ __('Quizzes Required') }}</label>
    <input type="number" class="form-control" id="quizzes_required" name="quizzes_required" value="{{ old('quizzes_required', $badge->quizzes_required ?? 0) }}">
</div>

<div class="mb-3">
    <label for="icon" class="form-label">{{ __('Icon') }}</label>
    <input type="file" class="form-control" id="icon" name="icon" accept="image/*">
    @if (isset($badge) && $badge->icon)
        <div class="mt-2">
            <small>{{ __('Current Icon:') }}</small><br>
            <img src="{{ asset('storage/' . $badge->icon) }}" alt="{{ $badge->name }}" width="100">
        </div>
    @endif
</div>
