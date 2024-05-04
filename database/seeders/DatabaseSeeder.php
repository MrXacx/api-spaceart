<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            ArtSeeder::class,
            UserSeeder::class,
            ArtistSeeder::class,
            EnterpriseSeeder::class,
            AgreementSeeder::class,
            RateSeeder::class,
            SelectiveSeeder::class,
            SelectiveCandidateSeeder::class,
        ]);
    }
}
