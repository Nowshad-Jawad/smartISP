<!-- Add New Credit Card Modal -->
<div class="modal fade" id="editManagerModal_{{$manager->id}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
      <div class="modal-content p-3 p-md-5">
        <div class="modal-body">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <div class="text-center mb-4">
            <h3 class="mb-2">Edit Manager</h3>
          </div>
          <form action="{{route('managers-update-manager', $manager->id)}}" class="row g-3" method="POST">
            @csrf
            @method('PUT')
            <div class="col-12">
                <label class="form-label" for="type">Type</label>
                <select id="type" name="type" class="select2 form-select">
                    <option value="">Please Select One</option>
                    <option value="franchise" @if($manager->type == 'franchise') selected @endif>Franchise</option>
                    <option value="app_manager" @if($manager->type == 'app_manager') selected @endif>App Manager</option>
                </select>
            </div>
            <div class="col-12">
                 <label class="form-label w-100" for="name">Name</label>
                <input id="name" name="name" class="form-control" type="text" value="{{$manager->name}}" />
            </div>
            <div class="col-12">
                <label class="form-label w-100" for="email">Email</label>
                <input id="email" name="email" class="form-control" type="email" value="{{$manager->email}}" />
            </div>
            <div class="col-12">
                <label class="form-label w-100" for="password">Password</label>
                <input id="password" name="password" class="form-control" type="password" />
            </div>
            <div class="col-12">
                <label class="form-label w-100" for="password_confirmation">Confirm Password</label>
                <input id="password_confirmation" name="password_confirmation" class="form-control" type="password" />
            </div>
            <div class="col-12">
                <label class="form-label w-100" for="phone">Phone</label>
                <input id="phone" name="phone" class="form-control" type="text" value="{{$manager->phone}}" />
            </div>
            <div class="col-12">
                <label class="form-label" for="zone_id">Zone</label>
                <select id="zone_id" name="zone_id" class="select2 form-select">
                    <option value="">Please Select One</option>
                    @foreach($zones as $zone)
                        <option value="{{$zone->id}}" @if($manager->zone_id == $zone->id) selected @endif>{{$zone->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label" for="subzone_id">Sub Zone</label>
                <select id="subzone_id" name="subzone_id" class="select2 form-select">
                    <option value="">Please Select One</option>
                    @foreach($sub_zones as $sub_zone)
                        <option value="{{$sub_zone->id}}" @if($manager->sub_zone_id == $sub_zone->id) selected @endif>{{$sub_zone->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label" for="mikrotik_id">Mikrotik</label>
                <select id="mikrotik_id" name="mikrotik_id" class="select2 form-select">
                    <option value="">Please Select One</option>
                    @foreach($mikrotiks as $mikrotik)
                        <option value="{{$mikrotik->id}}" @if($manager->mikrotik_id == $mikrotik->id) selected @endif>{{$mikrotik->identity}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label w-100" for="address">Address</label>
                <input id="address" name="address" class="form-control" type="text" value="{{$manager->address}}"/>
            </div>
            <div class="col-12">
                <label class="form-label w-100" for="grace">Grace Allowed</label>
                <input id="grace" name="grace" class="form-control" type="number" value="{{$manager->grace_allowed}}" />
            </div>
            <div class="col-12">
                <label class="form-label w-100" for="prefix">Prefix</label>
                <input id="prefix" name="prefix" type="checkbox" onchange="showPrefixTextField(this)" @if($manager->prefix == true) checked @else  @endif />
            </div>
            <div id="prefix_text_field" class="col-12 d-none">
                <label class="form-label w-100" for="prefix_text">Prefix Text</label>
                <input id="prefix_text" name="prefix_text" class="form-control" type="text" value="{{$manager->prefix_text}}" />
            </div>
            <div class="col-12 text-center">
              <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
              <button type="reset" class="btn btn-label-secondary btn-reset" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
            </div>

          </form>
  
          
        </div>
      </div>
    </div>
  </div>

  <script>
    window.addEventListener("load", function(){
        let expiry_date = document.getElementById('prefix');
        console.log(expiry_date);
        if(expiry_date.value == "on"){
            document.getElementById('prefix_text_field').classList.remove('d-none');
        }
        else{
            document.getElementById('prefix_text_field').classList.add('d-none');
        }
    })

    function showPrefixTextField(field){
        if(field.checked == true){
            document.getElementById('prefix_text_field').classList.remove('d-none');
        }
        else{
            document.getElementById('prefix_text_field').classList.add('d-none');
        }
    }
  </script>

  