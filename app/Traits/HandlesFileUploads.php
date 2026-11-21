<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HandlesFileUploads
{
    public static function mediaDisk(): string
    {
        return config('filesystems.media_disk', 'public');
    }

    /**
     * Store one file and return its relative path (save this in the database).
     * If $oldPath is given, the old file is deleted after the new one is saved.
     */
    public static function uploadFile(UploadedFile $file, string $folder = 'uploads', ?string $oldPath = null): string
    {
        $name = Str::uuid() . '.' . $file->extension();   // extension guessed from real MIME type
        $path = $file->storeAs(trim($folder, '/'), $name, static::mediaDisk());

        if ($path === false) {
            throw new \RuntimeException('File upload failed.');
        }

        if ($oldPath) {
            static::deleteFile($oldPath);
        }

        return $path;
    }

    /** Store several files and return an array of paths. */
    public static function uploadFiles(array $files, string $folder = 'uploads'): array
    {
        return collect($files)
            ->map(fn ($file) => static::uploadFile($file, $folder))
            ->all();
    }

    public static function deleteFile(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        return Storage::disk(static::mediaDisk())->delete($path);
    }

    /** Public URL (works for the branding/ folder you made public on S3). */
    public static function fileUrl(?string $path): ?string
    {
        return $path ? Storage::disk(static::mediaDisk())->url($path) : null;
    }

    /** Expiring link for private S3 files such as expense bills. Falls back to url() on local disk. */
    public static function privateFileUrl(?string $path, int $minutes = 10): ?string
    {
        if (!$path) {
            return null;
        }

        $disk = Storage::disk(static::mediaDisk());

        return static::mediaDisk() === 's3'
            ? $disk->temporaryUrl($path, now()->addMinutes($minutes))
            : $disk->url($path);
    }

    /** Base64 data URI, useful for images inside DomPDF templates. */
    public static function fileDataUri(?string $path): ?string
    {
        $disk = Storage::disk(static::mediaDisk());

        if (!$path || !$disk->exists($path)) {
            return null;
        }

        return 'data:' . $disk->mimeType($path) . ';base64,' . base64_encode($disk->get($path));
    }
}