<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\SelectiveCandidate;
use Illuminate\Database\Seeder;

class SelectiveCandidateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Artist::all()
            ->random()
            ->each(
                fn(Artist $a) => SelectiveCandidate::factory(1)
                    ->create(['artist_id' => $a->id])
            );
    }
}
