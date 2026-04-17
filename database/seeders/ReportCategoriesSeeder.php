<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('report_categories')->insert([
            ['id' => 1, 'category_name' => 'Perundungan/Bullying', 'description' => 'Laporan terkait intimidasi, kekerasan fisik/verbal antar siswa',               'responsible_role_id' => 3, 'created_at' => '2026-04-07 09:39:49', 'updated_at' => '2026-04-07 09:39:49'],
            ['id' => 2, 'category_name' => 'Fasilitas Sekolah',    'description' => 'Kerusakan atau masalah pada fasilitas dan infrastruktur sekolah',               'responsible_role_id' => 4, 'created_at' => '2026-04-07 09:39:49', 'updated_at' => '2026-04-07 09:39:49'],
            ['id' => 3, 'category_name' => 'Kedisiplinan',         'description' => 'Pelanggaran tata tertib sekolah oleh siswa',                                    'responsible_role_id' => 5, 'created_at' => '2026-04-07 09:39:49', 'updated_at' => '2026-04-07 09:39:49'],
            ['id' => 4, 'category_name' => 'Masalah KBM',          'description' => 'Gangguan atau kendala dalam kegiatan belajar mengajar di kelas',                 'responsible_role_id' => 7, 'created_at' => '2026-04-07 09:39:49', 'updated_at' => '2026-04-07 09:39:49'],
        ]);
    }
}
