<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SupabaseStorageService
{
    private string $url;
    private string $key;
    private string $bucket;

    public function __construct()
    {
        $this->url    = rtrim(config('services.supabase.url'), '/');
        $this->key    = config('services.supabase.key');
        $this->bucket = config('services.supabase.bucket');
    }

    public function upload(UploadedFile $file, string $folder): string
    {
        $ext      = $file->getClientOriginalExtension() ?: 'bin';
        $name     = Str::uuid() . '.' . $ext;
        $path     = trim($folder, '/') . '/' . $name;
        $mime     = $file->getMimeType();
        $contents = file_get_contents($file->getRealPath());

        $endpoint = "{$this->url}/storage/v1/object/{$this->bucket}/{$path}";

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->key}",
            'Content-Type'  => $mime,
            'x-upsert'      => 'true',
        ])->withBody($contents, $mime)->post($endpoint);

        if ($response->failed()) {
            throw new \RuntimeException('Supabase upload failed: ' . $response->body());
        }

        return $path;
    }

    public function uploadResized(UploadedFile $file, string $folder, int $maxWidth = 800): string
    {
        $mime = $file->getMimeType();
        $realPath = $file->getRealPath();

        if (str_contains($mime, 'png')) {
            $src = imagecreatefrompng($realPath);
        } elseif (str_contains($mime, 'gif')) {
            $src = imagecreatefromgif($realPath);
        } else {
            $src = imagecreatefromjpeg($realPath);
        }

        $origW = imagesx($src);
        $origH = imagesy($src);

        if ($origW > $maxWidth) {
            $newW = $maxWidth;
            $newH = (int) round($origH * $maxWidth / $origW);
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
        imagejpeg($dst, null, 80);
        $jpeg = ob_get_clean();
        imagedestroy($dst);

        $name     = Str::uuid() . '.jpg';
        $path     = trim($folder, '/') . '/' . $name;
        $endpoint = "{$this->url}/storage/v1/object/{$this->bucket}/{$path}";

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->key}",
            'Content-Type'  => 'image/jpeg',
            'x-upsert'      => 'true',
        ])->withBody($jpeg, 'image/jpeg')->post($endpoint);

        if ($response->failed()) {
            throw new \RuntimeException('Supabase upload failed: ' . $response->body());
        }

        return $path;
    }

    public function delete(string $path): void
    {
        if (empty($path)) return;

        $endpoint = "{$this->url}/storage/v1/object/{$this->bucket}/{$path}";

        Http::withHeaders([
            'Authorization' => "Bearer {$this->key}",
        ])->delete($endpoint);
    }

    public function deleteMany(array $paths): void
    {
        $paths = array_values(array_filter($paths));
        if (empty($paths)) return;

        $endpoint = "{$this->url}/storage/v1/object/{$this->bucket}";

        Http::withHeaders([
            'Authorization' => "Bearer {$this->key}",
            'Content-Type'  => 'application/json',
        ])->delete($endpoint, ['prefixes' => $paths]);
    }

    public function publicUrl(string $path): string
    {
        if (empty($path)) return '';
        return "{$this->url}/storage/v1/object/public/{$this->bucket}/{$path}";
    }
}
