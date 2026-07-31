<?php
namespace App\Http\Controllers;
use App\Models\{Event,Game,Program,EventAgendaItem,EventBudget,EventExpense,Rating}; use Illuminate\Http\Request; use Illuminate\Support\Facades\Auth;
class EventController extends Controller {
 public function dashboard(){ $events=Event::withCount('agendaItems')->orderBy('event_date')->take(6)->get(); return view('dashboard',compact('events')); }
 public function index(){return view('events.index',['events'=>Event::latest()->get()]);}
 public function create(){return view('events.form',['event'=>new Event]);}
 public function store(Request $r){$data=$r->validate(['name'=>'required','event_date'=>'required|date','start_time'=>'nullable','end_time'=>'nullable','description'=>'nullable','venue'=>'nullable','invitation_title'=>'nullable','invitation_message'=>'nullable']); $data['created_by']=Auth::id(); $event=Event::create($data); return redirect()->route('events.show',$event)->with('success','Event created — build its agenda next.');}
 public function show(Event $event){$event->load(['agendaItems.item','expenses','budget']); $games=Game::where('status',1)->get(); $programs=Program::where('status',1)->get(); return view('events.show',compact('event','games','programs'));}
 public function addAgenda(Request $r,Event $event){abort_if($event->status!=='draft',403,'Verified events are locked.'); $d=$r->validate(['item_id'=>'required','start_time'=>'required','end_time'=>'nullable','expense_amount'=>'nullable|numeric|min:0','notes'=>'nullable']); $d['expense_amount']=$d['expense_amount'] ?? 0; $ref=(string)$d['item_id']; $d['item_type']=str_starts_with($ref,'p')?'program':'game'; $d['item_id']=ltrim($ref,'p'); abort_unless(is_numeric($d['item_id']),422); $d['event_id']=$event->id; $d['sequence']=$event->agendaItems()->max('sequence')+1; EventAgendaItem::create($d); return back()->with('success','Added to the timeline.');}
 public function removeAgenda(Event $event,EventAgendaItem $agendaItem){abort_if($event->status!=='draft',403); $agendaItem->delete(); return back()->with('success','Agenda item removed.');}
 public function saveBudget(Request $r,Event $event){EventBudget::updateOrCreate(['event_id'=>$event->id],$r->validate(['total_budget'=>'nullable|numeric|min:0','notes'=>'nullable'])); return back()->with('success','Budget cap saved.');}
 public function addExpense(Request $r,Event $event){$d=$r->validate(['title'=>'required','category'=>'nullable','amount'=>'required|numeric|min:0','notes'=>'nullable']); $event->expenses()->create($d); return back()->with('success','Extra expense added.');}
 public function verify(Event $event){abort_if($event->status!=='draft',403); $event->update(['status'=>'verified','verified_at'=>now(),'verified_by'=>Auth::id()]); return back()->with('success','Event verified and locked. PDFs are ready.');}
 public function rate(Request $r,Event $event){$d=$r->validate(['event_agenda_item_id'=>'required|exists:event_agenda_items,id','rating_value'=>'required|integer|between:1,5','feedback'=>'nullable']); $d['event_id']=$event->id; $d['rated_by']=Auth::id(); Rating::create($d); return back()->with('success','Thank you for your rating!');}
}
