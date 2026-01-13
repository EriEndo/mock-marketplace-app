<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ItemCreateTest extends TestCase
{

    public function test_user_can_create_item_with_required_fields()
    {
        $this->seed([
            \Database\Seeders\CategoriesTableSeeder::class,
            \Database\Seeders\ConditionsTableSeeder::class,
        ]);

        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $categories = Category::take(2)->get();
        $condition = Condition::first();
        $this->actingAs($user);
        $response = $this->post(route('sell.store'), [
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => 'テスト説明',
            'price' => 5000,
            'condition_id' => $condition->id,
            'categories'   => $categories->pluck('id')->toArray(),
            'image' => UploadedFile::fake()->create('test.png', 100, 'image/png'),

        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('items', [
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => 'テスト説明',
            'price' => 5000,
            'condition_id' => $condition->id,
            'user_id' => $user->id,
        ]);
    }
}
