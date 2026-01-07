<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentProfileMenuTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function profile_link_is_present_in_filament_user_menu()
    {
        $user = User::factory()->create(['role' => User::ROLE_ANAK_MAGANG, 'is_active' => true]);

        $this->actingAs($user)
            ->get('/admin/profile')
            ->assertStatus(200)
            ->assertSeeText($user->name);
    }
}
