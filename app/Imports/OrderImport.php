<?php

namespace App\Imports;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OrderImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $user_id = User::where('name', $row['staff'])->first();
        if(empty($user_id)){
            $newUser = User::create([
               'name' => $row['staff'],
                'password' => Hash::make('123456'),
            ]);

            $user = $newUser->id;
        }else{
            $user = $user_id->id;
        }

        if($row['payment'] === 'Venmo'){
            $payment = 2;
        }elseif($row['payment'] === 'Cash App'){
            $payment = 3;
        }elseif($row['payment'] === 'Zerre'){
            $payment = 4;
        }elseif($row['payment'] === 'Cash'){
            $payment = 5;
        }else{
            $payment = 1;
        }
        $order = [
            'code' => $row['code'],
            'service_charge' => $row['service_charge'],
            'user_id' => $user,
            'tips' => $row['tips'],
            'payment_type_id' => $payment,
            'note' => $row['note'],
            'create_by' => Auth::id(),
        ];

        return new Order($order);
    }
}
