<!-- Add New Credit Card Modal -->
<div class="modal fade" id="createGroupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
      <div class="modal-content p-3 p-md-5">
        <div class="modal-body">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="text-center mb-4">
                <h3 class="mb-2">Create Group</h3>
            </div>
            <form action="{{route('sms-save-group')}}" class="row g-3" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="name">Group Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Full Name" />
                </div>
                <div class="mb-3">
                    <label class="form-label" for="user_type">User Type</label>
                    <select id="user_type" name="user_type" class="select2 form-select">
                        <option value="">Please Select One</option>
                        <option value="home_user">Home User</option>
                        <option value="corporate_user">Corporate User</option>
                        <option value="dashboard_user">Dashboard User</option>
                        <option value="active_customer">Active Customer</option>
                        <option value="inactive_customer">Inactive Customer</option>
                        <option value="pending_customer">Pending Customer</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="users">Menbers</label>
                    <select id="users" name="users[]" class="select2 form-select" onchange="selectMembers(this)" multiple>
                        <option value="all_users">All Users</option>
                        <option value="customers">Customers</option>
                        @for($i = 0; $i < count($customers); $i++)
                            <option id="'customer_'.{{$customers[$i]->id}}" value="customer {{$customers[$i]->id}}">{{$customers[$i]->full_name}}</option>
                        @endfor
                        <option value="managers">Managers</option>
                        @foreach($managers as $manager)
                            <option value="manager {{$manager->id}}">{{$manager->name}}</option>
                        @endforeach
                    </select>
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
    function selectMembers(element){
        // element.selectedOptions.forEach( () => {
        //     console.log('hello')
        // })
        // for(option in element.selectedOptions){
        //     console.log(option.item)
        // }
        // console.log(typeof(element.selectedOptions))
        // console.log(document.getElementById(element.selectedOptions[0].id).checked = false);
    }
  </script>

  