<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttachmentsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('attachments')->insert([
            [
                'id'              => 1,
                'source_type'     => 'announcement',
                'source_id'       => 1,
                'attachment_type' => 'file',
                'file_name'       => 'jadwal_uas.pdf',
                'file_path'       => 'uploads/attachments/announcements/jadwal_uas.pdf',
                'file_type'       => 'application/pdf',
                'file_size'       => 512000,
                'link_url'        => null,
                'link_label'      => null,
                'label'           => 'Jadwal UAS Semester Genap',
                'uploaded_by'     => 4,
                'created_at'      => '2026-04-07 09:39:49',
                'updated_at'      => '2026-04-07 09:39:49',
            ],
            [
                'id'              => 2,
                'source_type'     => 'announcement',
                'source_id'       => 1,
                'attachment_type' => 'link',
                'file_name'       => null,
                'file_path'       => null,
                'file_type'       => null,
                'file_size'       => null,
                'link_url'        => 'https://drive.google.com/file/d/example',
                'link_label'      => 'Lihat di Google Drive',
                'label'           => 'Versi lengkap jadwal',
                'uploaded_by'     => 4,
                'created_at'      => '2026-04-07 09:39:49',
                'updated_at'      => '2026-04-07 09:39:49',
            ],
        ]);
    }
}
