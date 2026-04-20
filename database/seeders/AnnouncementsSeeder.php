<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnnouncementsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('announcements')->insert([
            [
                'id'           => 1,
                'title'        => 'Info Ujian',
                'content'      => 'Jadwal ujian semester genap telah ditetapkan. Silakan unduh lampiran untuk melihat jadwal lengkap.',
                'is_published' => 1,
                'created_by'   => 4,
                'created_at'   => '2026-03-20 04:30:49',
                'updated_at'   => '2026-04-07 03:03:29',
            ],
        ]);
    }
}
