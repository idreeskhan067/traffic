@extends('layouts.app')

@section('title', 'Add New Setting')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Add New Setting</h2>

    <form action="#" method="POST">
        @csrf

        <div class="form-group mb-3">
            <label for="key">Setting Key</label>
            <input type="text" name="key" class="form-control" placeholder="e.g., notify_shift_change" required>
        </div>

        <div class="form-group mb-3">
            <label for="value">Setting Value</label>
            <input type="text" name="value" class="form-control" placeholder="e.g., enabled" required>
        </div>

        <div class="form-group mb-4">
            <label for="description">Description</label>
            <textarea name="description" rows="3" class="form-control" placeholder="What does this setting control?"></textarea>
        </div>

        <button type="submit" class="btn btn-success">Save Setting</button>
        <a href="{{ route('settings.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
