@extends('layouts.app')

@section('content')
<x-navigation>
    <div class="container py-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Header Card -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                                <i class="fas fa-edit text-primary fs-4"></i>
                            </div>
                            <div>
                                <h4 class="mb-0 fw-bold">Edit Program</h4>
                                <p class="text-muted mb-0">Update program details and information</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-exclamation-circle fs-4"></i>
                            </div>
                            <div>
                                <strong>Error!</strong> {{ session('error') }}
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Form Card -->
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-body p-4">
                        <form action="{{ route('admin.programs.update', $program) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                            @csrf
                            @method('PUT')

                            <div class="row mb-4">
                                <div class="col-lg-6">
                                    <!-- Basic Information Section -->
                                    <h5 class="border-bottom pb-2 mb-4">
                                        <i class="fas fa-info-circle me-2 text-primary"></i>Basic Information
                                    </h5>

                                    <!-- Department -->
                                    <div class="mb-4">
                                        <label for="department_id" class="form-label fw-medium">Department</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-university text-muted"></i>
                                            </span>
                                            <select name="department_id" id="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                                                <option value="">Select Department</option>
                                                @foreach($departments as $department)
                                                    <option value="{{ $department->id }}" {{ (old('department_id', $program->department_id) == $department->id) ? 'selected' : '' }}>
                                                        {{ $department->name }} ({{ $department->code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('department_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-text">Select the department this program belongs to</div>
                                    </div>

                                    <!-- Name -->
                                    <div class="mb-4">
                                        <label for="name" class="form-label fw-medium">Program Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-graduation-cap text-muted"></i>
                                            </span>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name', $program->name) }}"
                                                placeholder="Enter program name" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-text">Full name of the academic program</div>
                                    </div>

                                    <!-- Code -->
                                    <div class="mb-4">
                                        <label for="code" class="form-label fw-medium">Program Code</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-code text-muted"></i>
                                            </span>
                                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                                id="code" name="code" value="{{ old('code', $program->code) }}"
                                                placeholder="e.g. BSC-CS" required>
                                            @error('code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-text">Unique identifier code for the program</div>
                                    </div>

                                    <!-- Duration -->
                                    <div class="mb-4">
                                        <label for="duration_years" class="form-label fw-medium">Duration (years)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-calendar-alt text-muted"></i>
                                            </span>
                                            <input type="number" class="form-control @error('duration_years') is-invalid @enderror"
                                                id="duration_years" name="duration_years"
                                                value="{{ old('duration_years', $program->duration_years) }}"
                                                placeholder="Enter duration" required min="1" max="10">
                                            @error('duration_years')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-text">Length of the program in years</div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <!-- Media Section -->
                                    <h5 class="border-bottom pb-2 mb-4">
                                        <i class="fas fa-image me-2 text-primary"></i>Media & Description
                                    </h5>

                                    <!-- Description -->
                                    <div class="mb-4">
                                        <label for="description" class="form-label fw-medium">Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror"
                                            id="description" name="description" rows="4"
                                            placeholder="Describe the program curriculum, objectives, and outcomes">{{ old('description', $program->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Detailed information about the program (optional)</div>
                                    </div>

                                    <!-- Program Image -->
                                    <div class="card bg-light border-0 mb-4">
                                        <div class="card-body">
                                            <h6 class="mb-3">Program Image</h6>

                                            @if($program->image_url)
                                                <div class="text-center mb-3 position-relative program-image-preview">
                                                    <img src="{{ asset('storage/' . $program->image_url) }}"
                                                        alt="{{ $program->name }}"
                                                        class="img-thumbnail"
                                                        style="max-height: 200px; object-fit: cover;">
                                                    <div class="image-overlay">
                                                        <span>Current Image</span>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-center mb-3">
                                                    <div class="image-placeholder">
                                                        <i class="fas fa-image"></i>
                                                        <p class="mb-0">No image uploaded</p>
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="input-group mb-2">
                                                <span class="input-group-text bg-white">
                                                    <i class="fas fa-upload text-muted"></i>
                                                </span>
                                                <input type="file" class="form-control @error('image') is-invalid @enderror"
                                                    id="image" name="image" accept="image/*">
                                                @error('image')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-text">Upload a new image to replace the current one (optional)</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="border-top pt-4 d-flex justify-content-between">
                                <a href="{{ route('admin.programs.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Program
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-navigation>
@endsection

@push('styles')
<style>
    /* Image preview styling */
    .program-image-preview {
        overflow: hidden;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: transform 0.3s;
    }

    .program-image-preview:hover {
        transform: scale(1.02);
    }

    .program-image-preview .image-overlay {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(0,0,0,0.6);
        color: white;
        font-size: 0.8rem;
        padding: 4px 8px;
        border-radius: 4px;
    }

    .image-placeholder {
        background-color: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 40px;
        text-align: center;
        color: #adb5bd;
    }

    .image-placeholder i {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
    }

    /* Form styling */
    .form-control:focus, .form-select:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .input-group-text {
        border: none;
    }

    /* Card hover effect */
    .card {
        transition: transform 0.2s;
        border-radius: 0.5rem;
    }

    .card:hover {
        transform: translateY(-2px);
    }

    /* Bottom margin for the container */
    .container {
        margin-bottom: 5rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Preview uploaded image
        const imageInput = document.getElementById('image');
        if (imageInput) {
            imageInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.addEventListener('load', function() {
                        // Create or update preview
                        let previewContainer = document.querySelector('.program-image-preview');

                        if (!previewContainer) {
                            // If no preview container exists, create one
                            const placeHolder = document.querySelector('.image-placeholder');
                            if (placeHolder) {
                                placeHolder.parentNode.innerHTML = `
                                    <div class="text-center mb-3 position-relative program-image-preview">
                                        <img src="${reader.result}" class="img-thumbnail" style="max-height: 200px; object-fit: cover;">
                                        <div class="image-overlay">
                                            <span>New Image</span>
                                        </div>
                                    </div>
                                `;
                            }
                        } else {
                            // Update existing preview
                            const img = previewContainer.querySelector('img');
                            if (img) {
                                img.src = reader.result;
                            }

                            // Update overlay text
                            const overlay = previewContainer.querySelector('.image-overlay span');
                            if (overlay) {
                                overlay.textContent = 'New Image';
                            }
                        }
                    });
                    reader.readAsDataURL(file);
                }
            });
        }

        // Bootstrap form validation
        const forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    });
</script>
@endpush
