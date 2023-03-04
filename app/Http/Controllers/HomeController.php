<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(){
        $title = "Home";
        return view('index', compact('title'));
    }

    public function dashboard(){
        $title = "Dashboard";
        return view ('dashboard', compact('title'));
    }
}