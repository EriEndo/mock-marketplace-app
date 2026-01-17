<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Item;
use App\Models\User;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_search_items_by_partial_name()
    {
        Item::factory()->create(['name' => '赤いりんご']);
        Item::factory()->create(['name' => '青いバナナ']);
        $response = $this->get('/?keyword=りんご');
        $response->assertStatus(200);
        $response->assertSee('赤いりんご');
        $response->assertDontSee('青いバナナ');
    }

    public function test_search_keyword_is_kept_when_moving_to_mylist()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/?tab=mylist&keyword=りんご');
        $response->assertStatus(200);
        $response->assertSee('value="りんご"', false);
    }
}
