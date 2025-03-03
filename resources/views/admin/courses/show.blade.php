@extends('layouts.app')

@section('content')
<x-navigation>
    <div class="dashboard-content">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body d-flex justify-content-between align-items-center py-3">
                <div>
                    <h5 class="fw-bold mb-0">Course Details</h5>
                    <p class="text-muted small mb-0">View information about this course</p>
                </div>
                <div>
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary btn-sm me-2">
                        <i class="fas fa-arrow-left me-1"></i> Back to Courses
                    </a>
                    <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-warning btn-sm text-white">
                        <i class="fas fa-edit me-1"></i> Edit Course
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-info-circle me-2"></i>Basic Information
                        </h6>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="detail-label">Course Name</div>
                                <div class="detail-value fw-medium">{{ $course->name }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="detail-label">Course Code</div>
                                <div class="detail-value">
                                    <span class="badge bg-primary">{{ $course->code }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="detail-label">Credits</div>
                                <div class="detail-value">
                                    <span class="badge bg-info">{{ $course->credits }} {{ Str::plural('Credit', $course->credits) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="detail-label">Program</div>
                                <div class="detail-value">
                                    <i class="fas fa-graduation-cap me-1 text-muted"></i>
                                    {{ $course->program->name ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="detail-label">Department</div>
                                <div class="detail-value">
                                    <i class="fas fa-building me-1 text-muted"></i>
                                    {{ $course->program->department->name ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="detail-label">Year & Semester</div>
                                <div class="detail-value">
                                    <i class="fas fa-calendar-alt me-1 text-muted"></i>
                                    Year {{ $course->year }}, Semester {{ $course->semester }}
                                </div>
                            </div>
                        </div>

                        <h6 class="fw-bold mb-2 mt-4">
                            <i class="fas fa-align-left me-2"></i>Description
                        </h6>
                        <p class="course-description text-muted">
                            {{ $course->description ?? 'No description available' }}
                        </p>

                        <h6 class="fw-bold mb-2 mt-4">
                            <i class="fas fa-users me-2"></i>Students
                        </h6>
                        <p>
                            <!-- Use a safer way to count students that avoids the ambiguous column issue -->
                            @php
                                $studentsCount = DB::table('student_courses')
                                    ->where('course_id', $course->id)
                                    ->where('student_courses.status', 'enrolled')
                                    ->count();
                            @endphp
                            <span class="badge bg-success">{{ $studentsCount }} Enrolled {{ Str::plural('Student', $studentsCount) }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-image me-2"></i>Course Image
                        </h6>
                        @if($course->image_url)
                            <img src="{{ asset('storage/' . $course->image_url) }}"
                                 alt="{{ $course->name }}"
                                 class="img-fluid rounded shadow-sm w-100"
                                 style="object-fit: cover; max-height: 300px;">
                        @else
                            <div class="course-image-placeholder d-flex align-items-center justify-content-center">
                                <i class="fas fa-book fa-3x text-muted"></i>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-cog me-2"></i>Actions
                        </h6>
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-warning text-white">
                                <i class="fas fa-edit me-2"></i>Edit Course
                            </a>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash me-2"></i>Delete Course
                            </button>
                            <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Courses
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete the course "{{ $course->name }}"?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.courses.destroy', $course) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-navigation>

@push('styles')
<style>
    .dashboard-content {
        padding: 1.5rem;
        background-color: #f8f9fa;
        min-height: 100vh;
    }

    .detail-label {
        font-size: 0.75rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
        font-weight: 500;
    }

    .detail-value {
        font-size: 0.875rem;
    }

    .course-description {
        line-height: 1.6;
        font-size: 0.9rem;
        text-align: justify;
    }

    .course-image-placeholder {
        width: 100%;
        height: 200px;
        background-color: #f8f9fa;
        border: 1px dashed #dee2e6;
        border-radius: 0.25rem;
    }

    /* Fix for button text not showing */
    .btn {
        text-align: center;
        justify-content: center;
        min-width: 68px; /* Ensure minimum width */
        color: #fff !important;
    }

    .btn-outline-secondary {
        color: #6c757d !important;
    }
</style>
@endpush
@endsection
