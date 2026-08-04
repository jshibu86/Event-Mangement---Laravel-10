<?php
namespace App\Jobs; use App\Models\NotificationLog; use App\Mail\ContributorNotification; use App\Services\MailSettings; use Illuminate\Bus\Queueable; use Illuminate\Contracts\Queue\ShouldQueue; use Illuminate\Foundation\Bus\Dispatchable; use Illuminate\Queue\InteractsWithQueue; use Illuminate\Queue\SerializesModels; use Illuminate\Support\Facades\Mail;
class SendContributorNotification implements ShouldQueue { use Dispatchable,InteractsWithQueue,Queueable,SerializesModels; public function __construct(public int $logId,public string $email,public string $subjectLine,public string $bodyHtml){} 
public function handle(): void
{
    $log = NotificationLog::find($this->logId);
    try {
        MailSettings::apply();
        Mail::to($this->email)->send(new ContributorNotification($this->subjectLine, $this->bodyHtml));
        $log?->update(['status' => 'sent', 'sent_at' => now(), 'error_message' => null]);
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error('Contributor mail failed', ['msg' => $e->getMessage()]);
        $log?->update(['status' => 'failed', 'error_message' => substr($e->getMessage(), 0, 1000)]);
    }
}
}
