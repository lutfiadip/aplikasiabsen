<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileViewTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function anak_magang_can_view_their_profile_page()
    {
        $user = User::factory()->create([
            'role' => User::ROLE_ANAK_MAGANG,
            'is_active' => true,
            'avatar' => 'avatars/test.png',
        ]);

        $this->actingAs($user)
            ->get(route('profile.show'))
            ->assertStatus(200)
            ->assertSeeText($user->name)
            ->assertSee('/storage/avatars/test.png');
    }
}
