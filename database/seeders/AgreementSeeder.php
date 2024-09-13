<?php

namespace Database\Seeders;

use App\Models\Agreement;
use Illuminate\Database\Seeder;

class AgreementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Agreement::factory(10)->create();
    }
}
