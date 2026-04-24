<?php
/**
 * Jalankan: php supabase_init.php
 * Script ini membuat bucket sintem-files di Supabase Storage.
 */

$supabaseUrl = 'https://ehbmbivtxlnjobtpixsw.supabase.co';
$serviceKey  = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImVoYm1iaXZ0eGxuam9idHBpeHN3Iiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc3Njk0NjAyOCwiZXhwIjoyMDkyNTIyMDI4fQ.NBsfZmDmXMMPQOidSTu6c_OsQ6BvfNOobmeVY3qRDJQ';
$bucket      = 'sintem-files';

function supabaseRequest(string $method, string $url, string $key, array $body = []): array
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $key,
        'Content-Type: application/json',
        'apikey: ' . $key,
    ]);
    if (!empty($body)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['code' => $httpCode, 'body' => json_decode($response, true)];
}

echo "=== Supabase Init ===\n\n";

// 1. Create bucket
echo "1. Membuat bucket '{$bucket}'...\n";
$result = supabaseRequest('POST', "{$supabaseUrl}/storage/v1/bucket", $serviceKey, [
    'id'     => $bucket,
    'name'   => $bucket,
    'public' => true,
]);

if ($result['code'] === 200 || $result['code'] === 201) {
    echo "   ✓ Bucket berhasil dibuat.\n";
} elseif (isset($result['body']['error']) && str_contains($result['body']['error'], 'already exists')) {
    echo "   ✓ Bucket sudah ada.\n";
} else {
    echo "   ✗ Gagal: " . json_encode($result['body']) . "\n";
}

// 2. Test DB connection
echo "\n2. Test koneksi PostgreSQL...\n";
try {
    $pdo = new PDO(
        'pgsql:host=db.ehbmbivtxlnjobtpixsw.supabase.co;port=5432;dbname=postgres;sslmode=require',
        'postgres',
        'sintempass12'
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query("SELECT current_database(), current_user, version()");
    $row  = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "   ✓ Terhubung ke: {$row['current_database']} sebagai {$row['current_user']}\n";
} catch (\Throwable $e) {
    echo "   ✗ Gagal: " . $e->getMessage() . "\n";
}

echo "\n=== Selesai ===\n";
echo "Selanjutnya: jalankan schema SQL di Supabase SQL Editor.\n";
echo "File: database/migrations/supabase_schema.sql\n";
