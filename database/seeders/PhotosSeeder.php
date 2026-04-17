<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhotosSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('photos')->insert([
            [
                'id'          => 1,
                'source_type' => 'anonymous_report',
                'source_id'   => 1,
                'file_name'   => 'bukti_foto.jpg',
                'file_path'   => 'uploads/photos/reports/bukti_foto.jpg',
                'file_type'   => 'image/jpeg',
                'file_size'   => 2048000,
                'uploaded_by' => null,
                'created_at'  => '2026-04-07 09:39:49',
                'updated_at'  => '2026-04-07 09:39:49',
            ],
            [
                'id'          => 2,
                'source_type' => 'lost_found',
                'source_id'   => 1,
                'file_name'   => 'foto_barang_1.jpeg',
                'file_path'   => 'upload/photos/lost_founds/DRfaQopFrlix6uPTvPBZzJrLbIXRrklHtYcL1Ke6.jpg',
                'file_type'   => 'image/jpeg',
                'file_size'   => 36451,
                'uploaded_by' => 2,
                'created_at'  => '2026-04-07 02:45:18',
                'updated_at'  => '2026-04-07 02:45:18',
            ],
            [
                'id'          => 3,
                'source_type' => 'lost_found',
                'source_id'   => 2,
                'file_name'   => 'foto_barang_2.jpeg',
                'file_path'   => 'upload/photos/lost_founds/wkxfUkRKSt6oAj2J4x5KelETAoH8M44uyeCJRLyZ.jpg',
                'file_type'   => 'image/jpeg',
                'file_size'   => 71433,
                'uploaded_by' => 2,
                'created_at'  => '2026-04-07 02:47:47',
                'updated_at'  => '2026-04-07 02:47:47',
            ],
            [
                'id'          => 4,
                'source_type' => 'anonymous_report',
                'source_id'   => 2,
                'file_name'   => 'foto_laporan_2.jpeg',
                'file_path'   => 'upload/photos/reports/9asFbfh9wSu4vypKeCpj0yJ73ZuMWlvRWC1ELxcs.jpg',
                'file_type'   => 'image/jpeg',
                'file_size'   => 64119,
                'uploaded_by' => null,
                'created_at'  => '2026-04-07 02:49:08',
                'updated_at'  => '2026-04-07 02:49:08',
            ],
            [
                'id'          => 5,
                'source_type' => 'event',
                'source_id'   => 1,
                'file_name'   => 'foto_upacara.jpeg',
                'file_path'   => 'uploads/photos/events/oRtG20di1gEYR7gaAYvUYzGoHJVol5EsEkdPllch.jpg',
                'file_type'   => 'image/jpeg',
                'file_size'   => 49747,
                'uploaded_by' => 1,
                'created_at'  => '2026-04-07 02:58:06',
                'updated_at'  => '2026-04-07 02:58:06',
            ],
            [
                'id'          => 7,
                'source_type' => 'anonymous_report',
                'source_id'   => 3,
                'file_name'   => 'fan_broke.jpeg',
                'file_path'   => 'upload/photos/reports/z82bVXlR6txp7iRU8AQe2jY02VJ2HTEZCMR113WI.jpg',
                'file_type'   => 'image/jpeg',
                'file_size'   => 23183,
                'uploaded_by' => null,
                'created_at'  => '2026-04-07 03:01:24',
                'updated_at'  => '2026-04-07 03:01:24',
            ],
            [
                'id'          => 8,
                'source_type' => 'announcement',
                'source_id'   => 1,
                'file_name'   => 'foto_pengumuman.png',
                'file_path'   => 'uploads/photos/announcements/9q8RZSWvvLd7FfsWzkVLrHIPKD7j8RPzpq4kjEoQ.png',
                'file_type'   => 'image/png',
                'file_size'   => 2090723,
                'uploaded_by' => 1,
                'created_at'  => '2026-04-07 03:03:29',
                'updated_at'  => '2026-04-07 03:03:29',
            ],
            [
                'id'          => 9,
                'source_type' => 'lost_found',
                'source_id'   => 3,
                'file_name'   => 'foto_barang_3.jpeg',
                'file_path'   => 'upload/photos/lost_founds/qVygbQqULCgotvJdlbJbgTNBQUU53L0v9YGMW19f.jpg',
                'file_type'   => 'image/jpeg',
                'file_size'   => 23183,
                'uploaded_by' => 2,
                'created_at'  => '2026-04-07 03:16:36',
                'updated_at'  => '2026-04-07 03:16:36',
            ],
            [
                'id'          => 10,
                'source_type' => 'lost_found',
                'source_id'   => 4,
                'file_name'   => 'foto_barang_4.jpeg',
                'file_path'   => 'upload/photos/lost_founds/rbmgzyyXPLnipLA791GHEyw0ZhGC9kOtaTa0GHfa.jpg',
                'file_type'   => 'image/jpeg',
                'file_size'   => 67029,
                'uploaded_by' => 1,
                'created_at'  => '2026-04-07 03:26:28',
                'updated_at'  => '2026-04-07 03:26:28',
            ],
        ]);
    }
}
