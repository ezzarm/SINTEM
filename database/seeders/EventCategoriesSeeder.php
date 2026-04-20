<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('event_categories')->insert([
            ['id' => 1, 'name' => 'Upacara',      'color' => '#4A90D9', 'description' => 'Upacara bendera dan acara seremonial resmi',                      'created_at' => '2026-04-07 09:39:49', 'updated_at' => '2026-04-07 09:39:49'],
            ['id' => 2, 'name' => 'Workshop',     'color' => '#F5A623', 'description' => 'Pelatihan, seminar, atau kegiatan pengembangan diri',              'created_at' => '2026-04-07 09:39:49', 'updated_at' => '2026-04-07 09:39:49'],
            ['id' => 3, 'name' => 'Sosial',       'color' => '#7ED321', 'description' => 'Bakti sosial, penggalangan dana, dan kegiatan kemasyarakatan',     'created_at' => '2026-04-07 09:39:49', 'updated_at' => '2026-04-07 09:39:49'],
            ['id' => 4, 'name' => 'Olahraga',     'color' => '#D0021B', 'description' => 'Pertandingan, turnamen, dan kegiatan olahraga',                    'created_at' => '2026-04-07 09:39:49', 'updated_at' => '2026-04-07 09:39:49'],
            ['id' => 5, 'name' => 'Keagamaan',    'color' => '#9B59B6', 'description' => 'Perayaan hari besar keagamaan dan kegiatan rohani',                'created_at' => '2026-04-07 09:39:49', 'updated_at' => '2026-04-07 09:39:49'],
            ['id' => 6, 'name' => 'Akademik',     'color' => '#1ABC9C', 'description' => 'Ujian, olimpiade, pameran karya, dan kegiatan akademik',           'created_at' => '2026-04-07 09:39:49', 'updated_at' => '2026-04-07 09:39:49'],
            ['id' => 7, 'name' => 'Seni & Budaya','color' => '#E67E22', 'description' => 'Pentas seni, lomba, dan kegiatan budaya sekolah',                  'created_at' => '2026-04-07 09:39:49', 'updated_at' => '2026-04-07 09:39:49'],
            ['id' => 8, 'name' => 'Lainnya',      'color' => '#95A5A6', 'description' => 'Kegiatan yang tidak masuk kategori di atas',                       'created_at' => '2026-04-07 09:39:49', 'updated_at' => '2026-04-07 09:39:49'],
        ]);
    }
}
