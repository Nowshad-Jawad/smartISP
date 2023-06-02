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
    <table class="datatables-users table border-top">
        <thead>
            <tr>
            <th>SL No.</th>
            <th>Sub-Zone Name</th>
            <th>Zone</th>
            <th>Total User</th>
            <th>Total Active</th>
            <th>Total Inactive</th>
            <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sub_zones as $sub_zone)
                <tr>
                    <td>{{$sub_zone->id}}</td>
                    <td>{{$sub_zone->name}}</td>
                    <td>{{$sub_zone->zone->name}}</td>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="d-flex justify-content-around">
                        <a href="{{route('network-edit-sub-zone', $sub_zone->id)}}">
                            <div class="cursor-pointer">
                                <i class="bi bi-pencil-square"></i>
                            </div>
                        </a>
                        <div class="cursor-pointer"><i class="bi bi-trash"></i></div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
  </div>
</div>
@endsection