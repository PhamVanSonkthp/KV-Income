<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;


class StaffController extends Controller
{

    public function list(){
        $staffs = User::where('is_admin', 0)->orderby('name', 'ASC')->get();

        if(auth()->user()->is_admin == 1){
            $staffs = User::whereIn('branch_id', json_decode(auth()->user()->branch_id))->orderby('name', 'ASC')->get();
        }

        return response()->json($staffs);
    }
}
