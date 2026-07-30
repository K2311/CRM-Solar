<?php

namespace App\Http\Controllers;

use App\Services\GeminiService;
use Illuminate\Http\Request;

class SimulatorController extends Controller
{
    public function chat(Request $request, GeminiService $gemini)
    {
        $message = $request->input('message');
        $history = $request->input('history', []); // array of previous messages for context

        try {
            $response = $gemini->generateResponse($message, $history);
            return response()->json([
                'success' => true,
                'reply' => $response['text'],
                'create_lead' => $response['create_lead']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
