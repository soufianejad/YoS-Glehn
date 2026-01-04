@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('New Conversation') }}</h1>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('conversations.store') }}" method="post" id="create-conversation-form">
                @csrf
                <div class="form-group">
                    <label for="type">{{ __('Conversation Type') }}</label>
                    <select name="type" id="type" class="form-control">
                        <option value="private">{{ __('Private') }}</option>
                        <option value="group">{{ __('Group') }}</option>
                        <option value="school">{{ __('School') }}</option>
                    </select>
                </div>
                <div class="form-group" id="name-group" style="display: none;">
                    <label for="name">{{ __('Group Name') }}</label>
                    <input type="text" name="name" id="name" class="form-control">
                </div>
                <div class="form-group" id="participants-group">
                    <label for="participants">{{ __('Participants') }}</label>
                    <select name="participants[]" id="participants" class="form-control" multiple></select>
                </div>
                <div class="form-group" id="school-participants-group" style="display: none;">
                    <label for="school_participants">{{ __('Schools') }}</label>
                    <select name="school_participants[]" id="school_participants" class="form-control" multiple></select>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('Create Conversation') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const type = document.getElementById('type');
        const nameGroup = document.getElementById('name-group');
        const participantsGroup = document.getElementById('participants-group');
        const schoolParticipantsGroup = document.getElementById('school-participants-group');
        const participants = document.getElementById('participants');
        const school_participants = document.getElementById('school_participants');

        $('#participants').select2({
            ajax: {
                url: '/api/users-for-messaging',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            placeholder: 'Search for a user',
            minimumInputLength: 1,
        });

        $('#school_participants').select2({
            ajax: {
                url: '/api/schools-for-messaging',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            placeholder: 'Search for a school',
            minimumInputLength: 1,
        });

        type.addEventListener('change', function () {
            if (this.value === 'group') {
                nameGroup.style.display = 'block';
                participantsGroup.style.display = 'block';
                schoolParticipantsGroup.style.display = 'none';
            } else if (this.value === 'school') {
                nameGroup.style.display = 'block';
                participantsGroup.style.display = 'none';
                schoolParticipantsGroup.style.display = 'block';
            } else {
                nameGroup.style.display = 'none';
                participantsGroup.style.display = 'block';
                schoolParticipantsGroup.style.display = 'none';
            }
        });

        document.getElementById('create-conversation-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            data.participants = $('#participants').val();
            data.school_participants = $('#school_participants').val();


            fetch('{{ route('conversations.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                window.location.href = `/conversations/${data.id}`;
            });
        });
    });
</script>
@endpush
