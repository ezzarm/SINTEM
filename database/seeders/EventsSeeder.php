<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('events')->insert([
            [
                'id'            => 1,
                'event_name'    => 'Upacara Kemerdekaan',
                'category_id'   => 1,
                'location_id'   => 3,
                'event_date'    => '2026-08-17',
                'event_date_end'=> null,
                'description'   => 'Pelaksanaan upacara bendera hari ini akan dilaksanakan dengan durasi yang lebih singkat dikarenakan suhu udara yang meningkat secara ekstrem. Seluruh siswa diharapkan tetap menjaga hidrasi dan segera melapor ke petugas UKS jika merasa pusing atau tidak enak badan.',
                'is_published'  => 1,
                'created_by'    => 1,
                'created_at'    => '2026-04-07 02:58:06',
                'updated_at'    => '2026-04-07 02:58:21',
            ],
        ]);
    }
}
