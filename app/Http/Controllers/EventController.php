<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventAgendaItem;
use App\Models\EventBudget;
use App\Models\EventExpense;
use App\Models\Game;
use App\Models\Program;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class EventController extends Controller
{
    public function dashboard()
    {
        $events = Event::withCount('agendaItems')
            ->orderBy('event_date')
            ->take(6)
            ->get();

        return view('dashboard', compact('events'));
    }

    public function index(Request $request)
    {
        $search = $request->string('search')->trim()->toString();

        $events = Event::when($search, fn ($query) => $query->where(
            fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('venue', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
        ))
            ->latest()
            ->get();

        return view('events.index', compact('events', 'search'));
    }

    public function create()
    {
        return view('events.form', [
            'event' => new Event,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'event_date' => 'required|date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'description' => 'nullable',
            'venue' => 'nullable',
            'invitation_title' => 'nullable',
            'invitation_message' => 'nullable',
        ]);

        $data['created_by'] = Auth::id();

        $event = Event::create($data);

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Event created — build its agenda next.');
    }

    public function show(Event $event)
    {
        $event->load([
            'agendaItems.item',
            // 'expenses',
            'expenses.media',
            'budget',
        ]);

        $games = Game::where('status', 1)->get();
        $programs = Program::where('status', 1)->get();

        return view('events.show', compact('event', 'games', 'programs'));
    }

    public function addAgenda(Request $request, Event $event)
    {
        abort_if($event->status !== 'draft', 403, 'Verified events are locked.');

        $data = $request->validate([
            'item_id' => 'required',
            'start_time' => 'required',
            'end_time' => 'nullable',
            'expense_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable',
        ]);

        $data['expense_amount'] = $data['expense_amount'] ?? 0;

        $ref = (string) $data['item_id'];

        $data['item_type'] = str_starts_with($ref, 'p')
            ? 'program'
            : 'game';

        $data['item_id'] = ltrim($ref, 'p');

        abort_unless(is_numeric($data['item_id']), 422);

        $data['event_id'] = $event->id;
        $data['sequence'] = $event->agendaItems()->max('sequence') + 1;

        EventAgendaItem::create($data);

        return back()->with('success', 'Added to the timeline.');
    }

    public function removeAgenda(
        Event $event,
        EventAgendaItem $agendaItem
    ) {
        abort_if($event->status !== 'draft', 403);
        
        $agendaItem->delete();

        return back()->with('success', 'Agenda item removed.');
    }

    public function editAgenda(
        Event $event,
        EventAgendaItem $agendaItem
    ) {
        abort_if($event->status !== 'draft', 403, 'Verified events are locked.');
        abort_unless($agendaItem->event_id === $event->id, 404);

        $agendaItem->load('item');

        return view('events.agenda-form', compact('event', 'agendaItem'));
    }

    public function updateAgenda(
        Request $request,
        Event $event,
        EventAgendaItem $agendaItem
    ) {
        abort_if($event->status !== 'draft', 403, 'Verified events are locked.');
        abort_unless($agendaItem->event_id === $event->id, 404);

        $data = $request->validate([
            'title_override' => 'nullable|max:255',
            'start_time' => 'required',
            'end_time' => 'nullable',
            'expense_amount' => 'nullable|numeric|min:0',
            'expense_notes' => 'nullable',
            'notes' => 'nullable',
        ]);

        $data['expense_amount'] = $data['expense_amount'] ?? 0;

        $agendaItem->update($data);

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Agenda item updated.');
    }

    public function saveBudget(Request $request, Event $event)
    {
        EventBudget::updateOrCreate(
            ['event_id' => $event->id],
            $request->validate([
                'total_budget' => 'nullable|numeric|min:0',
                'notes' => 'nullable',
            ])
        );

        return back()->with('success', 'Budget cap saved.');
    }

    public function addExpense(Request $request, Event $event)
    {
        $data = $request->validate([
            'title' => 'required',
            'category' => 'nullable',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable',
        ]);

           DB::transaction(function () use ($request, $event, $data) {
                $expense = $event->expenses()->create(collect($data)->except('bill')->all());

                if ($request->hasFile('bill')) {
                    $expense->addMediaFromRequest('bill')->toMediaCollection('bills');
                }
            });

        return back()->with('success', 'Extra expense added.');
    }

    public function verify(Event $event)
    {
        abort_if($event->status !== 'draft', 403);

        $event->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by' => Auth::id(),
        ]);

        return back()->with('success', 'Event verified and locked. PDFs are ready.');
    }

    public function rate(Request $request, Event $event)
    {
        $data = $request->validate([
            'event_agenda_item_id' => 'required|exists:event_agenda_items,id',
            'rating_value' => 'required|integer|between:1,5',
            'feedback' => 'nullable',
        ]);

        $data['event_id'] = $event->id;
        $data['rated_by'] = Auth::id();

        Rating::create($data);

        return back()->with('success', 'Thank you for your rating!');
    }
}
