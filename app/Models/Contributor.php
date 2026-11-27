<?php

namespace App\Models;

use App\Traits\HandlesFileUploads;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;

class Contributor extends Model implements HasMedia
{
    use HandlesFileUploads;

    protected $guarded = [];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function contributions()
    {
        return $this->hasMany(EventContribution::class);
    }

    public function registerMediaCollections(): void
    {
        $this->collection(
            'photo',
            public: true,
            single: true, // a new upload replaces the old photo
            mimes: ['image/jpeg', 'image/png', 'image/webp']
        );
    }
}