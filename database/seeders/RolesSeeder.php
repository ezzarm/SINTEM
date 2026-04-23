<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['id' => 1, 'role_name' => 'superadmin',   'description' => 'Full system access; manages users, roles, and all content',              'created_at' => '2026-04-07 09:39:48', 'updated_at' => '2026-04-07 09:39:48'],
            ['id' => 2, 'role_name' => 'user',          'description' => 'Regular student or general user with read-only access to public content',  'created_at' => '2026-04-07 09:39:48', 'updated_at' => '2026-04-07 09:39:48'],
            ['id' => 3, 'role_name' => 'BK',            'description' => 'Bimbingan Konseling – handles student counseling and bullying reports',     'created_at' => '2026-04-07 09:39:48', 'updated_at' => '2026-04-07 09:39:48'],
            ['id' => 4, 'role_name' => 'TU',            'description' => 'Tata Usaha – administrative staff managing school facilities and announcements', 'created_at' => '2026-04-07 09:39:48', 'updated_at' => '2026-04-07 09:39:48'],
            ['id' => 5, 'role_name' => 'Kesiswaan',     'description' => 'Student affairs division; handles discipline reports',                      'created_at' => '2026-04-07 09:39:48', 'updated_at' => '2026-04-07 09:39:48'],
            ['id' => 6, 'role_name' => 'Wali Kelas',    'description' => 'Homeroom teacher with access to class-specific information',                'created_at' => '2026-04-07 09:39:48', 'updated_at' => '2026-04-07 09:39:48'],
            ['id' => 7, 'role_name' => 'Guru NA',       'description' => 'Subject teacher handling academic (KBM) related reports',                   'created_at' => '2026-04-07 09:39:48', 'updated_at' => '2026-04-07 09:39:48'],
            ['id' => 8, 'role_name' => 'Guru Jurusan',  'description' => 'Vocational/department teacher with department-specific access',              'created_at' => '2026-04-07 09:39:48', 'updated_at' => '2026-04-07 09:39:48'],
        ]);
    }
}
