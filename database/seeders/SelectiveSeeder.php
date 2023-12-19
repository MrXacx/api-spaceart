<?php

namespace Database\Seeders;

use App\Models\Enterprise;
use App\Models\Selective;
use Illuminate\Database\Seeder;

class SelectiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Enterprise::all()
            ->some(
                fn (Enterprise $enterprise) => Selective::factory()->create(['owner' => $enterprise->id])
            );
    }
}
