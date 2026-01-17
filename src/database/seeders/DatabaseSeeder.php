<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
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

        $fixedUser = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => '山田太郎',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $fixedUser->profile()->updateOrCreate(
            ['user_id' => $fixedUser->id],
            [
                'username' => 'やまださん',
                'profile_image' => 'profile_images/banana.png', // 空で良ければ '' でもOK
                'postal_code' => '123-4567',
                'address' => '東京都渋谷区神宮前',
                'building' => '第一ビル',
            ]
        );

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
