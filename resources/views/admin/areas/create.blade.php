@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Assign New Area</h1>
        <a href="{{ route('admin.areas.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Areas
        </a>
    </div>
    
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Area Assignment Form</h5>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.areas.store') }}" method="POST">
                @csrf
                
                <div class="form-group mb-3">
                    <label for="warden_id">Assign To Warden</label>
                    <select name="warden_id" id="warden_id" class="form-control" required>
                        <option value="">-- Select Warden --</option>
                        @foreach($wardens as $warden)
                            <option value="{{ $warden->id }}">{{ $warden->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group mb-3">
                    <label for="name">Area Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                
                <div class="form-group mb-3">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>
                
                <div class="form-group mb-3">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                
                <div class="form-group mb-3">
                    <label for="boundaries">Area Boundaries (GeoJSON)</label>
                    <textarea name="boundaries" id="boundaries" class="form-control" rows="4">{{ old('boundaries') }}</textarea>
                    <small class="text-muted">Optional. Enter coordinates as JSON array.</small>
                </div>
                
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Assign Area</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection