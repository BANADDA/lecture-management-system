@extends('layouts.app')

@section('content')
<x-navigation>
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="fw-bold mb-0">Lecturers Management</h4>
                            <p class="text-muted mb-0">Manage lecturer records</p>
                        </div>
                        <a href="{{ route('admin.lecturers.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create New Lecturer
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title mb-3"><i class="fas fa-filter me-2"></i>Filter Lecturers</h5>
                        <form method="GET" action="{{ route('admin.lecturers.index') }}">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-search text-muted"></i>
                                        </span>
                                        <input type="text" name="search" class="form-control" placeholder="Search by staff ID or name" value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-building text-muted"></i>
                                        </span>
                                        <select name="department_id" class="form-select">
                                            <option value="">All Departments</option>
                                            @foreach($departments as $department)
                                                <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                                    {{ $department->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-filter me-2"></i>Apply
                                    </button>
                                    <a href="{{ route('admin.lecturers.index') }}" class="btn btn-outline-secondary w-100">
                                        <i class="fas fa-redo me-1"></i>Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lecturers Table Card -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 py-3">#</th>
                                        <th class="py-3">Staff ID</th>
                                        <th class="py-3">Name</th>
                                        <th class="py-3">Department</th>
                                        <th class="py-3">Office Location</th>
                                        <th class="py-3">Status</th>
                                        <th class="py-3">Profile Photo</th>
                                        <th class="py-3 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($lecturers as $lecturer)
                                        <tr class="lecturer-row">
                                            <td class="ps-4">{{ $loop->iteration + (($lecturers->currentPage()-1) * $lecturers->perPage()) }}</td>
                                            <td>{{ $lecturer->staff_id }}</td>
                                            <td>{{ $lecturer->full_name }}</td>
                                            <td>{{ $lecturer->department->name ?? 'N/A' }}</td>
                                            <td>{{ $lecturer->office_location ?? 'N/A' }}</td>
                                            <td>{{ ucfirst($lecturer->status) }}</td>
                                            <td>
                                                @if($lecturer->profile_photo)
                                                    <img src="{{ asset('storage/' . $lecturer->profile_photo) }}" alt="{{ $lecturer->full_name }}" style="width:40px; height:40px; object-fit:cover;" class="rounded">
                                                @else
                                                    <div style="width:40px; height:40px;" class="bg-light rounded d-flex align-items-center justify-content-center text-muted">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a href="{{ route('admin.lecturers.show', $lecturer) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="View Lecturer">
                                                        View
                                                    </a>
                                                    <a href="{{ route('admin.lecturers.edit', $lecturer) }}" class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip" title="Edit Lecturer">
                                                        Edit
                                                    </a>
                                                    <form action="{{ route('admin.lecturers.destroy', $lecturer) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete {{ $lecturer->full_name }}?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Delete Lecturer">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5">
                                                <div class="empty-state">
                                                    <i class="fas fa-folder-open empty-state-icon"></i>
                                                    <h6>No Lecturers Found</h6>
                                                    <p class="text-muted">No lecturers match your current filters</p>
                                                    <a href="{{ route('admin.lecturers.index') }}" class="btn btn-sm btn-primary mt-2">Clear Filters</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($lecturers->hasPages())
                        <div class="card-footer bg-white d-flex justify-content-center py-3">
                            {{ $lecturers->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-navigation>
@endsection

@push('styles')
<style>
    .lecturer-row {
        transition: all 0.2s;
    }
    .lecturer-row:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.05);
    }
    .empty-state {
        padding: 2rem;
        text-align: center;
    }
    .empty-state-icon {
        font-size: 3rem;
        color: #dee2e6;
        margin-bottom: 1rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
