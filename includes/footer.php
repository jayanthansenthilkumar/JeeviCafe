<!-- Chatbot Global Injected UI -->
<script>
function toggleChatbot() {
    const w = document.getElementById('chatbot-window');
    w.style.display = w.style.display === 'flex' ? 'none' : 'flex';
}

function processChat() {
    const input = document.getElementById('chat-input');
    const msg = input.value.trim();
    if (!msg) return;
    
    appendMessage('user', msg);
    input.value = '';
    
    const messagesDiv = document.getElementById('chatbot-messages');
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
    
    $.post('chatbot.php', { message: msg }, function(response) {
        let reply = response.reply || "I didn't quite catch that. How can I help you?";
        appendMessage('bot', reply);
    }).fail(function() {
        appendMessage('bot', "Connection lost to the AI Cafeteria Core.");
    });
}

function appendMessage(sender, text) {
    const div = document.createElement('div');
    div.className = 'chat-msg chat-' + sender;
    div.innerHTML = text.replace(/\n/g, "<br>");
    
    const messagesDiv = document.getElementById('chatbot-messages');
    messagesDiv.appendChild(div);
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

// Allow Enter key to send
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('chat-input');
    if(input) {
        input.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                processChat();
            }
        });
    }
});
</script>

<div class="chatbot-bubble" onclick="toggleChatbot()">
    ☕
</div>

<div class="chatbot-window" id="chatbot-window">
    <div class="chatbot-header">
        <div>Cafe <span>AI</span> Assistant</div>
        <button class="chatbot-close" onclick="toggleChatbot()">&times;</button>
    </div>
    <div class="chatbot-messages" id="chatbot-messages">
        <div class="chat-msg chat-bot">Hello! I'm your Smart Canteen AI. I can instantly fetch the menu, place orders for you, check your vault balance, or cancel pending orders! What would you like to do?</div>
    </div>
    <div class="chatbot-input-area">
        <input type="text" id="chat-input" placeholder="e.g. 'Order Pizza' or 'Check Balance'" autocomplete="off">
        <button class="btn-send" onclick="processChat()">></button>
    </div>
</div>

    </div>
</div>
</body>
</html>
