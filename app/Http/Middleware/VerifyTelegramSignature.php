<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// App\Http\Middleware\VerifyTelegramSignature.php

class VerifyTelegramSignature
{
    public function handle(Request $request, Closure $next)
    {
        $provided = $request->header('X-API-KEY');
        $expected = config('services.telegram.api_key');

        if (!$expected) {
            return response()->json([
                'message' => 'Server misconfigured (API key missing)'
            ], 500);
        }

        if (!$provided || !hash_equals($expected, $provided)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}


