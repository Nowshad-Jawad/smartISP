<!-- Add New Credit Card Modal -->
<div class="modal fade" id="editRoleModal_{{$role->id}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
      <div class="modal-content p-3 p-md-5">
        <div class="modal-body">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <div class="text-center mb-4">
            <h3 class="mb-2">Edit Manager</h3>
          </div>
          <form action="{{route('managers-update-roll', $role->id)}}" class="row g-3" method="POST">
            @csrf
            @method('PUT')
            <div class="col-12">
                 <label class="form-label w-100" for="name">Name</label>
                <input id="name" name="name" class="form-control" type="text" value="{{$role->name}}" />
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

  