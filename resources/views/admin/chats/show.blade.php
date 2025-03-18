@extends('layouts.admin')

@section('content')
<div class="container-fluid h-100">
    <div class="row h-100">
        <!-- Sidebar -->
        <div class="col-md-4 col-lg-3 d-none d-md-block" id="chatSidebar">
            @if(request()->has('website_id'))
                <div class="chat-list-container h-100 d-flex flex-column">
                    <div class="p-3 border-bottom sticky-top bg-light">
                        <h5 class="fw-bold mb-0">Chat Conversations</h5>
                    </div>
                    <div class="chat-list flex-grow-1 overflow-auto" id="chatSidebarList" style="overflow-y: auto; height: calc(100vh - 60px);">
                        <!-- Chat items loaded via JS -->
                    </div>
                </div>
            @endif
        </div>

        <!-- Chat Content Area -->
        <div class="col-md-8 col-lg-9 d-flex flex-column p-0 h-100">
            <div class="chat-content-container h-100 d-flex flex-column">
                
                <!-- Chat Header with Status Update -->
                <div class="chat-header p-3 border-bottom sticky-top" style="background-color: #f0f2f5;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="chat-avatar me-3">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                                    {{ strtoupper(substr($chat->customer_name ?? 'C', 0, 1)) }}
                                </div>
                            </div>
                            <div class="chat-info">
                                <h5 class="mb-0">{{ $chat->customer_name ?? 'Customer' }}</h5>
                                <small class="text-muted">
                                    <span class="online-status">
                                        <i class="bi bi-circle-fill text-success me-1" style="font-size: 0.5rem;"></i>
                                        online
                                    </span>
                                    <span class="last-seen d-none">
                                        last seen today at {{ now()->format('H:i') }}
                                    </span>
                                </small>
                            </div>
                        </div>
                        @php
                            $status = $chat->status ?? 'open';
                            $badgeClass = $needsHuman ? 'bg-warning' : match($status) {
                                'closed' => 'bg-secondary',
                                'trial_scheduled' => 'bg-info',
                                default => 'bg-success',
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }} text-white text-capitalize">
                            {{ $needsHuman ? 'Needs Human' : str_replace('_', ' ', $status) }}
                        </span>
                    </div>
                    <!-- Status Update Form -->
                    <div class="mt-3">
                        <form action="{{ route('admin.chats.update', $chat->id) }}" method="POST" id="statusForm" class="d-flex align-items-center">
                            @csrf
                            @method('PUT')
                            <label for="chatStatus" class="form-label mb-0 me-2"><strong>Status:</strong></label>
                            <select name="status" id="chatStatus" class="form-select form-select-sm me-2" style="width: auto;">
                                <option value="open" {{ $chat->status === 'open' ? 'selected' : '' }}>Open</option>
                                <option value="trial_scheduled" {{ $chat->status === 'trial_scheduled' ? 'selected' : '' }}>Trial Scheduled</option>
                                <option value="closed" {{ $chat->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-secondary">Update</button>
                        </form>
                    </div>
                </div>

                <!-- Customer Info Panel -->
                <div class="bg-light p-3 border-bottom">
                    <a class="d-flex justify-content-between align-items-center text-decoration-none text-dark" 
                       data-bs-toggle="collapse" href="#customerInfoPanel" role="button" aria-expanded="false">
                        <h6 class="mb-0"><i class="bi bi-person-circle me-2"></i> Customer Information</h6>
                        <i class="bi bi-chevron-down"></i>
                    </a>
                    <div class="collapse mt-3" id="customerInfoPanel">
                        <div class="card card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Name:</strong> {{ $chat->customer_name ?? 'Not provided' }}</p>
                                    <p class="mb-1"><strong>Email:</strong> {{ $chat->customer_email ?? 'Not provided' }}</p>
                                    <p class="mb-1"><strong>Phone:</strong> {{ $chat->customer_phone ?? 'Not provided' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Student Grade:</strong> {{ $chat->customer_grade ?? 'Not provided' }}</p>
                                    <p class="mb-1"><strong>Student Age:</strong> {{ $chat->student_age ?? 'Not provided' }}</p>
                                    <p class="mb-1"><strong>Start Date:</strong> 
                                        {{ $chat->start_date ? $chat->start_date->format('Y-m-d') : 'Not provided' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Messages Section -->
                <div class="messages-container flex-grow-1 p-3 overflow-auto" style="background-color: #e5ddd5; background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAB3RJTUUH4QgNDTEn8iVjUAAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAABrElEQVRo3u2aMU7DMBSGvzZ5AxMSExJsZWGCgYGJBSExwAFgYmRiQYKJhQN0YGVg6QlYWHuADkiMqGJpQIoaO7GrJI7T/5NS5Wd/+Z5jx05eIYSgJRQtogJRgahAVCAqEBWICkQFogJRgahAVCAqEBWICkQFogJRgahAVCAqEBWICkQFogJRgahAVCAqkP8Ksn1yRnF1y+LyBoCL6wfunp7Jz0+/9N0/OQVgfnHD9smZNZD85JjF5Q3Z8QF5MQcgOz4gLy5ZXN6QnxzXQOZ5QXZ8wDwv7ILk5/Mvy8yLOdnxwcqK5CfHKyuRF3OWt/fWQKJBn+XtPdGg/22/aNCnuLqtXRMN+tZAokGf6f4e06PHNO8xzXtM8x7TvMc07zHd32MadQGIht1aW9QFLIJEwy7TqEt5/8Ly7m1jW3n/QjTsMo26VkHKhxJTlJiixBQlpiipHkpMUWKKklqbKUpMUVoFKR9GTFFiipJq21SPiiofFVU+Kqp8VNQgHxU1yEdFDfJRUYN8VNSgHBU1KEdFDcpRUYNyVLQxKvqjUdEvjYpuZVT0G6OiPx4V3dqo6E+Nin4AaYBYXhB7BDoAAAAASUVORK5CYII=');">
                    @if($chat->messages->count() === 0)
                        <div class="d-flex justify-content-center align-items-center h-100">
                            <p class="text-muted">No messages yet.</p>
                        </div>
                    @else
                        <div class="message-list">
                            @foreach($chat->messages as $message)
                                <div class="message mb-3 d-flex 
                                    {{ $message->sender_type === 'admin' || $message->sender_type === 'ai' ? 'justify-content-end' : 'justify-content-start' }}">
                                    <div class="message-content p-3 rounded-3 shadow-sm 
                                        {{ $message->sender_type === 'admin' || $message->sender_type === 'ai' ? 'bg-success text-white' : 'bg-white text-dark' }}" 
                                        style="max-width: 75%; position: relative; border-radius: 12px; 
                                        {{ $message->sender_type === 'admin' || $message->sender_type === 'ai' ? 
                                            'border-top-right-radius: 3px; background-color: #128C7E !important;' : 
                                            'border-top-left-radius: 3px;' }}">
                                        
                                        @if($message->sender_type === 'customer')
                                            <div class="sender-name small mb-1 text-muted">
                                                Customer
                                            </div>
                                        @elseif($message->sender_type === 'ai')
                                            <div class="sender-name small mb-1 text-white">
                                                AI Assistant
                                            </div>
                                        @elseif($message->sender_type === 'admin' && $message->user)
                                            <div class="sender-name small mb-1 text-white">
                                                {{ $message->user->name }}
                                            </div>
                                        @endif
                                        
                                        <div class="message-text" style="white-space: pre-wrap;">{{ $message->content }}</div>
                                        
                                        <div class="message-time small mt-1 text-end {{ $message->sender_type === 'admin' || $message->sender_type === 'ai' ? 'text-white-50' : 'text-muted' }}">
                                            {{ $message->created_at->format('H:i') }}
                                            
                                            @if($message->sender_type === 'admin' || $message->sender_type === 'ai')
                                                <i class="bi bi-check2-all ms-1" style="color: #34B7F1;"></i>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            
                            <!-- Typing indicator -->
                            <div class="typing-indicator mb-3 d-none">
                                <div class="message mb-3 d-flex justify-content-start">
                                    <div class="message-content p-3 rounded-3 shadow-sm bg-white text-dark" 
                                        style="max-width: 75%; position: relative; border-radius: 12px; border-top-left-radius: 3px;">
                                        <div class="typing-dots">
                                            <span class="dot"></span>
                                            <span class="dot"></span>
                                            <span class="dot"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Chat Controls -->
                <div class="chat-controls p-3 border-top" style="position: sticky; bottom: 0; left: 0; right: 0; z-index: 100; background-color: #f0f2f5;">
                    <form
                        action="{{ route('admin.chats.sendMessage', $chat->id) }}"
                        method="POST"
                        id="messageForm"
                        data-send-url="{{ route('admin.chats.sendMessage', $chat->id) }}"
                        data-get-messages-url="{{ route('admin.chats.getMessages', $chat->id) }}"
                    >
                        @csrf
                        <div class="input-group">
                            <div class="input-group-prepend d-flex align-items-end">
                                <button type="button" class="btn btn-light border-0 rounded-circle me-2" style="width: 40px; height: 40px;">
                                    <i class="bi bi-emoji-smile"></i>
                                </button>
                                <button type="button" class="btn btn-light border-0 rounded-circle me-2" style="width: 40px; height: 40px;">
                                    <i class="bi bi-paperclip"></i>
                                </button>
                            </div>
                            <textarea 
                                class="form-control rounded-pill"
                                id="messageContent" 
                                name="content" 
                                rows="1" 
                                placeholder="Type a message..." 
                                required 
                                style="overflow:hidden; resize:none; border-radius: 20px; padding: 10px 15px; max-height: 120px;"
                                onkeydown="if(event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); document.getElementById('sendMsgBtn').click(); }"
                            ></textarea>
                            <div class="input-group-append">
                                <button type="submit" class="btn rounded-circle ms-2" id="sendMsgBtn" style="width: 40px; height: 40px; background-color: #128C7E; color: white;">
                                    <i class="bi bi-send-fill"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .message-content {
        max-width: 75%;
        position: relative;
        border-radius: 12px;
    }
    
    .message-content.admin-message, .message-content.ai-message {
        background-color: #128C7E !important;
        color: white;
        border-top-right-radius: 3px;
    }
    
    .message-content.customer-message {
        background-color: white;
        color: black;
        border-top-left-radius: 3px;
    }
    
    .typing-indicator .message-content {
        padding: 15px 20px;
    }
    
    .typing-dots span {
        display: inline-block;
        width: 8px;
        height: 8px;
        background-color: rgba(0, 0, 0, 0.3);
        border-radius: 50%;
        margin-right: 3px;
        animation: typing 1.4s infinite ease-in-out;
    }
    
    .typing-dots span:nth-child(1) { animation-delay: 0s; }
    .typing-dots span:nth-child(2) { animation-delay: 0.2s; }
    .typing-dots span:nth-child(3) { animation-delay: 0.4s; margin-right: 0; }
    
    @keyframes typing {
        0%, 60%, 100% { transform: translateY(0); }
        30% { transform: translateY(-5px); }
    }
    
    .messages-container, #chatSidebarList {
        scrollbar-width: thin;
        scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
    }
    
    .messages-container::-webkit-scrollbar, #chatSidebarList::-webkit-scrollbar {
        width: 6px;
    }
    
    .messages-container::-webkit-scrollbar-thumb, #chatSidebarList::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
        border-radius: 3px;
    }
    
    .row.h-100 { overflow: hidden; }
    #chatSidebar, .chat-content-container { height: 100vh; overflow-y: hidden; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.querySelector('.messages-container');
    const chatSidebarList = document.getElementById('chatSidebarList');
    
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
        messagesContainer.addEventListener('scroll', function(e) {
            e.stopPropagation();
        });
    }

    const textarea = document.getElementById('messageContent');
    const messageForm = document.getElementById('messageForm');
    const sendMsgBtn = document.getElementById('sendMsgBtn');
    const typingIndicator = document.querySelector('.typing-indicator');
    const messageList = document.querySelector('.message-list');
    const MAX_HEIGHT = 150;

    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        sendMessage();
    });

    function attachTextareaListeners() {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(MAX_HEIGHT, this.scrollHeight) + 'px';
            this.style.overflowY = this.scrollHeight > MAX_HEIGHT ? 'auto' : 'hidden';
        });
    }

    attachTextareaListeners();

    if (textarea) {
        textarea.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }

    if (sendMsgBtn) {
        sendMsgBtn.addEventListener('click', function(e) {
            e.preventDefault();
            sendMessage();
        });
    }

    function sendMessage() {
        console.log('sendMessage() called');
        if (!textarea || !messageList) return;
        
        let content = textarea.value;
        if (!content) return;
        
        textarea.disabled = true;
        sendMsgBtn.disabled = true;
        
        setTimeout(() => {
            showTypingIndicator();
        }, 500);
        
        let formData = new FormData();
        formData.append('content', content);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        fetch(messageForm.dataset.sendUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Message sent response:', data);
            
            textarea.value = '';
            textarea.style.height = 'auto';
            textarea.disabled = false;
            sendMsgBtn.disabled = false;
            textarea.focus();
            
            if (data.success && data.data) {
                const message = data.data;
                let senderName = message.sender_type === 'customer' ? 'Customer' :
                                message.sender_type === 'ai' ? 'AI Assistant' :
                                message.user_name || 'Admin';
                
                const time = message.created_at || new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                const messageElement = createMessageElement(message.content, message.sender_type, senderName, time);
                
                messageList.appendChild(messageElement);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
                
                setTimeout(() => {
                    hideTypingIndicator();
                    refreshMessages();
                }, Math.random() * 2000 + 1000);
            } else {
                hideTypingIndicator();
                console.error('Error sending message:', data.error || 'Unknown error');
                alert('Error sending message. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
            hideTypingIndicator();
            textarea.disabled = false;
            sendMsgBtn.disabled = false;
            alert('Error sending message. Please try again.');
        });
    }
    
    function createMessageElement(content, senderType, senderName, time) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message mb-3 d-flex ${senderType === 'admin' || senderType === 'ai' ? 'justify-content-end' : 'justify-content-start'}`;
        
        const messageContent = document.createElement('div');
        messageContent.className = `message-content p-3 rounded-3 shadow-sm ${senderType === 'admin' || senderType === 'ai' ? 'bg-success text-white' : 'bg-white text-dark'}`;
        messageContent.style.maxWidth = '75%';
        messageContent.style.position = 'relative';
        messageContent.style.borderRadius = '12px';
        
        if (senderType === 'admin' || senderType === 'ai') {
            messageContent.style.borderTopRightRadius = '3px';
            messageContent.style.background = '#128C7E';
        } else {
            messageContent.style.borderTopLeftRadius = '3px';
        }
        
        if (senderName) {
            const senderNameDiv = document.createElement('div');
            senderNameDiv.className = `sender-name small mb-1 ${senderType === 'admin' || senderType === 'ai' ? 'text-white' : 'text-muted'}`;
            senderNameDiv.textContent = senderName;
            messageContent.appendChild(senderNameDiv);
        }
        
        const messageText = document.createElement('div');
        messageText.className = 'message-text';
        messageText.style.whiteSpace = 'pre-wrap';
        messageText.textContent = content;
        messageContent.appendChild(messageText);
        
        const messageTime = document.createElement('div');
        messageTime.className = `message-time small mt-1 text-end ${senderType === 'admin' || senderType === 'ai' ? 'text-white-50' : 'text-muted'}`;
        messageTime.textContent = time;
        
        if (senderType === 'admin' || senderType === 'ai') {
            const readReceipt = document.createElement('i');
            readReceipt.className = 'bi bi-check2-all ms-1';
            readReceipt.style.color = '#34B7F1';
            messageTime.appendChild(readReceipt);
        }
        
        messageContent.appendChild(messageTime);
        messageDiv.appendChild(messageContent);
        
        return messageDiv;
    }
    
    const statusForm = document.getElementById('statusForm');
    if (statusForm) {
        statusForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const statusBadge = document.querySelector('.chat-header .badge');
                    const newStatus = document.getElementById('chatStatus').value;
                    
                    statusBadge.className = 'badge text-white text-capitalize';
                    if (newStatus === 'closed') {
                        statusBadge.classList.add('bg-secondary');
                    } else if (newStatus === 'trial_scheduled') {
                        statusBadge.classList.add('bg-info');
                    } else {
                        statusBadge.classList.add('bg-success');
                    }
                    
                    statusBadge.textContent = newStatus.replace('_', ' ');
                    
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    
                    Toast.fire({
                        icon: 'success',
                        title: 'Chat status updated successfully'
                    });
                }
            })
            .catch(error => {
                console.error('Error updating status:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update status. Please try again.',
                    timer: 3000
                });
            });
        });
    }
    
    function refreshMessages() {
        if (!messageList) return;
        
        fetch(messageForm.dataset.getMessagesUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.messages && data.messages.length > 0) {
                messageList.innerHTML = '';
                data.messages.forEach(message => {
                    let senderName = message.sender_type === 'customer' ? 'Customer' :
                                    message.sender_type === 'ai' ? 'AI Assistant' :
                                    message.user_name || 'Admin';
                    const time = message.created_at || new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    const messageElement = createMessageElement(message.content, message.sender_type, senderName, time);
                    messageList.appendChild(messageElement);
                });
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        })
        .catch(error => {
            console.error('Error refreshing messages:', error);
        });
    }
    
    function showTypingIndicator() {
        if (typingIndicator) {
            typingIndicator.classList.remove('d-none');
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    }
    
    function hideTypingIndicator() {
        if (typingIndicator) {
            typingIndicator.classList.add('d-none');
        }
    }
    
    const onlineStatus = document.querySelector('.online-status');
    const lastSeen = document.querySelector('.last-seen');
    
    function simulateOnlineStatus() {
        if (Math.random() > 0.3) {
            onlineStatus.classList.remove('d-none');
            lastSeen.classList.add('d-none');
        } else {
            onlineStatus.classList.add('d-none');
            lastSeen.classList.remove('d-none');
        }
    }
    
    function scheduleStatusChange() {
        const delay = Math.floor(Math.random() * 30000) + 30000;
        setTimeout(() => {
            simulateOnlineStatus();
            scheduleStatusChange();
        }, delay);
    }
    
    simulateOnlineStatus();
    scheduleStatusChange();
    
    function loadChatSidebar() {
        const chatSidebarList = document.getElementById('chatSidebarList');
        if (!chatSidebarList) return;
        
        const urlParams = new URLSearchParams(window.location.search);
        const websiteId = urlParams.get('website_id');
        if (!websiteId) return;
        
        fetch(`/admin/chats/list?website_id=${websiteId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    chatSidebarList.innerHTML = '';
                    
                    if (data.chats.length === 0) {
                        chatSidebarList.innerHTML = '<div class="p-3 text-center text-muted">No chats found</div>';
                        return;
                    }
                    
                    data.chats.forEach(chat => {
                        const chatItem = document.createElement('div');
                        chatItem.className = 'chat-item p-3 border-bottom';
                        if (chat.id === {{ $chat->id }}) {
                            chatItem.classList.add('bg-light');
                        }
                        const badgeClass = chat.needs_human ? 'bg-warning' : 
                                        chat.status === 'closed' ? 'bg-secondary' :
                                        chat.status === 'trial_scheduled' ? 'bg-info' : 'bg-success';
                        chatItem.innerHTML = `
                            <a href="/admin/chats/${chat.id}?website_id=${websiteId}" class="text-decoration-none text-dark">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">${chat.customer_name || 'Customer'}</h6>
                                        <p class="mb-1 text-muted small">${chat.last_message || 'No messages'}</p>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">${formatTime(chat.last_activity_at)}</small>
                                        ${chat.unread_count > 0 ? `<span class="badge bg-primary rounded-pill ms-2">${chat.unread_count}</span>` : ''}
                                        <div>
                                            <span class="badge ${badgeClass}">${chat.needs_human ? 'Needs Human' : formatStatus(chat.status)}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        `;
                        chatSidebarList.appendChild(chatItem);
                    });
                }
            })
            .catch(error => {
                console.error('Error loading chat list:', error);
            });
    }
    
    function formatTime(timestamp) {
        if (!timestamp) return '';
        const date = new Date(timestamp);
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }
    
    function formatStatus(status) {
        if (!status) return 'Open';
        return status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
    }
    
    if (window.location.search.includes('website_id')) {
        loadChatSidebar();
        setInterval(loadChatSidebar, 30000);
    }
});
</script>
@endpush

@endsection