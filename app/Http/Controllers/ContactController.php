<?php

namespace App\Http\Controllers;

use App\Jobs\SentToTelegram;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'institution' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
        ]);

        try {
            // Dispatch the job to send message to Telegram
            SentToTelegram::dispatch($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Xabar Telegramga yuborildi ✅',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Xabar yuborishda xatolik yuz berdi',
                'error' => $e->getMessage()
            ], 500);
        }

    }

}
