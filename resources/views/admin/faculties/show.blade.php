@extends('layouts.app')

@section('content')
<x-navigation>
    <div class="dashboard-content">
        <h5>Faculty Details</h5>

        <!-- Display any session messages (success/error) -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card mb-3">
            @if($faculty->image_url)
                <img
                    src="{{ asset('storage/' . $faculty->image_url) }}"
                    alt="{{ $faculty->name }}"
                    class="card-img-top"
                    style="max-height: 300px; object-fit: cover;"
                >
            @endif

            <div class="card-body">
                <h5 class="card-title">
                    {{ $faculty->name }} ({{ $faculty->code }})
                </h5>
                <p class="card-text">
                    {{ $faculty->description }}
                </p>

                <p>
                    <strong>Departments:</strong> {{ $departmentsCount }}<br>
                    <strong>Programs:</strong> {{ $programsCount }}<br>
                    <strong>Students:</strong> {{ $studentsCount }}
                </p>

                <!-- Action Buttons -->
                <div class="d-flex">
                    <a href="{{ route('admin.faculties.index') }}" class="btn btn-secondary me-2">
                        Back
                    </a>
                    <a href="{{ route('admin.faculties.edit', $faculty) }}" class="btn btn-warning">
                        Edit Faculty
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-navigation>
@endsection
