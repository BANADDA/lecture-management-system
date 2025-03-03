<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\FacultyController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\LecturerController;
use App\Http\Controllers\Admin\LectureController; // Admin lectures controller
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TimetableController;
use App\Http\Controllers\Lecturer\LecturerDashboardController;
use App\Http\Controllers\Lecturer\LectureScheduleController;
use App\Http\Controllers\Lecturer\LectureController as LecturerLectureController; // For lecturer role
use App\Http\Controllers\Lecturer\LectureAttendanceController;
use App\Http\Controllers\Lecturer\ClassRepresentativeController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\StudentScheduleController;
use App\Http\Controllers\Student\StudentAttendanceController;
use App\Http\Controllers\Student\LectureController as StudentLectureController; // For student role
use App\Http\Controllers\Student\LecturerController as StudentLecturerController; // For student role

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect to login page
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Auth::routes();

// Dashboard Redirect based on role
Route::get('/dashboard', function () {
    if (auth()->check()) {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif (auth()->user()->isLecturer()) {
            return redirect()->route('lecturer.dashboard');
        } elseif (auth()->user()->isStudent()) {
            return redirect()->route('student.dashboard');
        }
    }
    return redirect()->route('login');
})->name('dashboard');

// Admin Routes
Route::prefix('admin')->middleware(['auth'])->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('profile', [AdminDashboardController::class, 'profile'])->name('profile');
    Route::put('profile', [AdminDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::put('profile/password', [AdminDashboardController::class, 'updatePassword'])->name('profile.password');

    // Faculties
    Route::resource('faculties', FacultyController::class)
        ->names([
            'index' => 'faculties.index',
            'create' => 'faculties.create',
            'store' => 'faculties.store',
            'show' => 'faculties.show',
            'edit' => 'faculties.edit',
            'update' => 'faculties.update',
            'destroy' => 'faculties.destroy'
        ]);

    // Departments
    Route::resource('departments', DepartmentController::class);

    // Programs
    Route::resource('programs', ProgramController::class);

    // Courses
    Route::resource('courses', CourseController::class);

    // Lectures Management
    Route::resource('lectures', LectureController::class);
    Route::get('lectures/{lecture}/mark-completed', [LectureController::class, 'markCompleted'])->name('lectures.mark-completed');
    Route::get('lectures/{lecture}/attendance', [LectureController::class, 'showAttendance'])->name('lectures.attendance');
    Route::get('lectures/{lecture}/export-attendance', [LectureController::class, 'exportAttendance'])->name('lectures.export-attendance');

    // Timetable routes - make sure this comes after the lecture resource routes
    Route::get('timetable', [LectureController::class, 'timetable'])->name('timetable.index');
    Route::get('weekly-timetable', [LectureController::class, 'weeklyTimetable'])->name('weekly-timetable');
    Route::get('export-weekly-timetable', [LectureController::class, 'exportWeeklyTimetable'])->name('export-weekly-timetable');
    Route::get('export-timetable-pdf', [TimetableController::class, 'exportPDF'])->name('export-timetable-pdf');

    // Students
    Route::resource('students', StudentController::class);
    Route::post('students/{student}/reset-password', [StudentController::class, 'resetPassword'])->name('students.reset-password');

    // Lecturers
    Route::resource('lecturers', LecturerController::class);
    Route::post('lecturers/{lecturer}/reset-password', [LecturerController::class, 'resetPassword'])->name('lecturers.reset-password');

    // Users
    Route::resource('users', UserController::class);
    Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

    // Reports
    Route::get('reports/attendance', [AdminDashboardController::class, 'attendanceReport'])->name('reports.attendance');
    Route::get('reports/lecturers', [AdminDashboardController::class, 'lecturersReport'])->name('reports.lecturers');
    Route::get('reports/students', [AdminDashboardController::class, 'studentsReport'])->name('reports.students');
});

// Lecturer Routes
Route::prefix('lecturer')->middleware(['auth', 'role:lecturer'])->name('lecturer.')->group(function () {
    Route::get('/', [LecturerDashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('profile', [LecturerDashboardController::class, 'profile'])->name('profile');
    Route::put('profile', [LecturerDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::put('profile/password', [LecturerDashboardController::class, 'updatePassword'])->name('profile.password');

    // Lecture Schedules
    Route::resource('schedules', LectureScheduleController::class);
    Route::post('schedules/{schedule}/generate-lectures', [LectureScheduleController::class, 'generateLectures'])->name('schedules.generate-lectures');

    // Lectures
    Route::resource('lectures', LecturerLectureController::class);
    Route::post('lectures/{lecture}/complete', [LecturerLectureController::class, 'markAsCompleted'])->name('lectures.complete');
    Route::get('lectures/{lecture}/attendance-qr', [LecturerLectureController::class, 'generateAttendanceQR'])->name('lectures.attendance-qr');

    // Lecture Attendance
    Route::get('lectures/{lecture}/attendance', [LectureAttendanceController::class, 'index'])->name('lectures.attendance');
    Route::post('lectures/{lecture}/attendance/{student}', [LectureAttendanceController::class, 'store'])->name('lectures.attendance.store');
    Route::delete('lectures/{lecture}/attendance/{student}', [LectureAttendanceController::class, 'destroy'])->name('lectures.attendance.destroy');
    Route::post('lectures/{lecture}/attendance/bulk', [LectureAttendanceController::class, 'bulkStore'])->name('lectures.attendance.bulk-store');

    // Class Representatives
    Route::resource('class-representatives', ClassRepresentativeController::class)->except(['show']);
    Route::get('courses/{course}/class-representative/create', [ClassRepresentativeController::class, 'create'])->name('courses.class-representative.create');

    // Department-specific routes
    Route::get('department/{department}/lecturers', [LecturerDashboardController::class, 'departmentLecturers'])->name('department.lecturers');
    Route::get('department/{department}/courses', [LecturerDashboardController::class, 'departmentCourses'])->name('department.courses');

    // Course-specific routes
    Route::get('courses/{course}/students', [LecturerDashboardController::class, 'courseStudents'])->name('courses.students');
    Route::get('courses/{course}/attendance-report', [LecturerDashboardController::class, 'courseAttendanceReport'])->name('courses.attendance-report');
});

// Student Routes
Route::prefix('student')->middleware(['auth', 'role:student'])->name('student.')->group(function () {
    Route::get('/', [StudentDashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('profile', [StudentDashboardController::class, 'profile'])->name('profile');
    Route::put('profile', [StudentDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::put('profile/password', [StudentDashboardController::class, 'updatePassword'])->name('profile.password');

    // Schedule
    Route::get('schedule', [StudentScheduleController::class, 'index'])->name('schedule');
    Route::get('schedule/{date}', [StudentScheduleController::class, 'showDate'])->name('schedule.date');

    // Attendance
    Route::get('attendance', [StudentAttendanceController::class, 'index'])->name('attendance');
    Route::get('attendance/{course}', [StudentAttendanceController::class, 'course'])->name('attendance.course');
    Route::post('attendance/scan', [StudentAttendanceController::class, 'scanQR'])->name('attendance.scan');

    // NEW: Lectures and Lecturers for students
    Route::resource('lectures', StudentLectureController::class);
    Route::resource('lecturers', StudentLecturerController::class);

    // Class Representative access
    Route::middleware(['classrep'])->prefix('class-rep')->name('class-rep.')->group(function () {
        Route::get('dashboard', [StudentDashboardController::class, 'classRepDashboard'])->name('dashboard');
        Route::get('course/{course}/attendance', [StudentDashboardController::class, 'viewAttendance'])->name('attendance');
        Route::post('course/{course}/report-issue', [StudentDashboardController::class, 'reportIssue'])->name('report-issue');
    });
});
