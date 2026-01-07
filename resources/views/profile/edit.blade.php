@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Profil</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ auth()->user()->isAdmin() && isset($user) && auth()->user()->id !== $user->id ? route('users.profile.update', $user) : route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password (kosongkan jika tidak ingin mengganti)</label>
            <input type="password" name="password" class="form-control">
            <input type="password" name="password_confirmation" class="form-control mt-2" placeholder="Konfirmasi password">
        </div>

        <div class="mb-3">
            <label class="form-label">Avatar</label>
            <input type="file" name="avatar" class="form-control">
            @if($user->avatar)
                <div class="mt-2"><img src="{{ asset('storage/' . $user->avatar) }}" style="height:80px;"></div>
            @endif
        </div>

        <hr>

        <h5>Data Administratif (hanya dapat diubah oleh admin)</h5>

        <div class="mb-3">
            <label class="form-label">Intern ID (NIM)</label>
            <input type="text" name="intern_id" value="{{ old('intern_id', $user->intern_id) }}" class="form-control" {{ auth()->user()->isAdmin() ? '' : 'disabled' }}>
        </div>

        <div class="mb-3">
            <label class="form-label">Division</label>
            <input type="text" name="division" value="{{ old('division', $user->division) }}" class="form-control" {{ auth()->user()->isAdmin() ? '' : 'disabled' }}>
        </div>

        <div class="mb-3">
            <label class="form-label">Mentor</label>
            <select name="mentor_id" class="form-control" {{ auth()->user()->isAdmin() ? '' : 'disabled' }}>
                <option value="">-</option>
                @foreach(App\Models\User::where('role', App\Models\User::ROLE_PEMBIMBING)->get() as $m)
                    <option value="{{ $m->id }}" @selected(old('mentor_id', $user->mentor_id) == $m->id)>{{ $m->name }}</option>
                @endforeach
            </select>
        </div>

        @if($user->role !== \App\Models\User::ROLE_ANAK_MAGANG)
            <div class="mb-3">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" value="{{ old('start_date', optional($user->start_date)->toDateString()) }}" class="form-control" {{ auth()->user()->isAdmin() ? '' : 'disabled' }}>
            </div>

            <div class="mb-3">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" value="{{ old('end_date', optional($user->end_date)->toDateString()) }}" class="form-control" {{ auth()->user()->isAdmin() ? '' : 'disabled' }}>
            </div>
        @endif

        @if(!auth()->user()->isAdmin())
            <p class="text-muted">Data administratif hanya dapat diubah oleh admin.</p>
        @endif

        <button class="btn btn-primary" type="submit">Simpan</button>
    </form>
</div>
@endsection
