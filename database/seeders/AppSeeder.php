<?php

namespace Database\Seeders;
use App\Modules\ServerManagement\Region\Models\Region;


class AppSeeder extends \Illuminate\Database\Seeder
{
    public function run()
    {
        $regions = [
            [
                'name' => 'Myanmar',
            ],
            [
                'name' => 'Singapore',
            ],
            [
                'name' => 'Thailand',
            ],
            [
                'name' => 'Netherland',
            ]
        ];

        Region::insert($regions);
    }
}
