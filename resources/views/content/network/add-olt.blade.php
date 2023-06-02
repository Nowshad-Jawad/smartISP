@extends('layouts/layoutMaster')


@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Network/</span> Add OLT</h4>

<!-- Basic Layout -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{route('network-store-olt')}}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="name">OLT Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="OLT Name" />
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
            <div class="mb-3">
                <label class="form-label" for="sub_zone_id">Sub-Zone</label>
                <select id="sub_zone_id" name="sub_zone_id" class="select2 form-select">
                    <option value="">Please Select One</option>
                    @foreach($sub_zones as $sub_zone)
                        <option value="{{$sub_zone->id}}">{{$sub_zone->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" for="type">Type</label>
                <select id="type" name="type" class="select2 form-select">
                    <option value="">Please Select One</option>
                    <option value="EPON">EPON</option>
                    <option value="GPON">GPON</option>
                    <option value="XGPON">XGPON</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" for="pon">No Of PON Port</label>
                <input type="text" class="form-control" id="pon" name="pon" placeholder="No Of PON Port" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="management_ip">Management IP</label>
                <input type="text" class="form-control" id="management_ip" name="management_ip" placeholder="Management IP" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="total_onu">Total ONU</label>
                <input type="text" class="form-control" id="total_onu" name="total_onu" placeholder="Total ONU" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="vlan_id">Management VLAN ID</label>
                <input type="text" class="form-control" id="vlan_id" name="vlan_id" placeholder="Management VLAN ID" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="vlan_ip">Management VLAN IP</label>
                <input type="text" class="form-control" id="vlan_ip" name="vlan_ip" placeholder="Management VLAN IP" />
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>
@endsection