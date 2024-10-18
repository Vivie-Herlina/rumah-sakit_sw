<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Layanan extends Controller
{
    public function index()
    {
        return view('Pages.Layanan_pages');
    }
}
