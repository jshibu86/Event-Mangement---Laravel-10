<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\MediaCollection;

trait HandlesFileUploads
{
    use InteractsWithMedia;

    /** Production => S3, otherwise local public disk. */
    public static function mediaDisk(bool $public = false): string
    {
        $disk = config('filesystems.media_disk', 'public');

        return $disk === 's3' && $public ? 's3_public' : $disk;
    }

    /** Use inside registerMediaCollections(). */
    protected function collection(
        string $name,
        bool $public = false,
        bool $single = false,
        ?array $mimes = null
    ): MediaCollection {
        $collection = $this->addMediaCollection($name)
            ->useDisk(static::mediaDisk($public));

        if ($single) {
            $collection->singleFile(); // new upload deletes the old file
        }
        if ($mimes) {
            $collection->acceptsMimeTypes($mimes);
        }

        return $collection;
    }

    /** URL for public files, expiring URL for private S3 files. */
    public function mediaUrl(string $collection, int $minutes = 10): ?string
    {
        $media = $this->getFirstMedia($collection);

        if (!$media) {
            return null;
        }

        return $media->disk === 's3'
            ? $media->getTemporaryUrl(now()->addMinutes($minutes))
            : $media->getUrl();
    }

    /** Base64 data URI for DomPDF templates. */
    public function mediaDataUri(string $collection): ?string
    {
        $media = $this->getFirstMedia($collection);

        if (!$media || !Storage::disk($media->disk)->exists($media->getPathRelativeToRoot())) {
            return null;
        }

        return 'data:' . $media->mime_type . ';base64,'
            . base64_encode(Storage::disk($media->disk)->get($media->getPathRelativeToRoot()));
    }
}