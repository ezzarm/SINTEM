<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnonymousReportsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('anonymous_reports')->insert([
            [
                'id'             => 1,
                'ticket_number'  => 'TKT-001',
                'category_id'    => 1,
                'report_content' => 'Laporan bullying di parkiran belakang. Ada sekelompok siswa yang mengintimidasi adik kelas setiap pulang sekolah.',
                'admin_notes'    => null,
                'status'         => 'in_progress',
                'resolved_at'    => null,
                'created_at'     => '2026-03-20 04:30:48',
                'updated_at'     => '2026-04-07 02:50:35',
            ],
            [
                'id'             => 2,
                'ticket_number'  => 'TKT-002',
                'category_id'    => 4,
                'report_content' => '<span class="">Melaporkan ketidaknyamanan saat proses Belajar Mengajar (KBM) di kelas12 SIJA 1</span><span class="">.</span><span class=""> Suhu ruangan sangat tinggi (panas) sehingga mengakibatkan konsentrasi siswa menurun drastis dan proses pembelajaran menjadi tidak efisien.</span>',
                'admin_notes'    => null,
                'status'         => 'pending',
                'resolved_at'    => null,
                'created_at'     => '2026-04-07 02:49:08',
                'updated_at'     => '2026-04-07 02:49:08',
            ],
            [
                'id'             => 3,
                'ticket_number'  => 'TKT-003',
                'category_id'    => 2,
                'report_content' => '3 buah kipas rusak',
                'admin_notes'    => null,
                'status'         => 'solved',
                'resolved_at'    => '2026-04-07 03:01:56',
                'created_at'     => '2026-04-07 03:01:24',
                'updated_at'     => '2026-04-07 03:01:56',
            ],
        ]);
    }
}
