<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;

class ImageService
{
    public function uploadAndResize(
        UploadedFile $file,
        string $directory,
        int $width = 800,
        int $height = 600
    ): string {
        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $path = $directory . '/' . $filename;

        // Create and resize image
        $image = Image::read($file->getRealPath());
        $image->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // Save to storage
        Storage::disk('public')->put($path, $image->encode());

        return $path;
    }

    public function createThumbnail(
        string $imagePath,
        int $width = 300,
        int $height = 200
    ): string {
        $image = Image::read(Storage::disk('public')->path($imagePath));
        $image->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $thumbnailPath = str_replace('.', '_thumb.', $imagePath);
        Storage::disk('public')->put($thumbnailPath, $image->encode());

        return $thumbnailPath;
    }

    public function deleteImage(string $path): bool
    {
        return Storage::disk('public')->delete($path);
    }
}
