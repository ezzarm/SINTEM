<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LostFoundsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('lost_founds')->insert([
            [
                'id'          => 1,
                'user_id'     => 1,
                'type'        => 'found',
                'item_name'   => 'adasdasd',
                'found_at'    => null,
                'description' => null,
                'status'      => 'approved',
                'created_at'  => '2026-04-07 04:03:19',
                'updated_at'  => '2026-04-07 04:03:19',
            ],
        ]);
    }
}
