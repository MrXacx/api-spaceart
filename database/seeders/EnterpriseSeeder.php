<?php

namespace Database\Seeders;

use App\Models\Enterprise;
use App\Models\User;
use Enumerate\Account;
use Illuminate\Database\Seeder;

class EnterpriseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::all(['id', 'type'])
            ->whereStrict('type', Account::ENTERPRISE)
            ->each(fn (User $user) => Enterprise::factory(1)->create(['id' => $user->id]));
    }
}
