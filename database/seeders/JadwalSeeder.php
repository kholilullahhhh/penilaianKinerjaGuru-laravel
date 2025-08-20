<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $today = Carbon::today();

        $jadwals = [];

        // ========================
        // KELAS 1-2 (Wali 4 JP/hari)
        // ========================
        $jadwals[] = [
            'user_id' => 1, // wali kelas 1
            'mapel_id' => 1, // contoh: Gowa Angngaji
            'tanggal' => $today,
            'jam_mulai' => '07:30:00',
            'jam_selesai' => '09:10:00', // 4 JP
            'keterangan' => 'tidak'
        ];

        $jadwals[] = [
            'user_id' => 2, // wali kelas 2
            'mapel_id' => 2, // contoh: Bahasa Indonesia
            'tanggal' => $today,
            'jam_mulai' => '07:30:00',
            'jam_selesai' => '09:10:00',
            'keterangan' => 'tidak'
        ];

        // ========================
        // KELAS 3-6 (Wali 5-6 JP/hari)
        // ========================
        $jadwals[] = [
            'user_id' => 3, // wali kelas 3
            'mapel_id' => 3, // contoh: Matematika
            'tanggal' => $today,
            'jam_mulai' => '07:30:00',
            'jam_selesai' => '09:55:00', // 5 JP
            'keterangan' => 'tidak'
        ];

        $jadwals[] = [
            'user_id' => 4, // wali kelas 4
            'mapel_id' => 4,
            'tanggal' => $today,
            'jam_mulai' => '07:30:00',
            'jam_selesai' => '10:40:00', // 6 JP
            'keterangan' => 'tidak'
        ];

        $jadwals[] = [
            'user_id' => 5, // wali kelas 5
            'mapel_id' => 5,
            'tanggal' => $today,
            'jam_mulai' => '07:30:00',
            'jam_selesai' => '10:40:00',
            'keterangan' => 'tidak'
        ];

        $jadwals[] = [
            'user_id' => 6, // wali kelas 6
            'mapel_id' => 6,
            'tanggal' => $today,
            'jam_mulai' => '07:30:00',
            'jam_selesai' => '10:40:00',
            'keterangan' => 'tidak'
        ];

        // ========================
        // MAPEL TAMBAHAN (2 JP/kelas)
        // ========================
        $mapelTambahan = [
            ['user_id' => 7, 'mapel_id' => 7, 'nama' => 'Mulok'],
            ['user_id' => 8, 'mapel_id' => 8, 'nama' => 'SBDP'],
            ['user_id' => 9, 'mapel_id' => 9, 'nama' => 'IPAS'], // hanya kelas 3-6
        ];

        $start = Carbon::createFromTime(11, 00, 0); // mulai setelah wali kelas
        foreach ($mapelTambahan as $m) {
            $jadwals[] = [
                'user_id' => $m['user_id'],
                'mapel_id' => $m['mapel_id'],
                'tanggal' => $today,
                'jam_mulai' => $start->format('H:i:s'),
                'jam_selesai' => $start->copy()->addMinutes(80)->format('H:i:s'), // 2 JP
                'keterangan' => "tidak"
            ];
            $start->addMinutes(90); // geser 2 JP + 10 menit transisi
        }

        DB::table('jadwals')->insert($jadwals);
    }
}
