<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Artist;
use Enumerate\Account;
use Illuminate\Database\Seeder;

class ArtistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::all(['id', 'type'])
        ->whereStrict('type', Account::ARTIST)
        ->each(fn(User $user) => Artist::factory(1)->create(['id' => $user->id]));
    }
}
