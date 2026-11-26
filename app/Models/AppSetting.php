<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Traits\HandlesFileUploads;
class AppSetting extends Model implements HasMedia
{
    use HandlesFileUploads;

    protected $table = 'app_settings';
    protected $guarded = [];

    public static function value(string $key, mixed $default = null): mixed
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function branding(): self
    {
        return static::firstOrCreate(['key' => 'branding'], ['value' => null]);
    }

    public static function logoUrl(): ?string
    {
        return static::branding()->mediaUrl('logo');
    }

    public function registerMediaCollections(): void
    {
        $this->collection('logo', public: true, single: true,
            mimes: ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml']);
    }
}