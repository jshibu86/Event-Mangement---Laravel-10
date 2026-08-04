<?php
namespace App\Services; use App\Models\AppSetting;
class MailSettings { 
    public static function apply(): void
{
    $sandbox = AppSetting::value('mail_sandbox_mode', '1') === '1';
    $prefix = $sandbox ? 'mailtrap_' : 'mail_gmail_';

    $host = $sandbox ? AppSetting::value($prefix.'host') : 'smtp.gmail.com';
    $port = $sandbox ? AppSetting::value($prefix.'port', '587') : '587';
    $user = $sandbox ? AppSetting::value($prefix.'username') : AppSetting::value('mail_gmail_address');
    $password = $sandbox ? AppSetting::value($prefix.'password') : AppSetting::value('mail_gmail_app_password');

    // Always a real email, never the SMTP auth username
    $fromAddress = AppSetting::value('mail_gmail_address', 'no-reply@festiva.local');

    config([
        'mail.default' => 'smtp',
        'mail.mailers.smtp.host' => $host,
        'mail.mailers.smtp.port' => $port,
        'mail.mailers.smtp.username' => $user,
        'mail.mailers.smtp.password' => $password,
        'mail.mailers.smtp.encryption' => 'tls',
        'mail.from.address' => $fromAddress,
        'mail.from.name' => AppSetting::value('mail_from_name', AppSetting::value('site_name', 'Festiva')),
    ]);

    \Illuminate\Support\Facades\Mail::purge('smtp');
}
}
