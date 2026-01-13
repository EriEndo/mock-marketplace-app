<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_purchase_item()
    {
        /** @var \App\Models\User $buyer */
        $buyer = User::factory()->create();
        $buyer->profile()->create([
            'username'    => 'テストユーザー',
            'postal_code' => '123-4567',
            'address'     => '東京都渋谷区',
            'building'    => 'テストビル101',
        ]);
        $seller = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);
        $this->actingAs($buyer);
        $this->withSession([
            "purchase.{$item->id}.payment_method" => 'card',
        ]);
        $response = $this->get(route('purchase.success', $item->id), [
            'payment_method' => 'card',
            'postal_code'    => '123-4567',
            'address'        => '東京都渋谷区',
            'building'       => 'テストビル101',
        ]);
        $response->assertRedirect('/');
        $this->assertDatabaseHas('purchases', [
            'user_id' => $buyer->id,
            'item_id' => $item->id,
        ]);
    }

    public function test_purchased_item_is_marked_as_sold_in_item_list()
    {
        /** @var \App\Models\User $buyer */
        $buyer = User::factory()->create();
        $buyer->profile()->create([
            'username'    => 'テストユーザー',
            'postal_code' => '123-4567',
            'address'     => '東京都渋谷区',
            'building'    => 'テストビル101',
        ]);
        $seller = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);
        Purchase::create([
            'user_id'        => $buyer->id,
            'item_id'        => $item->id,
            'payment_method' => 'card',
            'postal_code'    => '123-4567',
            'address'        => '東京都渋谷区',
            'building'       => 'テストビル101',
        ]);
        $this->actingAs($buyer);
        $this->withSession([
            "purchase.{$item->id}.payment_method" => 'card',
        ]);
        $response = $this->get(route('items.index'));
        $response->assertStatus(200);
        $response->assertSee('Sold');
    }

    public function test_purchased_item_is_shown_in_profile_purchase_list()
    {
        /** @var \App\Models\User $buyer */
        $buyer = User::factory()->create();
        $buyer->profile()->create([
            'username'    => 'テストユーザー',
            'postal_code' => '123-4567',
            'address'     => '東京都渋谷区',
            'building'    => 'テストビル101',
        ]);
        $seller = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);
        Purchase::create([
            'user_id'        => $buyer->id,
            'item_id'        => $item->id,
            'payment_method' => 'card',
            'postal_code'    => '123-4567',
            'address'        => '東京都渋谷区',
            'building'       => 'テストビル101',
        ]);
        $this->actingAs($buyer);
        $this->withSession([
            "purchase.{$item->id}.payment_method" => 'card',
        ]);
        $response = $this->get(route('mypage.index', ['page' => 'buy']));
        $response->assertStatus(200);
        $response->assertSee($item->name);
    }
}
