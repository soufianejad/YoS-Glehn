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
        overflow: hidden;
        position: relative;
    }

    /* Sidebar (Liste des conversations) */
    .conversations-sidebar {
        width: 350px;
        border-right: 1px solid #e3e6f0;
        display: flex;
        flex-direction: column;
        background: #fff;
        z-index: 2;
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
        transition: background 0.2s;
    }

    .conversation-item:hover, .conversation-item.active {
        background-color: #f8f9fc;
    }

    /* Fenêtre de Chat */
    .chat-main {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        background: #fff;
        z-index: 1;
    }

    .chat-header {
        padding: 1rem;
        border-bottom: 1px solid #e3e6f0;
        display: flex;
        align-items: center;
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
        max-width: 85%;
        word-wrap: break-word;
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

    /* RESPONSIVE DESIGN */
    @media (max-width: 768px) {
        .messaging-wrapper {
            height: 85vh; /* Un peu plus grand sur mobile */
        }

        .conversations-sidebar {
            width: 100%;
            position: absolute;
            height: 100%;
            left: 0;
            top: 0;
            transition: transform 0.3s ease;
        }

        .chat-main {
            width: 100%;
            position: absolute;
            height: 100%;
            left: 0;
            top: 0;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }

        /* Classes pour basculer l'affichage sur mobile */
        .messaging-wrapper.show-chat .conversations-sidebar {
            transform: translateX(-100%);
        }

        .messaging-wrapper.show-chat .chat-main {
            transform: translateX(0);
        }
    }

    /* Bouton Retour (caché sur desktop) */
    .btn-back {
        display: none;
        margin-right: 1rem;
    }

    @media (max-width: 768px) {
        .btn-back {
            display: inline-block;
        }
    }
</style>
@endpush

@section('content')
<div class="messaging-wrapper" id="messaging-container">
    <!-- Conversations Sidebar -->
    <div class="conversations-sidebar">
        <div class="conversations-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">{{ __('Conversations') }}</h4>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#startConversationModal">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <div class="mt-3">
                <input type="text" class="form-control" placeholder="{{ __('Search conversations...') }}">
            </div>
        </div>
        <div class="conversations-list" id="conversations-list">
            @forelse($conversations as $conversation)
                @php
                    $participant = $conversation->participants->first();
                    $conversationName = $conversation->name ?? ($participant->name ?? 'Conversation');
                    $lastMessage = $conversation->latestMessage;
                @endphp
                <div class="conversation-item @if($activeConversation && $activeConversation->id === $conversation->id) active @endif" 
                     data-conversation-id="{{ $conversation->id }}" 
                     onclick="openMobileChat()">
                    <img src="{{ $participant->avatar_url ?? asset('images/default-avatar.png') }}" alt="avatar" class="rounded-circle me-3" style="width: 45px; height: 45px; object-fit: cover;">
                    <div class="w-100 overflow-hidden">
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-0 text-truncate">{{ $conversationName }}</h6>
                            <small class="text-muted">{{ $lastMessage ? $lastMessage->created_at->diffForHumans(null, true) : '' }}</small>
                        </div>
                        <p class="mb-0 text-muted text-truncate" style="font-size: 0.85rem;">
                            @if($lastMessage)
                                @if($lastMessage->sender_id === Auth::id()) You: @endif {{ $lastMessage->content }}
                            @else
                                No messages yet.
                            @endif
                        </p>
                    </div>
                </div>
            @empty
                <p class="p-3 text-muted">{{ __('No conversations yet.') }}</p>
            @endforelse
        </div>
    </div>

    <!-- Main Chat Area -->
    <div class="chat-main">
        <div class="chat-header">
            <!-- Bouton Retour pour Mobile -->
            <button class="btn btn-light btn-sm btn-back" onclick="closeMobileChat()">
                <i class="fas fa-arrow-left"></i>
            </button>
            
            <h5 class="mb-0 text-truncate" id="conversation-title">
                @if ($recipient && !$activeConversation)
                    New Message: {{ $recipient->name }}
                @elseif ($activeConversation)
                    {{ $activeConversation->name ?? $activeConversation->participants->where('id', '!=', Auth::id())->first()->name ?? 'Conversation' }}
                @else
                    Select a conversation
                @endif
            </h5>
        </div>

        <div class="chat-messages" id="messages-container">
            @if ($recipient && !$activeConversation)
                <div class="h-100 d-flex align-items-center justify-content-center">
                    <p class="text-muted">{{ __('Start the conversation below.') }}</p>
                </div>
            @elseif (!$activeConversation)
                <div class="h-100 d-flex align-items-center justify-content-center">
                    <p class="text-muted">{{ __('Select a conversation to start chatting.') }}</p>
                </div>
            @endif
        </div>

        <div class="chat-footer">
            @if ($recipient && !$activeConversation)
                <form action="{{ route('messaging.start.post') }}" method="post">
                    @csrf
                    <input type="hidden" name="recipient_ids[]" value="{{ $recipient->id }}">
                    <div class="input-group">
                        <input type="text" name="content" placeholder="{{ __('Your message...') }}" class="form-control" required>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i></button>
                    </div>
                </form>
            @else
                <form action="{{ route('messaging.store') }}" method="post" id="message-form">
                    @csrf
                    <input type="hidden" name="conversation_id" id="conversation_id" value="{{ $activeConversation->id ?? '' }}">
                    <div class="input-group">
                        <input type="text" name="content" placeholder="{{ __('Type Message ...') }}" class="form-control" id="message-input">
                        <button type="submit" class="btn btn-primary" id="send-button">
                            <span id="send-button-text"><i class="fas fa-paper-plane"></i></span>
                            <span id="send-button-spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

<!-- Modal reste identique -->
<div class="modal fade" id="startConversationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Start New Conversation') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <form action="{{ route('messaging.start.post') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Conversation Name (optional)') }}</label>
                        <input type="text" class="form-control" name="name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Select User(s)') }}</label>
                        <select class="form-control" name="recipient_ids[]" required multiple style="height: 120px;">
                            <!-- AJAX content -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Message') }}</label>
                        <textarea class="form-control" name="content" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    {{ __('Start') }}</button>
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

    <script>
        // Fonctions pour gérer l'affichage mobile
        function openMobileChat() {
            if (window.innerWidth <= 768) {
                document.getElementById('messaging-container').classList.add('show-chat');
            }
        }

        function closeMobileChat() {
            document.getElementById('messaging-container').classList.remove('show-chat');
        }

        // Si on arrive sur la page avec une conversation active déjà sélectionnée (PHP)
        document.addEventListener('DOMContentLoaded', function() {
            @if($activeConversation || $recipient)
                if (window.innerWidth <= 768) {
                    openMobileChat();
                }
            @endif
        });
    </script>
@endpush