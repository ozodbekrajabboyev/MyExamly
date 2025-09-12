<?php

namespace App\Http\Controllers;

use App\Jobs\SentToTelegram;
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
            'name' => 'required|string',
            'phone' => 'required|string',
            'institution' => 'required|string',
            'message' => 'required|string',
        ]);


        $response = SentToTelegram::dispatch($validated);

        if ($response) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Xabar Telegramga yuborildi ✅',
            ]);
        }

    }

}
