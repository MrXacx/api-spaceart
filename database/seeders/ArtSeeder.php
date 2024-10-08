<?php

namespace Database\Seeders;

use App\Enumerate\Art as EnumerateArt;
use App\Models\Art;
use Illuminate\Database\Seeder;

class ArtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (EnumerateArt::values() as $name) {
            Art::create([
                'name' => $name,
            ]);
        }
    }
}
