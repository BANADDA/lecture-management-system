@extends('layouts.app')

@section('content')
<x-navigation>
    <div class="container-fluid py-4">
        <div class="row">
            {{-- Existing Lecturer Details Card --}}
            <div class="col-12 col-lg-4">
                <div class="card shadow-lg border-0 rounded-lg mb-4">
                    <div class="card-header bg-gradient-primary text-white">
                        <h3 class="text-center font-weight-light my-2">
                            <i class="fas fa-user-tie mr-2"></i>Lecturer Profile
                        </h3>
                    </div>
                    <div class="card-body">
                        {{-- Profile Photo Section --}}
                        @if($lecturer->profile_photo)
                            <div class="text-center mb-4">
                                <img
                                    src="{{ asset('storage/' . $lecturer->profile_photo) }}"
                                    alt="{{ $lecturer->full_name }}"
                                    class="img-fluid rounded-circle shadow-lg"
                                    style="max-width: 250px; height: 250px; object-fit: cover;"
                                >
                            </div>
                        @endif

                        {{-- Lecturer Details --}}
                        <div class="mb-3">
                            <p class="mb-2">
                                <strong class="text-primary">
                                    <i class="fas fa-id-badge mr-2"></i>Staff ID:
                                </strong>
                                {{ $lecturer->staff_id }}
                            </p>
                            <p class="mb-2">
                                <strong class="text-primary">
                                    <i class="fas fa-user mr-2"></i>Full Name:
                                </strong>
                                {{ $lecturer->full_name }}
                            </p>
                            <p class="mb-2">
                                <strong class="text-primary">
                                    <i class="fas fa-phone mr-2"></i>Contact:
                                </strong>
                                {{ $lecturer->phone }}
                            </p>
                            <p class="mb-2">
                                <strong class="text-primary">
                                    <i class="fas fa-building mr-2"></i>Department:
                                </strong>
                                {{ $lecturer->department->name ?? 'Not Assigned' }}
                            </p>
                            <p class="mb-2">
                                <strong class="text-primary">
                                    <i class="fas fa-map-marker-alt mr-2"></i>Office Location:
                                </strong>
                                {{ $lecturer->office_location ?? 'Unspecified' }}
                            </p>
                            <p class="mb-2">
                                <strong class="text-primary">
                                    <i class="fas fa-check-circle mr-2"></i>Status:
                                </strong>
                                <span class="badge {{
                                    $lecturer->status === 'active'
                                        ? 'badge-success'
                                        : 'badge-warning'
                                }}">
                                    {{ ucfirst($lecturer->status) }}
                                </span>
                            </p>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="mt-4 text-center">
                            <a
                                href="{{ route('admin.lecturers.index') }}"
                                class="btn btn-secondary btn-lg mr-2"
                            >
                                <i class="fas fa-arrow-left mr-2"></i>Back to List
                            </a>
                            <a
                                href="{{ route('admin.lecturers.edit', $lecturer) }}"
                                class="btn btn-warning btn-lg"
                            >
                                <i class="fas fa-edit mr-2"></i>Edit Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Schedule and Availability Section --}}
            <div class="col-12 col-lg-8">
                {{-- Livewire or Vue component would be integrated here --}}
                <div id="lecturer-schedule-component"
                     data-lecturer="{{ json_encode($lecturer) }}">
                    {{-- React component will be mounted here --}}
                </div>
            </div>
        </div>
    </div>
</x-navigation>

@push('scripts')
<script src="{{ mix('js/lecturer-schedule.js') }}"></script>
@endpush
@endsection
