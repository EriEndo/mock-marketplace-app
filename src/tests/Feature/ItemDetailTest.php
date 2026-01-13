<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use App\Models\User;
use App\Models\Comment;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_detail_page_displays_all_required_information()
    {
        $this->seed([
            \Database\Seeders\CategoriesTableSeeder::class,
        ]);

        $user = User::factory()->create();
        $user->profile()->create([
            'username'    => 'テストユーザー',
            'postal_code' => '123-4567',
            'address'     => '東京都テスト区',
            'building'    => 'テストマンション101',
        ]);
        $condition = Condition::factory()->create(['name' => '新品']);
        $item = Item::factory()->create([
            'name' => 'テスト商品',
            'image' => 'items/test.png',
            'brand' => 'テストブランド',
            'price' => 1000,
            'description' => 'テスト説明',
            'condition_id' => $condition->id,
        ]);
        $categories = Category::take(2)->get();
        $item->categories()->attach($categories->pluck('id'));
        Comment::factory()->create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'content' => 'テストコメント',
        ]);
        $response = $this->get(route('items.detail', $item->id));
        $response->assertStatus(200);
        $response->assertSee('storage/items/test.png');
        $response->assertSee('<img', false);
        $response->assertSee('テスト商品');
        $response->assertSee('テストブランド');
        $response->assertSee('1,000');
        $response->assertSee('0');
        $response->assertSee('コメント(1)');
        $response->assertSee('テスト説明');
        $response->assertSee('新品');
        $response->assertSee('テストユーザー');
        $response->assertSee('テストコメント');
    }

    public function test_multiple_categories_are_displayed_on_item_detail_page()
    {
        $item = Item::factory()->create();
        $categories = Category::take(2)->get();
        $item->categories()->attach($categories->pluck('id'));
        $response = $this->get(route('items.detail', $item->id));
        $response->assertStatus(200);
        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }
    }
}
