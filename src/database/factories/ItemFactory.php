<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Condition;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'condition_id' => Condition::factory(),
            'name' => 'テスト商品',
            'brand' => null,
            'description' => 'テスト説明',
            'price' => 1000,
            'image' => 'dummy.jpg',
        ];
    }
}
