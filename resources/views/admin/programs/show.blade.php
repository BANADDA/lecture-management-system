@extends('layouts.app')

@section('content')
<x-navigation>
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-12">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb bg-light p-3 rounded">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.programs.index') }}">Programs</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $program->name }}</li>
                    </ol>
                </nav>

                <div class="card shadow-sm border-0 overflow-hidden">
                    @if($program->image_url)
                        <div class="position-relative program-header">
                            <img src="{{ asset('storage/' . $program->image_url) }}" class="card-img-top" alt="{{ $program->name }}" style="height: 350px; object-fit: cover; width: 100%;">
                            <div class="position-absolute bottom-0 start-0 w-100 bg-dark bg-opacity-75 text-white p-4">
                                <h2 class="card-title fw-bold mb-1">{{ $program->name }}</h2>
                                <p class="mb-0 d-flex align-items-center">
                                    <span class="badge bg-primary me-2">{{ $program->code }}</span>
                                    <span class="me-3"><i class="fas fa-calendar-alt me-1"></i> {{ $program->duration_years }} year{{ $program->duration_years > 1 ? 's' : '' }}</span>
                                    <span><i class="fas fa-university me-1"></i> {{ $program->department->name ?? 'N/A' }}</span>
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="card-header bg-primary text-white p-4">
                            <h2 class="card-title fw-bold mb-1">{{ $program->name }}</h2>
                            <p class="mb-0 d-flex align-items-center">
                                <span class="badge bg-light text-primary me-2">{{ $program->code }}</span>
                                <span class="me-3"><i class="fas fa-calendar-alt me-1"></i> {{ $program->duration_years }} year{{ $program->duration_years > 1 ? 's' : '' }}</span>
                                <span><i class="fas fa-university me-1"></i> {{ $program->department->name ?? 'N/A' }}</span>
                            </p>
                        </div>
                    @endif

                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2 mb-4"><i class="fas fa-info-circle me-2"></i>Program Description</h5>
                                <p class="lead">{{ $program->description }}</p>

                                @if($program->coursesByYearAndSemester)
                                    <h5 class="border-bottom pb-2 mb-4 mt-5"><i class="fas fa-book me-2"></i>Curriculum Structure</h5>

                                    <div class="accordion" id="programCurriculum">
                                        @foreach($program->coursesByYearAndSemester as $year => $semesters)
                                            <div class="accordion-item border mb-3 shadow-sm">
                                                <h2 class="accordion-header" id="heading{{ $year }}">
                                                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $year }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse{{ $year }}">
                                                        <span class="fw-bold">Year {{ $year }}</span>
                                                    </button>
                                                </h2>
                                                <div id="collapse{{ $year }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="heading{{ $year }}" data-bs-parent="#programCurriculum">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            @foreach($semesters as $semester => $courses)
                                                                <div class="col-md-6 mb-3">
                                                                    <div class="card h-100 border-0 shadow-sm">
                                                                        <div class="card-header bg-light">
                                                                            <h6 class="mb-0">Semester {{ $semester }}</h6>
                                                                        </div>
                                                                        <div class="card-body">
                                                                            @if($courses->count())
                                                                                <ul class="list-group list-group-flush">
                                                                                    @foreach($courses as $course)
                                                                                        <li class="list-group-item px-0 py-2 border-bottom">
                                                                                            <div class="d-flex justify-content-between align-items-center">
                                                                                                <div>
                                                                                                    <h6 class="mb-0">{{ $course->name }}</h6>
                                                                                                    <small class="text-muted">{{ $course->code }}</small>
                                                                                                </div>
                                                                                                <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-sm btn-outline-primary">
                                                                                                    <i class="fas fa-eye"></i>
                                                                                                </a>
                                                                                            </div>
                                                                                        </li>
                                                                                    @endforeach
                                                                                </ul>
                                                                            @else
                                                                                <div class="alert alert-info mb-0">
                                                                                    <i class="fas fa-info-circle me-2"></i>No courses assigned for this semester
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="d-flex justify-content-between mt-5">
                                    <a href="{{ route('admin.programs.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Back to Programs
                                    </a>
                                    <a href="{{ route('admin.programs.edit', $program) }}" class="btn btn-warning">
                                        <i class="fas fa-edit me-2"></i>Edit Program
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-navigation>
@endsection

@push('styles')
<style>
    .program-header {
        position: relative;
    }

    .program-header::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(rgba(0,0,0,0), rgba(0,0,0,0.7));
        pointer-events: none;
    }

    .accordion-button:not(.collapsed) {
        background-color: rgba(var(--bs-primary-rgb), 0.1);
        color: var(--bs-primary);
    }

    .list-group-item:hover {
        background-color: rgba(var(--bs-light-rgb), 0.5);
    }

    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
    }
</style>
@endpush

@push('scripts')
<script>
    // Initialize any necessary JavaScript here
    document.addEventListener('DOMContentLoaded', function() {
        // Add animation classes to elements when they come into view
        const animateElements = document.querySelectorAll('.card, .accordion-item');

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate__animated', 'animate__fadeIn');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1
        });

        animateElements.forEach(element => {
            observer.observe(element);
        });
    });
</script>
@endpus
