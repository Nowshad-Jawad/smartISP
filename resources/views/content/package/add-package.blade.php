@extends('layouts/layoutMaster')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Package/</span> Add Package</h4>

<!-- Basic Layout -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{route('packages-store-package')}}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="mikrotik_id">Mikrotik</label>
                <select id="mikrotik_id" name="mikrotik_id" class="select2 form-select">
                    <option value="">Please Select One</option>
                    @foreach($mikrotiks as $mikrotik)
                        <option value="{{$mikrotik->id}}">{{$mikrotik->identity}}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" for="name">Package Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Package Name" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="synonym">Synonym</label>
                <input type="text" class="form-control" id="synonym"  name="synonym" placeholder="Synonym" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="ip_pool">IP Pool <span class="text-green-300">(Remote Address)</span></label>
                <select id="ip_pool" name="ip_pool" class="select2 form-select">
                    <option value="">Please Select One</option>
                    @foreach($ips as $ip)
                        <option value="{{$ip->id}}">{{$ip->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" for="price">Price</label>
                <input type="text" class="form-control" id="price"  name="price" placeholder="Price" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="manager_price">Manager Price</label>
                <input type="text" class="form-control" id="manager_price"  name="manager_price" placeholder="Manager Price" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="duration">Duration</label>
                <select id="duration" name="duration" class="select2 form-select">
                    <option value="">Select Duration</option>
                    <option value="Months">Months</option>
                    <option value="Weeks">Weeks</option>
                    <option value="Days">Days</option>
                    <option value="Hours">Hours</option>
                    <option value="Minutes">Minutes</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" for="period">Period</label>
                <input type="text" class="form-control" id="period"  name="period" placeholder="Period" />
            </div>
            <div class="mb-3 row">
                <div class="col-3">
                    <input type="checkbox" name="fixed_expiry" id="fixed_expiry" onchange="toggleFixedExpiryDateField()">
                    <label for="fixed_expiry">Fixed Expiry Day</label>
                </div>
                <div class="col-9" id="expiry_date_field" style="display:none">
                    <label for="fixed_expiry_day" class="col-2 col-form-label">Enter Fixed Expiry Day</label>
                    <div class="col-10">
                      <input class="form-control" type="datetime-local" id="fixed_expiry_day" name="fixed_expiry_day"/>
                    </div>
                </div>
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

<script>
    function toggleFixedExpiryDateField(){
        let expiry_date = document.getElementById('fixed_expiry');
        if(expiry_date.checked == false){
            document.getElementById('expiry_date_field').style.display = 'none'
        }
        else{
            document.getElementById('expiry_date_field').style.display = 'block'
        }
    }
</script>