@extends('layouts/layoutMaster')


@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Network/</span> Add Sub-Zone</h4>

<!-- Basic Layout -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{route('network-store-sub-zone')}}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="name">Sub-Zone Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Sub-Zone Name" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="zone_id">Zone</label>
                <select id="zone_id" name="zone_id" class="select2 form-select">
                    <option value="">Please Select One</option>
                    @foreach($zones as $zone)
                        <option value="{{$zone->id}}">{{$zone->name}}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>
@endsection