@extends('layouts.app')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Warden Details</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('admin.wardens.edit', $warden->id) }}" class="btn btn-warning btn-sm">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.wardens.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fa fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="text-center">
                                <img src="{{ $warden->avatar ?? asset('inspinia/img/profile_small.jpg') }}" 
                                     class="rounded-circle img-fluid" width="150" height="150" alt="Avatar">
                                <h3 class="mt-3">{{ $warden->name }}</h3>
                                <span class="badge badge-{{ $warden->status === 'on-duty' ? 'success' : 'secondary' }} badge-lg">
                                    {{ ucfirst(str_replace('-', ' ', $warden->status)) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4>Personal Information</h4>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="150">Name:</th>
                                            <td>{{ $warden->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email:</th>
                                            <td>{{ $warden->email }}</td>
                                        </tr>
                                        <tr>
                                            <th>Phone:</th>
                                            <td>{{ $warden->phone ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Badge Number:</th>
                                            <td>{{ $warden->badge_number ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h4>Work Information</h4>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="150">Status:</th>
                                            <td>
                                                <span class="badge badge-{{ $warden->status === 'on-duty' ? 'success' : 'secondary' }}">
                                                    {{ ucfirst(str_replace('-', ' ', $warden->status)) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Last Active:</th>
                                            <td>
                                                @if($warden->last_active_at)
                                                    {{ $warden->last_active_at->format('M d, Y g:i A') }}
                                                @else
                                                    Never
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Joined:</th>
                                            <td>{{ $warden->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <div class="text-center">
                                <form action="{{ route('admin.wardens.toggleStatus', $warden->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn {{ $warden->status === 'on-duty' ? 'btn-warning' : 'btn-success' }}">
                                        <i class="fa fa-{{ $warden->status === 'on-duty' ? 'pause' : 'play' }}"></i>
                                        {{ $warden->status === 'on-duty' ? 'Set Off-Duty' : 'Set On-Duty' }}
                                    </button>
                                </form>
                                
                                <form action="{{ route('admin.wardens.destroy', $warden->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger ml-2" 
                                            onclick="return confirm('Are you sure you want to delete this warden?')">
                                        <i class="fa fa-trash"></i> Delete Warden
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection