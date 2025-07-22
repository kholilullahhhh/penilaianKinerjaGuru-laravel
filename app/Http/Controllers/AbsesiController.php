<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Agenda;
use App\Models\User;


class AbsesiController extends Controller
{
    private $menu = 'absensi';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menu = $this->menu;
        $datas = Absensi::with(['agenda', 'user'])->get();
        return view('pages.admin.absensi.index', compact('datas', 'menu'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $menu = $this->menu;
        $agendas = Agenda::where('status', 'publish')->get();
        $users = User::where('role', 'user')->get();
        return view('pages.admin.absensi.create', compact('users', 'agendas', 'menu'));
    }
    public function store(Request $request)
    {
        $r = $request->all();
        Absensi::create($r);
        return redirect()->route('absensi.index')->with('message', 'store');
    }
    public function edit($id)
    {
        $data = Absensi::find($id);
        $menu = $this->menu;
        $agendas = Agenda::where('status', 'publish')->get();
        $users = User::where('role', 'user')->get();
        return view('pages.admin.absensi.edit', compact('data', 'agendas', 'users', 'menu'));
    }
    public function update(Request $request)
    {
        $r = $request->all();
        $data = Absensi::find($r['id']);
        // dd($r);
        $data->update($r);
        return redirect()->route('absensi.index')->with('message', 'update');
    }
    public function destroy($id)
    {
        $data = Absensi::find($id);
        $data->delete();
        return response()->json($data);
    }



}
