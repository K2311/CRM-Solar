<?php

namespace App\Services;

use App\Models\CompanySetting;
use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent';
        $this->apiKey = \App\Models\CompanySetting::where('key', 'gemini_api_key')->value('value') ?: config('services.gemini.key', env('GEMINI_API_KEY', ''));
    }

    /**
     * Build context and query Gemini.
     *
     * @param string $userMessage
     * @param array $chatHistory
     * @return array
     */
    public function generateResponse(string $userMessage, array $chatHistory = []): array
    {
        $context = $this->buildContext();
        
        $systemPrompt = "You are an intelligent and helpful assistant for a solar installation company. " .
                        "Answer questions based on the following company and product information: \n" . $context . "\n\n" .
                        "CRITICAL LANGUAGE RULES:\n" .
                        "- You must detect the language of the user's message.\n" .
                        "- If the user writes in Gujarati script (e.g., નમસ્તે) OR if they write Gujarati using English letters (e.g., 'kem cho', 'su chale chhe', 'enguj'), you MUST reply in Gujarati.\n" .
                        "- You MUST reply in English ONLY when the user's message is strictly in English.\n\n" .
                        "If the customer seems interested in an installation or a specific product, output a JSON block at the very end of your message in this exact format: \n" .
                        "```json\n{\"action\": \"create_lead\", \"details\": {\"name\": \"Customer Name (if known)\", \"interest\": \"Brief description of what they want\"}}\n```\n" .
                        "Otherwise, just answer their question naturally in the correct language.";

        $contents = [];
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $systemPrompt]]
        ];

        // Since Gemini 1.5 doesn't have a distinct 'system' role in the same way, we pass it as the first user message,
        // then an 'model' acknowledgment, then the history.
        $contents[] = [
            'role' => 'model',
            'parts' => [['text' => 'Understood. I will act as the solar company assistant and extract lead info when appropriate.']]
        ];

        foreach ($chatHistory as $msg) {
            $contents[] = [
                'role' => $msg['sender_type'] === 'user' ? 'user' : 'model',
                'parts' => [['text' => $msg['message_text']]]
            ];
        }

        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $userMessage]]
        ];

        $response = Http::post($this->apiUrl . '?key=' . $this->apiKey, [
            'contents' => $contents,
        ]);

        if ($response->successful()) {
            $text = $response->json('candidates.0.content.parts.0.text') ?? '';
            return $this->parseResponse($text);
        }

        Log::error('Gemini API Error', ['response' => $response->json()]);
        return ['text' => 'Sorry, I am currently unable to process your request.', 'create_lead' => null];
    }

    protected function buildContext(): string
    {
        // Simple context builder
        $settings = CompanySetting::first();
        $companyName = $settings ? $settings->company_name : 'Our Solar Company';
        
        $products = Product::all()->map(function ($p) {
            return "- {$p->name}: {$p->description} (Price: {$p->price})";
        })->implode("\n");

        return "Company Name: {$companyName}\nAvailable Products:\n{$products}";
    }

    protected function parseResponse(string $text): array
    {
        $createLead = null;
        
        if (preg_match('/```json\s*(\{.*?"action":\s*"create_lead".*?\})\s*```/is', $text, $matches)) {
            $createLead = json_decode($matches[1], true);
            // Remove the JSON block from the final text sent to the user
            $text = preg_replace('/```json\s*(\{.*?"action":\s*"create_lead".*?\})\s*```/is', '', $text);
        }

        return [
            'text' => trim($text),
            'create_lead' => $createLead['details'] ?? null
        ];
    }
}
