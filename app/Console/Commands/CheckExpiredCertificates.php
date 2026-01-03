<?php

namespace App\Console\Commands;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;

class CheckExpiredCertificates extends Command
{
    protected $signature = 'certificates:check-expiry {--debug : Show debug information}';
    protected $description = 'Checks teacher certificates from cache, warns at 3 days, and removes expired files.';

    protected array $certificateLabels = [
        'malaka_toifa_path' => 'malaka toifa sertifikat',
        'milliy_sertifikat1_path' => 'birinchi milliy sertifikat',
        'milliy_sertifikat2_path' => 'ikkinchi milliy sertifikat',
        'xalqaro_sertifikat_path' => 'xalqaro sertifikat',
        'ustama_sertifikat_path' => 'ustama sertifikat',
    ];

    public function handle(): int
    {
        $this->info('Starting certificate expiry check at: ' . now()->toDateTimeString());
        Log::info('Certificate expiry check started', ['timestamp' => now()]);

        $certificateFields = Teacher::getCertificateFields();
        $expiringCount = 0;
        $expiredCount = 0;
        $debugMode = $this->option('debug');

        $this->info('Checking certificates...');

        Teacher::with(['user'])->chunkById(200, function ($teachers) use ($certificateFields, &$expiringCount, &$expiredCount, $debugMode) {
            foreach ($teachers as $teacher) {
                if ($debugMode) {
                    $this->line("Checking teacher: {$teacher->full_name} (ID: {$teacher->id})");
                }

                foreach ($certificateFields as $field => $config) {
                    $expiryField = $config['expiry_field'];

                    if (!$teacher->{$field} || !$teacher->{$expiryField}) {
                        continue;
                    }

                    try {
                        $expiry = Carbon::parse($teacher->{$expiryField})->startOfDay();
                        $today = Carbon::now('Asia/Tashkent')->startOfDay();
                        $daysLeft = $today->diffInDays($expiry, false);

                        if ($debugMode) {
                            $this->line("  {$field}: expires {$expiry->toDateString()}, days left: {$daysLeft}");
                        }

                        if ($daysLeft <= 0) {
                            $this->processExpired($teacher, $field, $expiry);
                            $expiredCount++;
                        } elseif ($daysLeft <= 3) {
                            $this->notifyExpiringFilament($teacher, $field, $expiry, $daysLeft);
                            $expiringCount++;
                        }

                    } catch (\Throwable $e) {
                        $this->error("Error processing {$teacher->full_name} - {$config['label']}: {$e->getMessage()}");
                        Log::error("Certificate check error", [
                            'teacher_id' => $teacher->id,
                            'field' => $field,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        });

        $message = "📊 Certificate check completed: {$expiringCount} expiring, {$expiredCount} expired & removed";
        $this->info($message);
        Log::info($message);

        return self::SUCCESS;
    }

    protected function notifyExpiringFilament(Teacher $teacher, string $field, Carbon $expiry, int $daysLeft): void
    {
        $certificateName = $this->certificateLabels[$field] ?? $field;

        // Check if notification already sent for this expiry date
        $notificationFlag = "expiry_notif_sent:{$teacher->id}:{$field}:" . $expiry->toDateString();
        if (Cache::has($notificationFlag)) {
            if ($this->option('debug')) {
                $this->line("  Notification already sent for this expiry date");
            }
            return;
        }

        $recipients = $this->getNotificationRecipients($teacher);
        if (empty($recipients)) {
            $this->error("  No recipients found for {$teacher->full_name}");
            return;
        }

        // Send notification to teacher
        $teacherBody = "Sizning {$certificateName}ingiz {$daysLeft} kun ichida amal qilish muddati tugaydi. Iltimos {$certificateName}ingizni yangilang!";

        // Send notification to admin
        $adminBody = "{$teacher->full_name}ning {$certificateName}i {$daysLeft} kun ichida tugaydi";

        foreach ($recipients as $recipient) {
            // Check if this recipient is the teacher or admin
            $isTeacher = $recipient->id === $teacher->user_id;
            $body = $isTeacher ? $teacherBody : $adminBody;

            try {
                Notification::make()
                    ->title('⚠️ Sertifikat muddati tugamoqda')
                    ->body($body)
                    ->icon('heroicon-o-exclamation-triangle')
                    ->iconColor('warning')
                    ->duration(null)
                    ->sendToDatabase($recipient);

                if ($this->option('debug')) {
                    $this->line("  Notification sent to: " . ($isTeacher ? 'Teacher' : 'Admin') . " - {$recipient->email}");
                }

            } catch (\Throwable $e) {
                $this->error("Failed to send notification to {$recipient->email}: " . $e->getMessage());
            }
        }

        // Set flag to prevent duplicate notifications
        Cache::put($notificationFlag, true, $expiry->addDay());

        $this->info("✅ NOTIFICATION SENT: {$teacher->full_name} - {$certificateName} (to " . count($recipients) . " recipients)");

        Log::channel('certificate')->info('Certificate expiring notification sent', [
            'teacher_id' => $teacher->id,
            'certificate' => $certificateName,
            'expires_at' => $expiry->toDateString(),
            'days_left' => $daysLeft,
            'recipients_count' => count($recipients)
        ]);
    }

    protected function processExpired(Teacher $teacher, string $field, Carbon $expiry): void
    {
        $certificateName = $this->certificateLabels[$field] ?? $field;

        // 1. Delete file from storage
        $filePath = $teacher->{$field};
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            try {
                Storage::disk('public')->delete($filePath);
                $this->info("✅ File deleted: {$filePath}");
            } catch (\Throwable $e) {
                $this->error("❌ Failed to delete file: {$filePath}");
            }
        }

        // 2. Update database
        try {
            $teacher->update([$field => null]);
            $this->info("✅ Database updated: {$field} set to null");
        } catch (\Throwable $e) {
            $this->error("❌ Failed to update database");
        }

        // 3. Send Filament notification about removal
        $this->sendExpiredNotification($teacher, $certificateName, $expiry);

        $this->error("🗑️ EXPIRED: {$teacher->full_name} - {$certificateName} removed");
    }

    protected function sendExpiredNotification(Teacher $teacher, string $certificateName, Carbon $expiry): void
    {
        // Check if notification already sent
        $notificationFlag = "expired_notif_sent:{$teacher->id}:{$certificateName}:" . $expiry->toDateString();
        if (Cache::has($notificationFlag)) {
            return;
        }

        $recipients = $this->getNotificationRecipients($teacher);
        if (empty($recipients)) {
            return;
        }

        // Different messages for teacher and admin
        $teacherBody = "Sizning {$certificateName}ingiz muddati tugagani sababli profilingizdan olib tashlandi. Iltimos yangi {$certificateName} yuklang!";
        $adminBody = "{$teacher->full_name}ning {$certificateName}i muddati tugab, profilidan olib tashlandi";

        foreach ($recipients as $recipient) {
            // Check if this recipient is the teacher or admin
            $isTeacher = $recipient->id === $teacher->user_id;
            $body = $isTeacher ? $teacherBody : $adminBody;

            try {
                Notification::make()
                    ->title('🚨 Sertifikat olib tashlandi')
                    ->body($body)
                    ->icon('heroicon-o-trash')
                    ->iconColor('danger')
                    ->duration(null)
                    ->sendToDatabase($recipient);

            } catch (\Throwable $e) {
                $this->error("Failed to send expired notification to {$recipient->email}: " . $e->getMessage());
            }
        }

        // Set flag to prevent duplicates
        Cache::put($notificationFlag, true, now()->addWeek());

        Log::channel('certificate')->info('Certificate expired notification sent', [
            'teacher_id' => $teacher->id,
            'certificate' => $certificateName,
            'expired_at' => $expiry->toDateString()
        ]);
    }

    protected function getNotificationRecipients(Teacher $teacher): array
    {
        $recipients = [];

        // 1. Add teacher's user
        if ($teacher->user) {
            $recipients[] = $teacher->user;
            if ($this->option('debug')) {
                $this->line("  Added teacher user: {$teacher->user->email}");
            }
        }

        // 2. Add school admin using maktab_id
        if ($teacher->maktab_id) {
            $schoolAdmin = User::where('maktab_id', $teacher->maktab_id)
                ->where('role_id', 2)
                ->first();

            if ($schoolAdmin) {
                $recipients[] = $schoolAdmin;
                if ($this->option('debug')) {
                    $this->line("  Added school admin: {$schoolAdmin->email}");
                }
            } else {
                if ($this->option('debug')) {
                    $this->line("  No school admin found for maktab_id: {$teacher->maktab_id}");
                }
            }
        }

        if ($this->option('debug')) {
            $this->line("  Total recipients: " . count($recipients));
        }

        return $recipients;
    }
}
