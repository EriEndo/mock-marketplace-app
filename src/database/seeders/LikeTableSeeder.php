<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Like;
use App\Models\User;
use App\Models\Item;

class LikeTableSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $items = Item::all();
        foreach ($users as $user) {
            $likeCount = rand(0, min(3, $items->count()));
            $itemsToLike = $items->random($likeCount);
            foreach ($itemsToLike as $item) {
                Like::firstOrCreate([
                    'user_id' => $user->id,
                    'item_id' => $item->id,
                ]);
            }
        }
    }
}
