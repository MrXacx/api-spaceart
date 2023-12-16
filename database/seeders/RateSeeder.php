<?php

namespace Database\Seeders;

use App\Models\Rate;
use App\Models\Agreement;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Agreement::all()->each(function (Agreement $agreement) {
            Rate::factory()->create([
                "agreement" => $agreement->id,
                "author" => $agreement->hirer,
            ]);
            Rate::factory()->create([
                "agreement" => $agreement->id,
                "author" => $agreement->hired,
            ]);
        });
    }
}
