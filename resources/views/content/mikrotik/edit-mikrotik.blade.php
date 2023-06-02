@extends('layouts/layoutMaster')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Mikrotik/</span> Edit Mikrotik</h4>

<!-- Basic Layout -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{route('mikrotik-update-mikrotik', $mikrotik->id)}}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
            <label class="form-label" for="identity">Mikrotik Identity</label>
            <input type="text" class="form-control" id="identity" name="identity" placeholder="Mikrotik Identity" value="{{$mikrotik->identity}}" />
            </div>
            <div class="mb-3">
            <label class="form-label" for="host">Mikrotik IP</label>
            <input type="text" class="form-control" id="host"  name="host" placeholder="Mikrotik IP" value="{{$mikrotik->host}}"/>
            </div>
            <div class="mb-3">
            <label class="form-label" for="username">API User Name</label>
            <input type="text" class="form-control" id="username"  name="username" placeholder="API User Name" value="{{$mikrotik->username}}"/>
            </div>
            <div class="mb-3">
            <label class="form-label" for="password">API User Password</label>
            <input type="text" class="form-control" id="password"  name="password" placeholder="API User Password" value="{{$mikrotik->password}}"/>
            </div>
            <div class="mb-3">
            <label class="form-label" for="port">API Port</label>
            <input type="text" class="form-control" id="port"  name="port" placeholder="API Port" value="{{$mikrotik->port}}"/>
            </div>
            <div class="mb-3">
            <label class="form-label" for="sitename">Site Name</label>
            <input type="text" class="form-control" id="sitename"  name="sitename" placeholder="Site Name" value="{{$mikrotik->sitename}}"/>
            </div>
            <div class="mb-3">
            <label class="form-label" for="address">Address</label>
            <input type="text" class="form-control" id="address"  name="address" placeholder="Address" value="{{$mikrotik->address}}"/>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>
@endsection
