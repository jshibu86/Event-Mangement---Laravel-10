<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class NotificationLog extends Model { protected $guarded=[]; protected $casts=['sent_at'=>'datetime']; public function contributor(){return $this->belongsTo(Contributor::class);} public function event(){return $this->belongsTo(Event::class);} }
