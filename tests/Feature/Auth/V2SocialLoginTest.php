<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Tests\TestCase;

class V2SocialLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_social_signin_creates_profile_with_name_and_avatar(): void
    {
        $fakeSocialUser = new class {
            public function getEmail(): string
            {
                return 'social@example.com';
            }

            public function getName(): string
            {
                return 'Social User';
            }

            public function getAvatar(): string
            {
                return 'https://example.com/avatar.jpg';
            }
        };

        $driver = \Mockery::mock();
        $driver->shouldReceive('stateless')->andReturnSelf();
        $driver->shouldReceive('userFromToken')->with('fake-token')->andReturn($fakeSocialUser);

        Socialite::shouldReceive('driver')->with('facebook')->andReturn($driver);

        $response = $this->postJson('/api/v1/social/signin', [
            'access_token' => 'fake-token',
            'provider' => 'facebook',
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.user.profile.name', 'Social User');
        $response->assertJsonPath('data.user.profile.avatar', 'https://example.com/avatar.jpg');
        $this->assertDatabaseHas('profiles', ['name' => 'Social User']);
    }
}
