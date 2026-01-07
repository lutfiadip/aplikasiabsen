<div style="display:none;">@php /* Invisible widget injecting JS */ @endphp</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        try {
            var topbar = document.querySelector('.filament-topbar');
            if (! topbar) return;

            // Add a small profile link to the topbar
            var a = document.createElement('a');
            a.href = '{{ route('profile.show') }}';
            a.className = 'filament-profile-link inline-flex items-center ml-4 text-sm';
            a.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor"><path d="M10 10a4 4 0 100-8 4 4 0 000 8zm-7 8a7 7 0 0114 0H3z" /></svg>Profile';

            // Append to topbar (right side)
            topbar.appendChild(a);
        } catch (e) {
            // ignore
            console.error(e);
        }
    });
</script>