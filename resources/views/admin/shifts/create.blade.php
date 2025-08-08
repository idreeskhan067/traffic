@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2>Create New Shift</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('shifts.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Shift Name</label>
            <input type="text" name="name" class="form-control" required placeholder="e.g. Night Shift">
        </div>

        <div class="mb-3">
            <label for="start_time" class="form-label">Start Time</label>
            <input type="time" name="start_time" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="end_time" class="form-label">End Time</label>
            <input type="time" name="end_time" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Create Shift</button>
        <a href="{{ route('shifts.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
