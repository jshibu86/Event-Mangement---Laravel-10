<?php
namespace App\Mail; use Illuminate\Bus\Queueable; use Illuminate\Mail\Mailable; use Illuminate\Queue\SerializesModels;
class ContributorNotification extends Mailable { use Queueable,SerializesModels; public function __construct(public string $subjectLine,public string $bodyHtml){} public function build(){return $this->subject($this->subjectLine)->view('mail.contributor-notification');} }
