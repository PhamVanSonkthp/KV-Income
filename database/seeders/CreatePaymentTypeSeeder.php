<?php

namespace Database\Seeders;

use App\Models\PaymentType;
use Illuminate\Database\Seeder;

class CreatePaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentType::firstOrCreate([
            "name" => "Card",
        ]);

        PaymentType::firstOrCreate([
            "name" => "Venmo",
        ]);

        PaymentType::firstOrCreate([
            "name" => "Cash App",
        ]);

        PaymentType::firstOrCreate([
            "name" => "Zerre",
        ]);

        PaymentType::firstOrCreate([
            "name" => "Cash",
        ]);
    }
}
