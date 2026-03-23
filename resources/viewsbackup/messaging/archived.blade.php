@extends('layouts.dashboard')

@section('title', 'Archived Messages')

@section('content_header')
    <h1>{{ __('Archived Messages') }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Archived Conversations') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('messaging.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-inbox"></i> {{ __('Back to Inbox') }}
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-pills flex-column" id="conversations-list">
                        @forelse($conversations as $conversation)
                            @php
                                $conversationName = $conversation->name ?? $conversation->participants->where('id', '!=', Auth::id())->first()->name ?? 'New Conversation';
                            @endphp
                            <li class="nav-item conversation-list-item" data-conversation-id="{{ $conversation->id }}">
                                <a href="#" class="nav-link conversation-item" data-id="{{ $conversation->id }}">
                                    <i class="fas fa-archive"></i> {{ $conversationName }}
                                </a>
                                <div class="conversation-options dropdown">
                                    <button class="btn btn-sm btn-link text-secondary" type="button" id="dropdownMenuButton{{ $conversation->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton{{ $conversation->id }}">
                                        <li><a class="dropdown-item unarchive-conversation" href="#" data-id="{{ $conversation->id }}">{{ __('Unarchive') }}</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item delete-conversation text-danger" href="#" data-id="{{ $conversation->id }}">{{ __('Delete') }}</a></li>
                                    </ul>
                                </div>
                            </li>
                        @empty
                            <li class="nav-item">
                                <span class="nav-link text-muted">{{ __('No archived conversations.') }}</span>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card direct-chat direct-chat-primary">
                <div class="card-header">
                    <h3 class="card-title" id="conversation-title">{{ __('Select a conversation') }}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="direct-chat-messages" id="messages-container" style="height: 400px; overflow-y: scroll;">
                        <!-- Messages will be loaded here via AJAX -->
                    </div>
                </div>
                <div class="card-footer">
                    <form action="#" method="post" id="message-form">
                        <div class="input-group">
                            <input type="text" name="message" placeholder="{{ __('Type Message ...') }}" class="form-control" id="message-input" disabled>
                            <span class="input-group-append">
                                <button type="submit" class="btn btn-primary" id="send-button" disabled>{{ __('Send') }}</button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@push('scripts')
    <script src="{{ asset('js/archived-messaging-custom.js') }}"></script>
@endpush