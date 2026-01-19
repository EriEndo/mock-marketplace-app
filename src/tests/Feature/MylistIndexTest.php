<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;
use App\Models\Purchase;

class MylistIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_liked_items_only_are_displayed_in_mylist()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $likedItem = Item::factory()->create([
            'name' => 'いいねした商品',
        ]);
        $notLikedItem = Item::factory()->create([
            'name' => 'いいねしてない商品',
        ]);
        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $likedItem->id,
        ]);
        $response = $this->actingAs($user)->get('/?tab=mylist');
        $response->assertStatus(200);
        $response->assertSee('いいねした商品');
        $response->assertDontSee('いいねしてない商品');
    }

    public function test_purchased_items_are_marked_as_sold_in_mylist()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'name' => '購入済みのいいね商品',
        ]);
        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
        Purchase::factory()->create([
            'item_id' => $item->id,
            'user_id' => User::factory()->create()->id, // 購入者（誰でもOK）
        ]);
        $response = $this->actingAs($user)->get('/?tab=mylist');
        $response->assertStatus(200);
        $response->assertSee('購入済みのいいね商品');
        $response->assertSee('Sold');
    }

    public function test_guest_sees_nothing_in_mylist()
    {
        Item::factory()->create(['name' => '本来見えたら困る商品']);
        $response = $this->get('/?tab=mylist');
        $response->assertStatus(200);
        $response->assertDontSee('本来見えたら困る商品');
    }
}
