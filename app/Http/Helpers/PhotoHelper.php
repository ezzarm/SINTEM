<?php

namespace App\Http\Helpers;

use App\Services\SupabaseStorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class PhotoHelper
{
    public static function store(
        UploadedFile $file,
        string $sourceType,
        int $sourceId,
        ?int $uploadedBy = null
    ): void {
        $storage = app(SupabaseStorageService::class);
        $folder  = 'photos/' . $sourceType;
        $path    = $storage->uploadResized($file, $folder);
        $url     = $storage->publicUrl($path);

        DB::table('photos')->insert([
            'source_type' => $sourceType,
            'source_id'   => $sourceId,
            'file_name'   => $file->getClientOriginalName(),
            'file_path'   => $path,
            'file_data'   => $url,
            'file_type'   => 'image/jpeg',
            'file_size'   => $file->getSize(),
            'uploaded_by' => $uploadedBy,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    public static function delete(string $sourceType, int $sourceId): void
    {
        $storage = app(SupabaseStorageService::class);

        $photos = DB::table('photos')
            ->where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->get();

        foreach ($photos as $photo) {
            if (!empty($photo->file_path)) {
                $storage->delete($photo->file_path);
            }
        }

        DB::table('photos')
            ->where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->delete();
    }

    public static function url(?object $photo): ?string
    {
        if (!$photo) return null;

        if (!empty($photo->file_path)) {
            return app(SupabaseStorageService::class)->publicUrl($photo->file_path);
        }

        if (!empty($photo->file_data)) {
            return $photo->file_data;
        }

        return null;
    }
}
