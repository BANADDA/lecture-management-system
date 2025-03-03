@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 mt-5">
            <div class="card">
                <div class="card-header bg-danger text-white">Unauthorized Access</div>
                <div class="card-body text-center">
                    <h1 class="text-danger">Access Denied</h1>
                    <p>You do not have permission to access this page.</p>

                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">Go to Admin Dashboard</a>
                    @elseif(auth()->user()->isLecturer())
                        <a href="{{ route('lecturer.dashboard') }}" class="btn btn-primary">Go to Lecturer Dashboard</a>
                    @elseif(auth()->user()->isStudent())
                        <a href="{{ route('student.dashboard') }}" class="btn btn-primary">Go to Student Dashboard</a>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
