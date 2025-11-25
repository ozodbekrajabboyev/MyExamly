<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Norbek\Aivent\Facades\Aivent;
use App\Models\Teacher;
use Throwable;

class FetchCertificateExpiry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $teacherId;
    public string $field;      // e.g. "milliy_sertifikat1_path"
    public string $cacheKey;
    public int $ttlSeconds;

    public function __construct(int $teacherId, string $field, string $cacheKey, int $ttlSeconds = 60 * 60 * 24 * 30)
    {
        $this->teacherId = $teacherId;
        $this->field = $field;
        $this->cacheKey = $cacheKey;
        $this->ttlSeconds = $ttlSeconds; // default 30 days
    }

    public function handle()
    {
        $teacher = Teacher::find($this->teacherId);
        if (! $teacher) {
            return;
        }

        $path = $teacher->{$this->field} ?? null;
        if (! $path) {
            // Cache 'no_document' to avoid repeated calls for empty paths
            Cache::put($this->cacheKey, 'no_document', now()->addHours(24));
            return;
        }

        try {
            $result = Aivent::validateCertificate($path);
            $expiresAt = $result->expires_at ?? null;

            if ($expiresAt) {
                // Certificate has expiry date - cache it
                Cache::put($this->cacheKey, $expiresAt, $this->ttlSeconds);
            } else {
                // Certificate has no expiry date - mark as 'no_expiry' to skip future processing
                Cache::put($this->cacheKey, 'no_expiry', now()->addDays(30));
            }
        } catch (Throwable $e) {
            // API error - cache 'error' with longer TTL to prevent frequent retries
            // Only retry after 1 hour instead of 5 minutes to reduce server load
            Cache::put($this->cacheKey, 'error', now()->addHour());

            // Log the error for debugging
            Log::warning("Certificate validation failed for teacher {$this->teacherId}, field {$this->field}: " . $e->getMessage());
        }
    }

}
