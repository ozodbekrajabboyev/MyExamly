<?php

namespace App\Jobs;

use http\Env\Request;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class SentToTelegram implements ShouldQueue
{
    use Queueable;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Number of seconds to wait before retrying.
     */
    public int $backoff = 60;

    /**
     * Timeout for the job.
     */
    public int $timeout = 120;

    /**
     * Create a new job instance.
     */
    protected $validated;
    public function __construct(array $validated)
    {
        $this->validated = $validated;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $token   = env('TELEGRAM_BOT_TOKEN');
        $chatId  = env('TELEGRAM_CHAT_ID');

        if (!$token || !$chatId) {
            throw new \Exception('Telegram credentials not configured');
        }

        $text = <<<EOT
                📩 Yangi murojaat:

                👤 Ism: {$this->validated['name']}
                📞 Telefon: {$this->validated['phone']}
                🏫 Muassasa: {$this->validated['institution']}
                💬 Xabar: {$this->validated['message']}
                EOT;

        $response = Http::timeout(30)->post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text'    => $text,
            'parse_mode' => 'HTML',
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to send Telegram message: ' . $response->body());
        }
    }
}
