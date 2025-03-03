@php
    $user = auth()->user();
    $currentRoute = request()->route()->getName();
@endphp

<nav class="navbar navbar-expand-lg navbar-dark bg-dark py-2">
    <div class="container-fluid">
        <span class="navbar-brand mb-0">
            @switch($user->role)
                @case('admin')
                    MMU Admin Portal
                @break
                @case('lecturer')
                    Lecturer Dashboard
                @break
                @case('student')
                    Student Portal
                @break
                @default
                    Lecture Management System
            @endswitch
        </span>

        <div class="d-flex ms-auto align-items-center">
            <div class="dropdown me-2">
                <a href="#" class="dropdown-toggle text-white text-decoration-none" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.85rem;">
                    @if($user->profile_photo)
                        <img src="{{ asset('storage/' . $user->profile_photo) }}" class="rounded-circle me-1" style="width: 25px; height: 25px; object-fit: cover;">
                    @endif
                    {{ $user->name }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="{{ route($user->role . '.profile') }}">Profile</a></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
