<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyTelegramSignature
{
    public function handle(Request $request, Closure $next)
    {
        $provided = $request->header('X-API-KEY');
        $expected = env('TELEGRAM_API_KEY');

        if (!$provided || !hash_equals($expected, $provided)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}

