@extends('layouts.app')

@section('content')
<x-navigation>
    <div class="dashboard-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-semibold mb-1">{{ isset($lecture) ? 'Edit Lecture' : 'Schedule New Lecture' }}</h4>
                <p class="text-muted small mb-0">
                    {{ isset($lecture) ? 'Update lecture information' : 'Create a new lecture schedule' }}
                </p>
            </div>
            <div>
                <a href="{{ route('admin.lectures.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back to Lectures
                </a>
            </div>
        </div>

        <!-- Form Card -->
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ isset($lecture) ? route('admin.lectures.update', $lecture) : route('admin.lectures.store') }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @if(isset($lecture))
                        @method('PUT')
                    @endif

                    <div class="row">
                        <!-- Course & Lecturer Selection -->
                        <div class="col-md-6">
                            <h6 class="form-section-title">Course Information</h6>

                            <div class="mb-3">
                                <label for="course_id" class="form-label required">Course</label>
                                <select id="course_id" name="course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                                    <option value="">-- Select Course --</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ (old('course_id', $lecture->course_id ?? null) == $course->id) ? 'selected' : '' }}>
                                            {{ $course->code }} - {{ $course->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="lecturer_id" class="form-label required">Lecturer</label>
                                <select id="lecturer_id" name="lecturer_id" class="form-select @error('lecturer_id') is-invalid @enderror" required>
                                    <option value="">-- Select Lecturer --</option>
                                    @foreach($lecturers as $lecturer)
                                        <option value="{{ $lecturer->id }}" {{ (old('lecturer_id', $lecture->lecturer_id ?? null) == $lecturer->id) ? 'selected' : '' }}>
                                            {{ $lecturer->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('lecturer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="lecture_schedule_id" class="form-label">Based on Schedule (Optional)</label>
                                <select id="lecture_schedule_id" name="lecture_schedule_id" class="form-select @error('lecture_schedule_id') is-invalid @enderror">
                                    <option value="">-- Select Schedule Template (Optional) --</option>
                                    @foreach($lectureSchedules as $schedule)
                                        <option value="{{ $schedule->id }}" {{ (old('lecture_schedule_id', $lecture->lecture_schedule_id ?? null) == $schedule->id) ? 'selected' : '' }}>
                                            {{ $schedule->course->code }} - {{ $schedule->day_name }} {{ $schedule->start_time_formatted }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('lecture_schedule_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">If selected, some fields will be auto-filled based on the schedule template.</div>
                            </div>

                            <div class="mb-3">
                                <label for="room" class="form-label required">Room</label>
                                <input type="text" class="form-control @error('room') is-invalid @enderror"
                                       id="room" name="room"
                                       value="{{ old('room', $lecture->room ?? '') }}" required>
                                @error('room')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Schedule & Attendance -->
                        <div class="col-md-6">
                            <h6 class="form-section-title">Schedule & Attendance</h6>

                            <div class="mb-3">
                                <label for="start_time" class="form-label required">Start Date & Time</label>
                                <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror"
                                       id="start_time" name="start_time"
                                       value="{{ old('start_time', isset($lecture) ? $lecture->start_time->format('Y-m-d\TH:i') : '') }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="end_time" class="form-label required">End Date & Time</label>
                                <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror"
                                       id="end_time" name="end_time"
                                       value="{{ old('end_time', isset($lecture) ? $lecture->end_time->format('Y-m-d\TH:i') : '') }}" required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="expected_students" class="form-label required">Expected Students</label>
                                <input type="number" class="form-control @error('expected_students') is-invalid @enderror"
                                       id="expected_students" name="expected_students" min="0"
                                       value="{{ old('expected_students', $lecture->expected_students ?? 0) }}" required>
                                @error('expected_students')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input @error('is_completed') is-invalid @enderror"
                                           id="is_completed" name="is_completed"
                                           {{ old('is_completed', $lecture->is_completed ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_completed">Mark as Completed</label>
                                </div>
                                @error('is_completed')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Image Upload -->
                        <div class="col-12 mt-3">
                            <h6 class="form-section-title">Lecture Image</h6>

                            <div class="mb-3">
                                <label for="image" class="form-label">Upload Image (Optional)</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror"
                                       id="image" name="image" accept="image/*">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if(isset($lecture) && $lecture->image_url)
                                    <div class="form-text">Current image: {{ basename($lecture->image_url) }}</div>
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $lecture->image_url) }}"
                                             alt="Current Image"
                                             class="img-thumbnail"
                                             style="max-height: 100px;">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            {{ isset($lecture) ? 'Update Lecture' : 'Schedule Lecture' }}
                        </button>
                        <a href="{{ route('admin.lectures.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-navigation>

@push('styles')
<style>
    /* Dashboard content styling */
    .dashboard-content {
        padding: 1.5rem;
        background-color: #f8f9fa;
        min-height: 100vh;
    }

    /* Card styling */
    .card {
        border-radius: 0.5rem;
        overflow: hidden;
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    /* Form section title */
    .form-section-title {
        color: #0d6efd;
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e9ecef;
    }

    /* Required field label */
    .form-label.required::after {
        content: " *";
        color: #dc3545;
    }

    /* Form control focus */
    .form-control:focus,
    .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    /* Button styling */
    .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .btn-outline-secondary {
        color: #6c757d;
        border-color: #6c757d;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.25rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle lecture schedule selection to auto-fill certain fields
        const scheduleSelect = document.getElementById('lecture_schedule_id');
        if (scheduleSelect) {
            scheduleSelect.addEventListener('change', function() {
                if (this.value) {
                    // You would need to implement AJAX to get the schedule details and auto-fill
                    // This is a placeholder for that implementation
                    fetch(`/api/lecture-schedules/${this.value}`)
                        .then(response => response.json())
                        .then(data => {
                            // Auto-fill form fields based on schedule template
                            if (data) {
                                // Update course
                                const courseSelect = document.getElementById('course_id');
                                if (courseSelect && data.course_id) {
                                    courseSelect.value = data.course_id;
                                }

                                // Update lecturer
                                const lecturerSelect = document.getElementById('lecturer_id');
                                if (lecturerSelect && data.lecturer_id) {
                                    lecturerSelect.value = data.lecturer_id;
                                }

                                // Update room
                                const roomInput = document.getElementById('room');
                                if (roomInput && data.room) {
                                    roomInput.value = data.room;
                                }

                                // Update expected students
                                const studentsInput = document.getElementById('expected_students');
                                if (studentsInput && data.expected_students) {
                                    studentsInput.value = data.expected_students;
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching schedule data:', error);
                        });
                }
            });
        }

        // Date time validation
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');

        function validateTimes() {
            const startTime = new Date(startTimeInput.value);
            const endTime = new Date(endTimeInput.value);

            if (startTime >= endTime) {
                endTimeInput.setCustomValidity('End time must be after start time');
            } else {
                endTimeInput.setCustomValidity('');
            }
        }

        if (startTimeInput && endTimeInput) {
            startTimeInput.addEventListener('change', validateTimes);
            endTimeInput.addEventListener('change', validateTimes);

            // Initial validation
            if (startTimeInput.value && endTimeInput.value) {
                validateTimes();
            }
        }

        // Image preview
        const imageInput = document.getElementById('image');
        if (imageInput) {
            imageInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();

                    // Find or create image preview element
                    let previewContainer = document.querySelector('.image-preview-container');
                    if (!previewContainer) {
                        previewContainer = document.createElement('div');
                        previewContainer.className = 'image-preview-container mt-2';
                        this.parentNode.appendChild(previewContainer);
                    }

                    reader.onload = function(e) {
                        previewContainer.innerHTML = `
                            <p>New image preview:</p>
                            <img src="${e.target.result}" class="img-thumbnail" style="max-height: 100px;">
                        `;
                    }

                    reader.readAsDataURL(file);
                }
            });
        }
    });
</script>
@endpush
@endsection
