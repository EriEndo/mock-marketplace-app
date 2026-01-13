<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Purchase;
use App\Models\Item;
use App\Models\Comment;
use App\Models\Like;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            CategoriesTableSeeder::class,
            ConditionsTableSeeder::class,
            ProfileImageSeeder::class,
        ]);

        $users = User::factory()->count(4)->withProfile()->create()
            ->merge(User::factory()->count(4)->withIncompleteProfile()->create());

        $this->call([ItemsTableSeeder::class]);
        $items = Item::all();

        $this->call([CategoryItemTableSeeder::class]);

        for ($i = 0; $i < 10; $i++) {
            $user = $users->random();
            $item = $items->random();
            Comment::factory()
                ->forExisting($user, $item)
                ->create();
        }

        Purchase::factory()->count(2)->sequence(
            fn($sequence) => [
                'user_id' => $users->random()->id,
                'item_id' => $items->random()->id,
            ]
        )->create();

        $this->call([LikeTableSeeder::class]);
    }
}
