<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;

class AbsesiController extends Controller
{
    private $menu = 'absensi';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menu = $this->menu;
        $datas= Absensi::with(['agenda', 'user'])->get();
        return view('pages.admin.absensi.index', compact('datas','menu'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $menu = $this->menu;
        return view('pages.admin.absensi.create', compact('menu'));
    }

}
