@extends('layouts.app')

@section('content')
<div class="container">
    <h1>
        @if($conversation->type == 'private')
            Conversation with {{ $conversation->users->where('id', '!=', auth()->id())->first()->first_name }} {{ $conversation->users->where('id', '!=', auth()->id())->first()->last_name }}
        @else
            {{ $conversation->name }}
        @endif
    </h1>
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Messages') }}</div>
                <div class="card-body" id="messages" style="height: 400px; overflow-y: scroll;">
                    @foreach($messages as $message)
                        <div class="message @if($message->sender_id == auth()->id()) text-right @endif">
                            <strong>{{ $message->sender->first_name }} {{ $message->sender->last_name }}</strong>
                            <p>{{ $message->content }}</p>
                            <small class="text-muted">{{ $message->created_at->format('d M g:i a') }}</small>
                        </div>
                        <hr>
                    @endforeach
                </div>
                <div class="card-footer">
                    <form action="{{ route('conversations.messages.store', $conversation) }}" method="post" id="message-form">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="content" placeholder="{{ __('Type Message ...') }}" class="form-control" id="message-input">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">{{ __('Send') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @if($conversation->type != 'private')
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">{{ __('Participants') }}</div>
                    <div class="card-body">
                        <ul>
                            @foreach($conversation->users as $user)
                                <li>
                                    {{ $user->first_name }} {{ $user->last_name }}
                                    <form action="{{ route('conversations.participants.remove', $conversation) }}" method="post" class="float-right">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                                        <button type="submit" class="btn btn-danger btn-sm">{{ __('Remove') }}</button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="card-footer">
                        <form action="{{ route('conversations.participants.add', $conversation) }}" method="post">
                            @csrf
                            <div class="input-group">
                                <select name="user_id" class="form-control" id="add-participant-select"></select>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">Add</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const messages = document.getElementById('messages');
        messages.scrollTop = messages.scrollHeight;

        const messageForm = document.getElementById('message-form');
        const messageInput = document.getElementById('message-input');

        messageForm.addEventListener('submit', function (e) {
            e.preventDefault();

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    content: messageInput.value
                })
            })
            .then(response => response.json())
            .then(data => {
                messageInput.value = '';
                const messageElement = document.createElement('div');
                messageElement.classList.add('message');
                messageElement.innerHTML = `
                    <strong>${data.sender.first_name} ${data.sender.last_name}</strong>
                    <p>${data.content}</p>
                    <small class="text-muted">${new Date().toLocaleTimeString()}</small>
                    <hr>
                `;
                messages.appendChild(messageElement);
                messages.scrollTop = messages.scrollHeight;
            });
        });

        $('#add-participant-select').select2({
            ajax: {
                url: '{{ route('messaging.users') }}',
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
            placeholder: 'Search for a user to add',
            minimumInputLength: 1,
        });
    });
</script>
@endpush
