<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Image;
use function auth;
use function view;

class DashboardController extends Controller
{
    public function index(){
        $page = 'Admin';
        $title = 'admin';
        if(auth()->check()){
            return view('administrator.dashboard.index')->with(compact('title', 'page'));
        }
        return redirect()->to('/admin');
    }
}
