<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\MorphTo;
class EventAgendaItem extends Model { protected $guarded=[]; protected $casts=['expense_amount'=>'decimal:2']; public function event(){return $this->belongsTo(Event::class);} public function item(): MorphTo { return $this->morphTo(__FUNCTION__, 'item_type', 'item_id'); } public function ratings(){return $this->hasMany(Rating::class);} public function getTitleAttribute(){return $this->title_override ?: optional($this->item)->name;} }
