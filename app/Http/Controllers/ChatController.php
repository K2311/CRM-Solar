<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        $conversations = ChatConversation::with('lead')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('marketing.chat', compact('conversations'));
    }

    public function show(ChatConversation $conversation)
    {
        $messages = ChatMessage::where('chat_conversation_id', $conversation->id)
            ->orderBy('created_at', 'asc')
            ->get();
            
        return response()->json([
            'conversation' => $conversation,
            'messages' => $messages
        ]);
    }

    public function toggleAi(ChatConversation $conversation)
    {
        if ($conversation->ai_paused_at) {
            $conversation->update(['ai_paused_at' => null]);
        } else {
            $conversation->update(['ai_paused_at' => now()]);
        }
        
        return response()->json(['success' => true, 'ai_paused_at' => $conversation->ai_paused_at]);
    }

    public function send(Request $request, ChatConversation $conversation, WhatsAppService $whatsapp)
    {
        $request->validate(['message' => 'required|string']);
        
        // Save Admin Message
        $msg = ChatMessage::create([
            'chat_conversation_id' => $conversation->id,
            'sender_type' => 'admin',
            'message_text' => $request->message,
        ]);
        
        // Send via WhatsApp
        $whatsapp->sendMessage($conversation->phone_number, $request->message);
        
        return response()->json(['success' => true, 'message' => $msg]);
    }

    public function startChat(\App\Models\Customer $customer)
    {
        if (!$customer->phone) {
            return back()->with('error', 'Customer does not have a phone number.');
        }

        $conversation = ChatConversation::firstOrCreate(
            ['phone_number' => $customer->phone],
            ['status' => 'active']
        );

        return redirect()->route('chat.index', ['conversation_id' => $conversation->id]);
    }

    public function startLeadChat(\App\Models\Lead $lead)
    {
        if (!$lead->customer || !$lead->customer->phone) {
            return back()->with('error', 'Lead customer does not have a phone number.');
        }

        $conversation = ChatConversation::firstOrCreate(
            ['phone_number' => $lead->customer->phone],
            ['status' => 'active']
        );

        if (!$conversation->lead_id) {
            $conversation->update(['lead_id' => $lead->id]);
        }

        return redirect()->route('chat.index', ['conversation_id' => $conversation->id]);
    }
}
