<?php

namespace App\Http\Controllers\User;

use App\Models\Absensi;
use App\Models\Agenda;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class AbsenController extends Controller
{
    private $menu = 'absensi';
    public function userIndex()
    {
        // $datas = Absensi::with('agenda')->get();
        $datas = Absensi::whereHas('user', function ($query) {
            $query->where('nuptk', auth()->user()->nuptk);
        })->with(['user', 'agenda'])->latest()->get();
        $menu = $this->menu;

        return view('pages.user.index', compact('menu', 'datas'));
    }
    public function userCreate()
    {
        $menu = $this->menu;
        $agendas = Agenda::where('status', 'publish')->get();
        return view('pages.user.create', compact('agendas', 'menu'));
    }
    public function userStore(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'agenda_id' => 'required|exists:agendas,id',
            'status' => 'required|in:hadir,tidak_hadir,izin',
            'keterangan' => 'nullable|string'
        ]);

        $validated['user_id'] = $user->id;

        Absensi::create($validated);
        return redirect()->route('user.absensi.index')->with('success', 'Data absensi berhasil disimpan');
    }
}
