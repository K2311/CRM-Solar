

<!-- Simulator Slide-over Panel -->
<div id="ai-simulator-panel" class="ai-simulator-closed">
    <div style="padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center; background: #075e54; color: white;">
        <h3 style="margin: 0; font-size: 1.1rem; color: white; display: flex; align-items: center; gap: 0.5rem;"><i class="bi bi-whatsapp"></i> AI Auto-Chat</h3>
        <button onclick="toggleAiSimulator()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: white; opacity: 0.8;">&times;</button>
    </div>
    
    <div id="sim-messages" style="flex: 1; padding: 1rem; overflow-y: auto; display: flex; flex-direction: column; gap: 1rem;">
        <div style="text-align: center; color: #555; font-size: 0.85rem; margin-top: 1rem; background: rgba(255,255,255,0.6); padding: 0.5rem; border-radius: 8px; align-self: center;">
            Test how the AI responds to customer queries. Messages here are not saved.
        </div>
    </div>
    
    <div style="padding: 0.75rem; background: #f0f0f0; display: flex; gap: 0.5rem; align-items: center;">
        <input type="text" id="sim-input" placeholder="Type a message..." style="flex: 1; padding: 0.75rem 1rem; border: none; border-radius: 20px; outline: none; font-size: 0.95rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <button id="sim-send-btn" onclick="sendSimMessage()" style="background: #128c7e; color: white; border: none; border-radius: 50%; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 1px 3px rgba(0,0,0,0.2);"><i class="bi bi-send-fill" style="margin-left: -2px;"></i></button>
    </div>
</div>

<style>
    #ai-simulator-panel, #ai-simulator-panel * {
        box-sizing: border-box !important;
    }
    #ai-simulator-panel {
        position: fixed;
        top: 0;
        width: 100%;
        max-width: min(400px, 100vw);
        height: 100vh;
        background: #efeae2;
        box-shadow: -4px 0 15px rgba(0,0,0,0.1);
        z-index: 1050;
        transition: right 0.3s ease;
        display: flex;
        flex-direction: column;
    }
    .ai-simulator-closed {
        right: -100%;
    }
    .ai-simulator-open {
        right: 0;
    }
    @media (min-width: 401px) {
        .ai-simulator-closed {
            right: -400px;
        }
    }
    .sim-msg {
        max-width: 80%;
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        font-size: 0.95rem;
        position: relative;
        box-shadow: 0 1px 1px rgba(0,0,0,0.1);
        color: #111;
        line-height: 1.4;
    }
    .sim-user {
        align-self: flex-end;
        background: #dcf8c6;
        border-top-right-radius: 0;
    }
    .sim-user::after {
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
    .sim-ai {
        align-self: flex-start;
        background: #ffffff;
        border-top-left-radius: 0;
    }
    .sim-ai::before {
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
    .sim-badge {
        font-size: 0.7rem;
        color: #888;
        margin-bottom: 0.2rem;
        font-weight: 600;
        display: block;
    }
</style>

<script>
    let simHistory = [];

    function toggleAiSimulator() {
        const panel = document.getElementById('ai-simulator-panel');
        if (panel.classList.contains('ai-simulator-closed')) {
            panel.classList.remove('ai-simulator-closed');
            panel.classList.add('ai-simulator-open');
        } else {
            panel.classList.remove('ai-simulator-open');
            panel.classList.add('ai-simulator-closed');
        }
    }

    async function sendSimMessage() {
        const input = document.getElementById('sim-input');
        const text = input.value.trim();
        if (!text) return;
        
        // Add user message to UI
        addSimMessageToUI('customer', text);
        input.value = '';
        input.disabled = true;
        document.getElementById('sim-send-btn').disabled = true;
        
        // Call API
        try {
            const response = await fetch('{{ route("simulator.chat") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ message: text, history: simHistory })
            });
            
            const data = await response.json();
            
            // Add user message to history array
            simHistory.push({ sender_type: 'user', message_text: text });
            
            if (data.success) {
                addSimMessageToUI('ai', data.reply);
                simHistory.push({ sender_type: 'ai', message_text: data.reply });
                
                if (data.create_lead) {
                    addSimSystemMessage(`[System Action: Create Lead] Name: ${data.create_lead.name}, Interest: ${data.create_lead.interest}`);
                }
            } else {
                addSimSystemMessage('Error: ' + data.error);
            }
        } catch (error) {
            addSimSystemMessage('Network Error');
        }
        
        input.disabled = false;
        document.getElementById('sim-send-btn').disabled = false;
        input.focus();
    }

    function addSimMessageToUI(type, text) {
        const container = document.getElementById('sim-messages');
        const div = document.createElement('div');
        const isUser = type === 'customer';
        div.className = `sim-msg ${isUser ? 'sim-user' : 'sim-ai'}`;
        div.innerHTML = `<div class="sim-badge">${type}</div><div>${text}</div>`;
        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
    }
    
    function addSimSystemMessage(text) {
        const container = document.getElementById('sim-messages');
        const div = document.createElement('div');
        div.style.background = 'rgba(16, 185, 129, 0.1)';
        div.style.color = 'var(--primary)';
        div.style.padding = '0.5rem';
        div.style.borderRadius = '8px';
        div.style.fontSize = '0.8rem';
        div.style.textAlign = 'center';
        div.style.fontWeight = '600';
        div.innerText = text;
        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
    }

    // Add Enter key support
    document.addEventListener('DOMContentLoaded', () => {
        const simInput = document.getElementById('sim-input');
        if (simInput) {
            simInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('sim-send-btn').click();
                }
            });
        }
    });
</script>
