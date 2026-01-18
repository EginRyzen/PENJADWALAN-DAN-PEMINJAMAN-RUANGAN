<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roleAdmin = Role::where('name_role', 'TENAGA_TU')->first();
        $roleDosen = Role::where('name_role', 'DOSEN')->first();
        $roleMhs = Role::where('name_role', 'MAHASISWA')->first();
        $roleDirektur = Role::where('name_role', 'DIREKTUR')->first();
        $roleWadir1 = Role::where('name_role', 'WADIR 1')->first();
        $roleWadir2 = Role::where('name_role', 'WADIR 2')->first();

        $wadir1 = User::create([
            'name' => 'Dr. Ir. Heru Prasetyo, M.T.',
            'username' => 'heru_wadir1',
            'email' => 'heru.wadir1@kampus.ac.id',
            'identity_number' => '197802152005011003',
            'password' => Hash::make('password123'),
            'phone_number' => '081233445566',
            'is_active' => true,
        ]);
        $wadir1->roles()->attach([$roleDosen->id, $roleWadir1->id]);

        $wadir2 = User::create([
            'name' => 'Dra. Siti Aminah, M.Si.',
            'username' => 'siti_wadir2',
            'email' => 'siti.wadir2@kampus.ac.id',
            'identity_number' => '198005202008122001',
            'password' => Hash::make('password123'),
            'phone_number' => '081277889900',
            'is_active' => true,
        ]);
        $wadir2->roles()->attach([$roleDosen->id, $roleWadir2->id]);

        $tu = User::create([
            'name' => 'Admin Sarpras',
            'username' => 'admin_tu',
            'email' => 'tu@kampus.ac.id',
            'identity_number' => '198801012015011001',
            'password' => Hash::make('password123'),
            'phone_number' => '081234567890',
            'is_active' => true,
        ]);
        $tu->roles()->attach($roleAdmin->id);

        $mhs = User::create([
            'name' => 'Andi Wijaya',
            'username' => 'andi_mhs',
            'email' => 'andi@student.kampus.ac.id',
            'identity_number' => '20241010001',
            'password' => Hash::make('password123'),
            'phone_number' => '085711112222',
            'is_active' => true,
        ]);
        $mhs->roles()->attach($roleMhs->id);

        $direktur = User::create([
            'name' => 'Prof. Ahmad Subagjo',
            'username' => 'ahmad_dir',
            'email' => 'ahmad@kampus.ac.id',
            'identity_number' => '196508201990031001',
            'password' => Hash::make('password123'),
            'phone_number' => '081122334455',
            'is_active' => true,
        ]);
        $direktur->roles()->attach([$roleDosen->id, $roleDirektur->id]);
    }
}