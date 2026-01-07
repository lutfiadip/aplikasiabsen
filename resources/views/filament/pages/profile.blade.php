<div class="filament-page-heading">
    <h1 class="text-2xl font-bold">Profil</h1>
</div>

<div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white p-6 rounded shadow">
        <h2 class="font-semibold">Informasi</h2>
        <p class="mt-2">Nama: {{ auth()->user()->name }}</p>
        <p>Email: {{ auth()->user()->email }}</p>
        <p>Role: {{ auth()->user()->role }}</p>
        @if(auth()->user()->intern_id)
            <p>Intern ID: {{ auth()->user()->intern_id }}</p>
        @endif
        @if(auth()->user()->division)
            <p>Division: {{ auth()->user()->division }}</p>
        @endif
    </div>

    <div class="bg-white p-6 rounded shadow flex items-center justify-center">
        @if(auth()->user()->avatar)
            <img src="/storage/{{ auth()->user()->avatar }}" alt="avatar" class="rounded-full" style="height:160px; width:160px;">
        @else
            <div class="rounded-full bg-gray-200 flex items-center justify-center" style="height:160px; width:160px;">No avatar</div>
        @endif
    </div>
</div>