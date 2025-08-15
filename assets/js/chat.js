// Chat functionality for OrangeRoute
document.addEventListener('DOMContentLoaded', function() {
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    const chatMessages = document.getElementById('chatMessages');
    
    if (!chatForm || !messageInput || !chatMessages) {
        return; // Chat elements not found
    }
    
    // Load messages on page load
    loadMessages();
    
    // Auto-refresh messages every 5 seconds
    setInterval(loadMessages, 5000);
    
    // Handle form submission
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        sendMessage();
    });
    
    function sendMessage() {
        const message = messageInput.value.trim();
        if (!message) return;
        
        const formData = new FormData();
        formData.append('action', 'send');
        formData.append('message', message);
        
        fetch('../../backend/chat.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageInput.value = '';
                loadMessages(); // Reload messages after sending
            } else {
                alert('Failed to send message: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
            alert('Error sending message. Please try again.');
        });
    }
    
    function loadMessages() {
        const formData = new FormData();
        formData.append('action', 'get');
        
        fetch('../../backend/chat.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayMessages(data.messages);
            }
        })
        .catch(error => {
            console.error('Error loading messages:', error);
        });
    }
    
    function displayMessages(messages) {
        chatMessages.innerHTML = '';
        
        if (messages.length === 0) {
            chatMessages.innerHTML = '<div class="chat-message text-center" style="color: var(--dark-gray);">No messages yet. Start the conversation!</div>';
            return;
        }
        
        // Reverse messages to show newest at bottom
        messages.reverse().forEach(message => {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'chat-message';
            
            const time = new Date(message.created_at).toLocaleTimeString();
            messageDiv.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <strong style="color: var(--primary-orange);">${message.username}</strong>
                    <small style="color: var(--dark-gray);">${time}</small>
                </div>
                <div>${escapeHtml(message.message)}</div>
            `;
            
            chatMessages.appendChild(messageDiv);
        });
        
        // Scroll to bottom
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
