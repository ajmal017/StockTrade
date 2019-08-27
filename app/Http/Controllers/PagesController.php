<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function home()
    {
      return view('welcome');
    }

    public function dashboard()
    {
      return view('dashboard');
    }

    public function watchlist()
    {
      return view('embed/watchlist');
    }

    public function demotrader()
    {
      return view('embed/watchlist');
    }

    public function docsapi() {
      return view('api');
    }


}
