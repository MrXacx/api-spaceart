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
        $i = rand();
        Agreement::all()->random()->each(fn ($a) => Rate::factory(1)->create([
            'author_id' => [$a->artist_id, $a->enterprise_id][($i++) % 2],
            'rated_id' => [$a->artist_id, $a->enterprise_id][$i % 2],
            'agreement_id' => $a->id,
        ]));
    }
}
