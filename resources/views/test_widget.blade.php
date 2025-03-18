@extends('layouts.test')

@section('content')
<div class="container mt-5">
    <h1>Test Chat Widget (with AI)</h1>
    <p class="mb-5">Use this page to test the AI-enabled chat widget locally.</p>
</div>

<!-- Chat Bubble -->
<div id="chat-bubble">
    <span class="bubble-text">Hey! Chat with us now!</span>
    <button id="openChatBtn" class="wave-btn" title="Open Chat" aria-label="Open Chat">
        <span class="wave-icon">💬</span>
    </button>
</div>

<!-- Main Chat Window -->
<div id="chatWindow" role="dialog" aria-modal="true" aria-labelledby="chatWindowTitle" aria-hidden="true">
    <div class="chat-header">
        <div class="header-content">
            <h5 class="m-0" id="chatWindowTitle">AI Chat Support</h5>
            <small class="text-muted" id="agentStatus" style="display: none;">AI Assistant: Offline</small>
        </div>
        <div class="header-actions">
            <button id="transferToHumanBtn" class="btn btn-icon btn-warning me-2" title="Transfer to Human" aria-label="Transfer to Human">Transfer to Human</button>
            <button id="minimizeChatBtn" class="btn btn-icon" title="Minimize Chat" aria-label="Minimize Chat">
                <i class="bi bi-dash-lg"></i>
            </button>
            <button id="closeChatBtn" class="btn btn-icon" title="Close Chat" aria-label="Close Chat">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>
    <div id="chatMessages" class="chat-body">
        <div id="typingIndicator" style="display: none;">
            <span class="typing-label">AI is typing</span>
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
        </div>
    </div>
    <div class="chat-footer">
        <div class="input-group w-100">
            <textarea id="chatInput" class="form-control" placeholder="Type a message..." rows="1" aria-label="Type a message"></textarea>
            <button id="sendMsgBtn" class="btn btn-primary" title="Send Message" aria-label="Send Message">
                <i class="bi bi-send-fill"></i>
            </button>
        </div>
    </div>
</div>

<script>
let chatId = null;
let currentMessageCount = 0;
let lastMessageId = 0;
let isPolling = false;
let popupInterval = null;

document.addEventListener('DOMContentLoaded', () => {
    const bubble = document.getElementById('chat-bubble');

    function startPersistentPopup() {
        if (!bubble.classList.contains('pop-attention')) {
            bubble.classList.add('pop-attention');
            playNotificationSound();
        }
    }

    function stopPersistentPopup() {
        bubble.classList.remove('pop-attention');
        if (popupInterval) {
            clearInterval(popupInterval);
            popupInterval = null;
        }
    }

    function managePopup() {
        const chatWindow = document.getElementById('chatWindow');
        if (chatWindow.classList.contains('open') && chatWindow.style.display === 'flex') {
            stopPersistentPopup();
        } else {
            if (!popupInterval) {
                startPersistentPopup();
                popupInterval = setInterval(() => {
                    bubble.classList.remove('pop-attention');
                    setTimeout(() => {
                        startPersistentPopup();
                    }, 1000);
                }, 3000);
            }
        }
    }

    managePopup();

    document.getElementById('openChatBtn').addEventListener('click', () => {
        stopPersistentPopup();
        const chatWindow = document.getElementById('chatWindow');
        chatWindow.style.display = 'flex';
        chatWindow.classList.add('open');
        document.getElementById('openChatBtn').style.display = 'none';
        document.getElementById('chat-bubble').style.opacity = '0';
        document.getElementById('chatInput').focus();

        if (!chatId) {
            startChat().then(() => {
                document.getElementById('agentStatus').innerHTML = 'AI Assistant <i class="bi bi-circle-fill text-success"></i>';
                document.getElementById('agentStatus').style.display = 'block';
            });
        }
    });

    document.getElementById('closeChatBtn').addEventListener('click', () => {
        closeChatWindow();
    });

    document.getElementById('minimizeChatBtn').addEventListener('click', () => {
        closeChatWindow();
    });

    // Transfer to Human button
    document.getElementById('transferToHumanBtn').addEventListener('click', async () => {
        if (!chatId) return;
        try {
            const response = await fetch(`/api/widget/chats/${chatId}/transfer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            const data = await response.json();
            if (data.success) {
                document.getElementById('agentStatus').innerHTML = 'Waiting for Human Support';
                document.getElementById('agentStatus').style.display = 'block';
                document.getElementById('transferToHumanBtn').disabled = true;
            } else {
                alert('Failed to transfer: ' + data.message);
            }
        } catch (error) {
            console.error('Error transferring to human:', error);
            alert('Error transferring to human support.');
        }
    });
});

function playNotificationSound() {
    const audio = new Audio('https://www.soundjay.com/buttons/beep-01a.mp3');
    audio.volume = 0.2;
    audio.play().catch(err => console.log('Audio playback failed:', err));
}

function closeChatWindow() {
    const chatWindow = document.getElementById('chatWindow');
    chatWindow.classList.remove('open');
    setTimeout(() => {
        chatWindow.style.display = 'none';
        document.getElementById('openChatBtn').style.display = 'block';
        document.getElementById('chat-bubble').style.opacity = '1';
        managePopup();
    }, 300);
}

const chatInput = document.getElementById('chatInput');
chatInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
});

chatInput.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
    if (this.scrollHeight > 120) {
        this.style.overflowY = 'auto';
    } else {
        this.style.overflowY = 'hidden';
    }
});

async function startChat() {
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const response = await fetch('/api/widget/chats', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                website_id: 1,
                name: 'Test User'
            })
        });

        const data = await response.json();
        if (!response.ok) {
            let errorMessage = 'Failed to start chat';
            if (data && data.message) {
                errorMessage = data.message;
            } else if (data && data.errors) {
                errorMessage = Object.values(data.errors).flat().join(', ');
            }
            throw new Error(errorMessage);
        }

        chatId = data.chat_id;

        const chatMessagesDiv = document.getElementById('chatMessages');
        const welcomeMsg = document.createElement('div');
        welcomeMsg.innerHTML = `
            <div class="mb-4 d-flex flex-column align-items-start">
                <div class="bg-primary text-white p-4 rounded-3" style="max-width: 85%;">
                ${data.welcome_message || 'Welcome to our AI chat! How can I help you today?'}
                </div>
                <small class="text-muted mt-2">
                    ${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                </small>
            </div>`;
        chatMessagesDiv.prepend(welcomeMsg);

        pollMessages();
    } catch (error) {
        console.error('Error starting chat:', error);
        alert('Error starting chat: ' + error.message);
    }
}

document.getElementById('sendMsgBtn').addEventListener('click', sendMessage);

async function sendMessage() {
    const content = chatInput.value.trim();
    if (!content || !chatId) return;

    chatInput.value = '';
    chatInput.style.height = '40px';

    try {
        const response = await fetch(`/api/widget/chats/${chatId}/messages`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ message: content })
        });

        const data = await response.json();
        if (!response.ok) {
            let errorMessage = 'Failed to send message';
            if (data && data.message) {
                errorMessage = data.message;
            } else if (data && data.errors) {
                errorMessage = Object.values(data.errors).flat().join(', ');
            }
            throw new Error(errorMessage);
        }

        console.log('Message sent (AI triggered if configured):', data);
        return data;
    } catch (error) {
        console.error('Error sending message:', error);
        alert('Error sending message: ' + error.message);
        return null;
    }
}

async function pollMessages() {
    if (!chatId || isPolling) return;
    isPolling = true;

    try {
        const response = await fetch(`/api/widget/chats/${chatId}/messages`);
        if (!response.ok) {
            throw new Error('Failed to fetch messages');
        }
        const data = await response.json();
        if (!data.success) {
            throw new Error(data.message || 'Failed to fetch messages');
        }

        const messages = data.messages;
        const chatMessagesDiv = document.getElementById('chatMessages');
        const typingIndicator = document.getElementById('typingIndicator');

        if (messages.length > currentMessageCount) {
            const newMessages = messages.filter(m => m.id > lastMessageId);

            newMessages.forEach(message => {
                const timestamp = message.created_at || new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                if (message.sender_type === 'customer') {
                    const userMsgElement = document.createElement('div');
                    userMsgElement.innerHTML = `
                        <div class="mb-4 d-flex flex-column align-items-start" data-message-id="${message.id}">
                            <div class="bg-light p-3 rounded-3 me-auto" style="max-width: 80%;">
                                ${message.content}
                            </div>
                            <small class="text-muted mt-2">${timestamp}</small>
                        </div>
                    `;
                    chatMessagesDiv.insertBefore(userMsgElement, typingIndicator);
                }
                else if (message.sender_type === 'ai') {
                    typingIndicator.style.display = 'none';
                    const formattedContent = message.content
                        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                        .replace(/(\r\n|\n|\r)/g, '<br>');
                    const aiMsgElement = document.createElement('div');
                    aiMsgElement.innerHTML = `
                        <div class="mb-4 d-flex flex-column align-items-end">
                            <small class="text-muted">AI Assistant</small>
                            <div class="bg-primary text-white p-4 rounded-3 ms-auto" style="max-width: 85%;">
                                ${formattedContent}
                            </div>
                            <small class="text-muted mt-2">${timestamp}</small>
                        </div>
                    `;
                    chatMessagesDiv.insertBefore(aiMsgElement, typingIndicator);
                }
                else if (message.sender_type === 'admin') {
                    typingIndicator.style.display = 'none';
                    const adminMsgElement = document.createElement('div');
                    adminMsgElement.innerHTML = `
                        <div class="mb-4 d-flex flex-column align-items-end">
                            <small class="text-muted">Admin</small>
                            <div class="bg-primary text-white p-4 rounded-3 ms-auto" style="max-width: 85%;">
                                ${message.content.replace(/\n/g, '<br>')}
                            </div>
                            <small class="text-muted mt-2">${timestamp}</small>
                        </div>
                    `;
                    chatMessagesDiv.insertBefore(adminMsgElement, typingIndicator);
                }

                lastMessageId = message.id;
            });

            currentMessageCount = messages.length;
            chatMessagesDiv.scrollTop = chatMessagesDiv.scrollHeight;

            const transferred = messages.some(msg => msg.content === 'Chat transferred to human support');
            if (transferred) {
                document.getElementById('agentStatus').innerHTML = 'Waiting for Human Support';
                document.getElementById('transferToHumanBtn').disabled = true;
            }

            const anyAIMessage = newMessages.some(msg => msg.sender_type === 'ai');
            if (!anyAIMessage) {
                typingIndicator.style.display = 'none';
            }
        }

        setTimeout(pollMessages, 2000);
    } catch (error) {
        console.error('Error polling messages:', error);
        setTimeout(pollMessages, 5000);
    } finally {
        isPolling = false;
    }
}
</script>

<style>
/* ---------------
   Chat Bubble
   --------------- */
#chat-bubble {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
    display: flex;
    align-items: center;
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.bubble-text {
    background: linear-gradient(135deg, #6E8EFB, #a777e3);
    color: #fff;
    padding: 12px 18px;
    border-radius: 25px;
    margin-right: 12px;
    font-size: 0.9rem;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(110, 142, 251, 0.3);
    transition: transform 0.2s ease;
}

.bubble-text:hover {
    transform: scale(1.05);
}

.pop-attention {
    animation: popAttention 1.5s ease-in-out infinite;
}

@keyframes popAttention {
    0%   { transform: scale(1); box-shadow: 0 4px 15px rgba(110, 142, 251, 0.3); }
    50%  { transform: scale(1.15); box-shadow: 0 8px 25px rgba(110, 142, 251, 0.6); }
    100% { transform: scale(1); box-shadow: 0 4px 15px rgba(110, 142, 251, 0.3); }
}

/* ---------------
   Wave Button
   --------------- */
.wave-btn {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    border: none;
    background: linear-gradient(135deg, #6e8efb, #a777e3);
    color: #fff;
    font-size: 1.8rem;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 15px rgba(110, 142, 251, 0.3);
    position: relative;
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.wave-btn::after {
    content: '';
    position: absolute;
    width: 120px;
    height: 120px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    animation: ripple 2s infinite;
}

@keyframes ripple {
    0%   { transform: scale(0); opacity: 1; }
    100% { transform: scale(2); opacity: 0; }
}

.wave-btn:hover {
    transform: scale(1.08);
    box-shadow: 0 6px 20px rgba(110, 142, 251, 0.5);
}

/* ---------------
   Chat Window
   --------------- */
#chatWindow {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 400px;
    max-width: 90vw;
    height: 600px;
    max-height: 80vh;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    display: none;
    z-index: 1000;
    overflow: hidden;
    flex-direction: column;
}

#chatWindow.open {
    animation: slideUp 0.3s ease forwards;
}

@keyframes slideUp {
    from { transform: translateY(100px); opacity: 0; }
    to   { transform: translateY(0); opacity: 1; }
}

.chat-header {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #dee2e6;
}

.header-content h5 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
}

.header-actions .btn-icon {
    background: none;
    border: none;
    padding: 5px 10px;
    color: #7f8c8d;
    transition: color 0.2s ease, transform 0.2s ease;
}

.header-actions .btn-icon:hover {
    color: #2c3e50;
    transform: scale(1.1);
}

.chat-body {
    flex: 1;
    overflow-y: auto;
    padding: 25px;
    background: #f5f6fa;
    position: relative;
    scroll-behavior: smooth;
}

.chat-body::-webkit-scrollbar {
    width: 8px;
}
.chat-body::-webkit-scrollbar-thumb {
    background: #b0b8c4;
    border-radius: 4px;
}
.chat-body::-webkit-scrollbar-thumb:hover {
    background: #95a5a6;
}

.bg-primary, .bg-light {
    white-space: normal;
    line-height: 1.6;
    font-size: 0.9rem;
}

.bg-primary {
    background: linear-gradient(135deg, #6e8efb, #5d7ce0) !important;
    color: #fff;
    border-radius: 15px 15px 15px 5px;
    box-shadow: 0 3px 10px rgba(110, 142, 251, 0.2);
}

.bg-light {
    background: #ffffff !important;
    border: 1px solid #e9ecef;
    border-radius: 5px 15px 15px 15px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.05);
}

.chat-footer {
    padding: 15px 20px;
    border-top: 1px solid #e9ecef;
    background: #fff;
    display: flex;
    align-items: center;
    min-height: 80px;
}

.input-group {
    flex-grow: 1;
    display: flex;
    align-items: center;
}

#chatInput {
    resize: none;
    overflow-y: auto;
    min-height: 50px;
    max-height: 120px;
    border-radius: 10px 0 0 10px;
    border: 1px solid #ced4da;
    border-right: none;
    padding: 10px 15px;
    font-size: 0.85rem;
    flex-grow: 1;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
    box-sizing: border-box;
}

#chatInput:focus {
    border-color: #6e8efb;
    box-shadow: 0 0 5px rgba(110, 142, 251, 0.3);
    outline: none;
}

#sendMsgBtn {
    border-radius: 0 10px 10px 0;
    padding: 10px 20px;
    background: #6e8efb;
    border: none;
    transition: background 0.2s ease, transform 0.2s ease;
    height: 50px;
    align-self: stretch;
    margin-left: -1px;
    color: #fff;
}

#sendMsgBtn:hover {
    background: #5d7ce0;
    transform: translateY(-2px);
}

.typing-label {
    font-size: 0.8rem;
    color: #7f8c8d;
    margin-right: 10px;
}

#typingIndicator {
    display: none;
    align-items: center;
    justify-content: flex-start;
    padding: 15px 0;
    opacity: 0;
    transition: opacity 0.3s ease;
}

#typingIndicator[style*="display: flex"] {
    opacity: 1;
    animation: floatEffect 2s ease-in-out infinite;
}

#typingIndicator .dot {
    width: 10px;
    height: 10px;
    background: #6e8efb;
    border-radius: 50%;
    margin-right: 8px;
    animation: bounce 1s infinite;
}

#typingIndicator .dot:nth-child(2) {
    animation-delay: 0.2s;
}
#typingIndicator .dot:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes floatEffect {
    0%   { transform: translateY(0); }
    50%  { transform: translateY(-5px); }
    100% { transform: translateY(0); }
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50%      { transform: translateY(-5px); }
}

@media (max-width: 768px) {
    #chatWindow {
        width: 90vw;
        height: 70vh;
        bottom: 10px;
        right: 10px;
    }
    .chat-footer {
        padding: 10px 15px;
        min-height: 70px;
    }
    #chatInput {
        min-height: 35px;
        padding: 8px 12px;
    }
    #sendMsgBtn {
        padding: 8px 15px;
        height: 35px;
    }
}

@media (max-width: 576px) {
    #chatWindow {
        width: 100vw;
        height: 100vh;
        bottom: 0;
        right: 0;
        border-radius: 0;
    }
    #chat-bubble {
        bottom: 15px;
        right: 15px;
    }
    .wave-btn {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
    .bubble-text {
        font-size: 0.8rem;
        padding: 10px 15px;
    }
    .chat-footer {
        padding: 10px;
        min-height: 60px;
    }
    #chatInput {
        min-height: 30px;
        padding: 6px 10px;
        font-size: 0.8rem;
    }
    #sendMsgBtn {
        padding: 6px 12px;
        font-size: 0.9rem;
        height: 30px;
    }
}

@media (min-width: 1200px) {
    #chatWindow {
        width: 500px;
        max-height: 80vh;
    }
}
</style>
@endsection