<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;
use App\Models\Comment;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_logged_in_user_can_post_comment()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->actingAs($user);
        $response = $this->post(route('comment.store', $item->id), [
            'content' => 'テストコメント',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'テストコメント',
        ]);
        $this->assertEquals(1, $item->comments()->count());
    }

    public function test_guest_cannot_post_comment()
    {
        $item = Item::factory()->create();
        $response = $this->post(route('comment.store', $item->id), [
            'content' => 'テストコメント',
        ]);
        $response->assertRedirect('/login');
        $this->assertDatabaseCount('comments', 0);
    }


    public function test_comment_is_required()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->actingAs($user);
        $response = $this->from(route('items.detail', $item->id))
            ->post(route('comment.store', $item->id), [
                'content' => '',
            ]);
        $response->assertRedirect(route('items.detail', $item->id));
        $response->assertSessionHasErrors([
            'content' => 'コメントを入力してください',
        ]);
        $this->assertDatabaseCount('comments', 0);
    }

    public function test_comment_must_not_exceed_255_characters()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->actingAs($user);
        $response = $this->from(route('items.detail', $item->id))
            ->post(route('comment.store', $item->id), [
                'content' => str_repeat('あ', 256),
            ]);
        $response->assertRedirect(route('items.detail', $item->id));
        $response->assertSessionHasErrors([
            'content' => 'コメントは255文字以内で入力してください',
        ]);
        $this->assertDatabaseCount('comments', 0);
    }
}
