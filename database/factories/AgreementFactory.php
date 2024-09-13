<?php

namespace Database\Factories;

use App\Models\Artist;
use App\Models\Enterprise;
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

        $artist = Artist::find(Artist::pluck('id')->random());

        return [
            'artist_id' => $artist->id,
            'enterprise_id' => Enterprise::pluck('id')->random(),
            'art_id' => $artist->art_id,
            'price' => $artist->wage,

            'note' => $this->faker->text,
            'date' => $maxTime->format('d/m/Y'),
            'start_time' => $this->faker->time('H:i', $maxTime->format('H:i')),
            'end_time' => $maxTime->format('H:i'),
        ];
    }
}
