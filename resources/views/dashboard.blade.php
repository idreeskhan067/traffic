@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="ibox">
            <div class="ibox-title">On-Duty Wardens</div>
            <div class="ibox-content">23 currently active</div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="ibox">
            <div class="ibox-title">Reported Congestions</div>
            <div class="ibox-content">5 areas need response</div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="ibox">
            <div class="ibox-title">Pending Dispatches</div>
            <div class="ibox-content">2 teams on hold</div>
        </div>
    </div>
</div>
@endsection
