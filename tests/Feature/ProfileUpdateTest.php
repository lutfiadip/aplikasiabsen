<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function anak_magang_cannot_update_admin_fields()
    {
        $user = User::factory()->create(['role' => User::ROLE_ANAK_MAGANG]);
        $mentor = User::factory()->create(['role' => User::ROLE_PEMBIMBING]);

        // Attempt to change administrative fields should fail validation
        $response = $this->actingAs($user)->from(route('profile.edit'))->put(route('profile.update'), [
            'name' => 'Updated Name',
            'intern_id' => 'I-9999',
            'division' => 'Hacked Division',
            'mentor_id' => $mentor->id,
        ]);

        $response->assertSessionHasErrors(['intern_id', 'division', 'mentor_id']);

        $user->refresh();

        // Nothing administrative should have changed
        $this->assertNotEquals('I-9999', $user->intern_id);
        $this->assertNotEquals('Hacked Division', $user->division);
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
            'active_from' => '2026-01-01',
            'active_until' => '2026-03-01',
            'role' => User::ROLE_ANAK_MAGANG,
        ])->assertSessionHasNoErrors();

        $user->refresh();

        $this->assertEquals('Admin Updated', $user->name);
        $this->assertEquals('I-1234', $user->intern_id);
        $this->assertEquals('QA', $user->division);
        $this->assertEquals($admin->id, $user->mentor_id);
        $this->assertEquals('2026-01-01', $user->active_from->toDateString());
    }

    /** @test */
    public function anak_magang_can_update_personal_fields()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create(['role' => User::ROLE_ANAK_MAGANG]);

        Storage::fake('public');

        $file = \Illuminate\Http\UploadedFile::fake()->image('avatar.png');

        $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'New Name',
            'email' => 'new-email@example.test',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
            'avatar' => $file,
        ])->assertSessionHasNoErrors();

        $user->refresh();

        $this->assertEquals('New Name', $user->name);
        $this->assertEquals('new-email@example.test', $user->email);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newpassword', $user->password));
        // Avatar stored on public disk
        Storage::disk('public')->assertExists($user->avatar);

        // Filament avatar URL helper should return a usable storage URL
        $this->assertStringContainsString('/storage/avatars/', $user->getFilamentAvatarUrl());
    }
}
