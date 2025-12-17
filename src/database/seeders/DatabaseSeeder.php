<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CategoriesTableSeeder::class);
        $this->call(ConditionsTableSeeder::class);
        User::factory()->count(4)->withProfile()->create();
        User::factory()->count(4)->withIncompleteProfile()->create();
        $this->call(ItemsTableSeeder::class);
        $this->call(CategoryItemTableSeeder::class);
        $this->call(PurchasesTableSeeder::class);
    }
}
