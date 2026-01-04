@extends('layouts.dashboard')

@section('title', 'Messaging')

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
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Conversations</h4>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#startConversationModal">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <div class="mt-3">
                <input type="text" class="form-control" placeholder="Search conversations...">
            </div>
        </div>
        <div class="conversations-list" id="conversations-list">
            @forelse($conversations as $conversation)
                @php
                    // The controller now pre-filters the participants list to exclude the current user.
                    $participant = $conversation->participants->first();
                    $conversationName = $conversation->name ?? ($participant->name ?? 'Conversation');
                    $lastMessage = $conversation->latestMessage;
                @endphp
                <div class="conversation-item @if($activeConversation && $activeConversation->id === $conversation->id) active @endif" data-conversation-id="{{ $conversation->id }}">
                    <img src="{{ $participant->avatar_url ?? asset('images/default-avatar.png') }}" alt="avatar" class="rounded-circle me-3" style="width: 50px; height: 50px;">
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
        @if ($recipient && !$activeConversation)
            {{-- This displays when a recipient is passed AND no prior conversation exists --}}
            <div class="chat-header">
                <h5 class="mb-0">New Message to: {{ $recipient->name }}</h5>
            </div>
            <div class="chat-messages" style="display: flex; align-items: center; justify-content: center; background-color: #f8f9fa;">
                <p class="text-muted">Start the conversation below.</p>
            </div>
            <div class="chat-footer">
                <form action="{{ route('messaging.start.post') }}" method="post">
                    @csrf
                    <input type="hidden" name="recipient_ids[]" value="{{ $recipient->id }}">
                    <div class="input-group">
                        <input type="text" name="content" placeholder="Your message to {{ $recipient->first_name }}..." class="form-control" required>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Send</button>
                    </div>
                </form>
            </div>
        @else
            {{-- This is the default view for showing an active conversation or a placeholder --}}
            <div class="chat-header">
                 <h5 class="mb-0" id="conversation-title">
                    @if ($activeConversation)
                        {{ $activeConversation->name ?? $activeConversation->participants->where('id', '!=', Auth::id())->first()->name ?? 'Conversation' }}
                    @else
                        Select a conversation
                    @endif
                </h5>
            </div>
            <div class="chat-messages" id="messages-container">
                 @if (!$activeConversation && !$recipient)
                    <div style="display: flex; height: 100%; align-items: center; justify-content: center;">
                        <p class="text-muted">Select a conversation to start chatting.</p>
                    </div>
                 @endif
            </div>
            <div class="chat-footer">
                <form action="{{ route('messaging.store') }}" method="post" id="message-form">
                    @csrf
                    <input type="hidden" name="conversation_id" id="conversation_id" value="{{ $activeConversation->id ?? '' }}">
                    <div class="input-group">
                        <input type="text" name="content" placeholder="Type Message ..." class="form-control" id="message-input">
                        <button type="submit" class="btn btn-primary" id="send-button">
                            <span id="send-button-text"><i class="fas fa-paper-plane"></i> Send</span>
                            <span id="send-button-spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>

<!-- Modal for starting new conversation -->
<div class="modal fade" id="startConversationModal" tabindex="-1" role="dialog" aria-labelledby="startConversationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="startConversationModalLabel">{{ __('Start New Conversation') }}</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('messaging.start.post') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="conversation-name" class="form-label">Conversation Name (optional, for groups)</label>
                        <input type="text" class="form-control" id="conversation-name" name="name" placeholder="{{ __('E.g., Classe CE1 Project') }}">
                    </div>
                    <div class="form-group mb-3">
                        <label for="recipient-select" class="form-label">Select User(s)</label>
                        <select class="form-control" id="recipient-select" name="recipient_ids[]" required multiple style="height: 150px;">
                            <!-- Users will be loaded here via AJAX -->
                        </select>
                        <small class="form-text text-muted">Hold Ctrl (or Cmd on Mac) to select multiple users for a group chat.</small>
                    </div>
                    <div class="form-group">
                        <label for="first-message-content" class="form-label">{{ __('Message') }}</label>
                        <textarea class="form-control" id="first-message-content" name="content" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Start Conversation') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script id="messaging-data"
        data-base_url="{{ url('messaging') }}"
        data-user_id="{{ Auth::id() }}"
        data-route_store="{{ route('messaging.store') }}"
        data-route_messageable_users="{{ route('messaging.users.messageable') }}"
    ></script>
    <script src="{{ asset('js/messaging-custom.js') }}"></script>
@endpush
