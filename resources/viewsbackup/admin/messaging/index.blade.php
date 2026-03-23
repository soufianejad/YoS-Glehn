@extends('layouts.dashboard')

@section('title', 'Admin Messaging')

@push('styles')
<style>
    .messaging-wrapper {
        display: flex;
        height: 80vh;
        background: #fff;
        border-radius: 0.5rem;
        box-shadow: 0 0 1.5rem rgba(0,0,0,0.1);
    }
    .conversations-sidebar {
        width: 350px;
        border-right: 1px solid #e3e6f0;
        display: flex;
        flex-direction: column;
    }
    .conversations-header {
        padding: 1rem;
        border-bottom: 1px solid #e3e6f0;
    }
    .conversations-list {
        flex-grow: 1;
        overflow-y: auto;
    }
    .conversation-item {
        display: flex;
        align-items: center;
        padding: 1rem;
        cursor: pointer;
        border-bottom: 1px solid #f8f9fa;
    }
    .conversation-item:hover, .conversation-item.active {
        background-color: #f8f9fc;
    }
    .chat-main {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    .chat-header {
        padding: 1rem;
        border-bottom: 1px solid #e3e6f0;
    }
    .chat-messages {
        flex-grow: 1;
        padding: 1rem;
        overflow-y: auto;
        background-color: #f8f9fa;
    }
    .message {
        display: flex;
        margin-bottom: 1rem;
    }
    .message.sent {
        justify-content: flex-end;
    }
    .message-bubble {
        padding: 0.75rem 1rem;
        border-radius: 1.25rem;
        max-width: 70%;
    }
    .message.sent .message-bubble {
        background-color: #0d6efd;
        color: white;
        border-top-right-radius: 0.25rem;
    }
    .message.received .message-bubble {
        background-color: #e9ecef;
        border-top-left-radius: 0.25rem;
    }
    .chat-footer {
        padding: 1rem;
        border-top: 1px solid #e3e6f0;
    }
</style>
@endpush

@section('content')
<div class="messaging-wrapper">
    <!-- Conversations Sidebar -->
    <div class="conversations-sidebar">
        <div class="conversations-header">
            <h4 class="mb-0">Conversations</h4>
            <div class="mt-3">
                <input type="text" class="form-control" placeholder="Search conversations...">
            </div>
        </div>
        <div class="conversations-list" id="conversations-list"
             data-base_url="{{ url('api/admin/messaging') }}"
             data-user_id="{{ Auth::id() }}">
            @forelse($conversations as $conversation)
                @php
                    $participant = $conversation->participants->where('id', '!=', Auth::id())->first();
                    $conversationName = $conversation->name ?? ($participant->name ?? 'New Conversation');
                    $lastMessage = $conversation->latestMessage;
                @endphp
                <div class="conversation-item" data-conversation-id="{{ $conversation->id }}">
                    <img src="{{ $participant->avatar_url }}" alt="avatar" class="rounded-circle me-3" style="width: 50px; height: 50px;">
                    <div class="w-100">
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-0 conversation-name">{{ $conversationName }}</h6>
                            <small class="text-muted">{{ $lastMessage ? $lastMessage->created_at->diffForHumans(null, true) : '' }}</small>
                        </div>
                        <p class="mb-0 text-muted text-truncate" style="font-size: 0.9rem;">
                            @if($lastMessage)
                                @if($lastMessage->sender_id === Auth::id())
                                    You:
                                @endif
                                {{ $lastMessage->content }}
                            @else
                                No messages yet.
                            @endif
                        </p>
                    </div>
                </div>
            @empty
                <p class="p-3 text-muted">No conversations yet.</p>
            @endforelse
        </div>
    </div>

    <!-- Main Chat Area -->
    <div class="chat-main">
        <div class="chat-header">
            <h5 class="mb-0" id="conversation-title">Select a conversation</h5>
        </div>
        <div class="chat-messages" id="messages-container">
            <!-- Messages will be loaded here -->
        </div>
        <div class="chat-footer">
            <form action="#" method="post" id="message-form">
                <div class="input-group">
                    <input type="text" name="message" placeholder="Type Message ..." class="form-control" id="message-input" disabled>
                    <button type="submit" class="btn btn-primary" id="send-button" disabled><i class="fas fa-paper-plane"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin-messaging-custom.js') }}"></script>
@endpush