@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="flex h-[calc(100vh-12rem)]">
            <!-- Users List -->
            <div class="w-80 border-r border-gray-200 bg-white flex flex-col">
                <div class="p-4 border-b border-gray-200">
                    <h5 class="font-semibold flex items-center text-gray-800">
                        <i class="fas fa-comments text-indigo-600 mr-2"></i>
                        Chat List
                    </h5>
                </div>
                <div class="overflow-y-auto flex-1">
                    @if(Auth::guard('user')->check())
                        <div class="mb-4">
                            <h6 class="text-xs font-bold uppercase px-4 py-2 bg-gray-50 text-gray-600">Administrators</h6>
                            <div class="divide-y divide-gray-100">
                                @foreach($admins as $admin)
                                    <button class="w-full px-4 py-3 hover:bg-gray-50 transition-colors duration-150 user-select"
                                        data-user-type="admin"
                                        data-user-id="{{ $admin->id }}"
                                        data-user-name="{{ $admin->name }}">
                                        <div class="flex items-center relative">
                                            <div class="h-10 w-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-semibold">
                                                {{ substr($admin->name, 0, 1) }}
                                            </div>
                                            @if($admin->unread_count > 0)
                                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center notification-badge"
                                                    data-user-type="admin" 
                                                    data-user-id="{{ $admin->id }}">
                                                    {{ $admin->unread_count }}
                                                </span>
                                            @endif
                                            <div class="ml-3 text-left">
                                                <h6 class="text-sm font-medium text-gray-900">{{ $admin->name }}</h6>
                                                <p class="text-xs text-gray-500">Click to chat</p>
                                            </div>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="mb-4">
                            <h6 class="text-xs font-bold uppercase px-4 py-2 bg-gray-50 text-gray-600">Interns</h6>
                            <div class="divide-y divide-gray-100">
                                @foreach($interns as $intern)
                                    <button class="w-full px-4 py-3 hover:bg-gray-50 transition-colors duration-150 user-select"
                                        data-user-type="intern"
                                        data-user-id="{{ $intern->id }}"
                                        data-user-name="{{ $intern->name }}">
                                        <div class="flex items-center relative">
                                            <div class="h-10 w-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-semibold">
                                                {{ substr($intern->name, 0, 1) }}
                                            </div>
                                            @if($intern->unread_count > 0)
                                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center notification-badge"
                                                    data-user-type="intern" 
                                                    data-user-id="{{ $intern->id }}">
                                                    {{ $intern->unread_count }}
                                                </span>
                                            @endif
                                            <div class="ml-3 text-left">
                                                <h6 class="text-sm font-medium text-gray-900">{{ $intern->name }}</h6>
                                                <p class="text-xs text-gray-500">Click to chat</p>
                                            </div>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Chat Area -->
            <div class="flex-1 flex flex-col bg-gray-50">
                <!-- Chat Header -->
                <div id="chat-header" class="hidden p-4 bg-white border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-semibold">
                            <span id="chat-user-initial"></span>
                        </div>
                        <div class="ml-3">
                            <h5 id="chat-user-name" class="text-lg font-medium text-gray-900"></h5>
                            <div class="flex items-center">
                                <span class="h-2 w-2 rounded-full bg-green-500 mr-2"></span>
                                <span class="text-sm text-gray-500">Online</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Messages Area -->
                <div id="messages-container" class="hidden flex-1 overflow-y-auto p-4">
                    <div class="flex flex-col space-y-4">
                        <!-- Messages will be inserted here -->
                    </div>
                </div>

                <!-- Message Input -->
                <div id="message-form" class="hidden p-4 bg-white border-t border-gray-200">
                    <form id="send-message-form" class="flex items-center space-x-4">
                        <input type="hidden" id="receiver_type" name="receiver_type">
                        <input type="hidden" id="receiver_id" name="receiver_id">
                        <div class="flex-1">
                            <input type="text" 
                                id="message-input" 
                                name="content" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Type your message...">
                        </div>
                        <button type="submit" class="h-10 w-10 rounded-full bg-indigo-600 text-white flex items-center justify-center hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>

                <!-- No Chat Selected -->
                <div id="no-chat-selected" class="flex-1 flex items-center justify-center">
                    <div class="text-center">
                        <div class="h-20 w-20 rounded-full bg-white shadow-sm flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-comments text-gray-400 text-2xl"></i>
                        </div>
                        <h4 class="text-xl font-medium text-gray-900 mb-2">No conversation selected</h4>
                        <p class="text-gray-500">Choose a person from the list to start chatting</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize variables
    let currentChannel = null;
    let isSubscribed = false;
    const currentUserId = '{{ Auth::id() }}';
    const userType = '{{ Auth::guard("admin")->check() ? "admin" : "user" }}';
    let lastMessageId = null;

    // Initialize Echo
    function initializeEcho() {
        if (isSubscribed || !window.Echo) return;

        try {
            const channelName = `chat.${currentUserId}`;
            currentChannel = window.Echo.channel(channelName);

            currentChannel.listen('.MessageSent', (data) => {
                if (lastMessageId === data.message.id) return;
                
                lastMessageId = data.message.id;
                const currentReceiverId = document.getElementById('receiver_id').value;
                const currentReceiverType = document.getElementById('receiver_type').value;
                
                if ((data.message.sender_id == currentReceiverId && data.message.sender_type == currentReceiverType) || 
                    (data.message.receiver_id == currentReceiverId && data.message.receiver_type == currentReceiverType)) {
                    appendMessage(data.message);
                    scrollToBottom();
                    
                    if (data.message.receiver_id == currentUserId) {
                        markMessageAsRead(data.message.id);
                    }
                } else {
                    updateNotificationBadge(data.message.sender_type, data.message.sender_id);
                }
            });

            isSubscribed = true;
        } catch (error) {
            console.error('Error subscribing to channel:', error);
        }
    }

    if (window.Echo) {
        initializeEcho();
    } else {
        window.addEventListener('echoConnected', initializeEcho);
    }

    // Handle user selection
    document.querySelectorAll('.user-select').forEach(button => {
        button.addEventListener('click', function() {
            const userType = this.getAttribute('data-user-type');
            const userId = this.getAttribute('data-user-id');
            const userName = this.getAttribute('data-user-name');

            document.getElementById('chat-user-name').textContent = userName;
            document.getElementById('chat-user-initial').textContent = userName.charAt(0);
            document.getElementById('receiver_type').value = userType === 'intern' ? 'App\\Models\\User' : 'App\\Models\\Admin';
            document.getElementById('receiver_id').value = userId;

            document.getElementById('chat-header').classList.remove('hidden');
            document.getElementById('messages-container').classList.remove('hidden');
            document.getElementById('message-form').classList.remove('hidden');
            document.getElementById('no-chat-selected').classList.add('hidden');

            const badge = document.querySelector(`.notification-badge[data-user-type="${userType}"][data-user-id="${userId}"]`);
            if (badge) badge.remove();

            loadMessages(userId, userType);
        });
    });

    // Handle message sending
    document.getElementById('send-message-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const messageInput = document.getElementById('message-input');
        const content = messageInput.value.trim();
        if (!content) return;

        const formData = {
            content: content,
            receiver_type: document.getElementById('receiver_type').value,
            receiver_id: document.getElementById('receiver_id').value,
            _token: '{{ csrf_token() }}'
        };

        messageInput.value = '';

        fetch('{{ route("messages.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (lastMessageId !== data.message.id) {
                appendMessage(data.message);
                lastMessageId = data.message.id;
                scrollToBottom();
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
            messageInput.value = content;
            alert('Failed to send message. Please try again.');
        });
    });

    function loadMessages(userId, userType) {
        const messagesContainer = document.querySelector('#messages-container > div');
        messagesContainer.innerHTML = '';

        fetch(`/messages?user_id=${userId}&user_type=${userType}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            data.messages.forEach(message => appendMessage(message));
            scrollToBottom();
        })
        .catch(error => {
            console.error('Error loading messages:', error);
            alert('Failed to load messages. Please try again.');
        });
    }

    function appendMessage(message) {
        const currentUserType = '{{ Auth::guard("admin")->check() ? "App\\Models\\Admin" : "App\\Models\\User" }}';
        const currentUserId = parseInt('{{ Auth::id() }}');
        const isOwn = message.sender_type === currentUserType && parseInt(message.sender_id) === currentUserId;
        
        const messagesContainer = document.querySelector('#messages-container > div');
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex ${isOwn ? 'justify-end' : 'justify-start'} mb-4`;
        
        messageDiv.innerHTML = `
            <div class="max-w-[70%] ${isOwn ? 'items-end' : 'items-start'} flex flex-col">
                <div class="flex items-center mb-1 ${isOwn ? 'justify-end' : 'justify-start'} space-x-2">
                    <div class="flex items-center ${isOwn ? 'flex-row-reverse' : 'flex-row'} space-x-2">
                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full ${
                            isOwn ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800'
                        }">${message.sender_name.charAt(0)}</span>
                        <span class="text-xs text-gray-500">${isOwn ? 'You' : message.sender_name}</span>
                    </div>
                </div>
                <div class="${isOwn 
                    ? 'bg-indigo-600 text-white rounded-2xl rounded-tr-none' 
                    : 'bg-white text-gray-900 rounded-2xl rounded-tl-none'
                } px-4 py-2 shadow-sm">
                    ${message.content}
                </div>
                <div class="text-xs text-gray-400 mt-1 ${isOwn ? 'text-right' : 'text-left'}">
                    ${new Date(message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                    ${isOwn ? `
                        <svg class="inline-block h-4 w-4 text-blue-500 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    ` : ''}
                </div>
            </div>
        `;
        
        messagesContainer.appendChild(messageDiv);
    }

    function scrollToBottom() {
        const container = document.getElementById('messages-container');
        container.scrollTop = container.scrollHeight;
    }

    function markMessageAsRead(messageId) {
        fetch(`/messages/${messageId}/read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
    }

    function updateNotificationBadge(senderType, senderId) {
        fetch('/messages/unread-counts')
            .then(response => response.json())
            .then(counts => {
                Object.entries(counts).forEach(([key, count]) => {
                    const [type, id] = key.split('_');
                    let badge = document.querySelector(`.notification-badge[data-user-type="${type}"][data-user-id="${id}"]`);
                    
                    if (count > 0) {
                        if (badge) {
                            badge.textContent = count;
                        } else {
                            const userButton = document.querySelector(`.user-select[data-user-type="${type}"][data-user-id="${id}"] .relative`);
                            if (userButton) {
                                const newBadge = document.createElement('span');
                                newBadge.className = 'absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center notification-badge';
                                newBadge.setAttribute('data-user-type', type);
                                newBadge.setAttribute('data-user-id', id);
                                newBadge.textContent = count;
                                userButton.appendChild(newBadge);
                            }
                        }
                    } else if (badge) {
                        badge.remove();
                    }
                });
            });
    }

    // Start polling for unread messages
    setInterval(() => {
        updateNotificationBadge();
    }, 30000);
});
</script>

<style>
.user-select:hover {
    background-color: rgba(13, 110, 253, 0.05) !important;
}

.message {
    max-width: 80%;
    margin-bottom: 1rem;
}

.message.sent {
    margin-left: auto;
}

.message.received {
    margin-right: auto;
}

.message-content {
    padding: 0.75rem 1rem;
    border-radius: 1rem;
}

.message.sent .message-content {
    background-color: #0d6efd;
    color: white;
    border-top-right-radius: 0.25rem;
}

.message.received .message-content {
    background-color: white;
    border-top-left-radius: 0.25rem;
}

.message-time {
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

.message.sent .message-time {
    text-align: right;
}
</style>
@endpush

@endsection
