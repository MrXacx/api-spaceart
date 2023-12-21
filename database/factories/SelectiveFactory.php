<?php

namespace Database\Factories;

use Enumerate\Art;

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
            'name' => $this->faker->words(asText: true),
            'price' => $this->faker->randomFloat(2, 500, 1500),
            'start_moment' => $this->faker->dateTimeBetween('now', '+1 month'),
            'end_moment' => $this->faker->dateTimeBetween('+2 months', '+5 months'),
            'art' => $this->faker->randomElement(Art::cases()),
            'description' => $this->faker->sentence(variableNbWords: true),
        ];
    }
}
