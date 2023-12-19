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
        Agreement::all()->each(function (Agreement $agreement) {
            Rate::factory()->create([
                'agreement' => $agreement->id,
                'author' => $agreement->hirer,
            ]);
            Rate::factory()->create([
                'agreement' => $agreement->id,
                'author' => $agreement->hired,
            ]);
        });
    }
}
