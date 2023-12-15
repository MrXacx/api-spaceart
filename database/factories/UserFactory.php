<?php

namespace Database\Factories;

use Enumerate\Account;
use Enumerate\State;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends LocalFactory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => $this->faker->password,
            'state' => State::get(array_rand(State::cases(), 1)),
            'city' => $this->faker->city,
            'CEP' => preg_replace('/[^0-9]/', '', $this->faker->postcode()),
            'type' => Account::get(array_rand(Account::cases(), 1)),
            'phone' => $this->faker->phoneNumberCleared,
            'image' => $this->faker->imageUrl(350, 350, 'person', true),
        ];
    }
}
