<?php

namespace Database\Factories;

use Enumerate\Account;
use Enumerate\State;
use Faker\Provider\pt_BR\Address as AddressProvider;
use Faker\Provider\pt_BR\Person as PersonProvider;
use Faker\Provider\pt_BR\PhoneNumber as PhoneNumberProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $this->faker->addProvider(new PersonProvider($this->faker));
        $this->faker->addProvider(new AddressProvider($this->faker));
        $this->faker->addProvider(new PhoneNumberProvider($this->faker));

        return [
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
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
