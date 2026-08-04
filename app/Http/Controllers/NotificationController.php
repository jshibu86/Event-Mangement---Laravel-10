<?php
namespace App\Http\Controllers; use App\Models\{Event,NotificationLog}; use App\Jobs\SendContributorNotification; use Illuminate\Http\Request;
class NotificationController extends Controller { 
    public function show(Event $event){
        $event->load('contributions.contributor');
        $logs=NotificationLog::with('contributor')->where('event_id',$event->id)->latest()->get();
        return view('notifications.show',compact('event','logs'));
    } 
    public function send(Request $r,Event $event){
         \Log::info('Raw recipients payload', ['recipients' => $r->input('recipients')]);
        $data=$r->validate(['recipients'=>'required|array','type'=>'required|in:event_invite,payment_reminder,custom','subject'=>'required|max:255','message'=>'required']);
        $rows=$event->contributions()->with('contributor')->whereIn('contributor_id',$data['recipients'])->get();
        $sent=0;$skipped=0;
        foreach($rows as $row){
            if(!$row->contributor->status || !$row->contributor->email || ($data['type']==='payment_reminder' && $row->payment_status==='paid')){
                $skipped++;continue;
            }
       $tokens=['{{contributor_name}}'=>$row->contributor->name,'{{event_name}}'=>$event->name,'{{event_date}}'=>$event->event_date->format('d M Y'),'{{per_head_amount}}'=>$event->per_head_amount,'{{amount_due}}'=>$row->amount_due,'{{amount_paid}}'=>$row->amount_paid,'{{outstanding}}'=>(float)$row->amount_due-(float)$row->amount_paid];
       $log=NotificationLog::create(['event_id'=>$event->id,'contributor_id'=>$row->contributor_id,'type'=>$data['type'],'status'=>'queued']);
       SendContributorNotification::dispatch($log->id,$row->contributor->email,strtr($data['subject'],$tokens),nl2br(e(strtr($data['message'],$tokens))));$sent++;}
       return back()->with('success',"{$sent} notification(s) queued; {$skipped} skipped.");} 
    }
