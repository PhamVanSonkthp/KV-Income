<?php

namespace App\Imports;

use App\Models\Helper;
use App\Models\SystemBranch;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class UserImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $branch = SystemBranch::where('name', $row['branch'])->first();
        if(empty($branch)){
            $newBranch = SystemBranch::create([
                'name' => $row['branch'],
                'color' => Helper::rand_color()
            ]);

            $branch_id = $newBranch->id;
        }else{
            $branch_id = $branch->id;
        }

        $user = [
            'code' => $row['code'],
            'name' => $row['name'],
            'password' => Hash::make('password'),
            'phone' => $row['phone'],
            'start' => Date::excelToDateTimeObject($row['start'])->format('Y-m-d'),
            'date_of_birth' => Date::excelToDateTimeObject($row['birthday'])->format('Y-m-d'),
            'address' => $row['address'],
            'branch_id' => $branch_id,
            'create_by_user' => Auth::id(),
        ];

        return new User($user);
    }
}
