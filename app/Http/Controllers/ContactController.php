<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ContactController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string',
            'phone'       => 'required|string',
            'institution' => 'required|string',
            'message'     => 'required|string',
        ]);

        $token   = env('TELEGRAM_BOT_TOKEN');
        $chatId  = env('TELEGRAM_CHAT_ID');

        // Telegram xabari formatlangan matn
        $text = <<<EOT
📩 Yangi murojaat:

👤 Ism: {$validated['name']}
📞 Telefon: {$validated['phone']}
🏫 Muassasa: {$validated['institution']}
💬 Xabar: {$validated['message']}
EOT;

        $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text'    => $text,
            'parse_mode' => 'HTML',
        ]);

        if ($response->successful()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Xabar Telegramga yuborildi ✅',
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Xabar yuborilmadi ❌',
        ], 500);
    }

}
