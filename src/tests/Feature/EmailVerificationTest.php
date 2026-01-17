<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use App\Models\User;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verification_email_is_sent_after_registration()
    {
        Notification::fake();
        $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        Notification::assertSentTo(
            [$user],
            VerifyEmail::class
        );
    }

    public function test_verification_guide_page_contains_verification_link()
    {
        $user = User::factory()->unverified()->create();
        $response = $this->actingAs($user)
            ->get(route('verify.guide'));

        $response->assertStatus(200);
        $response->assertSee('認証はこちらから');
    }

    public function test_user_is_redirected_to_profile_after_email_verification()
    {
        Event::fake();
        $user = User::factory()->unverified()->create();
        $this->actingAs($user);
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );
        $response = $this->get($verificationUrl);
        $response->assertRedirect(route('mypage.profile.edit', ['from' => 'first']));
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
}
