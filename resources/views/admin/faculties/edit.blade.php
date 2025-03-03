@extends('layouts.app')

@section('content')
<x-navigation>
    <div class="dashboard-content">
        <h5>Edit Faculty</h5>

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

        <form
            action="{{ route('admin.faculties.update', $faculty) }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf
            @method('PUT')

            <!-- Name -->
            <div class="mb-3">
                <label for="name" class="form-label">Faculty Name</label>
                <input
                    type="text"
                    class="form-control @error('name') is-invalid @enderror"
                    id="name"
                    name="name"
                    value="{{ old('name', $faculty->name) }}"
                    required
                >
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Code -->
            <div class="mb-3">
                <label for="code" class="form-label">Faculty Code</label>
                <input
                    type="text"
                    class="form-control @error('code') is-invalid @enderror"
                    id="code"
                    name="code"
                    value="{{ old('code', $faculty->code) }}"
                    required
                >
                @error('code')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-3">
                <label for="description" class="form-label">Description (optional)</label>
                <textarea
                    class="form-control @error('description') is-invalid @enderror"
                    id="description"
                    name="description"
                    rows="4"
                >{{ old('description', $faculty->description) }}</textarea>
                @error('description')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Current Image Preview -->
            @if($faculty->image_url)
                <div class="mb-3">
                    <label class="form-label">Current Image</label><br>
                    <img
                        src="{{ asset('storage/' . $faculty->image_url) }}"
                        alt="{{ $faculty->name }}"
                        style="max-height: 150px; object-fit: cover;"
                    >
                </div>
            @endif

            <!-- Image Upload -->
            <div class="mb-3">
                <label for="image" class="form-label">Change Image (optional)</label>
                <input
                    type="file"
                    class="form-control @error('image') is-invalid @enderror"
                    id="image"
                    name="image"
                    accept="image/*"
                >
                @error('image')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <button type="submit" class="btn btn-primary">Update Faculty</button>
            <a href="{{ route('admin.faculties.index') }}" class="btn btn-secondary">
                Cancel
            </a>
        </form>
    </div>
</x-navigation>
@endsection
