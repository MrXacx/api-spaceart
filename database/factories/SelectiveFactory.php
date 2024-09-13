<?php

namespace Database\Factories;

use App\Models\Art;
use App\Models\Enterprise;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Selective>
 */
class SelectiveFactory extends FakerFactory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'enterprise_id' => Enterprise::pluck('id')->random(),
            'title' => $this->faker->words(asText: true),
            'price' => $this->faker->randomFloat(2, 500, 1500),
            'start_moment' => $this->faker->dateTimeBetween('now', '+1 month')->format('d/m/Y H:i'),
            'end_moment' => $this->faker->dateTimeBetween('+2 months', '+5 months')->format('d/m/Y H:i'),
            'art_id' => Art::pluck('id')->random(),
            'note' => $this->faker->sentence(variableNbWords: true),
        ];
    }
}
