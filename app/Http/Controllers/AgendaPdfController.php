<?php

namespace App\Http\Controllers;

use App\Models\{Event, AppSetting};
use Barryvdh\DomPDF\Facade\Pdf;

class AgendaPdfController extends Controller
{
    public function __invoke(Event $event, string $kind = 'guest')
    {
        $event->load(['agendaItems.item', 'expenses', 'budget']);

        $brandName = AppSetting::value('site_name', 'Festiva');
        $footer    = AppSetting::value('pdf_footer');
        $logoData  = AppSetting::fileDataUri(AppSetting::value('logo'));

        return Pdf::loadView('pdf.agenda', compact('event', 'kind', 'brandName', 'footer', 'logoData'))
            ->setPaper('a4')
            ->stream(($event->slug ?? 'agenda') . '.pdf');
    }
}