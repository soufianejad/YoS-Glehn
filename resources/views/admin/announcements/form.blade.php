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
    <label for="title" class="form-label">{{ __('Title') }}</label>
    <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $announcement->title ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="content" class="form-label">{{ __('Content') }}</label>
    <textarea class="form-control" id="content" name="content" rows="5" required>{{ old('content', $announcement->content ?? '') }}</textarea>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="published_at" class="form-label">{{ __('Published At (Optional)') }}</label>
            <input type="datetime-local" class="form-control" id="published_at" name="published_at" value="{{ old('published_at', isset($announcement) ? ($announcement->published_at ? $announcement->published_at->format('Y-m-d\TH:i') : '') : '') }}">
            <small class="form-text text-muted">{{ __("Leave blank to publish immediately.") }}</small>
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="expires_at" class="form-label">{{ __('Expires At (Optional)') }}</label>
            <input type="datetime-local" class="form-control" id="expires_at" name="expires_at" value="{{ old('expires_at', isset($announcement) ? ($announcement->expires_at ? $announcement->expires_at->format('Y-m-d\TH:i') : '') : '') }}">
            <small class="form-text text-muted">{{ __("Leave blank for the announcement to never expire.") }}</small>
        </div>
    </div>
</div>
