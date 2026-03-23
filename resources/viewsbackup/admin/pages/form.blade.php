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
    <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $page->title ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="body" class="form-label">{{ __('Content') }}</label>
    <textarea class="form-control" id="body" name="body" rows="10" required>{{ old('body', $page->body ?? '') }}</textarea>
</div>

<div class="mb-3 form-check">
    <input type="checkbox" class="form-check-input" id="is_published" name="is_published" value="1" {{ old('is_published', $page->is_published ?? false) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_published">{{ __('Published') }}</label>
</div>

@push('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: 'textarea#body',
        plugins: 'advlist autolink lists link image charmap print preview anchor',
        toolbar_mode: 'floating',
    });
</script>
@endpush
