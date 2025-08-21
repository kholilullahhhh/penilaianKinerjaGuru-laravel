<?php

namespace App\Http\Controllers;

use App\Models\Penilaian_kinerja;
use App\Models\User;
use App\Models\Jadwal;
use App\Models\Absensi;
use App\Models\Agenda;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PenilaianController extends Controller
{
    private $menu = 'penilaian_kinerja';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menu = $this->menu;
        $datas = Penilaian_kinerja::with('user')->orderBy('bulan', 'desc')->get();
        return view('pages.admin.penilaian_kinerja.index', compact('datas', 'menu'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $menu = $this->menu;
        $users = User::where('role', 'user')->get();
        return view('pages.admin.penilaian_kinerja.create', compact('users', 'menu'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'bulan' => 'required|date_format:Y-m',
        ]);

        try {
            DB::beginTransaction();

            $user_id = $request->user_id;
            $bulan = $request->bulan;

            // Cek duplikasi penilaian
            $existing = Penilaian_kinerja::where('user_id', $user_id)
                ->where('bulan', $bulan)
                ->exists();

            if ($existing) {
                return redirect()->back()
                    ->with('error', 'Penilaian untuk bulan tersebut sudah ada.')
                    ->withInput();
            }

            // Hitung nilai KPI
            $kpiData = $this->hitungKPI($user_id, $bulan);

            // Simpan hasil perhitungan
            Penilaian_kinerja::create([
                'user_id' => $user_id,
                'bulan' => $bulan,
                'kehadiran_mengajar' => $kpiData['kehadiran_mengajar'],
                'ketepatan_waktu' => $kpiData['ketepatan_waktu'],
                'jam_mengajar' => $kpiData['jam_mengajar'],
                'pengisian_nilai' => $kpiData['pengisian_nilai'],
                'kehadiran_rapat' => $kpiData['kehadiran_rapat'],
                'skor_akhir' => $kpiData['skor_akhir'],
                'kategori' => $kpiData['kategori'],
                'detail' => json_encode($kpiData['detail']),
            ]);

            DB::commit();

            return redirect()->route('penilaian_kinerja.index')
                ->with('success', 'Penilaian kinerja berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Menghitung nilai KPI berdasarkan data yang tersedia
     */
    private function hitungKPI($user_id, $bulan)
    {
        // Konversi bulan ke format yang diperlukan
        $startDate = Carbon::parse($bulan)->startOfMonth();
        $endDate = Carbon::parse($bulan)->endOfMonth();

        // 1. Hitung Kehadiran Mengajar
        $hariKerja = $this->getHariKerja($bulan);
        $jumlahHadir = $this->getJumlahHadir($user_id, $startDate, $endDate);
        $kehadiranMengajar = ($hariKerja > 0) ? ($jumlahHadir / $hariKerja) * 100 : 0;

        // 2. Hitung Ketepatan Waktu
        $tepatWaktu = $this->getTepatWaktu($user_id, $startDate, $endDate);
        $ketepatanWaktu = ($hariKerja > 0) ? ($tepatWaktu / $hariKerja) * 100 : 0;

        // 3. Hitung Jam Mengajar
        $jamTerlaksana = $this->getJamTerlaksana($user_id, $startDate, $endDate);
        $jamMaksimal = $this->getJamMaksimal($user_id, $bulan);
        $jamMengajar = ($jamMaksimal > 0) ? ($jamTerlaksana / $jamMaksimal) * 100 : 0;

        // 4. Pengisian Nilai (diasumsikan selalu Ya/100 berdasarkan dokumen)
        $pengisianNilai = 100;

        // 5. Kehadiran Rapat
        $jumlahRapat = $this->getJumlahRapat($startDate, $endDate);
        $hadirRapat = $this->getHadirRapat($user_id, $startDate, $endDate);
        $kehadiranRapat = ($jumlahRapat > 0) ? ($hadirRapat / $jumlahRapat) * 100 : 0;

        // Hitung skor akhir dengan bobot
        $bobot = [
            'kehadiran' => 0.25,
            'ketepatan' => 0.20,
            'jam' => 0.20,
            'nilai' => 0.20,
            'rapat' => 0.15
        ];

        $skorAkhir =
            ($kehadiranMengajar * $bobot['kehadiran']) +
            ($ketepatanWaktu * $bobot['ketepatan']) +
            ($jamMengajar * $bobot['jam']) +
            ($pengisianNilai * $bobot['nilai']) +
            ($kehadiranRapat * $bobot['rapat']);

        // Tentukan kategori
        $kategori = $this->getKategori($skorAkhir);

        // Simpan detail perhitungan
        $detail = [
            'hari_kerja' => $hariKerja,
            'jumlah_hadir' => $jumlahHadir,
            'tepat_waktu' => $tepatWaktu,
            'jam_terlaksana' => $jamTerlaksana,
            'jam_maksimal' => $jamMaksimal,
            'jumlah_rapat' => $jumlahRapat,
            'hadir_rapat' => $hadirRapat,
            'bobot' => $bobot
        ];

        return [
            'kehadiran_mengajar' => round($kehadiranMengajar, 2),
            'ketepatan_waktu' => round($ketepatanWaktu, 2),
            'jam_mengajar' => round($jamMengajar, 2),
            'pengisian_nilai' => $pengisianNilai,
            'kehadiran_rapat' => round($kehadiranRapat, 2),
            'skor_akhir' => round($skorAkhir, 2),
            'kategori' => $kategori,
            'detail' => $detail
        ];
    }

    /**
     * Mendapatkan jumlah hari kerja dalam bulan tertentu
     */
    private function getHariKerja($bulan)
    {
        $startDate = Carbon::parse($bulan)->startOfMonth();
        $endDate = Carbon::parse($bulan)->endOfMonth();
        $hariKerja = 0;

        while ($startDate->lte($endDate)) {
            if (!$startDate->isWeekend()) {
                $hariKerja++;
            }
            $startDate->addDay();
        }
        return $hariKerja;
    }

    /**
     * Mendapatkan jumlah kehadiran mengajar
     */
    private function getJumlahHadir($user_id, $startDate, $endDate)
    {
        // Implementasi query untuk menghitung jumlah kehadiran
        // Contoh: hitung berdasarkan jadwal yang terlaksana
        return Jadwal::where('user_id', $user_id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->count();
    }

    /**
     * Mendapatkan jumlah kehadiran tepat waktu
     */
    private function getTepatWaktu($user_id, $startDate, $endDate)
    {
        // Jika tidak ada kolom jam_seharusnya, gunakan logika alternatif
        return Jadwal::where('user_id', $user_id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->count(); // Sementara asumsikan semua tepat waktu
    }

    /**
     * Mendapatkan jumlah jam mengajar yang terlaksana
     */
    private function getJamTerlaksana($user_id, $startDate, $endDate)
    {
        // Implementasi query untuk menghitung jam mengajar
        $totalJam = Jadwal::where('user_id', $user_id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->sum(DB::raw('TIME_TO_SEC(TIMEDIFF(jam_selesai, jam_mulai)) / 3600'));

        return round($totalJam, 2);
    }

    /**
     * Mendapatkan jumlah jam mengajar maksimal
     */
    private function getJamMaksimal($user_id, $bulan)
    {
        // Jika tidak ada kolom jam_seharusnya, gunakan estimasi
        $hariKerja = $this->getHariKerja($bulan);

        // Asumsi rata-rata 6 jam per hari
        return $hariKerja * 6;
    }

    /**
     * Mendapatkan jumlah rapat dalam periode
     */
    private function getJumlahRapat($startDate, $endDate)
    {
        return Agenda::whereBetween('tgl_kegiatan', [$startDate, $endDate])
            ->count();
    }

    /**
     * Mendapatkan jumlah kehadiran rapat
     */
    private function getHadirRapat($user_id, $startDate, $endDate)
    {
        return Absensi::where('user_id', $user_id)
            ->whereHas('agenda', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tgl_kegiatan', [$startDate, $endDate]);
            })
            ->where('status', 'hadir')
            ->count();
    }

    /**
     * Menentukan kategori berdasarkan skor
     */
    private function getKategori($skor)
    {
        if ($skor >= 90) {
            return 'Sangat Baik';
        } elseif ($skor >= 80) {
            return 'Baik';
        } elseif ($skor >= 70) {
            return 'Cukup';
        } else {
            return 'Kurang';
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $menu = $this->menu;
        $penilaian = Penilaian_kinerja::findOrFail($id);
        $users = User::where('role', 'user')->get();
        return view('pages.admin.penilaian_kinerja.edit', compact('penilaian', 'users', 'menu'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:penilaian_kinerjas,id',
            'kehadiran_mengajar' => 'required|numeric|between:0,100',
            'ketepatan_waktu' => 'required|numeric|between:0,100',
            'jam_mengajar' => 'required|numeric|between:0,100',
            'pengisian_nilai' => 'required|numeric|between:0,100',
            'kehadiran_rapat' => 'required|numeric|between:0,100',
        ]);

        try {
            $penilaian = Penilaian_kinerja::findOrFail($request->id);

            // Hitung ulang skor akhir
            $bobot = [
                'kehadiran' => 0.25,
                'ketepatan' => 0.20,
                'jam' => 0.20,
                'nilai' => 0.20,
                'rapat' => 0.15
            ];

            $skorAkhir =
                ($request->kehadiran_mengajar * $bobot['kehadiran']) +
                ($request->ketepatan_waktu * $bobot['ketepatan']) +
                ($request->jam_mengajar * $bobot['jam']) +
                ($request->pengisian_nilai * $bobot['nilai']) +
                ($request->kehadiran_rapat * $bobot['rapat']);

            $kategori = $this->getKategori($skorAkhir);

            $penilaian->update([
                'kehadiran_mengajar' => $request->kehadiran_mengajar,
                'ketepatan_waktu' => $request->ketepatan_waktu,
                'jam_mengajar' => $request->jam_mengajar,
                'pengisian_nilai' => $request->pengisian_nilai,
                'kehadiran_rapat' => $request->kehadiran_rapat,
                'skor_akhir' => round($skorAkhir, 2),
                'kategori' => $kategori,
            ]);

            return redirect()->route('penilaian_kinerja.index')
                ->with('success', 'Penilaian kinerja berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $penilaian = Penilaian_kinerja::findOrFail($id);
            $penilaian->delete();

            return redirect()->route('penilaian_kinerja.index')
                ->with('success', 'Penilaian kinerja berhasil dihapus.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}