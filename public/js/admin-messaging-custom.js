$(function () {
    console.log('🔵 Admin Messaging script initialized');

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    const conversationsList = document.getElementById('conversations-list');
    const baseUrl = conversationsList.dataset.base_url;
    const userId = conversationsList.dataset.user_id;

    let currentConversationId = null;
    let pollingInterval = null;
    let lastMessageId = 0;

    function loadMessages(conversationId) {
        currentConversationId = conversationId;
        stopPolling();
        $('#messages-container').html('<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i></div>');
        $('#message-input, #send-button').prop('disabled', true);

        $.ajax({
            url: `${baseUrl}/${conversationId}`,
            method: 'GET',
            success: function (response) {
                let messages = typeof response === 'string' ? JSON.parse(response) : response;

                $('#conversation-title').text($(`.conversation-item[data-conversation-id=${conversationId}] .conversation-name`).text());
                
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
            url: `${baseUrl}/${currentConversationId}/new`,
            method: 'GET',
            data: { after_id: lastMessageId },
            success: function (messages) {
                if (Array.isArray(messages) && messages.length > 0) {
                    messages.forEach(appendMessage);
                }
            },
            error: (xhr) => console.error('Error checking for new messages:', xhr)
        });
    }

    function appendMessage(message) {
        if (!message || $(`#message-${message.id}`).length) return;
        
        lastMessageId = message.id;
        const isSender = message.sender_id == userId;
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
    
    // --- Event Handlers ---

    $(document).on('click', '.conversation-item', function() {
        $('.conversation-item.active').removeClass('active');
        $(this).addClass('active');
        loadMessages($(this).data('conversation-id'));
    });
});