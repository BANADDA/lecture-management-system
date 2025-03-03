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

<div class="main-content">
    {{-- SIDEBAR --}}
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
                    <!-- Programs -->
                    <li>
                        <a href="{{ route('admin.programs.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'admin.programs') ? 'active' : 'text-white' }}">
                            Programs
                        </a>
                    </li>
                    <!-- Courses -->
                    <li>
                        <a href="{{ route('admin.courses.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'admin.courses') ? 'active' : 'text-white' }}">
                            Courses
                        </a>
                    </li>
                    <!-- New Lecture and Lecturer Links -->
                    <li>
                        <a href="{{ route('admin.lectures.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'admin.lectures') ? 'active' : 'text-white' }}">
                            Lectures
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.lecturers.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'admin.lecturers') ? 'active' : 'text-white' }}">
                            Lecturers
                        </a>
                    </li>
                    <!-- Students -->
                    <li>
                        <a href="{{ route('admin.students.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'admin.students') ? 'active' : 'text-white' }}">
                            Students
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.timetable.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'admin.timetable') ? 'active' : 'text-white' }}">
                            Timetable
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
                    <!-- For lecturers, add a "Lecturer Info" link if needed -->
                    <li>
                        <a href="{{ route('lecturer.profile') }}" class="nav-link {{ str_starts_with($currentRoute, 'lecturer.profile') ? 'active' : 'text-white' }}">
                            Lecturer Info
                        </a>
                    </li>
                    <!-- Programs -->
                    <li>
                        <a href="{{ route('lecturer.programs.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'lecturer.programs') ? 'active' : 'text-white' }}">
                            Programs
                        </a>
                    </li>
                    <!-- Courses -->
                    <li>
                        <a href="{{ route('lecturer.courses.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'lecturer.courses') ? 'active' : 'text-white' }}">
                            Courses
                        </a>
                    </li>
                    <!-- Students -->
                    <li>
                        <a href="{{ route('lecturer.students.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'lecturer.students') ? 'active' : 'text-white' }}">
                            Students
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('lecturer.timetable.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'lecturer.timetable') ? 'active' : 'text-white' }}">
                            Timetable
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
                    <!-- Programs -->
                    <li>
                        <a href="{{ route('student.programs.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'student.programs') ? 'active' : 'text-white' }}">
                            Programs
                        </a>
                    </li>
                    <!-- Courses -->
                    <li>
                        <a href="{{ route('student.courses.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'student.courses') ? 'active' : 'text-white' }}">
                            Courses
                        </a>
                    </li>
                    <!-- Lectures -->
                    <li>
                        <a href="{{ route('student.lectures.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'student.lectures') ? 'active' : 'text-white' }}">
                            Lectures
                        </a>
                    </li>
                    <!-- Lecturers -->
                    <li>
                        <a href="{{ route('student.lecturers.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'student.lecturers') ? 'active' : 'text-white' }}">
                            Lecturers
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('student.timetable.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'student.timetable') ? 'active' : 'text-white' }}">
                            Timetable
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

    {{ $slot }}
</div>
