<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\mapel;


class MapelController extends Controller
{
        private $menu = 'mapel';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menu = $this->menu;
        $datas = Mapel::with(['user'])->get();
        return view('pages.user.mapel.index', compact('datas', 'menu'));
    }

}
