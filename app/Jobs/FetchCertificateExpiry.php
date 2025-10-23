<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
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
            // cache null to avoid repeated calls for empty paths (shorter TTL)
            Cache::put($this->cacheKey, null, now()->addMinutes(10));
            return;
        }

        try {
            $result = Aivent::validateCertificate($path);
            $expiresAt = $result->expires_at ?? null;
            Cache::put($this->cacheKey, $expiresAt, $this->ttlSeconds);
        } catch (Throwable $e) {
            Cache::put($this->cacheKey, null, now()->addMinutes(5));
        }
    }
}
