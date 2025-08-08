@extends('layouts.app')

@section('title', 'Edit Team')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Edit Team</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('teams.update', $team->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Team Name</label>
                <input type="text" name="name" value="{{ $team->name }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="lead_name" class="form-label">Team Lead</label>
                <input type="text" name="lead_name" value="{{ $team->lead_name }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" rows="3" class="form-control">{{ $team->description }}</textarea>
            </div>

            <button type="submit" class="btn btn-success">Update</button>
            <a href="{{ route('teams.index') }}" class="btn btn-secondary">Go Back</a>
        </form>
    </div>
</div>
@endsection
