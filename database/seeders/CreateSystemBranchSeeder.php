<?php

namespace Database\Seeders;

use App\Models\SystemBranch;
use Illuminate\Database\Seeder;

class CreateSystemBranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SystemBranch::firstOrCreate([
            'name' => 'SOUTH SACRAMENTO',
            'color' => '#F8961E',
        ]);

        SystemBranch::firstOrCreate([
            'name' => 'NORTH SACRAMENTO',
            'color' => '#F9C74F',
        ]);

        SystemBranch::firstOrCreate([
            'name' => 'ELK GROVE CITY',
            'color' => '#90BE6D',
        ]);

        SystemBranch::firstOrCreate([
            'name' => 'FOLSOM CITY',
            'color' => '#F3722C',
        ]);

        SystemBranch::firstOrCreate([
            'name' => 'MARCH LANE (STOCKTON CALIFORNIA)',
            'color' => '#F94144',
        ]);
    }
}
