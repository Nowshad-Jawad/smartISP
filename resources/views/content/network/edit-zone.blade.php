@extends('layouts/layoutMaster')


@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Network/</span> Edit Zone</h4>

<!-- Basic Layout -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{route('network-update-zone', $zone->id)}}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label" for="name">Zone Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Zone Name" value="{{$zone->name}}" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="abbr">Zone Abbreviation</label>
                <input type="text" class="form-control" id="abbr" name="abbr" placeholder="Zone Abbreviation" value="{{$zone->abbreviation}}" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="status">Status</label>
                <select id="status" name="status" class="select2 form-select">
                    <option value="on" @if($zone->status == true) selected @endif>On</option>
                    <option value="off" @if($zone->status == false) selected @endif>Off</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>
@endsection