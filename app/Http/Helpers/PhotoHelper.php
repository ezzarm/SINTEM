<?php

namespace App\Http\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PhotoHelper
{
    /**
     * Simpan foto ke storage (public disk) dan insert ke tabel photos.
     * Menggantikan pendekatan base64-in-DB yang menyebabkan error 500 di Railway
     * karena MySQL max_allowed_packet tidak cukup untuk data base64 besar.
     */
    public static function store(
        UploadedFile $file,
        string $sourceType,
        int $sourceId,
        ?int $uploadedBy = null
    ): void {
        $folder   = 'upload/photos/' . $sourceType;
        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $path     = $file->storeAs($folder, $filename, 'public');

        DB::table('photos')->insert([
            'source_type' => $sourceType,
            'source_id'   => $sourceId,
            'file_name'   => $file->getClientOriginalName(),
            'file_path'   => $path,
            'file_data'   => null,
            'file_type'   => $file->getMimeType(),
            'file_size'   => $file->getSize(),
            'uploaded_by' => $uploadedBy,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    /**
     * Hapus foto dari storage dan dari DB.
     */
    public static function delete(string $sourceType, int $sourceId): void
    {
        $photos = DB::table('photos')
            ->where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->get();

        foreach ($photos as $photo) {
            if ($photo->file_path) {
                Storage::disk('public')->delete($photo->file_path);
            }
        }

        DB::table('photos')
            ->where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->delete();
    }

    /**
     * Ambil URL foto untuk ditampilkan di view.
     * Menangani keduanya: file_path (storage) dan file_data (base64 lama).
     */
    public static function url(?object $photo): ?string
    {
        if (!$photo) return null;

        if (!empty($photo->file_path)) {
            return Storage::disk('public')->url($photo->file_path);
        }

        // Fallback: data base64 lama yang sudah tersimpan di DB
        if (!empty($photo->file_data)) {
            return $photo->file_data;
        }

        return null;
    }
}
