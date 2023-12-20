<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\Selective;
use App\Models\SelectiveCandidate;
use Illuminate\Database\Seeder;

class SelectiveCandidateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Selective::all()->each(fn (Selective $selective) => Artist::all()
            ->each(
                fn (Artist $artist) => SelectiveCandidate::factory()
                    ->create([
                        'artist' => $artist->id,
                        'selective' => $selective->id,
                    ])
            )
        );
    }
}
