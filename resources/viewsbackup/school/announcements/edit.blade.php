@extends('layouts.school')

@section('content')
<div class="container">
    <h1>{{ __('Edit Announcement') }}</h1>

    <form action="{{ route('school.announcements.update', $announcement) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="title" class="form-label">{{ __('Title') }}</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $announcement->title) }}" required>
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">{{ __('Content') }}</label>
            <textarea class="form-control" id="content" name="content" rows="5" required>{{ old('content', $announcement->content) }}</textarea>
        </div>
        <div class="mb-3">
            <label for="class_id" class="form-label">{{ __('Target Class (Optional)') }}</label>
            <select class="form-control" id="class_id" name="class_id">
                <option value="">{{ __('All School Classes') }}</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}" {{ old('class_id', $announcement->class_id) == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                @endforeach
            </select>
            <small class="form-text text-muted">{{ __('Leave blank for a school-wide announcement.') }}</small>
        </div>
        <div class="mb-3">
            <label for="published_at" class="form-label">{{ __('Publish Date (Optional)') }}</label>
            <input type="datetime-local" class="form-control" id="published_at" name="published_at" value="{{ old('published_at', $announcement->published_at ? $announcement->published_at->format('Y-m-d\TH:i') : '') }}">
        </div>
        <div class="mb-3">
            <label for="expires_at" class="form-label">{{ __('Expiry Date (Optional)') }}</label>
            <input type="datetime-local" class="form-control" id="expires_at" name="expires_at" value="{{ old('expires_at', $announcement->expires_at ? $announcement->expires_at->format('Y-m-d\TH:i') : '') }}">
        </div>
        <button type="submit" class="btn btn-primary">{{ __('Update Announcement') }}</button>
        <a href="{{ route('school.announcements.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
    </form>
</div>
@endsection
