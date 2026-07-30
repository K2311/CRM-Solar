<x-app-layout title="Chat Inbox">
<style>
    .chat-container {
        display: flex;
        gap: 1.5rem;
        height: calc(100vh - 120px);
    }
    .chat-sidebar {
        flex: 0 0 300px;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }
    .chat-main {
        flex: 1;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        display: none; /* hidden by default */
    }
    .chat-main.active {
        display: flex;
    }
    .sidebar-header {
        padding: 1.5rem 1rem;
        border-bottom: 1px solid var(--border);
        font-weight: 700;
        font-size: 1.2rem;
        background: #f8f9fa;
        color: #111;
        border-top-left-radius: 12px;
    }
    .conv-item {
        padding: 1rem;
        border-bottom: 1px solid var(--border);
        cursor: pointer;
        transition: background 0.2s;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .conv-item:hover {
        background: #f0f2f5;
    }
    .conv-item.active {
        background: #ebebeb;
    }
    .conv-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: #dfe5e7;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .conv-details {
        flex: 1;
        overflow: hidden;
    }
    .conv-title {
        font-weight: 600;
        color: #111;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .conv-meta {
        font-size: 0.8rem;
        color: #667781;
        margin-top: 0.2rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    .chat-header {
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #075e54;
        color: white;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }
    .chat-header h3 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
        color: white;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .messages-area {
        flex: 1;
        padding: 1.5rem;
        overflow-y: auto;
        background: #efeae2;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .msg-wrapper {
        display: flex;
        width: 100%;
    }
    .msg-wrapper.user {
        justify-content: flex-start;
    }
    .msg-wrapper.admin, .msg-wrapper.ai {
        justify-content: flex-end;
    }
    .msg-bubble {
        max-width: 70%;
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        font-size: 0.95rem;
        position: relative;
        box-shadow: 0 1px 1px rgba(0,0,0,0.1);
        color: #111;
        line-height: 1.4;
    }
    .msg-bubble.user {
        background: #ffffff;
        border-top-left-radius: 0;
    }
    .msg-bubble.user::before {
        content: '';
        position: absolute;
        top: 0;
        left: -8px;
        width: 0;
        height: 0;
        border: 8px solid transparent;
        border-top-color: #ffffff;
        border-left: 0;
        margin-top: 0;
    }
    .msg-bubble.admin, .msg-bubble.ai {
        background: #dcf8c6;
        border-top-right-radius: 0;
    }
    .msg-bubble.admin::after, .msg-bubble.ai::after {
        content: '';
        position: absolute;
        top: 0;
        right: -8px;
        width: 0;
        height: 0;
        border: 8px solid transparent;
        border-top-color: #dcf8c6;
        border-right: 0;
        margin-top: 0;
    }
    .msg-sender {
        font-size: 0.7rem;
        color: #888;
        margin-bottom: 0.2rem;
        font-weight: 600;
        display: block;
    }
    .chat-input-area {
        padding: 0.75rem;
        display: flex;
        gap: 0.5rem;
        background: #f0f0f0;
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
        align-items: center;
    }
    .chat-input {
        flex: 1;
        padding: 0.75rem 1rem;
        border: none;
        border-radius: 20px;
        outline: none;
        font-family: inherit;
        font-size: 0.95rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .chat-input:focus {
        border-color: transparent;
    }
    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        font-family: inherit;
        transition: opacity 0.2s;
    }
    .btn:hover {
        opacity: 0.9;
    }
    .btn-primary {
        background: #128c7e;
        color: white;
        border-radius: 50%;
        width: 45px;
        height: 45px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    .btn-danger {
        background: #ef4444;
        color: white;
    }
    .btn-success {
        background: #10b981;
        color: white;
    }
    
    .back-btn {
        display: none;
        background: transparent;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0;
        margin-right: 0.5rem;
    }

    @media (max-width: 768px) {
        .chat-container {
            flex-direction: column;
            gap: 0;
            height: calc(100vh - 120px);
        }
        .chat-sidebar {
            flex: 1;
            width: 100%;
            border-radius: 12px;
            display: flex !important;
        }
        .chat-sidebar.hidden-mobile {
            display: none !important;
        }
        .chat-main {
            display: none !important;
            width: 100%;
            flex: 1;
            border-radius: 12px;
        }
        .chat-main.active-mobile {
            display: flex !important;
        }
        .back-btn {
            display: block !important;
        }
        .sidebar-header {
            font-size: 1.1rem;
            padding: 1rem;
        }
    }
</style>

<div class="chat-container">
    <!-- Sidebar -->
    <div class="chat-sidebar">
        <div class="sidebar-header">Conversations</div>
        @if($conversations->isEmpty())
            <div style="padding: 2rem 1rem; text-align: center; color: var(--text-muted); font-size: 0.9rem;">
                No conversations yet.
            </div>
        @else
            @foreach($conversations as $conv)
                <div class="conv-item" id="conv-item-{{ $conv->id }}" onclick="loadConversation({{ $conv->id }})">
                    <div class="conv-avatar">
                        <i class="bi bi-person-fill" style="color: #aebac1;"></i>
                    </div>
                    <div class="conv-details">
                        <div class="conv-title">{{ $conv->phone_number }}</div>
                        @if($conv->lead)
                            <div class="conv-meta">
                                <i class="bi bi-person-badge"></i> {{ $conv->lead->first_name }}
                            </div>
                        @endif
                        <div class="conv-meta" style="color: {{ $conv->ai_paused_at ? '#ef4444' : '#10b981' }}">
                            @if($conv->ai_paused_at)
                                <i class="bi bi-pause-circle-fill"></i> AI Paused
                            @else
                                <i class="bi bi-robot"></i> AI Active
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <!-- Empty State -->
    <div class="chat-main" id="empty-state" style="display: flex; align-items: center; justify-content: center; background: #efeae2; color: #555;">
        <div style="text-align: center; background: rgba(255,255,255,0.6); padding: 2rem; border-radius: 12px;">
            <i class="bi bi-whatsapp" style="font-size: 3rem; color: #128c7e; margin-bottom: 1rem; display: block;"></i>
            <h3 style="margin: 0;">Select a conversation to start messaging</h3>
        </div>
    </div>

    <!-- Main Chat -->
    <div class="chat-main" id="chat-window" style="display: none;">
        <div class="chat-header">
            <h3 id="chat-title">
                <button class="back-btn" onclick="showSidebar()"><i class="bi bi-arrow-left"></i></button>
                <span id="chat-title-text">Select a conversation</span>
            </h3>
            <button id="toggle-ai-btn" class="btn btn-danger" style="display: none; padding: 0.5rem 1rem; font-size: 0.85rem;">
                Pause AI
            </button>
        </div>
        
        <div class="messages-area" id="messages-container">
            <!-- Messages -->
        </div>

        <div class="chat-input-area">
            <input type="text" id="message-input" class="chat-input" placeholder="Type a manual reply...">
            <button id="send-btn" class="btn btn-primary"><i class="bi bi-send-fill" style="margin-left: -2px;"></i></button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentConversationId = null;

    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const convId = urlParams.get('conversation_id');
        if (convId) {
            loadConversation(convId);
        }
    });

    async function loadConversation(id) {
        currentConversationId = id;
        
        // Highlight active conversation
        document.querySelectorAll('.conv-item').forEach(el => el.classList.remove('active'));
        const activeItem = document.getElementById(`conv-item-${id}`);
        if(activeItem) activeItem.classList.add('active');

        document.getElementById('empty-state').style.display = 'none';
        document.getElementById('chat-window').style.display = 'flex';

        // On mobile, hide sidebar when chat is opened
        if (window.innerWidth <= 768) {
            document.querySelector('.chat-sidebar').classList.add('hidden-mobile');
            document.getElementById('chat-window').classList.add('active-mobile');
        }
        
        const response = await fetch(`/marketing/chat/${id}`);
        const data = await response.json();
        
        document.getElementById('chat-title-text').innerText = data.conversation.phone_number;
        
        const toggleBtn = document.getElementById('toggle-ai-btn');
        toggleBtn.style.display = 'block';
        
        if (data.conversation.ai_paused_at) {
            toggleBtn.innerText = 'Resume AI';
            toggleBtn.className = 'btn btn-success';
        } else {
            toggleBtn.innerText = 'Pause AI';
            toggleBtn.className = 'btn btn-danger';
        }

        const msgContainer = document.getElementById('messages-container');
        msgContainer.innerHTML = '';
        
        data.messages.forEach(msg => {
            const isUser = msg.sender_type === 'user';
            const wrapperClass = isUser ? 'user' : 'admin';
            const bubbleClass = msg.sender_type; // 'user', 'admin', 'ai'
            
            const div = document.createElement('div');
            div.className = `msg-wrapper ${wrapperClass}`;
            div.innerHTML = `<div class="msg-bubble ${bubbleClass}">
                <div class="msg-sender">${msg.sender_type}</div>
                <div>${msg.message_text}</div>
            </div>`;
            msgContainer.appendChild(div);
        });
        
        msgContainer.scrollTop = msgContainer.scrollHeight;
    }

    document.getElementById('toggle-ai-btn').addEventListener('click', async () => {
        if (!currentConversationId) return;
        
        const response = await fetch(`/marketing/chat/${currentConversationId}/toggle-ai`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        });
        const data = await response.json();
        if (data.success) {
            loadConversation(currentConversationId);
        }
    });

    document.getElementById('send-btn').addEventListener('click', async () => {
        if (!currentConversationId) return;
        
        const input = document.getElementById('message-input');
        const message = input.value.trim();
        if (!message) return;
        
        input.value = '';
        input.disabled = true;
        
        await fetch(`/marketing/chat/${currentConversationId}/send`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ message: message })
        });
        
        input.disabled = false;
        loadConversation(currentConversationId);
    });

    document.getElementById('message-input').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('send-btn').click();
        }
    });

    function showSidebar() {
        document.querySelector('.chat-sidebar').classList.remove('hidden-mobile');
        document.getElementById('chat-window').classList.remove('active-mobile');
        document.getElementById('chat-window').style.display = 'none';
        document.getElementById('empty-state').style.display = window.innerWidth <= 768 ? 'none' : 'flex';
        currentConversationId = null;
        document.querySelectorAll('.conv-item').forEach(el => el.classList.remove('active'));
    }
</script>
@endpush
</x-app-layout>
