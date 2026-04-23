<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id'         => 1,
                'identifier' => 'admin_stemba',
                'name'       => 'Super Admin Utama',
                'email'      => 'admin@stemba.sch.id',
                'password'   => '$2y$12$rPFLUZCEFOnhTIeM0diROO.Ke46y1TrMpL0N7gUB8Bwcep.8xwDyC',
                'role_id'    => 1,
                'status'     => 'active',
                'last_login' => null,
                'created_at' => '2026-04-07 09:39:48',
                'updated_at' => '2026-04-07 02:40:01',
            ],
            [
                'id'         => 2,
                'identifier' => '220001',
                'name'       => 'Muhammad Rizki',
                'email'      => 'rizki@siswa.sch.id',
                'password'   => '$2y$12$FH2NwLgkKnqaKhO08149..oHFC2xqEfld2vLaeYngF3dPm2t2FEwq',
                'role_id'    => 2,
                'status'     => 'active',
                'last_login' => null,
                'created_at' => '2026-04-07 09:39:48',
                'updated_at' => '2026-04-07 02:43:06',
            ],
            [
                'id'         => 3,
                'identifier' => 'bk_budi',
                'name'       => 'Drs. Budi Raharjo',
                'email'      => 'budi@stemba.sch.id',
                'password'   => '$2y$12$Fvq2iyorxz9T//HotXicROFa3kSTRqZQfUnCuX1dKIqnFYSztVAj.',
                'role_id'    => 3,
                'status'     => 'active',
                'last_login' => null,
                'created_at' => '2026-04-07 09:39:48',
                'updated_at' => '2026-04-07 02:43:44',
            ],
            [
                'id'         => 4,
                'identifier' => 'tu_santi',
                'name'       => 'Santi Susanti',
                'email'      => 'santi@stemba.sch.id',
                'password'   => '$2y$12$E4qZmc1JGk23jtDiWetbaOQ0v/2QB328302hNKxz7guNv72pWzRAq',
                'role_id'    => 4,
                'status'     => 'active',
                'last_login' => null,
                'created_at' => '2026-04-07 09:39:48',
                'updated_at' => '2026-04-07 02:44:32',
            ],
        ]);
    }
}
