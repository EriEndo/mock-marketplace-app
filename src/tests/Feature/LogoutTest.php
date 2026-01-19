<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_logout()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->post('/logout');
        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
