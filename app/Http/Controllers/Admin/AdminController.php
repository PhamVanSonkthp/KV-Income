<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Salary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use function auth;
use function redirect;
use function view;

class AdminController extends Controller
{
    public function loginAdmin()
    {

        if (auth()->check()) {
            if (optional(auth()->user())->is_admin == 0) return view('administrator.login.index');
            return redirect()->route('administrator.dashboard.index');
        }

        return view('administrator.login.index');
    }

    public function postLoginAdmin(Request $request)
    {
//        $zxc = 'đâsdasdasd';
        $remember = $request->has('remember_me') ? true : false;
        if (auth()->attempt([
            'id' => $request->id,
            'password' => $request->password,
        ], $remember)) {
            if (optional(auth()->user())->is_admin == 0) return view('administrator.login.index');
            return redirect()->route('administrator.dashboard.index');
        }

        Session::flash("message", "Account is not correct");
        return back();
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/admin');
    }

    public function password()
    {

        View::share('title', 'Password');
        View::share('page', 'Password');

        $title = "Password";
        return view('administrator.password.index', compact('title'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required',
            'new_password_confirm' => 'required',
        ]);

        if (!Hash::check($request->old_password, auth()->user()->password)) {
            Session::flash("error", "Old password not correct");
            return back();
        }

        if ($request->new_password != $request->new_password_confirm){
            Session::flash("error", "Confirm password not correct");
            return back();
        }

        auth()->user()->update([
            'password' => Hash::make($request->new_password)
        ]);

        Session::flash("success", "Saved!");

        return back();
    }
}
