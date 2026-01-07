@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Profil Pengguna</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <p><strong>Nama:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Avatar:</strong>
                @php
                    $avatarUrl = $user->avatar ? ('/storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=128&background=ddd&color=333';
                @endphp
                <img src="{{ $avatarUrl }}" alt="avatar" style="height:80px; border-radius:50%">
            </p>
            <p><strong>Intern ID (NIM):</strong> {{ $user->intern_id ?? '-' }}</p>
            <p><strong>Division:</strong> {{ $user->division ?? '-' }}</p>
            <p><strong>Mentor:</strong> {{ $user->mentor ? $user->mentor->name : '-' }}</p>
            <p><strong>Start Date:</strong> {{ $user->start_date?->toDateString() ?? '-' }}</p>
            <p><strong>End Date:</strong> {{ $user->end_date?->toDateString() ?? '-' }}</p>

            <a href="{{ auth()->user()->isAdmin() ? route('users.profile.edit', $user) : route('profile.edit') }}" class="btn btn-primary">Edit Profil</a>
        </div>
    </div>
</div>
@endsection
