<?php
namespace App\Mail;
use App\Models\AppSetting; use Illuminate\Bus\Queueable; use Illuminate\Mail\Mailable; use Illuminate\Queue\SerializesModels;
class ContributorNotification extends Mailable { use Queueable,SerializesModels; public function __construct(public string $subjectLine,public string $bodyHtml){} public function build(){ $brandName=AppSetting::value('site_name','Festiva');$logo=AppSetting::value('logo');$mailLogoPath=$logo && is_file(storage_path('app/public/'.$logo))?storage_path('app/public/'.$logo):null;config(['app.name'=>$brandName]);return $this->subject($this->subjectLine)->markdown('mail.contributor-notification',['brandName'=>$brandName,'mailLogoPath'=>$mailLogoPath]);} }
