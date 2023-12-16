<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\Agreement;
use App\Models\Enterprise;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AgreementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $enterprises = Enterprise::all();
        
        Artist::all()
        ->each(
            fn(Artist $artist) => Agreement::factory()->create([
                "hired"=> $artist->id,
                "hirer"=> $enterprises->random()->id,
                "art"=> $artist->art,
                "price"=> $artist->wage,
            ])
        );


    }
}
