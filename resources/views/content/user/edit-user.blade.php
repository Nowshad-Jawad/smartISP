@extends('layouts/layoutMaster')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">User/</span> Edit User</h4>

<!-- Basic Layout -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{route('user-update-customer', $user->id)}}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label" for="name">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Full Name" value="{{$user->full_name}}" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="gender">Gender</label>
                <select id="gender" name="gender" class="select2 form-select">
                    <option value="">Please Select One</option>
                    <option value="Male" @if($user->gender == 'Male') selected @endif>Male</option>
                    <option value="Female" @if($user->gender == 'Female') selected @endif>Female</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" for="national_id">National ID</label>
                <input type="text" class="form-control" id="national_id" name="national_id" placeholder="National Id" value="{{$user->national_id}}" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="{{$user->email}}" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="phone">Phone No</label>
                <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone No" value="{{$user->phone}}" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="dob">Date Of Birth</label>
                <input type="date" class="form-control" id="dob" name="dob" placeholder="Date Of Birth" value="{{$user->date_of_birth}}" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="f_name">Father Name</label>
                <input type="text" class="form-control" id="f_name" name="f_name" placeholder="Father Name" value="{{$user->father_name}}" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="m_name">Mother Name</label>
                <input type="text" class="form-control" id="m_name" name="m_name" placeholder="Mother Name" value="{{$user->mother_name}}" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="address">Address</label>
                <input type="text" class="form-control" id="address" name="address" placeholder="Address" value="{{$user->address}}" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="zone_id">Zone</label>
                <select id="zone_id" name="zone_id" class="select2 form-select">
                    <option value="">Please Select One</option>
                    @foreach($zones as $zone)
                        <option value="{{$zone->id}}" @if($user->zone_id == $zone->id) selected @endif>{{$zone->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" for="reg_date">Registration Date</label>
                <input type="text" class="form-control" id="reg_date" name="reg_date" placeholder="Registration Date" value="{{$user->registration_date}}" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="conn_date">Connection Date</label>
                <input type="datetime-local" class="form-control" id="conn_date" name="conn_date" placeholder="Connection Date" value="{{$user->connection_date}}" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="package_id">Package</label>
                <select id="package_id" name="package_id" class="select2 form-select" onchange="addPriceToBillField()">
                    <option value="">Please Select One</option>
                    @foreach($packages as $package)
                        <option value="{{$package->id}}" @if($user->package_id == $package->id) selected @endif>{{$package->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" for="bill">Bill</label>
                <input type="text" class="form-control" id="bill" name="bill" placeholder="Bill" readonly value="{{$user->bill}}" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="discount">Discount</label>
                <input type="text" class="form-control" id="discount" name="discount" placeholder="Discount" value="{{$user->discount}}" />
            </div>
            <hr>
            <h4 class="fw-bold py-3 mb-4">Add Mikrotik Credentials</h4>
            <div class="mb-3">
                <label class="form-label" for="mikrotik_id">Mikrotik</label>
                <select id="mikrotik_id" name="mikrotik_id" class="select2 form-select">
                    <option value="">Please Select One</option>
                    @foreach($mikrotiks as $mikrotik)
                        <option value="{{$mikrotik->id}}" @if($user->mikrotik_id == $mikrotik->id) selected @endif>{{$mikrotik->identity}}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" for="username">User Name</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="User Name" value="{{$user->username}}"/>
            </div>
            <div class="mb-3">
                <label class="form-label" for="password">Password</label>
                <input type="text" class="form-control" id="password" name="password" placeholder="Pssword" value="{{$user->password}}" />
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>
@endsection

<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js" integrity="sha512-uMtXmF28A2Ab/JJO2t/vYhlaa/3ahUOgj1Zf27M5rOo8/+fcTUVH0/E0ll68njmjrLqOBjXM3V9NiPFL5ywWPQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    window.addEventListener("load", function(){
        let reg_date = document.getElementById('reg_date');
        if(reg_date.value != null){
            console.log('date present')
        }
        else{
            const timeElapsed = Date.now();
            const today = new Date(timeElapsed);
            
            reg_date.value = today.toLocaleDateString();
        }
        
    });

    function addPriceToBillField(){
        let package_id = document.getElementById('package_id').value;
        axios.get(`/user/get-package-details/${package_id}`).then((resp) => {
            if(resp.status == 200){
                let bill_field = document.getElementById('bill')
                bill_field.value = resp.data.bill;
            }
        })
    }
</script>