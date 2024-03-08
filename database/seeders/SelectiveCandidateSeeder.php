<?php

namespace Database\Seeders;

use App\Models\SelectiveCandidate;
use Illuminate\Database\Seeder;

class SelectiveCandidateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SelectiveCandidate::factory(10)->create();
    }
}
