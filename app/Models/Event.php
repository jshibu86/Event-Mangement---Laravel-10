<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\SoftDeletes;
class Event extends Model { use SoftDeletes; protected $guarded=[]; protected $casts=['event_date'=>'date','verified_at'=>'datetime']; public function agendaItems(){return $this->hasMany(EventAgendaItem::class)->orderBy('sequence');} public function expenses(){return $this->hasMany(EventExpense::class);} public function budget(){return $this->hasOne(EventBudget::class);} public function getAgendaTotalAttribute(){return $this->agendaItems()->sum('expense_amount');} public function getGrandTotalAttribute(){return $this->agenda_total+$this->expenses()->sum('amount');} }
