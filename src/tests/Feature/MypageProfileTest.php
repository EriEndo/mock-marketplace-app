<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class MypageProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_edit_page_shows_existing_values()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $user->profile()->create([
            'username' => '既存ユーザー',
            'postal_code' => '987-6543',
            'address' => '大阪府',
            'building' => '既存ビル',
            'profile_image' => 'profile_images/sample.png',
        ]);
        $this->actingAs($user);
        $response = $this->get(route('mypage.profile.edit'));
        $response->assertStatus(200);
        $response->assertSee('既存ユーザー');
        $response->assertSee('987-6543');
        $response->assertSee('大阪府');
        $response->assertSee('既存ビル');
    }
}
