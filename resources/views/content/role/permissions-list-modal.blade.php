<!-- Add New Credit Card Modal -->
<div class="modal fade" id="addPermissionsModal_{{$role->id}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
      <div class="modal-content p-3 p-md-5">
        <div class="modal-body">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <div class="text-center mb-4">
            <h3 class="mb-2">Add Permissions</h3>
          </div>
          <form action="{{route('managers-assign-permission', $role->id)}}" class="row g-3" method="POST">
            @csrf
            
            <div class="col-12 flex">
                <label class="form-label w-100" for="manager_list">Manager List</label>
                <input id="manager_list" name="manager_list" type="checkbox"/>
            </div>

            <div class="col-12 flex">
                <label class="form-label w-100" for="add_manager">Add Manager</label>
                <input id="add_manager" name="add_manager" type="checkbox"/>
            </div>

            <div class="col-12 flex">
                <label class="form-label w-100" for="edit_manager">Edit Manager</label>
                <input id="edit_manager" name="edit_manager" type="checkbox"/>
            </div>

            <div class="col-12 flex">
                <label class="form-label w-100" for="delete_manager">Delete Manager</label>
                <input id="delete_manager" name="delete_manager" type="checkbox"/>
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
    function showPrefixTextField(field){
        if(field.checked == true){
            document.getElementById('prefix_text_field').classList.remove('d-none');
        }
        else{
            document.getElementById('prefix_text_field').classList.add('d-none');
        }
    }
  </script>

  