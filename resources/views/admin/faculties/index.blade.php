@extends('layouts.app')

@section('content')
<x-navigation>
    <div class="dashboard-content">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body d-flex justify-content-between align-items-center py-3">
                <div>
                    <h5 class="fw-bold mb-0">Faculties Management</h5>
                    <p class="text-muted small mb-0">Manage your academic faculties</p>
                </div>
                <a href="{{ route('admin.faculties.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Add New Faculty
                </a>
            </div>
        </div>

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

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3">Image</th>
                                <th class="py-3">Faculty Name</th>
                                <th class="py-3">Code</th>
                                <th class="py-3">Description</th>
                                <th class="py-3">Departments</th>
                                <th class="py-3">Programs</th>
                                <th class="py-3 text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse($faculties as $faculty)
                                <tr class="faculty-row">
                                    <td class="ps-4">
                                        @if($faculty->image_url)
                                            <img src="{{ asset('storage/' . $faculty->image_url) }}"
                                                alt="{{ $faculty->name }}"
                                                style="width:50px; height:50px; object-fit:cover;"
                                                class="rounded">
                                        @else
                                            <div class="faculty-image-placeholder">
                                                <i class="fas fa-university"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="fw-medium">{{ $faculty->name }}</td>
                                    <td><span class="badge bg-primary">{{ $faculty->code }}</span></td>
                                    <td class="text-muted">{{ Str::limit($faculty->description, 80) }}</td>
                                    <td><span class="badge bg-info">{{ $faculty->departments_count }}</span></td>
                                    <td><span class="badge bg-success">{{ $faculty->programs_count }}</span></td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <a href="{{ route('admin.faculties.show', $faculty) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="{{ route('admin.faculties.edit', $faculty) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $faculty->id }}">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-folder-open empty-state-icon"></i>
                                            <h6>No Faculties Found</h6>
                                            <p class="text-muted">No faculties have been added yet</p>
                                            <a href="{{ route('admin.faculties.create') }}" class="btn btn-sm btn-primary mt-2">Add Faculty</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Delete Modals -->
        @foreach($faculties as $faculty)
            <div class="modal fade" id="deleteModal{{ $faculty->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $faculty->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel{{ $faculty->id }}">Confirm Delete</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to delete the faculty "{{ $faculty->name }}"?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <form action="{{ route('admin.faculties.destroy', $faculty) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-navigation>

@push('styles')
<style>
    /* Table styling */
    .table {
        font-size: 0.875rem;
    }

    .table th {
        font-weight: 600;
    }

    /* Card styling */
    .card {
        border-radius: 0.5rem;
        overflow: hidden;
    }

    /* Action buttons */
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        line-height: 1.5;
        border-radius: 0.2rem;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
    }

    .btn-sm i {
        margin-right: 3px;
    }

    .btn-info, .btn-warning, .btn-danger, .btn-primary {
        color: #fff !important;
    }

    /* Badge styling */
    .badge {
        font-weight: 500;
        padding: 0.4em 0.6em;
    }

    /* Faculty image placeholder */
    .faculty-image-placeholder {
        width: 50px;
        height: 50px;
        border-radius: 0.25rem;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #adb5bd;
        font-size: 1.2rem;
    }

    /* Empty state styling */
    .empty-state {
        padding: 2rem;
        text-align: center;
    }

    .empty-state-icon {
        font-size: 3rem;
        color: #dee2e6;
        margin-bottom: 1rem;
    }

    /* Add some shadow to the dashboard content */
    .dashboard-content {
        padding: 1.5rem;
        background-color: #f8f9fa;
        min-height: 100vh;
    }

    /* Table row hover effect */
    .faculty-row {
        transition: background-color 0.15s ease-in-out;
    }

    .faculty-row:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.03);
    }
</style>
@endpush

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var successToastEl = document.getElementById('successToast');
        if(successToastEl){
            var successToast = new bootstrap.Toast(successToastEl);
            successToast.show();
        }

        var errorToastEl = document.getElementById('errorToast');
        if(errorToastEl){
            var errorToast = new bootstrap.Toast(errorToastEl);
            errorToast.show();
        }
    });
</script>
@endsection
