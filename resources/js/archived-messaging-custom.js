$(function () {
    console.log('🔵 Archived Messaging script initialized');

    // CSRF Token Setup for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let currentConversationId = null;
    let isSubmitting = false;
    let pollingInterval = null;
    let lastMessageId = null;

    // Function to load messages for a conversation
    function loadMessages(conversationId, scrollToBottom = true) {
        console.log('📥 Loading messages for conversation:', conversationId);
        currentConversationId = conversationId;
        startPolling();

        $('#message-input').prop('disabled', false);
        $('#send-button').prop('disabled', false);
        $('#conversation-title').text('Loading...');

        $.ajax({
            url: `/messaging/${conversationId}`,
            method: 'GET',
            success: function (messages) {
                console.log('📨 Received messages:', messages.length);
                $('#conversation-title').text($(`.conversation-item[data-id=${conversationId}]`).text().trim());
                $('#messages-container').empty();
                lastMessageId = null;

                messages.forEach(function (message) {
                    appendMessage(message);
                    lastMessageId = message.id;
                });

                if (scrollToBottom) {
                    $('#messages-container').scrollTop($('#messages-container')[0].scrollHeight);
                }
            },
            error: function (xhr) {
                console.error('Error loading messages:', xhr);
                $('#conversation-title').text('Error loading conversation');
            }
        });
    }

    // Function to check for new messages
    function checkNewMessages() {
        if (!currentConversationId) return;
        $.ajax({
            url: `/messaging/${currentConversationId}/new`,
            method: 'GET',
            data: { after_id: lastMessageId || 0 },
            success: function (messages) {
                if (messages.length > 0) {
                    const container = $('#messages-container');
                    const isAtBottom = container[0].scrollHeight - container.scrollTop() <= container.outerHeight() + 100;
                    messages.forEach(function (message) {
                        appendMessage(message);
                        lastMessageId = message.id;
                    });
                    if (isAtBottom) {
                        container.scrollTop(container[0].scrollHeight);
                    }
                }
            },
            error: function (xhr) {
                console.error('❌ Error checking new messages:', xhr);
            }
        });
    }

    function startPolling() {
        stopPolling();
        pollingInterval = setInterval(checkNewMessages, 5000);
    }

    function stopPolling() {
        clearInterval(pollingInterval);
        pollingInterval = null;
    }

    function appendMessage(message) {
        if ($(`#message-${message.id}`).length > 0) return;
        const isSender = message.sender_id === {{ Auth::id() }};
        const messageClass = isSender ? 'direct-chat-msg right' : 'direct-chat-msg';
        const senderName = isSender ? 'You' : (message.sender ? message.sender.name : 'Unknown');
        const avatar = (message.sender && message.sender.avatar_url) ? message.sender.avatar_url : '{{ asset('assets/images/default-avatar.png') }}';
        const messageHtml = `
            <div class="${messageClass}" id="message-${message.id}">
                <div class="direct-chat-infos clearfix">
                    <span class="direct-chat-name ${isSender ? 'float-right' : 'float-left'}">${senderName}</span>
                    <span class="direct-chat-timestamp ${isSender ? 'float-left' : 'float-right'}">${new Date(message.created_at).toLocaleString()}</span>
                </div>
                <img class="direct-chat-img" src="${avatar}" alt="${JSON.stringify(__('message user image'))}">
                <div class="direct-chat-text">${message.content}</div>
            </div>`;
        $('#messages-container').append(messageHtml);
    }

    // --- Event Handlers ---

    $(document).on('click', '.conversation-item', function (e) {
        e.preventDefault();
        const conversationId = $(this).data('id');
        $('.conversation-list-item').removeClass('active');
        $(this).closest('.conversation-list-item').addClass('active');
        loadMessages(conversationId);
    });

    // Unarchive Conversation
    $(document).on('click', '.unarchive-conversation', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const conversationId = $(this).data('id');
        if (confirm(JSON.stringify('{{ __('Are you sure you want to unarchive this conversation?') }}'))) {
            $.ajax({
                url: `/messaging/${conversationId}/archive`,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', _method: 'PUT' }, // Use PUT for unarchive
                success: function(response) {
                    if (response.success) {
                        // Assuming conversationName is defined or retrieved correctly
                        alert(JSON.stringify('{{ __('Conversation unarchived and moved back to your inbox.') }}'));
                        window.location.reload(); // Simple reload for now
                    }
                },
                error: function(xhr) {
                    alert(JSON.stringify('{{ __('Error unarchiving conversation.') }}'));
                    console.error(xhr);
                }
            });
        }
    });

    // Delete Conversation
    $(document).on('click', '.delete-conversation', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const conversationId = $(this).data('id');
        if (confirm(JSON.stringify('{{ __('Are you sure you want to permanently delete this conversation?') }}'))) {
            $.ajax({
                url: `/messaging/${conversationId}`,
                method: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        $(`.conversation-list-item[data-conversation-id="${conversationId}"]`).remove();
                        // Clear message container if the deleted conversation was active
                        if (currentConversationId === conversationId) {
                            $('#messages-container').empty();
                            $('#conversation-title').text(JSON.stringify('{{ __('Select a conversation') }}'));
                            $('#message-input').prop('disabled', true);
                            $('#send-button').prop('disabled', true);
                            stopPolling();
                        }
                    }
                },
                error: function(xhr) {
                    alert(JSON.stringify('{{ __('Error deleting conversation.') }}'));
                    console.error(xhr);
                }
            });
        }
    });

    $('#message-form').off('submit').on('submit', function (e) {
        e.preventDefault();
        if (isSubmitting) return;
        const messageContent = $('#message-input').val();
        if (!messageContent.trim() || !currentConversationId) return;

        isSubmitting = true;
        $('#send-button').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        $('#message-input').prop('disabled', true);

        $.ajax({
            url: `/messaging`,
            method: 'POST',
            data: { conversation_id: currentConversationId, content: messageContent },
            success: function (message) {
                appendMessage(message);
                $('#messages-container').scrollTop($('#messages-container')[0].scrollHeight);
                $('#message-input').val('');
            },
            error: function (xhr) {
                console.error('Error sending message:', xhr);
                alert(JSON.stringify('{{ __('Error sending message.') }}'));
            },
            complete: function () {
                isSubmitting = false;
                $('#send-button').prop('disabled', false).text(JSON.stringify('{{ __('Send') }}'));
                $('#message-input').prop('disabled', false).focus();
            }
        });
    });

    $(window).on('beforeunload', stopPolling);
});