<?php

namespace App\Http\Helpers;

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
        $mime = $file->getMimeType() ?? 'image/jpeg';
        $path = $file->getRealPath();

        $rawBytes = file_get_contents($path);
        $src = @imagecreatefromstring($rawBytes);

        if ($src === false) {
            DB::table('photos')->insert([
                'source_type' => $sourceType,
                'source_id'   => $sourceId,
                'file_name'   => $file->getClientOriginalName(),
                'file_path'   => '',
                'file_data'   => 'data:' . $mime . ';base64,' . base64_encode($rawBytes),
                'file_type'   => $mime,
                'file_size'   => strlen($rawBytes),
                'uploaded_by' => $uploadedBy,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
            return;
        }

        $origW = imagesx($src);
        $origH = imagesy($src);

        if ($origW > 800) {
            $newW = 800;
            $newH = (int) round($origH * 800 / $origW);
        } else {
            $newW = $origW;
            $newH = $origH;
        }

        $dst   = imagecreatetruecolor($newW, $newH);
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefilledrectangle($dst, 0, 0, $newW, $newH, $white);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
        imagedestroy($src);

        ob_start();
        imagejpeg($dst, null, 75);
        $jpeg = ob_get_clean();
        imagedestroy($dst);

        DB::table('photos')->insert([
            'source_type' => $sourceType,
            'source_id'   => $sourceId,
            'file_name'   => $file->getClientOriginalName(),
            'file_path'   => '',
            'file_data'   => 'data:image/jpeg;base64,' . base64_encode($jpeg),
            'file_type'   => 'image/jpeg',
            'file_size'   => strlen($jpeg),
            'uploaded_by' => $uploadedBy,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    public static function delete(string $sourceType, int $sourceId): void
    {
        DB::table('photos')
            ->where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->delete();
    }

    public static function url(?object $photo): ?string
    {
        if (!$photo) return null;
        return !empty($photo->file_data) ? $photo->file_data : null;
    }
}