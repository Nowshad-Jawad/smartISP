@extends('layouts/layoutMaster')


@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Network/</span> Edit Sub-Zone</h4>

<!-- Basic Layout -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{route('network-update-sub-zone', $sub_zone->id)}}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label" for="name">Sub-Zone Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Sub-Zone Name" value="{{$sub_zone->name}}" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="zone_id">Zone</label>
                <select id="zone_id" name="zone_id" class="select2 form-select">
                    <option value="">Please Select One</option>
                    @foreach($zones as $zone)
                        <option value="{{$zone->id}}" @if($sub_zone->zone_id == $zone->id) selected @endif>{{$zone->name}}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>
@endsection