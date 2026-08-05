@extends('layouts.app')
@section('content')
<div class="hero p-4 mb-4"><h2 class="mb-1">Notify contributors</h2><p class="mb-0">{{$event->name}} · {{$event->event_date->format('d M Y')}}</p></div>
<div class="row g-4"><div class="col-lg-7"><div class="card p-4"><form method="post" action="{{route('events.notifications.send',$event)}}">@csrf <div class="d-flex justify-content-between"><h4>Recipients</h4><button type="button" id="toggle-all" class="btn btn-sm btn-outline-primary">Select none</button></div>@foreach($event->contributions as $row)@if($row->contributor->status)<div class="form-check border-bottom py-2"><input class="form-check-input recipient" type="checkbox" name="recipients[]" value="{{$row->contributor_id}}" checked id="r{{$row->id}}"><label class="form-check-label" for="r{{$row->id}}">{{$row->contributor->name}} <small class="text-muted">{{$row->contributor->email ?: 'No email'}}</small></label></div>@endif @endforeach <div class="mt-3"><label>Type</label><select class="form-select" name="type"><option value="event_invite">Event Invitation</option><option value="payment_reminder">Payment Reminder</option><option value="custom">Custom</option></select></div><div class="mt-3"><label>Subject</label><input class="form-control" name="subject" value="Invitation: {{$event->name}}"></div><div class="mt-3"><label>Message</label><textarea class="form-control" rows="7" name="message">Hello @{{contributor_name}}, you are invited to @{{event_name}} on @{{event_date}}. Your contribution is ₹@{{amount_due}} and outstanding amount is ₹@{{outstanding}}.</textarea><small class="text-muted">Tokens: contributor_name, event_name, event_date, per_head_amount, amount_due, amount_paid, outstanding.</small></div><button class="btn btn-primary mt-3">Queue notifications</button></form></div></div><div class="col-lg-5"><div class="card p-4"><h4>Notification history</h4><div class="mb-3"><input type="text" id="history-search" class="form-control" placeholder="Search history by name, type, status..."></div><div class="overflow-y-auto pe-2" style="max-height: 450px;" id="history-list">@forelse($logs as $log)<div class="border-bottom py-2 history-item" data-search="{{strtolower($log->contributor->name)}} {{strtolower($log->type)}} {{strtolower($log->status)}}"><strong>{{$log->contributor->name}}</strong><span class="badge text-bg-{{$log->status==='sent'?'success':($log->status==='failed'?'danger':'secondary')}} float-end">{{$log->status}}</span><br><small>{{$log->type}} · {{$log->sent_at?->format('d M Y H:i') ?: 'Queued'}}</small></div>@empty<p class="text-muted" id="no-history-msg">No notifications sent yet.</p>@endforelse<p class="text-muted d-none" id="no-results-msg">No matching history found.</p></div></div></div></div>
<script>
document.getElementById('toggle-all').onclick=function(){let boxes=document.querySelectorAll('.recipient'),all=[...boxes].every(x=>x.checked);boxes.forEach(x=>x.checked=!all);this.textContent=all?'Select all':'Select none'};
document.getElementById('history-search')?.addEventListener('input', function() {
    let q = this.value.toLowerCase().trim();
    let items = document.querySelectorAll('.history-item');
    let anyVisible = false;
    items.forEach(item => {
        let text = item.dataset.search || '';
        if (text.includes(q)) {
            item.classList.remove('d-none');
            anyVisible = true;
        } else {
            item.classList.add('d-none');
        }
    });
    let noResultsMsg = document.getElementById('no-results-msg');
    if (noResultsMsg) {
        if (items.length > 0 && !anyVisible) {
            noResultsMsg.classList.remove('d-none');
        } else {
            noResultsMsg.classList.add('d-none');
        }
    }
});
</script>
@endsection
