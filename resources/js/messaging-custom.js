$(function () {
    console.log('🔵 Messaging script initialized');

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    const messagingDataElement = document.getElementById('messaging-data');
    const MESSAGING_DATA = {
        base_url: messagingDataElement.dataset.base_url,
        user_id: messagingDataElement.dataset.user_id,
        routes: {
            store: messagingDataElement.dataset.route_store,
            messageable_users: messagingDataElement.dataset.route_messageable_users
        }
    };
    
    let currentConversationId = null;
    let pollingInterval = null;
    let lastMessageId = 0;
    let typingTimeout = null;

    // --- Main Functions ---

    function loadMessages(conversationId) {
        if (currentConversationId === conversationId) return;

        currentConversationId = conversationId;
        stopPolling();
        $('#messages-container').html('<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i></div>');
        $('#message-input, #send-button').prop('disabled', true);

        $.ajax({
            url: `${MESSAGING_DATA.base_url}/${conversationId}`,
            method: 'GET',
            success: function (response) {
                let messages = typeof response === 'string' ? JSON.parse(response) : response;
                
                $('#conversation-title').text($(`.conversation-item[data-conversation-id=${conversationId}] .conversation-name`).text() || 'Conversation');
                
                $('#messages-container').empty();
                lastMessageId = 0;
                
                if(Array.isArray(messages)) {
                    messages.forEach(appendMessage);
                }
                
                scrollToBottom();
                startPolling();
                $('#message-input, #send-button').prop('disabled', false);
            },
            error: (xhr) => console.error('Error loading messages:', xhr)
        });
    }

    function checkNewMessages() {
        if (!currentConversationId) return;

        $.ajax({
            url: `${MESSAGING_DATA.base_url}/${currentConversationId}/new`,
            method: 'GET',
            data: { after_id: lastMessageId },
            success: function (messages) {
                if (Array.isArray(messages) && messages.length > 0) {
                    messages.forEach(appendMessage);
                    scrollToBottom();
                }
            },
            error: (xhr) => console.error('Error checking for new messages:', xhr)
        });
    }

    function sendMessage() {
        const content = $('#message-input').val().trim();
        if (!content || !currentConversationId) return;

        $('#message-input').val('');
        stopTypingIndicator();

        $.ajax({
            url: MESSAGING_DATA.routes.store,
            method: 'POST',
            data: { conversation_id: currentConversationId, content: content },
            success: appendMessage,
            error: (xhr) => console.error('Error sending message:', xhr)
        });
    }
    
    function appendMessage(message) {
        if (!message || $(`#message-${message.id}`).length) return;
        
        lastMessageId = message.id;
        const isSender = message.sender_id == MESSAGING_DATA.user_id;
        const messageHtml = `
            <div class="message ${isSender ? 'sent' : 'received'}" id="message-${message.id}">
                <div class="message-bubble">
                    ${message.content}
                    <div class="text-end small mt-1 opacity-75">${new Date(message.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
                </div>
            </div>
        `;
        $('#messages-container').append(messageHtml);
        scrollToBottom();
    }
    
    function startPolling() {
        stopPolling();
        pollingInterval = setInterval(checkNewMessages, 3000);
    }

    function stopPolling() {
        clearInterval(pollingInterval);
    }
    
    function scrollToBottom() {
        $('#messages-container').scrollTop($('#messages-container')[0].scrollHeight);
    }

    // --- Typing Indicator ---
    
    function startTypingIndicator() {
        if (typingTimeout) clearTimeout(typingTimeout);
    }

    function stopTypingIndicator() {
    }

    // --- Event Handlers ---

    $(document).on('click', '.conversation-item', function() {
        $('.conversation-item.active').removeClass('active');
        $(this).addClass('active');
        loadMessages($(this).data('conversation-id'));
    });

    $('#message-form').on('submit', function(e) {
        e.preventDefault();
        sendMessage();
    });

    $('#message-input').on('keydown', function() {
        startTypingIndicator();
    });

    // Auto-select first conversation on page load
    if ($('.conversation-item').length > 0) {
        // Check for a conversation ID in the URL
        const urlParams = new URLSearchParams(window.location.search);
        const conversationIdFromUrl = urlParams.get('conversation');

        if (conversationIdFromUrl) {
            $(`.conversation-item[data-conversation-id="${conversationIdFromUrl}"]`).trigger('click');
        } else {
            $('.conversation-item').first().trigger('click');
        }
    }
    
    // --- Modal and Other Initializations ---

    $('#startConversationModal').on('show.bs.modal', function () {
        const recipientSelect = $('#recipient-select');
        recipientSelect.empty().append('<option value="">Loading users...</option>');
        $.ajax({
            url: MESSAGING_DATA.routes.messageable_users,
            method: 'GET',
            success: function (users) {
                recipientSelect.empty().append('<option value="">Select a user</option>');
                users.forEach(function (user) {
                    recipientSelect.append(`<option value="${user.id}">${user.full_name}</option>`);
                });
            },
            error: function (xhr) {
                console.error('Error loading users:', xhr);
                recipientSelect.empty().append('<option value="">Could not load users</option>');
            }
        });
    });
});