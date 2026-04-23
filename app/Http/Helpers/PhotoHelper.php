<?php

namespace App\Http\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PhotoHelper
{
    /**
     * Simpan foto ke kolom file_data (base64) di tabel photos.
     * Gambar di-resize & compress dulu supaya ukurannya kecil
     * dan tidak mentok max_allowed_packet Railway MySQL (~4MB default).
     *
     * Target: max 800px lebar, JPEG quality 75 → biasanya < 200KB base64.
     */
    public static function store(
        UploadedFile $file,
        string $sourceType,
        int $sourceId,
        ?int $uploadedBy = null
    ): void {
        // Resize & compress pakai GD (sudah include di PHP image default)
        $compressed = self::compressImage($file);

        $base64 = 'data:image/jpeg;base64,' . base64_encode($compressed);

        DB::table('photos')->insert([
            'source_type' => $sourceType,
            'source_id'   => $sourceId,
            'file_name'   => $file->getClientOriginalName(),
            'file_path'   => '',      // tidak pakai storage
            'file_data'   => $base64, // simpan langsung di DB
            'file_type'   => 'image/jpeg',
            'file_size'   => strlen($compressed),
            'uploaded_by' => $uploadedBy,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    /**
     * Compress & resize gambar ke max 800px lebar, JPEG quality 75.
     * Return raw JPEG bytes (bukan base64).
     */
    private static function compressImage(UploadedFile $file): string
    {
        $mime = $file->getMimeType();
        $path = $file->getRealPath();

        // Load image sesuai tipe
        $src = match (true) {
            str_contains($mime, 'jpeg'), str_contains($mime, 'jpg') => imagecreatefromjpeg($path),
            str_contains($mime, 'png')  => imagecreatefrompng($path),
            str_contains($mime, 'gif')  => imagecreatefromgif($path),
            str_contains($mime, 'webp') => imagecreatefromwebp($path),
            default                     => imagecreatefromjpeg($path),
        };

        $origW = imagesx($src);
        $origH = imagesy($src);

        // Resize kalau lebar > 800px
        $maxW = 800;
        if ($origW > $maxW) {
            $newW = $maxW;
            $newH = (int) round($origH * $maxW / $origW);
        } else {
            $newW = $origW;
            $newH = $origH;
        }

        $dst = imagecreatetruecolor($newW, $newH);

        // Preserve transparency untuk PNG
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
        imagefilledrectangle($dst, 0, 0, $newW, $newH, $transparent);

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
        imagedestroy($src);

        // Output ke buffer sebagai JPEG quality 75
        ob_start();
        imagejpeg($dst, null, 75);
        $jpeg = ob_get_clean();
        imagedestroy($dst);

        return $jpeg;
    }

    /**
     * Hapus foto dari DB (tidak ada file di storage yang perlu dihapus).
     */
    public static function delete(string $sourceType, int $sourceId): void
    {
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
