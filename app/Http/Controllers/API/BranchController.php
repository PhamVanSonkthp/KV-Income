<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SystemBranch;


class BranchController extends Controller
{

    public function list(){
        $branchs = SystemBranch::all();

        if(auth()->user()->is_admin == 1){
            $branchs = SystemBranch::whereIn('id', json_decode(auth()->user()->branch_id))->get();
        }

        return response()->json($branchs);
    }
}
