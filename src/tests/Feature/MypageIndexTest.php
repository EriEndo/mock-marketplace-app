<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;

class MypageIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_profile_and_items_are_displayed_correctly()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $seller = User::factory()->create();
        $user->profile()->create([
            'username' => 'テストユーザー',
            'profile_image' => 'profile_images/test.png',
            'postal_code' => '123-4567',
            'address' => '東京都',
            'building' => 'テストビル',
        ]);
        $sellingItem = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '出品商品',
        ]);
        $purchasedItem = Item::factory()->create([
            'user_id' => $seller->id,
            'name' => '購入商品',
        ]);
        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $purchasedItem->id,
            'payment_method' => 'card',
            'postal_code' => '123-4567',
            'address' => '東京都',
            'building' => 'テストビル',
        ]);
        $this->actingAs($user);
        $response = $this->get(route('mypage.index'));
        $response->assertSee('出品商品');
        $response->assertDontSee('購入商品');
        $response = $this->get(route('mypage.index', ['page' => 'buy']));
        $response->assertSee('購入商品');
    }
}
