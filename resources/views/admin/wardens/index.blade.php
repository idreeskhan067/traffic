@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Wardens</h1>
    <a href="{{ route('wardens.create') }}" class="btn btn-primary mb-3">Add Warden</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th><th>Email</th><th>Status</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($wardens as $warden)
            <tr>
                <td>{{ $warden->name }}</td>
                <td>{{ $warden->email }}</td>
                <td>{{ ucfirst($warden->status) }}</td>
                <td><!-- You can add edit/delete later --></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $wardens->links() }}
</div>
@endsection
