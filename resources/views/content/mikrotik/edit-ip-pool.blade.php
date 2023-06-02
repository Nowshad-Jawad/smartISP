@extends('layouts/layoutMaster')

@section('content')
<div class="">
    <div class="">
      <div class=" p-3 p-md-5">
        <div class="">
          <div class="text-center mb-4">
            <h3 class="mb-2">Edit Ip Pool</h3>
          </div>
          <form action="{{route('mikrotik-update-ip-pool', $ip->id)}}" class="row g-3" method="POST">
            @csrf
            @method('PUT')
            <div class="col-12">
                <label class="form-label" for="mikrotik_id">Mikrotik</label>
                <select id="mikrotik_id" name="mikrotik_id" class="select2 form-select">
                    <option value="">Please Select One</option>
                    @foreach($mikrotiks as $mikrotik)
                        <option value="{{$mikrotik->id}}" @if($ip->mikrotik_id == $mikrotik->id) selected @endif>{{$mikrotik->identity}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                 <label class="form-label w-100" for="name">Name</label>
                <input id="name" name="name" class="form-control" type="text" value="{{$ip->name}}" />
            </div>
            <div class="col-12">
                <label class="form-label w-100" for="subnet">Subnet</label>
                <input id="subnet" name="subnet" class="form-control" type="text" value="{{$ip->subnet}}"/>
            </div>
            <div class="col-12">
                <label class="form-label w-100" for="start_ip">Start IP</label>
                <input id="start_ip" name="start_ip" class="form-control" type="text" value="{{$ip->start_ip}}"/>
            </div>
            <div class="col-12">
                <label class="form-label w-100" for="end_ip">End IP</label>
                <input id="end_ip" name="end_ip" class="form-control" type="text" value="{{$ip->end_ip}}"/>
            </div>
            <div class="col-12">
                <label class="form-label w-100" for="total_number_of_ip">Total Number Of IP</label>
                <input id="total_number_of_ip" name="total_number_of_ip" class="form-control" type="text" value="{{$ip->total_number_of_ip}}"/>
            </div>
            <div class="row mt-3">
                <div class="col-4">
                    <label class="form-label" for="public_ip">Public IP</label>
                    <select id="public_ip" name="public_ip" class="select2 form-select" onchange="toggleChargeableField()">
                        <option value="no" @if($ip->public_ip == 'no' || $ip->public_ip == null) selected @endif>No</option>
                        <option value="yes" @if($ip->public_ip == 'yes') selected @endif>Yes</option>
                    </select>
                </div>
                <div class="col-4">
                    <label class="form-label" for="chargeable_ip">Chargeable IP</label>
                    <select id="chargeable_ip" name="chargeable_ip" class="select2 form-select" onchange="toggleChargeField()">
                        <option value="0" @if($ip->is_ip_charge == '0' || $ip->is_ip_charge == null) selected @endif>No</option>
                        <option value="1" @if($ip->is_ip_charge == '1') selected @endif>Yes</option>
                    </select>
                </div>
                <div class="col-4">
                    <label class="form-label" for="charge">Charge</label>
                    <input id="charge" name="charge" class="form-control" type="text"  value="{{$ip->public_ip_charge}}" />
                </div>
            </div>
            <div class="col-12 text-center">
              <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
              <button type="reset" class="btn btn-label-secondary btn-reset">Cancel</button>
            </div>

          </form>
  
          
        </div>
      </div>
    </div>
  </div>

  @endsection

  <script>
    function toggleChargeableField(){
        let public_ip = document.getElementById('public_ip');
        if(public_ip.value == 'yes'){
            document.getElementById('chargeable_ip').disabled = false;
        }
        else{
            document.getElementById('chargeable_ip').disabled = true;
        }
    }
    
    function toggleChargeField(){
        let chargeable_ip = document.getElementById('chargeable_ip');
        if(chargeable_ip.value == '1'){
            document.getElementById('charge').disabled = false;
        }
        else{
            document.getElementById('charge').disabled = true;
        }
    }
  </script>
  