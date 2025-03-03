@php
    $user = auth()->user();
    $currentRoute = request()->route()->getName();
@endphp

<div class="sidebar d-flex flex-column">
    <ul class="nav nav-pills flex-column mb-auto">
        @switch($user->role)
            @case('admin')
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ $currentRoute == 'admin.dashboard' ? 'active' : 'text-white' }}">
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.faculties.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'admin.faculties') ? 'active' : 'text-white' }}">
                        Faculties
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.departments.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'admin.departments') ? 'active' : 'text-white' }}">
                        Departments
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.reports.attendance') }}" class="nav-link {{ str_starts_with($currentRoute, 'admin.reports') ? 'active' : 'text-white' }}">
                        Reports
                    </a>
                </li>
            @break

            @case('lecturer')
                <li class="nav-item">
                    <a href="{{ route('lecturer.dashboard') }}" class="nav-link {{ $currentRoute == 'lecturer.dashboard' ? 'active' : 'text-white' }}">
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('lecturer.schedules.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'lecturer.schedules') ? 'active' : 'text-white' }}">
                        Lecture Schedules
                    </a>
                </li>
                <li>
                    <a href="{{ route('lecturer.lectures.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'lecturer.lectures') ? 'active' : 'text-white' }}">
                        Lectures
                    </a>
                </li>
                <li>
                    <a href="{{ route('lecturer.class-representatives.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'lecturer.class-representatives') ? 'active' : 'text-white' }}">
                        Class Representatives
                    </a>
                </li>
            @break

            @case('student')
                <li class="nav-item">
                    <a href="{{ route('student.dashboard') }}" class="nav-link {{ $currentRoute == 'student.dashboard' ? 'active' : 'text-white' }}">
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('student.schedule') }}" class="nav-link {{ str_starts_with($currentRoute, 'student.schedule') ? 'active' : 'text-white' }}">
                        Schedule
                    </a>
                </li>
                <li>
                    <a href="{{ route('student.attendance') }}" class="nav-link {{ str_starts_with($currentRoute, 'student.attendance') ? 'active' : 'text-white' }}">
                        Attendance
                    </a>
                </li>
                @if($user->student->isClassRep())
                <li>
                    <a href="{{ route('student.class-rep.dashboard') }}" class="nav-link {{ str_starts_with($currentRoute, 'student.class-rep') ? 'active' : 'text-white' }}">
                        Class Rep Dashboard
                    </a>
                </li>
                @endif
            @break
        @endswitch
    </ul>
</div>

<style>
.sidebar {
    width: 250px;
    background-color: #343a40;
    height: calc(100vh - 96px); /* Subtract navbar and footer height */
    position: fixed;
    top: 56px;
    left: 0;
    bottom: 40px;
    overflow-y: auto;
    z-index: 1000;
    padding: 1rem 0;
}

.sidebar .nav-link {
    color: #adb5bd;
    padding: 0.5rem 1rem;
    transition: all 0.3s ease;
}

.sidebar .nav-link:hover {
    background-color: rgba(255,255,255,0.1);
    color: #fff;
}

.sidebar .nav-link.active {
    background-color: #495057;
    color: #fff;
}
</style>
