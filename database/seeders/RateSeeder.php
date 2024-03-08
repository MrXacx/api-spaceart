<?php

namespace Database\Seeders;

use App\Models\Agreement;
use App\Models\Rate;
use Illuminate\Database\Seeder;

class RateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Agreement::all()->random()->each(fn ($a) => Rate::factory(1)->create([
            'user_id' => array_rand([$a->artist_id, $a->enterprise_id]),
            'agreement_id' => $a->id,
        ]));
    }
}
