<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function anak_magang_cannot_update_admin_fields()
    {
        $user = User::factory()->create(['role' => User::ROLE_ANAK_MAGANG]);
        $mentor = User::factory()->create(['role' => User::ROLE_PEMBIMBING]);

        $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Updated Name',
            'intern_id' => 'I-9999',
            'division' => 'Hacked Division',
            'mentor_id' => $mentor->id,
        ])->assertSessionHasNoErrors();

        $user->refresh();

        $this->assertEquals('Updated Name', $user->name);
        $this->assertNull($user->intern_id);
        $this->assertNull($user->division);
        $this->assertNotEquals($mentor->id, $user->mentor_id);
    }

    /** @test */
    public function admin_can_update_all_fields_of_user()
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user = User::factory()->create(['role' => User::ROLE_ANAK_MAGANG]);

        $this->actingAs($admin)->put(route('users.profile.update', $user), [
            'name' => 'Admin Updated',
            'email' => 'admin-updated@example.com',
            'intern_id' => 'I-1234',
            'division' => 'QA',
            'mentor_id' => $admin->id,
            'start_date' => '2026-01-01',
            'end_date' => '2026-03-01',
            'role' => User::ROLE_ANAK_MAGANG,
        ])->assertSessionHasNoErrors();

        $user->refresh();

        $this->assertEquals('Admin Updated', $user->name);
        $this->assertEquals('I-1234', $user->intern_id);
        $this->assertEquals('QA', $user->division);
        $this->assertEquals($admin->id, $user->mentor_id);
        $this->assertEquals('2026-01-01', $user->start_date->toDateString());
    }
}
