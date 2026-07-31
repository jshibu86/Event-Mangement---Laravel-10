<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model; class Rating extends Model { protected $guarded=[]; public function agendaItem(){return $this->belongsTo(EventAgendaItem::class,'event_agenda_item_id');} }
