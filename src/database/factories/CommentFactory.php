<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Comment;
use App\Models\User;
use App\Models\Item;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'item_id' => Item::factory(),
            'content' => $this->faker->randomElement([
                'サイズ感はどんな感じでしょうか？',
                '使用頻度はどれくらいですか？',
                '付属品はすべてそろっていますか？',
                '発送までどれくらいかかりますか？',
                'お値下げの相談は可能でしょうか？',
                '初心者でも使いやすいでしょうか？',
                '気になっています！',
            ]),
        ];
    }

    public function forExisting(User $user, Item $item): self
    {
        return $this->state(fn() => [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
}
