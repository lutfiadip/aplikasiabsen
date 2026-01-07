<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(User $user = null)
    {
        $target = $user ?? Auth::user();

        $this->authorize('view', $target);

        return view('profile.show', ['user' => $target]);
    }

    public function edit(User $user = null)
    {
        $target = $user ?? Auth::user();

        $this->authorize('update', $target);

        return view('profile.edit', ['user' => $target]);
    }

    public function update(Request $request, User $user = null)
    {
        $actor = $request->user();
        $target = $user ?? $actor;

        $this->authorize('update', $target);

        if ($actor->isAdmin()) {
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', Rule::unique('users')->ignore($target->id)],
                'password' => ['nullable', 'string', 'min:8', 'confirmed'],
                'avatar' => ['nullable', 'image', 'max:2048'],
                'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_PEMBIMBING, User::ROLE_ANAK_MAGANG])],
                'is_active' => ['nullable', 'boolean'],
                'intern_id' => ['nullable', 'string'],
                'division' => ['nullable', 'string'],
                'mentor_id' => ['nullable', 'exists:users,id'],
                'active_from' => ['nullable', 'date'],
                'active_until' => ['nullable', 'date', 'after_or_equal:active_from'],
            ];

            $validated = $request->validate($rules);

            $data = array_filter($validated, function ($v) { return $v !== null; });
        } else {
            // Anak Magang can only update personal fields. Any administrative fields are prohibited.
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                // email optional when not changing
                'email' => ['nullable', 'email', Rule::unique('users')->ignore($target->id)],
                'password' => ['nullable', 'string', 'min:8', 'confirmed'],
                'avatar' => ['nullable', 'image', 'max:2048'],

                // Prohibit administrative fields explicitly to prevent tampering
                'role' => ['prohibited'],
                'is_active' => ['prohibited'],
                'intern_id' => ['prohibited'],
                'division' => ['prohibited'],
                'mentor_id' => ['prohibited'],
                'start_date' => ['prohibited'],
                'end_date' => ['prohibited'],
            ];

            $validated = $request->validate($rules);

            // Only take allowed personal fields — ignore any administrative fields if tampered with
            $data = $request->only(['name', 'email']);
        }

        // Process password
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->input('password'));
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');

            // remove previous avatar if exists
            if ($target->avatar) {
                Storage::disk('public')->delete($target->avatar);
            }

            $data['avatar'] = $path;
        }

        // (debug logs removed)

        $target->update($data);

        return redirect()->route(isset($user) ? 'users.profile.edit' : 'profile.edit')
            ->with('success', 'Profile updated successfully.');
    }
}
