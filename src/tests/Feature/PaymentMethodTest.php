<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    public function test_selected_payment_method_is_stored_in_session()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $user->profile()->create([
            'username'    => 'テストユーザー',
            'postal_code' => '123-4567',
            'address'     => '東京都渋谷区',
            'building'    => 'テストビル101',
        ]);
        $this->actingAs($user);
        $response = $this->get(
            route('purchase.address.form', $item->id) . '?payment_method=card'
        );
        $response->assertSessionHas(
            "purchase.{$item->id}.payment_method",
            'card'
        );
    }
}
