<?php

namespace App\Console\Commands;

use App\Models\Teacher;
use App\Notifications\CertificateExpiredNotification;
use App\Notifications\CertificateExpiringNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CheckExpiredCertificates extends Command
{
    protected $signature = 'certificates:check-expiry {--debug : Show debug information}';
    protected $description = 'Checks teacher certificates from cache, warns at 3 days, and removes expired files.';

    protected array $fields;

    public function handle(): int
    {
        $this->fields = Teacher::getCertificateFields();

        $expiringCount = 0;
        $expiredCount = 0;
        $debugMode = $this->option('debug');

        $this->info('Checking certificates...');

        if ($debugMode) {
            $this->info('Debug mode enabled');
        }

        Teacher::with('user')->chunkById(200, function ($teachers) use (&$expiringCount, &$expiredCount, $debugMode) {
            foreach ($teachers as $teacher) {
                if ($debugMode) {
                    $this->line("Checking teacher: {$teacher->full_name} (ID: {$teacher->id})");
                }

                foreach ($this->fields as $field => $label) {
                    // Skip if no file uploaded
                    if (!$teacher->{$field}) {
                        if ($debugMode) {
                            $this->line("  - {$label}: No file uploaded");
                        }
                        continue;
                    }

                    $cacheKey = "teacher:{$teacher->id}:cert:{$field}:expires_at";
                    $expiresAt = Cache::get($cacheKey);

                    if (!$expiresAt) {
                        if ($debugMode) {
                            $this->line("  - {$label}: No expiry date in cache");
                        }
                        continue;
                    }

                    try {
                        // Convert to Illuminate\Support\Carbon
                        $expiry = Carbon::parse($expiresAt)->startOfDay();
                        $today = Carbon::now('Asia/Tashkent')->startOfDay();
                        $daysLeft = $today->diffInDays($expiry, false);

                        if ($debugMode) {
                            $this->line("  - {$label}: Expires {$expiry->format('Y-m-d')} ({$daysLeft} days left)");
                        }

                        // Check if expired (0 or negative days = expired)
                        if ($daysLeft <= 0) {
                            $this->processExpired($teacher, $field, $label, $expiry, $cacheKey);
                            $expiredCount++;
                            if ($debugMode) {
                                $this->line("    → EXPIRED - File removed and notification sent");
                            }
                        }
                        // Check if expiring soon (1, 2, or 3 days left)
                        elseif ($daysLeft <= 3) {
                            $this->notifyExpiringOnce($teacher, $field, $label, $expiry, $daysLeft);
                            $expiringCount++;
                            if ($debugMode) {
                                $this->line("    → EXPIRING SOON - Warning notification sent");
                            }
                        }
                        // Valid certificate (more than 3 days)
                        else {
                            if (!$debugMode) {
                                $this->line("Teacher {$teacher->id} ({$teacher->full_name}) - {$label}: {$daysLeft} days left");
                            } else {
                                $this->line("    → VALID - No action needed");
                            }
                        }

                    } catch (\Throwable $e) {
                        $this->error("Error processing {$teacher->full_name} - {$label}: {$e->getMessage()}");
                        Log::error("Certificate check error", [
                            'teacher_id' => $teacher->id,
                            'field' => $field,
                            'error' => $e->getMessage()
                        ]);

                        // Set temporary cache to avoid repeated errors
                        Cache::put($cacheKey, null, now()->addMinutes(5));
                    }
                }
            }
        });

        $this->newLine();
        $this->info('📊 Results:');
        $this->table(['Status', 'Count'], [
            ['Expiring soon (≤3 days)', $expiringCount],
            ['Expired & removed', $expiredCount],
        ]);
        $this->info('Certificate check completed!');

        // Log the results
        Log::channel('certificate')->info('Certificate check completed', [
            'expiring_count' => $expiringCount,
            'expired_count' => $expiredCount,
            'timestamp' => now()
        ]);

        return self::SUCCESS;
    }

    protected function notifyExpiringOnce(Teacher $teacher, string $field, string $label, Carbon $expiry, int $daysLeft): void
    {
        $user = $teacher->user;
        if (!$user) {
            return;
        }

        // Check if we already sent a notification for this specific expiry date
        $existingNotification = $user->notifications()
            ->where('data->kind', 'certificate_expiring')
            ->where('data->field', $field)
            ->where('data->expires_at', $expiry->toDateString())
            ->whereNull('read_at') // Only unread notifications
            ->exists();

        if (!$existingNotification) {
            $user->notify(new CertificateExpiringNotification(
                field: $field,
                certificateName: $label,
                expiresAt: $expiry, // Now using Illuminate\Support\Carbon
                daysLeft: $daysLeft,
            ));

            $this->warn("⚠️  WARNING: {$teacher->full_name} - {$label} expires in {$daysLeft} day(s) ({$expiry->format('d.m.Y')})");

            Log::channel('certificate')->info('Certificate expiring notification sent', [
                'teacher_id' => $teacher->id,
                'teacher_name' => $teacher->full_name,
                'certificate' => $label,
                'expires_at' => $expiry->toDateString(),
                'days_left' => $daysLeft
            ]);
        }
    }

    protected function processExpired(Teacher $teacher, string $field, string $label, Carbon $expiredAt, string $cacheKey): void
    {
        $this->error("🗑️  EXPIRED: {$teacher->full_name} - {$label} expired on {$expiredAt->format('d.m.Y')}");

        // 1. Delete file from storage if it exists
        $filePath = $teacher->{$field};
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            try {
                Storage::disk('public')->delete($filePath);
                $this->info("   ✅ File deleted: {$filePath}");

                Log::channel('certificate')->info('Expired certificate file deleted', [
                    'teacher_id' => $teacher->id,
                    'certificate' => $label,
                    'file_path' => $filePath
                ]);
            } catch (\Throwable $e) {
                $this->error("   ❌ Failed to delete file: {$filePath}");
                Log::error("File deletion failed", [
                    'teacher_id' => $teacher->id,
                    'field' => $field,
                    'file_path' => $filePath,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // 2. Update database - set field to null
        try {
            $teacher->update([$field => null]);
            $this->info("   ✅ Database updated: {$field} set to null");
        } catch (\Throwable $e) {
            $this->error("   ❌ Failed to update database");
            Log::error("Database update failed", [
                'teacher_id' => $teacher->id,
                'field' => $field,
                'error' => $e->getMessage()
            ]);
        }

        // 3. Clear cache
        Cache::forget($cacheKey);
        $this->info("   ✅ Cache cleared");

        // 4. Send notification to user
        $user = $teacher->user;
        if ($user) {
            // Check if we already sent an expired notification for this certificate
            $existingNotification = $user->notifications()
                ->where('data->kind', 'certificate_expired')
                ->where('data->field', $field)
                ->where('data->expired_at', $expiredAt->toDateString())
                ->exists();

            if (!$existingNotification) {
                $user->notify(new CertificateExpiredNotification(
                    field: $field,
                    certificateName: $label,
                    expiredAt: $expiredAt, // Now using Illuminate\Support\Carbon
                ));

                $this->info("   📧 Notification sent to user");

                Log::channel('certificate')->info('Certificate expired notification sent', [
                    'teacher_id' => $teacher->id,
                    'teacher_name' => $teacher->full_name,
                    'certificate' => $label,
                    'expired_at' => $expiredAt->toDateString()
                ]);
            }
        }
    }
}
