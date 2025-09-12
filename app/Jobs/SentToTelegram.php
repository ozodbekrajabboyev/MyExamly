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


        $text = <<<EOT
                📩 Yangi murojaat:

                👤 Ism: {$this->validated['name']}
                📞 Telefon: {$this->validated['phone']}
                🏫 Muassasa: {$this->validated['institution']}
                💬 Xabar: {$this->validated['message']}
                EOT;

        $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text'    => $text,
            'parse_mode' => 'HTML',
        ]);

    }
}
