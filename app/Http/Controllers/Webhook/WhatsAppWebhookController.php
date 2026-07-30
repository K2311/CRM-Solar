<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Lead;
use App\Services\WhatsAppService;
use App\Services\GeminiService;

class WhatsAppWebhookController extends Controller
{
    /**
     * Handle Meta Webhook Verification.
     */
    public function verify(Request $request)
    {
        $verifyToken = \App\Models\CompanySetting::where('key', 'whatsapp_verify_token')->value('value') ?: config('services.whatsapp.verify_token', env('WHATSAPP_VERIFY_TOKEN', ''));
        
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');
        
        if ($mode && $token) {
            if ($mode === 'subscribe' && $token === $verifyToken) {
                return response($challenge, 200);
            }
        }
        
        return response('Forbidden', 403);
    }

    /**
     * Handle incoming WhatsApp Messages.
     */
    public function handle(Request $request, WhatsAppService $whatsapp, GeminiService $gemini)
    {
        // Parse incoming message from Meta
        $data = $request->all();
        
        try {
            $entry = $data['entry'][0]['changes'][0]['value'];
            
            // Check if it's a message and not a status update
            if (isset($entry['messages']) && !empty($entry['messages'])) {
                $messageData = $entry['messages'][0];
                $phoneNumber = $messageData['from']; // Customer phone
                $messageText = $messageData['text']['body'] ?? '';

                if (empty($messageText)) {
                    return response()->json(['status' => 'ignored, not text']);
                }

                // 1. Get or Create Conversation
                $conversation = ChatConversation::firstOrCreate(
                    ['phone_number' => $phoneNumber],
                    ['status' => 'active']
                );

                // 2. Save User Message
                ChatMessage::create([
                    'chat_conversation_id' => $conversation->id,
                    'sender_type' => 'user',
                    'message_text' => $messageText,
                ]);

                // 3. Check if Admin took over
                if ($conversation->ai_paused_at !== null) {
                    // AI is paused, do nothing, let admin handle it manually.
                    return response()->json(['status' => 'ai_paused']);
                }

                // 4. Fetch recent chat history
                $history = ChatMessage::where('chat_conversation_id', $conversation->id)
                    ->orderBy('created_at', 'asc')
                    ->take(20) // send last 20 messages as context
                    ->get()
                    ->toArray();

                // 5. Query Gemini
                $geminiResponse = $gemini->generateResponse($messageText, $history);
                $replyText = $geminiResponse['text'];
                $leadData = $geminiResponse['create_lead'] ?? null;

                // 6. Save AI Message
                ChatMessage::create([
                    'chat_conversation_id' => $conversation->id,
                    'sender_type' => 'ai',
                    'message_text' => $replyText,
                ]);

                // 7. Send WhatsApp Reply
                $whatsapp->sendMessage($phoneNumber, $replyText);

                // 8. Create Lead if AI extracted info
                if ($leadData && !$conversation->lead_id) {
                    $lead = Lead::create([
                        'first_name' => $leadData['name'] ?? 'WhatsApp Lead',
                        'phone_number' => $phoneNumber,
                        'source' => 'WhatsApp AI',
                        'status' => 'NEW',
                        'notes' => 'Interest: ' . ($leadData['interest'] ?? 'Solar Installation'),
                    ]);

                    // Link Lead to Conversation
                    $conversation->update(['lead_id' => $lead->id]);
                }
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp Webhook Error', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error'], 500);
        }

        return response()->json(['status' => 'success'], 200);
    }
}
