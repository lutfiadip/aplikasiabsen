<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MentorshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_pembimbing_can_only_view_their_mentees()
    {
        // create mentor
        $mentor = User::factory()->create(['role' => User::ROLE_PEMBIMBING]);

        // mentee assigned to this mentor
        $myMentee = User::factory()->create(['role' => User::ROLE_ANAK_MAGANG, 'mentor_id' => $mentor->id]);

        // mentee assigned to another mentor
        $otherMentor = User::factory()->create(['role' => User::ROLE_PEMBIMBING]);
        $otherMentee = User::factory()->create(['role' => User::ROLE_ANAK_MAGANG, 'mentor_id' => $otherMentor->id]);

        // Mentor can viewAny (list) — policy allows pembimbing but queries must be filtered in UI
        $this->assertTrue($mentor->can('viewAny', User::class));

        // Mentor can view their assigned mentee
        $this->assertTrue($mentor->can('view', $myMentee));

        // Mentor cannot view someone else's mentee
        $this->assertFalse($mentor->can('view', $otherMentee));

        // Check the Eloquent-scoped list for a pembimbing
        $visible = User::query()->where('mentor_id', $mentor->id)->get();
        $this->assertTrue($visible->contains('id', $myMentee->id));
        $this->assertFalse($visible->contains('id', $otherMentee->id));
    }
}
