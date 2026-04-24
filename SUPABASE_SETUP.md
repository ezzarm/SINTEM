# Supabase Integration Setup

## 1. Database

Run `database/migrations/supabase_schema.sql` di Supabase SQL Editor.

URL: https://supabase.com/dashboard/project/ehbmbivtxlnjobtpixsw/sql

## 2. Storage Bucket

Buat bucket di Supabase Storage:
- Nama: `sintem-files`
- Public: **YES** (centang "Public bucket")

Atau via API:
```bash
curl -X POST https://ehbmbivtxlnjobtpixsw.supabase.co/storage/v1/bucket \
  -H "Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImVoYm1iaXZ0eGxuam9idHBpeHN3Iiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc3Njk0NjAyOCwiZXhwIjoyMDkyNTIyMDI4fQ.NBsfZmDmXMMPQOidSTu6c_OsQ6BvfNOobmeVY3qRDJQ" \
  -H "Content-Type: application/json" \
  -d '{"id":"sintem-files","name":"sintem-files","public":true}'
```

## 3. Storage Folder Structure

File akan otomatis tersimpan di folder:
```
sintem-files/
├── photos/
│   ├── announcement/     ← foto pengumuman
│   ├── event/            ← foto kegiatan
│   ├── lost_found/       ← foto temuan/kehilangan
│   └── anonymous_report/ ← foto laporan anonim
└── attachments/
    └── announcement/     ← file lampiran pengumuman
```

## 4. Environment Variables

Edit `.env`:
```
DB_CONNECTION=pgsql
DB_HOST=db.ehbmbivtxlnjobtpixsw.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=sintempass12
DB_SSLMODE=require

SUPABASE_URL=https://ehbmbivtxlnjobtpixsw.supabase.co
SUPABASE_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
SUPABASE_BUCKET=sintem-files
```

## 5. Deploy

```bash
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Arsitektur

```
Upload file  →  SupabaseStorageService::upload()
             →  Supabase Storage (sintem-files bucket)
             →  Simpan path + public URL ke tabel photos/attachments

Tampil foto  →  PhotoHelper::url($photo)
             →  Return public URL dari Supabase Storage
             →  https://ehbmbivtxlnjobtpixsw.supabase.co/storage/v1/object/public/sintem-files/{path}
```

**TIDAK ADA** file lokal, base64 di database, /storage/**, atau folder /uploads.
