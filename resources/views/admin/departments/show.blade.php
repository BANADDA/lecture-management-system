@extends('layouts.app')

@section('content')
<x-navigation>
    <div class="dashboard-content">
        <h5>Department Details</h5>

        <div class="card mb-3">
            @if($department->image_url)
                <img src="{{ asset('storage/' . $department->image_url) }}" class="card-img-top" alt="{{ $department->name }}" style="max-height:300px; object-fit: cover;">
            @endif
            <div class="card-body">
                <h5 class="card-title">{{ $department->name }} ({{ $department->code }})</h5>
                <p class="card-text">{{ $department->description }}</p>
                <p class="mb-1"><strong>Campus:</strong> {{ $department->campus }}</p>
                <p class="mb-1"><strong>Faculty:</strong> {{ $department->faculty->name ?? 'N/A' }}</p>
                <p class="mb-1"><strong>Programs:</strong> {{ $department->programs()->count() }}</p>
                <p class="mb-1"><strong>Lecturers:</strong> {{ $department->lecturers()->count() }}</p>
                <p class="mb-1"><strong>Students:</strong> {{ $department->students_count }}</p>

                <div class="mt-3">
                    <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">Back</a>
                    <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-warning">Edit</a>
                </div>
            </div>
        </div>
    </div>
</x-navigation>
@endsection
