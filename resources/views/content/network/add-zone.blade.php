@extends('layouts/layoutMaster')


@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Network/</span> Add Zone</h4>

<!-- Basic Layout -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{route('network-store-zone')}}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="name">Zone Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Zone Name" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="abbr">Zone Abbreviation</label>
                <input type="text" class="form-control" id="abbr" name="abbr" placeholder="Zone Abbreviation" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="status">Status</label>
                <select id="status" name="status" class="select2 form-select">
                    <option value="on" selected>On</option>
                    <option value="off">Off</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>
@endsection