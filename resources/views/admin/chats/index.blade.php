@extends('layouts.admin')

@section('content')
<div class="container-fluid h-100">
    <div class="row h-100">
        <!-- Chat List Sidebar -->
        <div class="col-md-4 col-lg-3 bg-light p-0 border-end" style="height: calc(100vh - 56px); overflow-y: auto;">
            <!-- Filter Controls -->
            <div class="p-3 border-bottom sticky-top bg-light">
                <h4 class="fw-bold mb-3">Chat Conversations</h4>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="chatSearch" placeholder="Search chats...">
                    <button class="btn btn-outline-secondary" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-filter"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown">
                        <li><h6 class="dropdown-header">Filter by Status</h6></li>
                        <li><a class="dropdown-item filter-item" href="#" data-filter="all">All Chats</a></li>
                        <li><a class="dropdown-item filter-item" href="#" data-filter="open">Open</a></li>
                        <li><a class="dropdown-item filter-item" href="#" data-filter="trial_scheduled">Trial Scheduled</a></li>
                        <li><a class="dropdown-item filter-item" href="#" data-filter="closed">Closed</a></li>
                    </ul>
                </div>
            </div>
            
            <!-- Chat List -->
            <div class="chat-list" id="chatList">
                @if($chats->count() === 0)
                    <div class="alert alert-info m-3">
                        No chats found.
                    </div>
                @else
                    @foreach($chats as $chat)
                        @php
                            $status = $chat->status ?? 'open';
                            $badgeClass = match($status) {
                                'closed' => 'bg-secondary',
                                'trial_scheduled' => 'bg-info',
                                default => 'bg-success', // for "open" or fallback
                            };
                            $lastMessage = $chat->messages->last();
                            $lastMessagePreview = $lastMessage ? Str::limit($lastMessage->content, 30) : 'No messages yet';
                            $lastMessageTime = $lastMessage ? $lastMessage->created_at->format('H:i') : '';
                            $unreadCount = $chat->messages->where('is_read', false)->where('sender_type', 'customer')->count();
                        @endphp
                        <a href="{{ route('admin.chats.show', $chat->id) }}"
                           class="chat-item d-flex p-3 border-bottom text-decoration-none text-dark position-relative"
                           data-status="{{ $chat->status }}">
                            <div class="chat-avatar bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                style="width: 50px; height: 50px; flex-shrink: 0;">
                                {{ strtoupper(substr(optional($chat->website)->name ?? 'Chat', 0, 1)) }}
                            </div>
                            <div class="chat-details flex-grow-1 overflow-hidden">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0 text-truncate">Chat #{{ $chat->id }}</h6>
                                    <small class="text-muted">{{ $lastMessageTime }}</small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="mb-0 text-truncate text-muted small">{{ $lastMessagePreview }}</p>
                                    <div class="d-flex align-items-center">
                                        <span class="badge {{ $badgeClass }} text-white me-2">
                                            {{ str_replace('_', ' ', $status) }}
                                        </span>
                                        @if($unreadCount > 0)
                                            <span class="badge bg-danger rounded-pill">{{ $unreadCount }}</span>
                                        @endif
                                    </div>
                                </div>
                                <small class="text-muted">{{ optional($chat->website)->name }}</small>
                            </div>
                        </a>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Chat Content Area -->
        <div class="col-md-8 col-lg-9 d-flex flex-column p-0" style="height: calc(100vh - 56px);">
            <div class="chat-placeholder d-flex flex-column justify-content-center align-items-center h-100 bg-light">
                <div class="text-center p-4">
                    <i class="bi bi-chat-dots text-primary" style="font-size: 5rem;"></i>
                    <h2 class="mt-4">Welcome to Chat Management</h2>
                    <p class="text-muted">Select a chat from the left to view the conversation.</p>
                </div>
            </div>
            
            <!-- Selected chat will be loaded here via AJAX -->
            <div id="selectedChatContainer" class="h-100 d-none">
                <!-- Chat content will be loaded here -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chat filtering
        const chatItems = document.querySelectorAll('.chat-item');
        const filterItems = document.querySelectorAll('.filter-item');
        const searchInput = document.getElementById('chatSearch');

        // Filter by status
        filterItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const filter = this.dataset.filter;

                chatItems.forEach(chat => {
                    chat.classList.toggle('d-none', !(filter === 'all' || chat.dataset.status === filter));
                });
            });
        });

        // Search functionality
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();

            chatItems.forEach(chat => {
                const chatText = chat.textContent.toLowerCase();
                chat.classList.toggle('d-none', !chatText.includes(searchTerm));
            });
        });

        // Load chat via AJAX when clicked
        chatItems.forEach(chat => {
            chat.addEventListener('click', function(e) {
                e.preventDefault();

                // Add active class to selected chat
                chatItems.forEach(c => c.classList.remove('active', 'bg-light'));
                this.classList.add('active', 'bg-light');

                // Show loading state
                document.querySelector('.chat-placeholder').classList.add('d-none');
                const selectedChatContainer = document.getElementById('selectedChatContainer');
                selectedChatContainer.classList.remove('d-none');
                selectedChatContainer.innerHTML = '<div class="d-flex justify-content-center align-items-center h-100"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';

                // Load chat content
                fetch(this.href)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const chatContent = doc.querySelector('.chat-content-container');

                        if (chatContent) {
                            selectedChatContainer.innerHTML = chatContent.innerHTML;

                            // Scroll to bottom of messages
                            const messagesContainer = selectedChatContainer.querySelector('.messages-container');
                            if (messagesContainer) {
                                messagesContainer.scrollTop = messagesContainer.scrollHeight;
                            }

                            // Initialize message form
                            const messageForm = selectedChatContainer.querySelector('#messageForm');
                            if (messageForm) {
                                messageForm.addEventListener('submit', function(e) {
                                    e.preventDefault();

                                    const formData = new FormData(this);
                                    fetch(this.action, {
                                        method: 'POST',
                                        body: formData,
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            // Reload chat content
                                            chat.click();
                                            this.reset();
                                        }
                                    });
                                });
                            }
                        }
                    });
            });
        });

        // Auto-load first chat if available
        if (chatItems.length > 0) {
            chatItems[0].click();
        }
    });
</script>
@endpush
@endsection