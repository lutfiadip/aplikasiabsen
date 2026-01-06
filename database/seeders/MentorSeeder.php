<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;

class MentorSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure there is at least one pembimbing and a couple of anak magang with that mentor
        $mentor = User::firstOrCreate(
            ['email' => 'pembimbing@example.test'],
            [
                'name' => 'Pembimbing Example',
                'password' => bcrypt('password123'),
                'role' => User::ROLE_PEMBIMBING,
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'anak1@example.test'],
            [
                'name' => 'Anak Magang 1',
                'password' => bcrypt('password123'),
                'role' => User::ROLE_ANAK_MAGANG,
                'mentor_id' => $mentor->id,
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'anak2@example.test'],
            [
                'name' => 'Anak Magang 2',
                'password' => bcrypt('password123'),
                'role' => User::ROLE_ANAK_MAGANG,
                'mentor_id' => $mentor->id,
                'is_active' => true,
            ]
        );
    }
}
