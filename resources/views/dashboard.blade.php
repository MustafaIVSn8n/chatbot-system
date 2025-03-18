<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Main Content -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                    
                    <!-- Notifications Container -->
                    <div id="notification-container" class="mt-4 space-y-2">
                        <!-- Loading State -->
                        <div id="notification-loading" class="text-gray-500">
                            Loading notifications...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notificationContainer = document.getElementById('notification-container');
            const loadingIndicator = document.getElementById('notification-loading');
            let isFetching = false;

            async function fetchNotifications() {
                if (isFetching) return;
                isFetching = true;
                
                try {
                    const response = await fetch('/notifications');
                    if (!response.ok) throw new Error('Network response was not ok');
                    
                    const notifications = await response.json();
                    notificationContainer.innerHTML = '';

                    if (notifications.length === 0) {
                        notificationContainer.innerHTML = `
                            <div class="bg-blue-50 p-3 rounded-md text-blue-600">
                                No new notifications
                            </div>
                        `;
                        return;
                    }

                    notifications.forEach(notification => {
                        const notificationElement = document.createElement('div');
                        notificationElement.className = `p-3 rounded-md ${
                            notification.type === 'urgent' ? 'bg-red-50 text-red-700' :
                            notification.type === 'info' ? 'bg-blue-50 text-blue-700' :
                            'bg-gray-50 text-gray-700'
                        }`;
                        notificationElement.innerHTML = `
                            <div class="flex justify-between items-center">
                                <div>
                                    <strong class="font-medium">${escapeHtml(notification.type)}:</strong>
                                    <span class="ml-2">${escapeHtml(notification.message)}</span>
                                </div>
                                <button 
                                    onclick="markAsRead('${notification.id}')" 
                                    class="ml-4 text-gray-400 hover:text-gray-600"
                                >
                                    ×
                                </button>
                            </div>
                        `;
                        notificationContainer.appendChild(notificationElement);
                    });

                    // Batch mark as read
                    await markAllAsRead(notifications);

                } catch (error) {
                    console.error('Error:', error);
                    notificationContainer.innerHTML = `
                        <div class="bg-red-50 p-3 rounded-md text-red-600">
                            Error loading notifications
                        </div>
                    `;
                } finally {
                    loadingIndicator.style.display = 'none';
                    isFetching = false;
                }
            }

            async function markAllAsRead(notifications) {
                try {
                    await fetch('/notifications/markAllAsRead', {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            ids: notifications.map(n => n.id)
                        })
                    });
                } catch (error) {
                    console.error('Error marking notifications as read:', error);
                }
            }

            function escapeHtml(unsafe) {
                return unsafe.toString()
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }

            // Polling with backoff
            let retryDelay = 5000;
            function pollNotifications() {
                fetchNotifications()
                    .then(() => {
                        retryDelay = 5000; // Reset delay on success
                        setTimeout(pollNotifications, retryDelay);
                    })
                    .catch(() => {
                        retryDelay = Math.min(retryDelay * 2, 30000); // Exponential backoff
                        setTimeout(pollNotifications, retryDelay);
                    });
            }

            // Initial fetch
            pollNotifications();
        });
    </script>
</x-app-layout>