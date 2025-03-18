// ChatBot Widget Script - Generated for {{ $website->name }}
(function() {
    // Configuration
    const config = {
        websiteId: {{ $website->id }},
        apiEndpoint: '{{ url('/widget') }}',
        widgetColor: '{{ $widgetColor }}',
        widgetPosition: '{{ $widgetPosition }}',
        csrfToken: '{{ csrf_token() }}'
    };

    // Create widget styles
    const createStyles = () => {
        const style = document.createElement('style');
        style.textContent = `
            #chat-bubble-{{ $website->id }} {
                position: fixed;
                z-index: 9999;
                display: flex;
                align-items: center;
                ${getPositionStyles(config.widgetPosition)}
            }

            #chat-bubble-{{ $website->id }} .bubble-text {
                margin-right: 10px;
                font-size: 1.2rem;
                color: ${config.widgetColor};
                animation: popInOut-{{ $website->id }} 5s infinite;
            }

            @keyframes popInOut-{{ $website->id }} {
                0%, 100% {
                    transform: scale(0);
                    opacity: 0;
                }
                50% {
                    transform: scale(1);
                    opacity: 1;
                }
            }

            #chat-bubble-{{ $website->id }} .wave-btn {
                width: 65px;
                height: 65px;
                border-radius: 50%;
                border: none;
                background: ${config.widgetColor};
                color: #fff;
                font-size: 1.4rem;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
                transition: transform 0.2s ease, box-shadow 0.2s ease;
                position: relative;
                animation: vibrate-{{ $website->id }} 2s infinite;
            }

            @keyframes vibrate-{{ $website->id }} {
                0%   { transform: translate(0, 0); }
                20%  { transform: translate(-1px, 1px); }
                40%  { transform: translate(-1px, -1px); }
                60%  { transform: translate(1px, 1px); }
                80%  { transform: translate(1px, -1px); }
                100% { transform: translate(0, 0); }
            }

            #chat-bubble-{{ $website->id }} .wave-btn:hover {
                transform: scale(1.08);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            }

            #chat-bubble-{{ $website->id }} .wave-icon {
                display: inline-block;
                animation: waveHand-{{ $website->id }} 2s infinite;
            }

            @keyframes waveHand-{{ $website->id }} {
                0%   { transform: rotate(0deg); }
                10%  { transform: rotate(14deg); }
                20%  { transform: rotate(-8deg); }
                30%  { transform: rotate(14deg); }
                40%  { transform: rotate(-4deg); }
                50%  { transform: rotate(10deg); }
                60%  { transform: rotate(0deg); }
                100% { transform: rotate(0deg); }
            }

            #chatWindow-{{ $website->id }} {
                position: fixed;
                width: 350px;
                height: 450px;
                background: #fff;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0,0,0,0.2);
                display: none;
                z-index: 10000;
                ${getPositionStyles(config.widgetPosition, true)}
            }

            #chatWindow-{{ $website->id }} .chat-header {
                background-color: ${config.widgetColor};
                color: white;
                border-top-left-radius: 10px;
                border-top-right-radius: 10px;
            }

            #chatWindow-{{ $website->id }} .btn-primary {
                background-color: ${config.widgetColor};
                border-color: ${config.widgetColor};
            }

            #chatMessages-{{ $website->id }} {
                height: 320px;
                overflow-y: auto;
                padding: 10px;
            }
            
            .widget-buttons-container {
                width: 100%;
            }
            
            .widget-action-button {
                margin-right: 8px;
                margin-bottom: 8px;
                transition: all 0.2s ease;
            }
            
            .widget-action-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }
        `;
        document.head.appendChild(style);
    };

    // Helper function to get position styles
    const getPositionStyles = (position, isWindow = false) => {
        const margin = isWindow ? '20px' : '20px';
        const windowOffset = isWindow ? '80px' : '0px';
        
        switch(position) {
            case 'bottom-right':
                return `bottom: ${margin}; right: ${margin};`;
            case 'bottom-left':
                return `bottom: ${margin}; left: ${margin};`;
            case 'top-right':
                return `top: ${margin}; right: ${margin};`;
            case 'top-left':
                return `top: ${margin}; left: ${margin};`;
            default:
                return `bottom: ${margin}; right: ${margin};`;
        }
    };

    // Create widget HTML
    const createWidgetHTML = () => {
        // Create the chat bubble
        const chatBubble = document.createElement('div');
        chatBubble.id = `chat-bubble-${config.websiteId}`;
        chatBubble.innerHTML = `
            <span class="bubble-text">How can I help?</span>
            <button id="openChatBtn-${config.websiteId}" class="wave-btn" title="Open Chat">
                <span class="wave-icon">👋</span>
            </button>
        `;
        document.body.appendChild(chatBubble);

        // Create the chat window
        const chatWindow = document.createElement('div');
        chatWindow.id = `chatWindow-${config.websiteId}`;
        chatWindow.innerHTML = `
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom chat-header">
                <div>
                    <h5 class="m-0">Chat Support</h5>
                    <small class="text-light" id="agentStatus-${config.websiteId}" style="display: none;">AI Assistant</small>
                </div>
                <div>
                    <button id="minimizeChatBtn-${config.websiteId}" class="btn btn-sm text-light" title="Minimize Chat">
                        <i class="bi bi-dash-lg"></i>
                    </button>
                    <button id="closeChatBtn-${config.websiteId}" class="btn btn-sm text-light" title="Close Chat">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
            <div id="chatMessages-${config.websiteId}" style="height: 320px; overflow-y: auto; padding: 10px;"></div>
            <div class="p-3 border-top">
                <div class="input-group">
                    <textarea id="chatInput-${config.websiteId}" class="form-control" placeholder="Type a message..." rows="1"></textarea>
                    <button id="sendMsgBtn-${config.websiteId}" class="btn btn-primary" title="Send Message">
                        <i class="bi bi-send-fill"></i>
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(chatWindow);
    };

    // Add Bootstrap Icons if not already present
    const addBootstrapIcons = () => {
        if (!document.querySelector('link[href*="bootstrap-icons"]')) {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css';
            document.head.appendChild(link);
        }
    };

    // Initialize the widget
    const initWidget = () => {
        createStyles();
        addBootstrapIcons();
        createWidgetHTML();
        
        let chatId = null;
        let currentMessageCount = 0;
        let hasOpened = false;
        let pendingMessages = [];
        let lastMessageId = 0;
        let hasSentWelcome = false;
        let isPolling = false;
        let popupInterval = null;
        
        // Function to add a welcome message
        const addWelcomeMessage = () => {
            if (hasSentWelcome) return;
            
            const welcomeMsg = document.createElement('div');
            welcomeMsg.className = 'mb-4 d-flex flex-column align-items-start';
            
            // Get the welcome message from the server
            fetch(`${config.apiEndpoint}/welcome-message/${config.websiteId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.welcome_message) {
                        welcomeMsg.innerHTML = `
                            <div class="bg-primary text-white p-4 rounded-3" style="max-width: 85%;">
                                ${data.welcome_message}
                            </div>
                            <small class="text-muted mt-2">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</small>
                        `;
                        document.getElementById(`chatMessages-${config.websiteId}`).prepend(welcomeMsg);
                        
                        // Add buttons if available
                        if (data.buttons && data.buttons.length > 0) {
                            const buttonsContainer = document.createElement('div');
                            buttonsContainer.className = 'widget-buttons-container mb-4';
                            
                            let buttonsHtml = '<div class="d-flex flex-wrap gap-2">';
                            data.buttons.forEach(button => {
                                if (button.is_active) {
                                    buttonsHtml += `
                                        <button 
                                            class="btn btn-outline-primary widget-action-button" 
                                            data-action-type="${button.action_type}" 
                                            data-action-value="${button.action_value}"
                                        >
                                            ${button.text}
                                        </button>
                                    `;
                                }
                            });
                            buttonsHtml += '</div>';
                            
                            buttonsContainer.innerHTML = buttonsHtml;
                            document.getElementById(`chatMessages-${config.websiteId}`).prepend(buttonsContainer);
                            
                            // Add event listeners to buttons
                            document.querySelectorAll('.widget-action-button').forEach(button => {
                                button.addEventListener('click', function() {
                                    const actionType = this.dataset.actionType;
                                    const actionValue = this.dataset.actionValue;
                                    
                                    if (actionType === 'message') {
                                        // Send the message
                                        const chatInput = document.getElementById(`chatInput-${config.websiteId}`);
                                        chatInput.value = actionValue;
                                        sendMessage();
                                    } else if (actionType === 'link') {
                                        // Open the link in a new tab
                                        window.open(actionValue, '_blank');
                                    }
                                });
                            });
                        }
                        
                        hasSentWelcome = true;
                    } else {
                        // Use default welcome message if none is set
                        welcomeMsg.innerHTML = `
                            <div class="bg-primary text-white p-4 rounded-3" style="max-width: 85%;">
                                Welcome! How can I help you today?
                            </div>
                            <small class="text-muted mt-2">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</small>
                        `;
                        document.getElementById(`chatMessages-${config.websiteId}`).prepend(welcomeMsg);
                        hasSentWelcome = true;
                    }
                })
                .catch(error => {
                    console.error('Error fetching welcome message:', error);
                    // Use default welcome message if there's an error
                    welcomeMsg.innerHTML = `
                        <div class="bg-primary text-white p-4 rounded-3" style="max-width: 85%;">
                            Welcome! How can I help you today?
                        </div>
                        <small class="text-muted mt-2">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</small>
                    `;
                    document.getElementById(`chatMessages-${config.websiteId}`).appendChild(welcomeMsg);
                    hasSentWelcome = true;
                });
        };
        
        // Function to add CSS for widget buttons
        const addButtonStyles = () => {
            const style = document.createElement('style');
            style.textContent = `
                .widget-buttons-container {
                    width: 100%;
                }
                
                .widget-action-button {
                    margin-right: 8px;
                    margin-bottom: 8px;
                    transition: all 0.2s ease;
                }
                
                .widget-action-button:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }
            `;
            document.head.appendChild(style);
        };
        
        // Add button styles
        addButtonStyles();
        
        // Event listeners
        document.getElementById(`openChatBtn-${config.websiteId}`).addEventListener('click', () => {
            stopPersistentPopup();
            const chatWindow = document.getElementById(`chatWindow-${config.websiteId}`);
            chatWindow.style.display = 'flex';
            chatWindow.classList.add('open');
            document.getElementById(`openChatBtn-${config.websiteId}`).style.display = 'none';
            document.getElementById(`chat-bubble-${config.websiteId}`).style.opacity = '0';
            hasOpened = true;
            document.getElementById(`chatInput-${config.websiteId}`).focus();

            if (!chatId) {
                startChat();
            }
            
            // Add welcome message when chat is opened
            addWelcomeMessage();
        });

        document.getElementById(`closeChatBtn-${config.websiteId}`).addEventListener('click', () => {
            document.getElementById(`chatWindow-${config.websiteId}`).style.display = 'none';
            document.getElementById(`openChatBtn-${config.websiteId}`).style.display = 'block';
        });

        document.getElementById(`minimizeChatBtn-${config.websiteId}`).addEventListener('click', () => {
            document.getElementById(`chatWindow-${config.websiteId}`).style.display = 'none';
            document.getElementById(`openChatBtn-${config.websiteId}`).style.display = 'block';
        });

        const chatInput = document.getElementById(`chatInput-${config.websiteId}`);
        chatInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                if (!e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            }
        });

        document.getElementById(`sendMsgBtn-${config.websiteId}`).addEventListener('click', sendMessage);

        // Start a new chat
        async function startChat() {
            try {
                const response = await fetch(`${config.apiEndpoint}/chats`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken
                    },
                    body: JSON.stringify({
                        website_id: config.websiteId,
                        name: 'Visitor'
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
                const welcomeMsg = document.createElement('div');
                welcomeMsg.innerHTML = `
                    <div class="mb-3 d-flex">
                        <div class="bg-light p-2 rounded-3 me-auto" style="max-width: 80%;">
                            Welcome to our chat support! How can I help you today?
                        </div>
                    </div>
                `;
                document.getElementById(`chatMessages-${config.websiteId}`).appendChild(welcomeMsg);
                document.getElementById(`agentStatus-${config.websiteId}`).style.display = 'block';
                document.getElementById(`agentStatus-${config.websiteId}`).innerHTML = data.agent_name
                    ? `Agent: ${data.agent_name} <i class="bi bi-circle-fill text-success"></i>`
                    : `AI Assistant <i class="bi bi-circle-fill text-success"></i>`;
                pollMessages();
            } catch (error) {
                console.error('Error starting chat:', error);
                alert('Error starting chat: ' + error.message);
            }
        }

        // Send a message
        async function sendMessage() {
            const content = chatInput.value.trim();
            if (!content || !chatId) return;
            chatInput.value = '';
            
            // Add user message to the chat window immediately
            const userMsgElement = document.createElement('div');
            userMsgElement.innerHTML = `
                <div class="mb-3 d-flex flex-column align-items-end">
                    <div class="bg-primary text-white p-2 rounded-3 ms-auto" style="max-width: 80%;">
                        ${content}
                    </div>
                </div>
            `;
            document.getElementById(`chatMessages-${config.websiteId}`).appendChild(userMsgElement);
            document.getElementById(`chatMessages-${config.websiteId}`).scrollTop = document.getElementById(`chatMessages-${config.websiteId}`).scrollHeight;
            
            try {
                const response = await fetch(`${config.apiEndpoint}/chats/${chatId}/messages`, {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken
                    },
                    body: JSON.stringify({ content })
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
                pollMessages();
            } catch (error) {
                console.error('Error sending message:', error);
                const errorMsgElement = document.createElement('div');
                errorMsgElement.innerHTML = `
                    <div class="mb-3 d-flex justify-content-center">
                        <div class="text-danger p-2" style="max-width: 80%;">
                            <small>Error: ${error.message}</small>
                        </div>
                    </div>
                `;
                document.getElementById(`chatMessages-${config.websiteId}`).appendChild(errorMsgElement);
                document.getElementById(`chatMessages-${config.websiteId}`).scrollTop = document.getElementById(`chatMessages-${config.websiteId}`).scrollHeight;
            }
        }

        // Poll for new messages
        async function pollMessages() {
            if (!chatId) return;
            try {
                const response = await fetch(`${config.apiEndpoint}/chats/${chatId}/messages`);
                if (!response.ok) {
                    throw new Error('Failed to fetch messages');
                }
                const data = await response.json();
                if (!data.success) {
                    throw new Error(data.message || 'Failed to fetch messages');
                }
                const messages = data.messages;
                const chatMessagesDiv = document.getElementById(`chatMessages-${config.websiteId}`);
                if (messages.length > currentMessageCount) {
                    const newMessages = messages.slice(currentMessageCount);
                    newMessages.forEach(message => {
                        if (message.sender_type === 'user' || message.sender_type === 'ai') {
                            const agentMsgElement = document.createElement('div');
                            agentMsgElement.innerHTML = `
                                <div class="mb-3 d-flex flex-column align-items-start">
                                    <small class="text-muted">${message.user_name || 'AI Assistant'}</small>
                                    <div class="bg-light p-2 rounded-3 me-auto" style="max-width: 80%;">
                                        ${message.content}
                                    </div>
                                </div>
                            `;
                            chatMessagesDiv.appendChild(agentMsgElement);
                        } else if (message.sender_type === 'customer') {
                            // Only add customer messages that weren't added when sending
                            // Skip the first message that was already added when sending
                            const userMsgElement = document.createElement('div');
                            userMsgElement.innerHTML = `
                                <div class="mb-3 d-flex flex-column align-items-end">
                                    <div class="bg-primary text-white p-2 rounded-3 ms-auto" style="max-width: 80%;">
                                        ${message.content}
                                    </div>
                                </div>
                            `;
                            chatMessagesDiv.appendChild(userMsgElement);
                        }
                    });
                    currentMessageCount = messages.length;
                    chatMessagesDiv.scrollTop = chatMessagesDiv.scrollHeight;
                }
                setTimeout(pollMessages, 3000);
            } catch (error) {
                console.error('Error polling messages:', error);
                setTimeout(pollMessages, 5000);
            }
        }
    };

    // Initialize when DOM is fully loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initWidget);
    } else {
        initWidget();
    }
})();
