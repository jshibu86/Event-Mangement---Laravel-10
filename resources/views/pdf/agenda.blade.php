<!doctype html>
<html><head><style>
@@page { margin: 32px 38px; }
body { font-family: DejaVu Sans; color: #302842; font-size: 11px; }
.brand { border-bottom: 3px solid #df4f72; padding-bottom: 12px; margin-bottom: 22px; }
.logo { max-height: 45px; max-width: 180px; float: right; }
.brand-name, h1 { color: #6d2b91; }.brand-name { font-weight: bold; font-size: 13px; }.clear { clear: both; }
h1 { font-size: 24px; margin: 12px 0 4px; }.meta,.footer { color: #777; }.slot { border-left: 4px solid #df4f72; padding: 10px 14px; margin: 10px 0; background: #faf8fc; }.cost { float: right; }.footer { position: fixed; bottom: -15px; left: 0; right: 0; text-align: center; font-size: 9px; }
</style></head><body>
<div class="brand">
@if($logoData)<img class="logo" src="{{$logoData}}">@endif
<div class="brand-name">{{$brandName}}</div><div class="clear"></div>
</div>
<h1>{{$event->name}}</h1><p class="meta">{{$event->event_date->format('l, d F Y')}} · {{$event->venue}}</p>
@if($event->invitation_title)<h3>{{$event->invitation_title}}</h3><p>{!! nl2br(e($event->invitation_message)) !!}</p>@endif
<h2>Programme</h2>
@foreach($event->agendaItems as $slot)
<div class="slot"><strong>{{$slot->start_time}} @if($slot->end_time)– {{$slot->end_time}}@endif</strong> · {{$slot->title}} <small>({{$slot->item_type}})</small>@if($kind === 'internal') <span class="cost">₹{{number_format($slot->expense_amount)}}</span> @endif</div>
@endforeach
@if($kind === 'internal')<h3>Budget summary: ₹{{number_format($event->grand_total)}}</h3>@endif
@if($footer)<div class="footer">{{$footer}}</div>@endif
</body></html>
