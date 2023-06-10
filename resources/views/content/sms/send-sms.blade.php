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
    <div>
        <button data-bs-toggle="modal" data-bs-target="#addNewTemplateModal">Add New</button>
    </div>
  </div>
  <div class="row m-2">
    <div class="col-7">
        <form action="">
            <div class="mb-3">
                <input type="checkbox" id="individual_sms" name="individual_sms">
                <label for="individual_sms" class="form-label">Individual Sms</label>
            </div>
            <div class="mb-3">
                <label class="form-label" for="api">Select API</label>
                <select id="api" name="api" class="select2 form-select">
                    <option value="">Please Select One</option>
                    {{-- @foreach($sms_templates as $template)
                        <option value="{{$template->id}}">{{$template->name}}</option>
                    @endforeach --}}
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" for="message">Message</label>
                <textarea name="message" id="message" cols="30" rows="10" class="form-control">
                </textarea>
            </div>
        </form>

        <div class="mt-4 mx-2">
            <div class="d-flex justify-content-between mb-3">
                <h6>GROUP</h6>
                <div>
                    <button  data-bs-toggle="modal" data-bs-target="#createGroupModal">Create Group</button>
                </div>
            </div>
            <hr>
            <div class="mt-2 mb-3">
                @foreach($sms_groups as $group)
                    <div class="mb-3">
                        <input type="checkbox" id="group_{{$group->id}}" name="group_{{$group->id}}" onchange="toggleUsers(this)">
                        <label class="form-label" for="group_{{$group->id}}">{{$group->name}}</label>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col card-datatable table-responsive">
        <table class="datatables-users table border-top">
            <thead>
                <tr>
                <th>Name</th>
                <th>Phone</th>
                <th>Type</th>
                </tr>
            </thead>
            <form action="{{route('send-sms')}}" method="POST">
                @csrf
                <tbody id="users_table">

                    
                    {{-- @foreach($users as $user)
                        <tr>
                            <td>{{$user->name}}</td>
                            <td>{{$user->phone}}</td>
                            <td></td>
                        </tr>
                    @endforeach --}}
                </tbody>
                <button type="submit"> Submit </button>
            </form>
        </table>
      </div>
  </div>
</div>
@include('content.sms.create-group-modal')
@endsection

<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js" integrity="sha512-uMtXmF28A2Ab/JJO2t/vYhlaa/3ahUOgj1Zf27M5rOo8/+fcTUVH0/E0ll68njmjrLqOBjXM3V9NiPFL5ywWPQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    function toggleUsers(element){
        console.log(element.id)
        let element_id = element.id
        axios({
        method: 'post',
        url: 'get-sms-group-users',
        data: {
            element_id: element.id
        }
        }).then((resp) => {
            console.log(resp.data.element[0])
            resp.data.element.forEach( ($key) => {

                tr = document.createElement('tr')
                td = document.createElement('td')

                input = document.createElement('input')
                div = document.createElement('div')
                input.type = 'checkbox'
                input.id = `user_${$key['id']}`
                input.name = `user_${$key['id']}`
                input.value=$key['id']
                input.setAttribute('onchange', 'user(this)')
                label = document.createElement('label')
                label.setAttribute('for', 'user')

                if($key['full_name'] != null){
                    label.innerHTML = $key['full_name']
                }

                if($key['name'] != null){
                    label.innerHTML = $key['name']
                }

                div.appendChild(input)
                div.appendChild(label)


                td.appendChild(div)
                tr.appendChild(td)


                td = document.createElement('td')
                td.innerHTML = $key['phone']
                tr.appendChild(td)


                td = document.createElement('td')
                tr.appendChild(td)

                document.getElementById('users_table').appendChild(tr)
                

            })
        })
    }

    function user(element){
        console.log(element)
    }
</script>
