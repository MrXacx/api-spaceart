<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Provider\pt_BR\Address;
use Faker\Provider\pt_BR\Person;
use Faker\Provider\pt_BR\PhoneNumber;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Artist>
 */
abstract class LocalFactory extends Factory
{
   public function __construct() {
    parent::__construct();

        $this->faker->addProvider(new Address($this->faker));
        $this->faker->addProvider(new Person($this->faker));
        $this->faker->addProvider(new PhoneNumber($this->faker));
   }
}
