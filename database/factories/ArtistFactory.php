<?php

namespace Database\Factories;

use Enumerate\Art;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Artist>
 */
class ArtistFactory extends FakerFactory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'CPF' => $this->faker->cpf,
            'art' => Art::get(array_rand(Art::cases(), 1)),
            'wage' => $this->faker->randomFloat(2, 500, 1200),
        ];
    }
}
