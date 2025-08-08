@extends('layouts.app')

@section('title', 'Create Team')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Create New Team</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('teams.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Team Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="lead_name" class="form-label">Team Lead</label>
                <input type="text" name="lead_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" rows="3" class="form-control"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Create</button>
            <a href="{{ route('teams.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
