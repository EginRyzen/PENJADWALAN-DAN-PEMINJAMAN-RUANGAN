<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'DIREKTUR',
            'WADIR 1',
            'WADIR 2',
            'KAPRODI',
            'DOSEN',
            'TENAGA_TU',
            'MAHASISWA',
        ];

        foreach ($roles as $role) {
            Role::create([
                'name_role' => $role,
            ]);
        }
    }
}