<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventLocationsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('event_locations')->insert([
            ['id' => 1, 'name' => 'Aula Utama',          'description' => 'Ruang serbaguna utama, kapasitas ±500 orang',    'created_at' => '2026-04-07 09:39:49', 'updated_at' => '2026-04-07 09:39:49'],
            ['id' => 2, 'name' => 'Lapangan Sepak Bola', 'description' => 'Lapangan outdoor di belakang sekolah',           'created_at' => '2026-04-07 09:39:49', 'updated_at' => '2026-04-07 09:39:49'],
            ['id' => 3, 'name' => 'Lapangan Upacara',    'description' => 'Lapangan utama untuk upacara bendera',           'created_at' => '2026-04-07 09:39:49', 'updated_at' => '2026-04-07 09:39:49'],
            ['id' => 4, 'name' => 'Ruang Kelas',         'description' => 'Kegiatan berlangsung di masing-masing kelas',    'created_at' => '2026-04-07 09:39:49', 'updated_at' => '2026-04-07 09:39:49'],
            ['id' => 5, 'name' => 'Lab Komputer',        'description' => 'Laboratorium komputer lantai 2',                 'created_at' => '2026-04-07 09:39:49', 'updated_at' => '2026-04-07 09:39:49'],
            ['id' => 6, 'name' => 'Perpustakaan',        'description' => 'Ruang baca dan referensi sekolah',               'created_at' => '2026-04-07 09:39:49', 'updated_at' => '2026-04-07 09:39:49'],
            ['id' => 7, 'name' => 'Masjid Sekolah',      'description' => 'Mushola/masjid untuk kegiatan keagamaan',        'created_at' => '2026-04-07 09:39:49', 'updated_at' => '2026-04-07 09:39:49'],
            ['id' => 8, 'name' => 'Lapangan Basket',     'description' => 'Lapangan olahraga basket di samping gedung',     'created_at' => '2026-04-07 09:39:49', 'updated_at' => '2026-04-07 09:39:49'],
        ]);
    }
}
