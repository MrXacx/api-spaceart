<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Agreement>
 */
class AgreementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $maxTime = $this->faker->dateTimeBetween('+1 month', '+1 year');

        return [
            "description" => $this->faker->text,
            "date" => $maxTime->format('Y-m-d'),
            "start_time" => $this->faker->time(max:$maxTime->format('H:i:s')),
            "end_time" => $maxTime->format('H:i:s')
        ];
    }
}
