<?php

namespace App\Mail;

use App\Models\AppSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ContributorNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $subjectLine, public string $bodyHtml) {}

    public function build()
    {
        $brandName = AppSetting::value('site_name', 'Festiva');
        $logo      = AppSetting::value('logo');
        $disk      = Storage::disk(AppSetting::mediaDisk());

        $logoData = $logoMime = $logoName = null;
        if ($logo && $disk->exists($logo)) {
            $logoData = $disk->get($logo);
            $logoMime = $disk->mimeType($logo) ?: 'image/png';
            $logoName = basename($logo);
        }

        config(['app.name' => $brandName]);

        return $this->subject($this->subjectLine)
            ->markdown('mail.contributor-notification', [
                'brandName' => $brandName,
                'logoData'  => $logoData,
                'logoMime'  => $logoMime,
                'logoName'  => $logoName,
            ]);
    }
}