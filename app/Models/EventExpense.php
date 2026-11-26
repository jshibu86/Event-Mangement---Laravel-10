<?php

namespace App\Models;

use App\Traits\HandlesFileUploads;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;

class EventExpense extends Model implements HasMedia
{
    use HandlesFileUploads;

    protected $guarded = [];

    protected $casts = ['amount' => 'decimal:2'];

    public function registerMediaCollections(): void
    {
        $this->collection('bills'); // private on S3 in production, local in dev
    }
}