<?php

namespace Database\Factories;

use App\Models\User;
use Enumerate\Art;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Artist>
 */
class ArtistFactory extends LocalFactory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->unique()->randomElement(User::all()->where('type', '=', 'artist')->count()),
            'CPF' => $this->faker->cpf,
            'art' => Art::get(array_rand(Art::cases(), 1)),
            'wage' => $this->faker->randomFloat(2, 500, 1200),
        ];
    }
}
