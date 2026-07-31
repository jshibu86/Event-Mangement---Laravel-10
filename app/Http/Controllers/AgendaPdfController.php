<?php
namespace App\Http\Controllers;
use App\Models\{Event,AppSetting}; use Barryvdh\DomPDF\Facade\Pdf;
class AgendaPdfController extends Controller { public function __invoke(Event $event,string $kind='guest'){ $event->load(['agendaItems.item','expenses','budget']); $brandName=AppSetting::value('site_name','Festiva'); $footer=AppSetting::value('pdf_footer'); $logo=AppSetting::value('logo'); $logoData=null; if($logo && is_file(storage_path('app/public/'.$logo))){$mime=mime_content_type(storage_path('app/public/'.$logo));$logoData='data:'.$mime.';base64,'.base64_encode(file_get_contents(storage_path('app/public/'.$logo)));} return Pdf::loadView('pdf.agenda',compact('event','kind','brandName','footer','logoData'))->setPaper('a4')->stream(($event->slug ?? 'agenda').'.pdf'); } }
