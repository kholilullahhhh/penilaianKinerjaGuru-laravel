<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Tambahkan guru dari daftar nama
        $names = [
            "Mas'ah",
            "Indahyani Tawakkal",
            "Risky Inesa",
            "Mulyati",
            "Riska",
            "Muh. Musyawwir",
            "St. Nurhalisa",
            "Sri Sundari Rasyid",
            "Arifuddin",
            "Savira D. Salsabella",
            "Nur Fadilah Putri"
        ];

        foreach ($names as $name) {
            $username = strtolower(Str::slug($name));
            $nuptk = str_pad(random_int(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);
            $hashedPassword = bcrypt('user123');

            User::create([
                'name' => $name,
                'username' => $username,
                'password' => $hashedPassword,
                'nuptk' => $nuptk,
                'role' => 'user',
            ]);

            Admin::create([
                'name' => $name,
                'username' => $username,
                'password' => $hashedPassword,
                'nuptk' => $nuptk,
                'role' => 'user',
            ]);
        }
        $akun = [
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'password' => bcrypt('admin'),
                'role' => 'admin',
            ],
            [
                'name' => 'Kepala Sekolah',
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

        // Masukkan akun admin/kepala/guru ke tabel Admin dan User
        foreach ($akun as $v) {
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
                'nuptk' => $v['nuptk'] ?? null,
                'role' => $v['role'],
            ]);
        }


    }
}
