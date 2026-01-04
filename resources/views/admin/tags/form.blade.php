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
    <label for="name" class="form-label">{{ __('Tag Name') }}</label>
    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $tag->name ?? '') }}" required>
</div>
