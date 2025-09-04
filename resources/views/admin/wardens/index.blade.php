@extends('layouts.app')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>Wardens Management</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('admin.wardens.create') }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus"></i> Add New Warden
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Success!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Filter Options -->
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.wardens.index') }}" 
                                   class="btn btn-sm {{ !request()->has('filter') ? 'btn-primary' : 'btn-outline-primary' }}">
                                    All Wardens
                                </a>
                                <a href="{{ route('admin.wardens.index', ['filter' => 'on-duty']) }}" 
                                   class="btn btn-sm {{ request('filter') == 'on-duty' ? 'btn-success' : 'btn-outline-success' }}">
                                    On-Duty Only
                                </a>
                                <a href="{{ route('admin.wardens.index', ['filter' => 'off-duty']) }}" 
                                   class="btn btn-sm {{ request('filter') == 'off-duty' ? 'btn-secondary' : 'btn-outline-secondary' }}">
                                    Off-Duty Only
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <form action="{{ route('admin.wardens.index') }}" method="GET" class="d-flex w-100">
                                    <input type="text" name="search" class="form-control" placeholder="Search wardens..." value="{{ request('search') }}">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Last Active</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($wardens as $warden)
                                <tr>
                                    <td>{{ $warden->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $warden->avatar ?? asset('inspinia/img/profile_small.jpg') }}" 
                                                 class="rounded-circle me-2" width="30" height="30" alt="Avatar">
                                            <div>
                                                <strong>{{ $warden->name }}</strong>
                                                @if($warden->badge_number)
                                                    <br><small class="text-muted">Badge: {{ $warden->badge_number }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $warden->email }}</td>
                                    <td>{{ $warden->phone ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $warden->status === 'on-duty' ? 'success' : 'secondary' }}">
                                            <i class="fa fa-{{ $warden->status === 'on-duty' ? 'check-circle' : 'clock' }}"></i>
                                            {{ ucfirst(str_replace('-', ' ', $warden->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($warden->last_active_at)
                                            <small class="text-muted">
                                                {{ $warden->last_active_at->diffForHumans() }}
                                            </small>
                                        @else
                                            <span class="text-muted">Never</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <!-- View Button -->
                                            <a href="{{ route('admin.wardens.show', $warden->id) }}" 
                                               class="btn btn-sm btn-info" title="View Details">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            
                                            <!-- Edit Button -->
                                            <a href="{{ route('admin.wardens.edit', $warden->id) }}" 
                                               class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            
                                            <!-- Toggle Status Button -->
                                            <form action="{{ route('admin.wardens.toggleStatus', $warden->id) }}" 
                                                  method="POST" style="display:inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="btn btn-sm {{ $warden->status === 'on-duty' ? 'btn-success' : 'btn-secondary' }}"
                                                        title="{{ $warden->status === 'on-duty' ? 'Set Off-Duty' : 'Set On-Duty' }}"
                                                        onclick="return confirm('Are you sure you want to change this warden\'s status?')">
                                                    <i class="fa fa-{{ $warden->status === 'on-duty' ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                            
                                            <!-- Delete Button -->
                                            <form action="{{ route('admin.wardens.destroy', $warden->id) }}" 
                                                  method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this warden? This action cannot be undone.')">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fa fa-users fa-3x mb-3"></i>
                                            <h4>No Wardens Found</h4>
                                            <p>There are no wardens matching your criteria.</p>
                                            <a href="{{ route('admin.wardens.create') }}" class="btn btn-primary">
                                                <i class="fa fa-plus"></i> Add First Warden
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($wardens instanceof \Illuminate\Pagination\LengthAwarePaginator && $wardens->hasPages())
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="dataTables_info">
                                    Showing {{ $wardens->firstItem() }} to {{ $wardens->lastItem() }} of {{ $wardens->total() }} entries
                                </div>
                            </div>
                            <div class="col-sm-7">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {{ $wardens->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
    // Add confirmation for status toggle
    $('form[action*="toggleStatus"]').on('submit', function(e) {
        const form = this;
        const wardenName = $(this).closest('tr').find('td:nth-child(2) strong').text();
        const currentStatus = $(this).find('button').hasClass('btn-success') ? 'on-duty' : 'off-duty';
        const newStatus = currentStatus === 'on-duty' ? 'off-duty' : 'on-duty';
        
        if (!confirm(`Are you sure you want to set ${wardenName} to ${newStatus}?`)) {
            e.preventDefault();
        }
    });
    
    // Add confirmation for delete
    $('form[action*="destroy"]').on('submit', function(e) {
        const wardenName = $(this).closest('tr').find('td:nth-child(2) strong').text();
        
        if (!confirm(`Are you sure you want to delete ${wardenName}? This action cannot be undone.`)) {
            e.preventDefault();
        }
    });
});
</script>
@endpush
@endsection