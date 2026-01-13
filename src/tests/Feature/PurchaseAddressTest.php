<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;

class PurchaseAddressTest extends TestCase
{
    use RefreshDatabase;

    public function test_updated_address_is_reflected_on_purchase_screen()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $user->profile()->create([
            'username'    => 'テストユーザー',
            'postal_code' => '000-0000',
            'address'     => '旧住所',
            'building'    => '旧建物',
        ]);
        $this->actingAs($user);
        $this->patch(route('purchase.address.update', $item->id), [
            'postal_code'   => '123-4567',
            'address'       => '東京都渋谷区',
            'building'      => '新ビル101',
            'payment_method' => 'card',
        ]);
        $response = $this->get(route('purchase.form', $item->id));

        $response->assertSee('123-4567');
        $response->assertSee('東京都渋谷区');
        $response->assertSee('新ビル101');
    }

    public function test_purchased_item_has_updated_address_saved()
    {
        /** @var \App\Models\User $buyer */
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $buyer->profile()->create([
            'username'    => '購入者',
            'postal_code' => '000-0000',
            'address'     => '旧住所',
            'building'    => '旧建物',
        ]);
        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);
        $this->actingAs($buyer);
        $this->withSession([
            "purchase.{$item->id}.purchase_address" => [
                'postal_code' => '987-6543',
                'address'     => '大阪府大阪市',
                'building'    => '配送ビル202',
            ],
            "purchase.{$item->id}.payment_method" => 'card',
        ]);
        $this->get(route('purchase.success', $item->id));
        $this->assertDatabaseHas('purchases', [
            'item_id'     => $item->id,
            'user_id'     => $buyer->id,
            'postal_code' => '987-6543',
            'address'    => '大阪府大阪市',
            'building'   => '配送ビル202',
        ]);
    }
}
