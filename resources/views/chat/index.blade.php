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
                                                    <span class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 group-hover:bg-indigo-200 transition-colors duration-200 shadow-sm">
                                                        <span class="text-base font-semibold text-indigo-800">{{ substr($intern->name, 0, 1) }}</span>
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
                                                    <span class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 group-hover:bg-indigo-200 transition-colors duration-200 shadow-sm">
                                                        <span class="text-base font-semibold text-indigo-800">{{ substr($admin->name, 0, 1) }}</span>
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
<!-- Pusher Script -->
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Pusher
    const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
        cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
        encrypted: true,
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-Token': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        }
    });

    // Subscribe to private channel for current user
    const currentUserId = '{{ Auth::guard("admin")->check() ? Auth::guard("admin")->id() : Auth::id() }}';
    const channel = pusher.subscribe(`private-chat.${currentUserId}`);
    
    // Debug connection status
    pusher.connection.bind('connected', () => {
        console.log('Connected to Pusher');
    });

    channel.bind('pusher:subscription_succeeded', () => {
        console.log('Successfully subscribed to channel');
    });

    pusher.connection.bind('error', error => {
        console.error('Pusher connection error:', error);
    });

    // Listen for messages
    channel.bind('MessageSent', function(data) {
        console.log('Received message:', data);
        if (data.message) {
            const currentReceiverId = document.getElementById('receiver_id').value;
            // Only append message if it's from the current chat
            if (data.message.sender_id == currentReceiverId || data.message.receiver_id == currentReceiverId) {
                appendMessage(data.message);
                scrollToBottom();
            }
        }
    });

    // Handle subscription error
    channel.bind('pusher:subscription_error', function(status) {
        console.error('Pusher subscription error:', status);
    });

    // Handle user selection
    const userButtons = document.querySelectorAll('.user-select');
    userButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userType = this.getAttribute('data-user-type');
            const userId = this.getAttribute('data-user-id');
            const userName = this.getAttribute('data-user-name');

            // Update hidden fields
            document.getElementById('receiver_type').value = userType === 'intern' ? 'App\\Models\\User' : 'App\\Models\\Admin';
            document.getElementById('receiver_id').value = userId;

            // Update chat header
            document.getElementById('chat-user-name').textContent = userName;
            document.getElementById('chat-user-initial').textContent = userName.charAt(0);

            // Show chat interface
            document.getElementById('chat-header').classList.remove('hidden');
            document.getElementById('messages-container').classList.remove('hidden');
            document.getElementById('message-form').classList.remove('hidden');
            document.getElementById('no-chat-selected').classList.add('hidden');

            // Load messages for selected user
            loadMessages(userId, userType);
        });
    });

    function loadMessages(userId, userType) {
        const messagesContainer = document.querySelector('#messages-container > div');
        messagesContainer.innerHTML = ''; // Clear existing messages

        fetch(`/messages?user_id=${userId}&user_type=${userType}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            window.currentUser = data.current_user;
            data.messages.forEach(message => {
                appendMessage(message);
            });
            scrollToBottom();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load messages. Please try again.');
        });
    }

    // Handle message form submission
    const messageForm = document.getElementById('send-message-form');
    messageForm.addEventListener('submit', function(e) {
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

        // Send message using fetch API
        fetch('{{ route("messages.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Update current user if needed
            if (data.current_user) {
                window.currentUser = data.current_user;
            }
            
            // Clear input
            messageInput.value = '';
            
            // Add message to chat
            appendMessage(data.message);

            // Scroll to bottom
            scrollToBottom();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to send message. Please try again.');
        });
    });

    function scrollToBottom() {
        const container = document.getElementById('messages-container');
        const messagesDiv = container.querySelector('div');
        container.scrollTop = messagesDiv.scrollHeight;
    }

    function appendMessage(message) {
        const isOwn = message.sender_type === window.currentUser.type && message.sender_id === window.currentUser.id;
        const messagesContainer = document.querySelector('#messages-container > div');
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex ${isOwn ? 'justify-end' : 'justify-start'} mb-4`;
        
        const messageContent = `
            <div class="max-w-[70%] ${isOwn ? 'items-end' : 'items-start'} flex flex-col">
                <div class="flex items-center mb-1 ${isOwn ? 'justify-end' : 'justify-start'}">
                    <span class="text-xs text-gray-500">${isOwn ? 'You' : message.sender_name}</span>
                </div>
                <div class="${isOwn 
                    ? 'bg-indigo-600 text-white rounded-2xl rounded-tr-none' 
                    : 'bg-gray-100 text-gray-900 rounded-2xl rounded-tl-none'
                } px-4 py-2 break-words shadow-sm">
                    ${message.content}
                </div>
                <div class="text-xs text-gray-400 mt-1 ${isOwn ? 'text-right' : 'text-left'}">
                    ${new Date(message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                </div>
            </div>
        `;
        
        messageDiv.innerHTML = messageContent;
        messagesContainer.appendChild(messageDiv);
        
        // Add a small delay to ensure the DOM has updated
        setTimeout(scrollToBottom, 100);
    }

    // Add mutation observer to handle dynamic content changes
    const messagesContainer = document.getElementById('messages-container');
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                scrollToBottom();
            }
        });
    });

    observer.observe(messagesContainer, {
        childList: true,
        subtree: true
    });
});
</script>
@endpush

@endsection
