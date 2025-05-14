@extends('Layouts.app')

@section('content')

<div class="py-8">
    <div class="max-w-full mx-auto px-4">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-4 lg:p-6 bg-white border-b border-gray-200">
                <!-- Chat container with fixed dimensions -->
                <div class="flex h-[600px] rounded-xl shadow-2xl overflow-hidden">
                    <!-- Users List with fixed width -->
                    <div class="w-[300px] border-r border-gray-200 bg-gray-50 rounded-l-xl flex flex-col overflow-hidden">
                        <h3 class="text-lg font-semibold text-gray-900 p-4 sticky top-0 bg-gray-50/95 backdrop-blur-sm border-b border-gray-200 z-10 flex items-center">
                            <svg class="h-5 w-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                            </svg>
                            Chat List
                        </h3>

                        <div class="flex-1 overflow-y-auto">
                            @if($userType === 'admin')
                                <div class="mb-6">
                                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-2 bg-gray-100/80 backdrop-blur-sm sticky top-0 z-[5]">Interns</h4>
                                    <div class="space-y-1">
                                        @foreach($interns as $intern)
                                            <button 
                                                class="w-full text-left px-4 py-3 hover:bg-indigo-50/80 transition-all duration-200 user-select group relative"
                                                data-user-type="intern"
                                                data-user-id="{{ $intern->id }}"
                                                data-user-name="{{ $intern->name }}"
                                            >
                                                <div class="flex items-center space-x-3">
                                                    <span class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 group-hover:bg-indigo-200 transition-colors duration-200 shadow-sm relative">
                                                        <span class="text-base font-semibold text-indigo-800">{{ substr($intern->name, 0, 1) }}</span>
                                                        @if($intern->unread_count > 0)
                                                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center notification-badge" data-user-type="intern" data-user-id="{{ $intern->id }}">
                                                                {{ $intern->unread_count }}
                                                            </span>
                                                        @endif
                                                    </span>
                                                    <div>
                                                        <span class="text-sm font-medium text-gray-900 group-hover:text-indigo-600">{{ $intern->name }}</span>
                                                        <p class="text-xs text-gray-500">Click to chat</p>
                                                    </div>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="mb-6">
                                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 py-2 bg-gray-100/80 backdrop-blur-sm sticky top-0 z-[5]">Administrators</h4>
                                    <div class="space-y-1">
                                        @foreach($admins as $admin)
                                            <button 
                                                class="w-full text-left px-4 py-3 hover:bg-indigo-50/80 transition-all duration-200 user-select group relative"
                                                data-user-type="admin"
                                                data-user-id="{{ $admin->id }}"
                                                data-user-name="{{ $admin->name }}"
                                            >
                                                <div class="flex items-center space-x-3">
                                                    <span class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 group-hover:bg-indigo-200 transition-colors duration-200 shadow-sm relative">
                                                        <span class="text-base font-semibold text-indigo-800">{{ substr($admin->name, 0, 1) }}</span>
                                                        @if($admin->unread_count > 0)
                                                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center notification-badge" data-user-type="admin" data-user-id="{{ $admin->id }}">
                                                                {{ $admin->unread_count }}
                                                            </span>
                                                        @endif
                                                    </span>
                                                    <div>
                                                        <span class="text-sm font-medium text-gray-900 group-hover:text-indigo-600">{{ $admin->name }}</span>
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
                    <div class="flex-1 flex flex-col bg-white rounded-r-xl overflow-hidden">
                        <!-- Header -->
                        <div id="chat-header" class="px-6 py-4 border-b border-gray-200 hidden bg-white z-10 shadow-sm flex-shrink-0">
                            <div class="flex items-center space-x-4">
                                <span class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 shadow-md transition-colors duration-200">
                                    <span id="chat-user-initial" class="text-lg font-semibold text-indigo-800"></span>
                                </span>
                                <div>
                                    <h3 id="chat-user-name" class="text-lg font-semibold text-gray-900"></h3>
                                    <div class="flex items-center">
                                        <span class="h-2 w-2 rounded-full bg-green-500 mr-2"></span>
                                        <p class="text-xs text-gray-500">Online</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Messages -->
                        <div id="messages-container" class="flex-1 overflow-y-auto px-4 py-3 hidden bg-gray-50/50 w-full" style="scroll-behavior: smooth;">
                            <div class="flex flex-col space-y-2 min-h-full w-full">
                                <!-- Messages will appear here -->
                            </div>
                        </div>

                        <!-- Message Form -->
                        <div id="message-form" class="p-4 border-t border-gray-200 hidden bg-white z-10 shadow-inner flex-shrink-0">
                            <form id="send-message-form" class="flex items-center space-x-2">
                                <input type="hidden" id="receiver_type" name="receiver_type">
                                <input type="hidden" id="receiver_id" name="receiver_id">
                                <div class="flex-1 relative">
                                    <input 
                                        type="text" 
                                        id="message-input" 
                                        name="content" 
                                        class="w-full rounded-lg border border-gray-300 px-4 py-3 shadow-sm focus:border-blue-400 focus:ring-1 focus:ring-blue-200 focus:ring-opacity-50 transition-all duration-200 outline-none text-sm"
                                        placeholder="Type your message..."
                                    >
                                </div>
                                <button 
                                    type="submit"
                                    class="inline-flex items-center justify-center w-12 h-12 bg-indigo-600 rounded-full text-white hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-md"
                                >
                                    <svg class="h-5 w-5 rotate-90" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                                    </svg>
                                </button>
                            </form>
                        </div>

                        <!-- No Chat Selected -->
                        <div id="no-chat-selected" class="flex-1 flex items-center justify-center bg-gray-50/50">
                            <div class="text-center space-y-4 p-6 max-w-sm mx-auto">
                                <div class="mx-auto h-20 w-20 text-gray-400 bg-gray-100 rounded-full flex items-center justify-center shadow-inner">
                                    <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-900">No conversation selected</h3>
                                    <p class="text-sm text-gray-500 mt-1">Choose a person from the list to start chatting</p>
                                </div>
                            </div>
                        </div>
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
    const userType = '{{ $userType }}';
    let lastMessageId = null; // Track last message to prevent duplicates

    // Initialize Echo
    function initializeEcho() {
        if (isSubscribed || !window.Echo) {
            return;
        }

        try {
            // Subscribe to private channel for receiving messages
            const channelName = `chat.${currentUserId}`;
            currentChannel = window.Echo.channel(channelName);

            // Listen for new messages
            currentChannel.listen('.MessageSent', (data) => {
                console.group('📨 New Message Received');
                console.log('Message Data:', data);
                
                // Prevent duplicate messages
                if (lastMessageId === data.message.id) {
                    console.log('⚠️ Duplicate message detected - ignoring');
                    console.groupEnd();
                    return;
                }
                lastMessageId = data.message.id;
                
                const currentReceiverId = document.getElementById('receiver_id').value;
                const currentReceiverType = document.getElementById('receiver_type').value;
                
                if ((data.message.sender_id == currentReceiverId && data.message.sender_type == currentReceiverType) || 
                    (data.message.receiver_id == currentReceiverId && data.message.receiver_type == currentReceiverType)) {
                    console.log('✅ Message belongs to current chat - displaying');
                    appendMessage(data.message);
                    scrollToBottom();
                    
                    // Mark message as read if it's incoming
                    if (data.message.receiver_id == currentUserId) {
                        markMessageAsRead(data.message.id);
                    }
                } else {
                    console.log('ℹ️ Message not for current chat - updating badge');
                    updateNotificationBadge(data.message.sender_type, data.message.sender_id);
                }
                console.groupEnd();
            });

            // Monitor connection state
            window.Echo.connector.pusher.connection.bind('state_change', (states) => {
                console.group('🔄 Connection State Change');
                console.log('Previous:', states.previous);
                console.log('Current:', states.current);
                console.groupEnd();
            });

            isSubscribed = true;
            console.log('✅ Echo channel subscription successful');
        } catch (error) {
            console.error('❌ Error subscribing to channel:', error);
        }
    }

    // Initialize Echo when page loads and Echo is available
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

            // Update UI
            document.getElementById('chat-user-name').textContent = userName;
            document.getElementById('chat-user-initial').textContent = userName.charAt(0);
            document.getElementById('receiver_type').value = userType === 'intern' ? 'App\\Models\\User' : 'App\\Models\\Admin';
            document.getElementById('receiver_id').value = userId;

            // Show chat interface
            document.getElementById('chat-header').classList.remove('hidden');
            document.getElementById('messages-container').classList.remove('hidden');
            document.getElementById('message-form').classList.remove('hidden');
            document.getElementById('no-chat-selected').classList.add('hidden');

            // Remove notification badge
            const badge = document.querySelector(`.notification-badge[data-user-type="${userType}"][data-user-id="${userId}"]`);
            if (badge) {
                badge.remove();
            }

            // Load messages
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

        // Clear input immediately for better UX
        messageInput.value = '';

        // Send message
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
            // Only append the message if it's not already displayed
            if (lastMessageId !== data.message.id) {
                appendMessage(data.message);
                lastMessageId = data.message.id;
                scrollToBottom();
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
            messageInput.value = content; // Restore message on error
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
            data.messages.forEach(message => {
                appendMessage(message);
            });
            scrollToBottom();
        })
        .catch(error => {
            console.error('Error loading messages:', error);
            alert('Failed to load messages. Please try again.');
        });
    }

    function appendMessage(message) {
        const currentUserType = '{{ $userType }}' === 'admin' ? 'App\\Models\\Admin' : 'App\\Models\\User';
        const currentUserId = parseInt('{{ Auth::id() }}');
        
        // Debug log to see message details
        console.log('Message comparison:', {
            message_sender_type: message.sender_type,
            message_sender_id: message.sender_id,
            current_user_type: currentUserType,
            current_user_id: currentUserId
        });

        const isOwn = message.sender_type === currentUserType && 
                     parseInt(message.sender_id) === currentUserId;
        
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
                    ? 'bg-indigo-600 text-white rounded-2xl rounded-tr-none ml-auto' 
                    : 'bg-gray-100 text-gray-900 rounded-2xl rounded-tl-none mr-auto'
                } px-4 py-2 break-words shadow-sm relative">
                    ${message.content}
                    <div class="absolute ${isOwn ? '-left-2' : '-right-2'} top-0 
                        ${isOwn ? 'border-r-indigo-600' : 'border-l-gray-100'} 
                        border-t-transparent border-b-transparent 
                        ${isOwn ? 'border-r-[10px]' : 'border-l-[10px]'} border-t-[10px] border-b-[10px]">
                    </div>
                </div>
                <div class="text-xs text-gray-400 mt-1 ${isOwn ? 'text-right' : 'text-left'} flex items-center ${isOwn ? 'justify-end' : 'justify-start'} space-x-2">
                    <span>${new Date(message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>
                    ${isOwn ? `
                        <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            const userButton = document.querySelector(`.user-select[data-user-type="${type}"][data-user-id="${id}"] .inline-flex`);
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
    }, 30000); // Every 30 seconds
});
</script>

<style>
/* Enhanced scrollbar styling */
#messages-container {
    scrollbar-width: thin;
    scrollbar-color: rgba(156, 163, 175, 0.5) rgba(229, 231, 235, 0.3);
}

#messages-container::-webkit-scrollbar {
    width: 6px;
}

#messages-container::-webkit-scrollbar-track {
    background: rgba(229, 231, 235, 0.3);
}

#messages-container::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.5);
    border-radius: 3px;
}

/* Message animations */
.message-enter {
    opacity: 0;
    transform: translateY(20px);
}

.message-enter-active {
    opacity: 1;
    transform: translateY(0);
    transition: opacity 300ms, transform 300ms;
}

/* Ensure proper container heights */
.flex.h-\[600px\] {
    height: 600px;
    max-height: 600px;
    min-height: 600px;
}

#messages-container {
    height: 100%;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}

#messages-container > div {
    width: 100%;
    padding-bottom: 15px;
}
</style>
@endpush

@endsection
