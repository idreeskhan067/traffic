@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2>Edit Shift</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('shifts.update', $shift->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Shift Name</label>
            <input type="text" name="name" class="form-control" value="{{ $shift->name }}" required>
        </div>

        <div class="mb-3">
            <label for="start_time" class="form-label">Start Time</label>
            <input type="time" name="start_time" class="form-control" value="{{ $shift->start_time }}" required>
        </div>

        <div class="mb-3">
            <label for="end_time" class="form-label">End Time</label>
            <input type="time" name="end_time" class="form-control" value="{{ $shift->end_time }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Update Shift</button>
        <a href="{{ route('shifts.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
