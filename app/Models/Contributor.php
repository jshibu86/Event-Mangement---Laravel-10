<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class Contributor extends Model { protected $guarded=[]; protected $casts=['status'=>'boolean']; public function contributions(){return $this->hasMany(EventContribution::class);} }
