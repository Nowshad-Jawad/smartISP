@extends('layouts/layoutMaster')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css"/>

@section('content')

<div class="row g-4 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span>Session</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">21,459</h4>
              <span class="text-success">(+29%)</span>
            </div>
            <span>Total Users</span>
          </div>
          <span class="badge bg-label-primary rounded p-2">
            <i class="ti ti-user ti-sm"></i>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span>Paid Users</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">4,567</h4>
              <span class="text-success">(+18%)</span>
            </div>
            <span>Last week analytics </span>
          </div>
          <span class="badge bg-label-danger rounded p-2">
            <i class="ti ti-user-plus ti-sm"></i>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span>Active Users</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">19,860</h4>
              <span class="text-danger">(-14%)</span>
            </div>
            <span>Last week analytics</span>
          </div>
          <span class="badge bg-label-success rounded p-2">
            <i class="ti ti-user-check ti-sm"></i>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span>Pending Users</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">237</h4>
              <span class="text-success">(+42%)</span>
            </div>
            <span>Last week analytics</span>
          </div>
          <span class="badge bg-label-warning rounded p-2">
            <i class="ti ti-user-exclamation ti-sm"></i>
          </span>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Users List Table -->
<div class="card">
  <div class="card-header border-bottom">
    <h5 class="card-title mb-3">Search Filter</h5>
    <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
      <div class="col-md-4 user_role"></div>
      <div class="col-md-4 user_plan"></div>
      <div class="col-md-4 user_status"></div>
    </div>
  </div>
  <div class="card-datatable table-responsive">
    <div class="p-3">
      <select name="user_view" id="user_view" onchange="toggleManualAndMikrotikUsers()">
        <option value="manual" selected>Manually Added</option>
        <option value="mikrotik">Mikrotik Imported</option>
      </select>
    </div>
    <table class="datatables-users table border-top">
      <thead>
        <tr>
          <th>SL No.</th>
          <th id="added_to_customer" class="d-none">Added As Customer</th>
          <th>Name</th>
          <th>Email</th>
          <th>National ID</th>
          <th>Phone</th>
          <th>Zone</th>
          <th>Registration Date</th>
          <th>Connection Date</th>
          <th>Package</th>
          <th>Bill</th>
          <th>Discount</th>
          <th>Mikrotik</th>
          <th>Username</th>
          <th>Password</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
    <tbody id="manual_added_users">
      @foreach($users as $user)
          <tr>
            <td>{{$user->id}}</td>
            <td>{{$user->full_name}}</td>
            <td>{{$user->email}}</td>
            <td>{{$user->national_id}}</td>
            <td>{{$user->phone}}</td>
            <td>{{$user->zone->name}}</td>
            <td>{{$user->registration_date}}</td>
            <td>{{date('m/d/Y', strtotime($user->connection_date))}}</td>
            <td>{{$user->package->name}}</td>
            <td>{{$user->bill}}</td>
            <td>{{$user->discount}}</td>
            <td>{{$user->mikrotik->identity}}</td>
            <td>{{$user->username}}</td>
            <td>{{$user->password}}</td>
            <td>
                @if($user->pending == true)
                    Pending
                @else
                    Approved
                @endif
            </td>
            <td class="d-flex justify-content-around">
                <a href="{{route('user-edit-customer', $user->id)}}">
                    <div class="cursor-pointer">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                </a>
                <div class="cursor-pointer" data-bs-toggle="modal" data-bs-target="#addNewIPPoolModal_{{$user->id}}">
                  <i class="bi bi-check-lg"></i>
                </div>
                <div class="cursor-pointer"><i class="bi bi-trash"></i></div>
            </td>
          </tr>
          @include('content/user/received-money-modal', ['user' => $user])
      @endforeach
    </tbody>
    <tbody id="mikrotik_added_users" class="d-none">
      @foreach($mikrotik_users as $user)
        @if($user->added_in_customers_table == true)
          @php
            // use App\Models\Customer;
            $customer = App\Models\Customer::where('id_in_mkt', $user->id_in_mkt)->first();
          @endphp
          <tr>
            <td>{{$customer->id}}</td>
            <td>
              Yes
            </td>
            <td>{{$customer->full_name}}</td>
            <td>{{$customer->email}}</td>
            <td>{{$customer->national_id}}</td>
            <td>{{$customer->phone}}</td>
            <td>{{$customer->zone->name}}</td>
            <td>
              @if($customer->registration_date)
                {{$customer->registration_date}}
              @else
              @endif
            </td>
            <td>
              @if($customer->connection_date)
                {{date('m/d/Y', strtotime($customer->connection_date))}}
              @else
              @endif
            </td>
            <td>{{$customer->package->name}}</td>
            <td>{{$customer->bill}}</td>
            <td>{{$customer->discount}}</td>
            <td>{{$customer->mikrotik->identity}}</td>
            <td>{{$customer->username}}</td>
            <td>{{$customer->password}}</td>
            <td></td>
            <td class="d-flex justify-content-around">
                <a href="{{route('user-edit-mikrotik-customer', $user->id)}}">
                    <div class="cursor-pointer">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                </a>
                <div class="cursor-pointer"><i class="bi bi-trash"></i></div>
            </td>
          </tr>
        @else
          <tr>
            <td></td>
            <td>
              @if($user->added_in_customers_table == true)
                Yes
              @else
                No
              @endif
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{$user->profile}}</td>
            <td></td>
            <td></td>
            <td>{{$user->mikrotik->identity}}</td>
            <td>{{$user->name}}</td>
            <td>{{$user->password}}</td>
            <td></td>
            <td class="d-flex justify-content-around">
                <a href="{{route('user-edit-mikrotik-customer', $user->id)}}">
                    <div class="cursor-pointer">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                </a>
                <div class="cursor-pointer"><i class="bi bi-trash"></i></div>
            </td>
          </tr>
        @endif
      @endforeach
    </tbody>
    </table>
  </div>
</div>
@endsection

<script>
  function toggleManualAndMikrotikUsers(){
    let user_view = document.getElementById('user_view').value;
    if(user_view == 'manual'){
      document.getElementById('manual_added_users').classList.remove('d-none');
      document.getElementById('mikrotik_added_users').classList.add('d-none');
      document.getElementById('added_to_customer').classList.add('d-none');
    }
    else{
      document.getElementById('manual_added_users').classList.add('d-none');
      document.getElementById('mikrotik_added_users').classList.remove('d-none');
      document.getElementById('added_to_customer').classList.remove('d-none');
    }
  }
</script>