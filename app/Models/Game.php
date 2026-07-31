<?php
namespace App\Models;
use App\Models\Concerns\HasHistoricalRatings; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\SoftDeletes;
class Game extends Model { use SoftDeletes, HasHistoricalRatings; protected $guarded=[]; protected $casts=['video_urls'=>'array','status'=>'boolean']; public function attachments(){return $this->hasMany(GameAttachment::class);} }
