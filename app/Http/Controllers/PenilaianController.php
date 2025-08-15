<?php

namespace App\Http\Controllers;

use App\Models\Indicators;
use App\Models\Penilaian_kinerja;
use App\Models\User;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    private $menu = 'penilaian_kinerja';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $datas = Penilaian_kinerja::with(['user', 'indikator'])->get();
        $menu = $this->menu;
        return view('pages.admin.penilaian_kinerja.index', compact('menu', 'datas'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $menu = $this->menu;
        $users = User::where('role', 'user')->get();
        $indicators = Indicators::all();

        return view('pages.admin.penilaian_kinerja.create', compact('menu', 'users', 'indicators'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $r = $request->all();
        // dd($r);

        // Menyimpan data guru
        Penilaian_kinerja::create($r);

        return redirect()->route('penilaian_kinerja.index')->with('message', 'Data guru berhasil ditambahkan.');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = Penilaian_kinerja::findOrFail($id);
        $users = User::where('role', 'user')->get();
        $indicators = Indicators::all();
        $menu = $this->menu;

        return view('pages.admin.penilaian_kinerja.edit', compact('data', 'indicators', 'users', 'menu'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $r = $request->all();
        $data = Penilaian_kinerja::find($r['id']);

        // dd($r);
        $data->update($r);

        return redirect()->route('penilaian_kinerja.index')->with('message', 'Data guru berhasil diperbarui.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = Penilaian_kinerja::find($id);
        $data->delete();

        return redirect()->route('penilaian_kinerja.index')->with('message', 'Data guru berhasil dihapus.');
    }

}
