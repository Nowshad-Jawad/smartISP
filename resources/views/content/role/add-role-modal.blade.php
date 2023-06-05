<!-- Add New Credit Card Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
      <div class="modal-content p-3 p-md-5">
        <div class="modal-body">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <div class="text-center mb-4">
            <h3 class="mb-2">Add Role</h3>
          </div>
          <form action="{{route('managers-store-roll')}}" class="row g-3" method="POST">
            @csrf
            <div class="col-12">
                 <label class="form-label w-100" for="name">Name</label>
                <input id="name" name="name" class="form-control" type="text" />
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

  