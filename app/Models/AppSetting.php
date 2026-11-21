<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HandlesFileUploads;
class AppSetting extends Model { 
    use HandlesFileUploads;
    protected $table='app_settings'; protected $guarded=[]; public static function value(string $key, mixed $default=null): mixed { return static::where('key',$key)->value('value') ?? $default; } }
