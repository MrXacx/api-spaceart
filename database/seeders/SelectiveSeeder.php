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
        Selective::factory(10)->create();
    }
}
