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
        <style>
            .sidebar {
                background-color: #343a40;
                min-height: calc(100vh - 56px);
                width: 220px;
                padding-top: 20px;
                transition: width 0.3s;
            }
            .sidebar.collapsed {
                width: 60px;
            }
            .sidebar-section {
                margin-bottom: 10px;
                padding: 0 15px;
                overflow: hidden;
            }
            .sidebar-section-title {
                color: #adb5bd;
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                font-weight: 600;
                margin-bottom: 10px;
                padding: 0 10px;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            .sidebar-section-title .toggle-icon {
                transform: rotate(0deg);
                transition: transform 0.3s;
                font-size: 0.7rem;
                width: 16px;
                text-align: center;
            }
            .sidebar-section-title.collapsed .toggle-icon {
                transform: rotate(-90deg);
            }
            .sidebar-section-content {
                max-height: 1000px;
                transition: max-height 0.3s ease;
                overflow: hidden;
            }
            .sidebar-section-content.collapsed {
                max-height: 0;
            }
            .sidebar .nav-link {
                border-radius: 5px;
                margin-bottom: 5px;
                display: flex;
                align-items: center;
                padding: 6px 10px;
                transition: all 0.2s;
                font-size: 0.85rem;
            }
            .sidebar .nav-link:hover {
                background-color: rgba(255, 255, 255, 0.1);
            }
            .sidebar .nav-link.active {
                background-color: #0d6efd;
            }
            .sidebar .nav-link i {
                margin-right: 10px;
                width: 18px;
                text-align: center;
                font-size: 0.9rem;
            }
            .sidebar .nav-link span {
                flex: 1;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            /* Toggle button for sidebar */
            .sidebar-toggle {
                position: absolute;
                bottom: 20px;
                left: 10px;
                width: 40px;
                height: 40px;
                background-color: #0d6efd;
                color: white;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                z-index: 1000;
                border: none;
                box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            }

            /* Responsive adjustments */
            @media (max-width: 768px) {
                .sidebar {
                    width: 60px;
                }
                .sidebar:hover {
                    width: 220px;
                }
            }
        </style>
        <ul class="nav nav-pills flex-column mb-auto">
            @switch($user->role)
                @case('admin')
                    <div class="sidebar-section">
                        <div class="sidebar-section-title" data-bs-toggle="collapse" data-bs-target="#mainSection">
                            <span>Main</span>
                            <i class="fas fa-chevron-down toggle-icon"></i>
                        </div>
                        <div class="sidebar-section-content" id="mainSection">
                            <li class="nav-item">
                                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ $currentRoute == 'admin.dashboard' ? 'active' : 'text-white' }}">
                                    <i class="fas fa-tachometer-alt"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                        </div>
                    </div>

                    <div class="sidebar-section">
                        <div class="sidebar-section-title" data-bs-toggle="collapse" data-bs-target="#academicSection">
                            <span>Academic Structure</span>
                            <i class="fas fa-chevron-down toggle-icon"></i>
                        </div>
                        <div class="sidebar-section-content" id="academicSection">
                            <li>
                                <a href="{{ route('admin.faculties.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'admin.faculties') ? 'active' : 'text-white' }}">
                                    <i class="fas fa-university"></i>
                                    <span>Faculties</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.departments.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'admin.departments') ? 'active' : 'text-white' }}">
                                    <i class="fas fa-building"></i>
                                    <span>Departments</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.programs.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'admin.programs') ? 'active' : 'text-white' }}">
                                    <i class="fas fa-graduation-cap"></i>
                                    <span>Programs</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.courses.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'admin.courses') ? 'active' : 'text-white' }}">
                                    <i class="fas fa-book"></i>
                                    <span>Courses</span>
                                </a>
                            </li>
                        </div>
                    </div>

                    <div class="sidebar-section">
                        <div class="sidebar-section-title" data-bs-toggle="collapse" data-bs-target="#teachingSection">
                            <span>Teaching</span>
                            <i class="fas fa-chevron-down toggle-icon"></i>
                        </div>
                        <div class="sidebar-section-content" id="teachingSection">
                            <li>
                                <a href="{{ route('admin.lectures.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'admin.lectures') ? 'active' : 'text-white' }}">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                    <span>Lectures</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.lecturers.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'admin.lecturers') ? 'active' : 'text-white' }}">
                                    <i class="fas fa-user-tie"></i>
                                    <span>Lecturers</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.students.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'admin.students') ? 'active' : 'text-white' }}">
                                    <i class="fas fa-user-graduate"></i>
                                    <span>Students</span>
                                </a>
                            </li>
                        </div>
                    </div>

                    <div class="sidebar-section">
                        <div class="sidebar-section-title" data-bs-toggle="collapse" data-bs-target="#planningSection">
                            <span>Planning</span>
                            <i class="fas fa-chevron-down toggle-icon"></i>
                        </div>
                        <div class="sidebar-section-content" id="planningSection">
                            <li>
                                <a href="{{ route('admin.timetable.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'admin.timetable') ? 'active' : 'text-white' }}">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>Timetable</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.reports.attendance') }}" class="nav-link {{ str_starts_with($currentRoute, 'admin.reports') ? 'active' : 'text-white' }}">
                                    <i class="fas fa-chart-bar"></i>
                                    <span>Reports</span>
                                </a>
                            </li>
                        </div>
                    </div>
                @break

                @case('lecturer')
                    <div class="sidebar-section">
                        <div class="sidebar-section-title" data-bs-toggle="collapse" data-bs-target="#mainSection">
                            <span>Main</span>
                            <i class="fas fa-chevron-down toggle-icon"></i>
                        </div>
                        <div class="sidebar-section-content" id="mainSection">
                            <li class="nav-item">
                                <a href="{{ route('lecturer.dashboard') }}" class="nav-link {{ $currentRoute == 'lecturer.dashboard' ? 'active' : 'text-white' }}">
                                    <i class="fas fa-tachometer-alt"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('lecturer.profile') }}" class="nav-link {{ str_starts_with($currentRoute, 'lecturer.profile') ? 'active' : 'text-white' }}">
                                    <i class="fas fa-id-card"></i>
                                    <span>Lecturer Info</span>
                                </a>
                            </li>
                        </div>
                    </div>

                    <div class="sidebar-section">
                        <div class="sidebar-section-title" data-bs-toggle="collapse" data-bs-target="#teachingSection">
                            <span>Teaching</span>
                            <i class="fas fa-chevron-down toggle-icon"></i>
                        </div>
                        <div class="sidebar-section-content" id="teachingSection">
                            <li>
                                <a href="{{ route('lecturer.schedules.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'lecturer.schedules') ? 'active' : 'text-white' }}">
                                    <i class="fas fa-clock"></i>
                                    <span>Lecture Schedules</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('lecturer.lectures.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'lecturer.lectures') ? 'active' : 'text-white' }}">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                    <span>Lectures</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('lecturer.timetable.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'lecturer.timetable') ? 'active' : 'text-white' }}">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>Timetable</span>
                                </a>
                            </li>
                        </div>
                    </div>

                    <div class="sidebar-section">
                        <div class="sidebar-section-title" data-bs-toggle="collapse" data-bs-target="#academicSection">
                            <span>Academic</span>
                            <i class="fas fa-chevron-down toggle-icon"></i>
                        </div>
                        <div class="sidebar-section-content" id="academicSection">
                            <li>
                                <a href="{{ route('lecturer.programs.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'lecturer.programs') ? 'active' : 'text-white' }}">
                                    <i class="fas fa-graduation-cap"></i>
                                    <span>Programs</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('lecturer.courses.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'lecturer.courses') ? 'active' : 'text-white' }}">
                                    <i class="fas fa-book"></i>
                                    <span>Courses</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('lecturer.students.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'lecturer.students') ? 'active' : 'text-white' }}">
                                    <i class="fas fa-user-graduate"></i>
                                    <span>Students</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('lecturer.class-representatives.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'lecturer.class-representatives') ? 'active' : 'text-white' }}">
                                    <i class="fas fa-user-friends"></i>
                                    <span>Class Representatives</span>
                                </a>
                            </li>
                        </div>
                    </div>
                @break

                @case('student')
                    <div class="sidebar-section">
                        <div class="sidebar-section-title" data-bs-toggle="collapse" data-bs-target="#mainSection">
                            <span>Main</span>
                            <i class="fas fa-chevron-down toggle-icon"></i>
                        </div>
                        <div class="sidebar-section-content" id="mainSection">
                            <li class="nav-item">
                                <a href="{{ route('student.dashboard') }}" class="nav-link {{ $currentRoute == 'student.dashboard' ? 'active' : 'text-white' }}">
                                    <i class="fas fa-tachometer-alt"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                        </div>
                    </div>

                    <div class="sidebar-section">
                        <div class="sidebar-section-title" data-bs-toggle="collapse" data-bs-target="#academicsSection">
                            <span>Academics</span>
                            <i class="fas fa-chevron-down toggle-icon"></i>
                        </div>
                        <div class="sidebar-section-content" id="academicsSection">
                            <li>
                                <a href="{{ route('student.schedule') }}" class="nav-link {{ str_starts_with($currentRoute, 'student.schedule') ? 'active' : 'text-white' }}">
                                    <i class="fas fa-calendar-week"></i>
                                    <span>Schedule</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('student.attendance') }}" class="nav-link {{ str_starts_with($currentRoute, 'student.attendance') ? 'active' : 'text-white' }}">
                                    <i class="fas fa-clipboard-check"></i>
                                    <span>Attendance</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('student.timetable.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'student.timetable') ? 'active' : 'text-white' }}">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>Timetable</span>
                                </a>
                            </li>
                        </div>
                    </div>

                    <div class="sidebar-section">
                        <div class="sidebar-section-title" data-bs-toggle="collapse" data-bs-target="#resourcesSection">
                            <span>Resources</span>
                            <i class="fas fa-chevron-down toggle-icon"></i>
                        </div>
                        <div class="sidebar-section-content" id="resourcesSection">
                            <li>
                                <a href="{{ route('student.programs.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'student.programs') ? 'active' : 'text-white' }}">
                                    <i class="fas fa-graduation-cap"></i>
                                    <span>Programs</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('student.courses.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'student.courses') ? 'active' : 'text-white' }}">
                                    <i class="fas fa-book"></i>
                                    <span>Courses</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('student.lectures.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'student.lectures') ? 'active' : 'text-white' }}">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                    <span>Lectures</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('student.lecturers.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'student.lecturers') ? 'active' : 'text-white' }}">
                                    <i class="fas fa-user-tie"></i>
                                    <span>Lecturers</span>
                                </a>
                            </li>
                        </div>
                    </div>

                    @if($user->student->isClassRep())
                    <div class="sidebar-section">
                        <div class="sidebar-section-title" data-bs-toggle="collapse" data-bs-target="#classRepSection">
                            <span>Class Rep</span>
                            <i class="fas fa-chevron-down toggle-icon"></i>
                        </div>
                        <div class="sidebar-section-content" id="classRepSection">
                            <li>
                                <a href="{{ route('student.class-rep.dashboard') }}" class="nav-link {{ str_starts_with($currentRoute, 'student.class-rep') ? 'active' : 'text-white' }}">
                                    <i class="fas fa-users-cog"></i>
                                    <span>Class Rep Dashboard</span>
                                </a>
                            </li>
                        </div>
                    </div>
                    @endif
                @break
            @endswitch
        </ul>

        <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-chevron-left"></i></button>
    </div>

    {{ $slot }}
</div>

<script>
    // Initialize collapsible sections
    document.addEventListener('DOMContentLoaded', function() {
        // Handle section toggles
        const sectionTitles = document.querySelectorAll('.sidebar-section-title');

        sectionTitles.forEach(title => {
            title.addEventListener('click', function() {
                this.classList.toggle('collapsed');
                const content = document.getElementById(this.getAttribute('data-bs-target').substring(1));
                content.classList.toggle('collapsed');
            });
        });

        // Handle sidebar toggle
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');

        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');

            // Change icon direction
            const icon = this.querySelector('i');
            if (sidebar.classList.contains('collapsed')) {
                icon.classList.remove('fa-chevron-left');
                icon.classList.add('fa-chevron-right');
            } else {
                icon.classList.remove('fa-chevron-right');
                icon.classList.add('fa-chevron-left');
            }
        });
    });
</script>
