<?php

namespace Tests\Feature\Items;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;

class ItemIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_items_index_displays_all_items()
    {
        $items = Item::factory()->count(3)->create();
        $response = $this->get('/');
        $response->assertStatus(200);
        foreach ($items as $item) {
            $response->assertSee($item->name);
        }
    }

    public function test_purchased_items_are_marked_as_sold()
    {
        $item = Item::factory()->create();
        Purchase::factory()->create([
            'item_id' => $item->id,
        ]);
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee($item->name);
        $response->assertSee('Sold');
    }

    public function test_own_items_are_not_displayed_in_items_index()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $ownItem = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '自分の商品',
        ]);
        $otherItem = Item::factory()->create([
            'name' => '他人の商品',
        ]);
        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
        $response->assertSee($otherItem->name);
        $response->assertDontSee($ownItem->name);
    }
}
