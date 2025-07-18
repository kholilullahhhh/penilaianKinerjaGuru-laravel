<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $akun = [
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'password' => bcrypt('admin'),
                'role' => 'admin',
            ],
            [
                'name' => 'kepala Sekolah',
                'username' => 'kepala',
                'password' => bcrypt('kepala'),
                'role' => 'kepala_sekolah',
            ],
            [
                'name' => 'Guru',
                'username' => 'guru',
                'password' => bcrypt('guru'),
                'nuptk' => '1234567890',
                'role' => 'user',
            ],

        ];

        foreach ($akun as $key => $v) {
            Admin::create([
                'name' => $v['name'],
                'username' => $v['username'],
                'password' => $v['password'],
                'role' => $v['role'],
            ]);

            User::create([
                'name' => $v['name'],
                'username' => $v['username'],
                'password' => $v['password'],
                'role' => $v['role'],
            ]);
        }
    }
}
