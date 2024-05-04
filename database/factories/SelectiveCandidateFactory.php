<?php

namespace Database\Factories;

use App\Models\Artist;
use App\Models\Selective;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SelectiveCandidate>
 */
class SelectiveCandidateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'selective_id' => Selective::pluck('id')->random(),
        ];
    }
}
