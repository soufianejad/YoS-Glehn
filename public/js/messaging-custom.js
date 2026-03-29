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
            messageable_users: messagingDataElement.dataset.route_messageable_users,
            search: messagingDataElement.dataset.route_search
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
    
        // Cache elements for efficiency and clarity
        const messageInput = $('#message-input');
        const sendButton = $('#send-button');
        const sendButtonText = $('#send-button-text');
        const sendButtonSpinner = $('#send-button-spinner');
    
        // Disable input and button, show spinner
        messageInput.prop('disabled', true);
        sendButton.prop('disabled', true);
        sendButtonText.hide();
        sendButtonSpinner.show();
    
        messageInput.val('');
        stopTypingIndicator();
    
        $.ajax({
            url: MESSAGING_DATA.routes.store,
            method: 'POST',
            data: { conversation_id: currentConversationId, content: content },
            success: function(response) {
                appendMessage(response); // Assuming response is the message object
            },
            error: function(xhr) {
                console.error('Error sending message:', xhr);
                // Optionally show an error message to the user, e.g., using Toastr
                // toastr.error('Failed to send message.');
            },
            complete: function() {
                // Re-enable input and button, hide spinner regardless of success or error
                messageInput.prop('disabled', false);
                sendButton.prop('disabled', false);
                sendButtonText.show();
                sendButtonSpinner.hide();
                // Refocus input if needed
                messageInput.focus();
            }
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
        const container = $('#messages-container');
        if (container.length) {
            container.scrollTop(container[0].scrollHeight);
        }
    }

    // --- Typing Indicator ---
    
    function startTypingIndicator() {
        if (typingTimeout) clearTimeout(typingTimeout);
    }

    function stopTypingIndicator() {
    }

    function escapeHtml(unsafe) {
        return (unsafe || '').toString()
             .replace(/&/g, "&amp;")
             .replace(/</g, "&lt;")
             .replace(/>/g, "&gt;")
             .replace(/"/g, "&quot;")
             .replace(/'/g, "&#039;");
    }

    function performSearch(query) {
        if (!query) {
            $('#conversations-list').removeClass('d-none');
            $('#search-results-container').addClass('d-none');
            return;
        }

        $('#conversations-list').addClass('d-none');
        $('#search-results-container').removeClass('d-none');
        $('#search-results-list').html('<div class="text-center p-3"><i class="fas fa-spinner fa-spin text-primary"></i></div>');

        $.ajax({
            url: MESSAGING_DATA.routes.search,
            method: 'GET',
            data: { q: query },
            success: function(results) {
                let html = '';
                if (results.length === 0) {
                    html = '<p class="p-3 text-muted text-center">No messages found.</p>';
                } else {
                    results.forEach(group => {
                        html += `
                            <div class="search-result-group border-bottom p-2">
                                <h6 class="mb-1 text-primary"><i class="fas fa-comments me-2"></i>${escapeHtml(group.conversation_name)}</h6>
                        `;
                        group.messages.forEach(msg => {
                            html += `
                                <div class="search-result-item p-2 mb-1 rounded bg-light cursor-pointer" data-conversation-id="${group.conversation_id}" onclick="openMobileChat()">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="fw-bold">${escapeHtml(msg.sender_name)}</small>
                                        <small class="text-muted">${escapeHtml(msg.created_at)}</small>
                                    </div>
                                    <div class="text-truncate small">${escapeHtml(msg.content)}</div>
                                </div>
                            `;
                        });
                        html += `</div>`;
                    });
                }
                $('#search-results-list').html(html);
            },
            error: function(xhr) {
                console.error('Search error:', xhr);
                $('#search-results-list').html('<p class="text-danger p-3 text-center">Error occurred during search.</p>');
            }
        });
    }

    // --- Event Handlers ---

    $(document).on('click', '.search-result-item', function() {
        const conversationId = $(this).data('conversation-id');

        // Find or create the conversation item to trigger click
        let convItem = $(`.conversation-item[data-conversation-id="${conversationId}"]`);

        if (convItem.length) {
            convItem.trigger('click');
        } else {
            // Load messages directly if conversation item isn't in the list
            loadMessages(conversationId);
        }
    });

    let searchTimeout;
    $('#search-messages-input').on('input', function() {
        clearTimeout(searchTimeout);
        const query = $(this).val().trim();
        searchTimeout = setTimeout(() => {
            performSearch(query);
        }, 300);
    });

    $('#search-messages-btn').on('click', function() {
        performSearch($('#search-messages-input').val().trim());
    });

    $('#search-conversations-input').on('input', function() {
        const term = $(this).val().toLowerCase();
        $('.conversation-item').each(function() {
            const name = $(this).find('h6').text().toLowerCase();
            if (name.includes(term)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

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