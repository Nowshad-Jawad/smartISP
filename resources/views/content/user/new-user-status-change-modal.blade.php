<!-- Add New Credit Card Modal -->
<div class="modal fade" id="newUserStatusChange" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
      <div class="modal-content p-3 p-md-5">
        <div class="modal-body">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <div class="text-center mb-4">
            <h3 class="mb-2">Change Status</h3>
          </div>
          <form action="" class="row g-3" method="POST">
            @csrf
            <div class="col-12">
                 <label class="form-label w-100" for="username">Username</label>
                <input id="username" name="username" class="form-control" type="text" />
            </div>
            <div class="col-12">
                <label class="form-label w-100" for="password">Password</label>
                <input id="password" name="password" class="form-control" type="text" />
            </div>
            <div class="col-12">
                <label class="form-label w-100" for="password_confirmation">Confirm Password</label>
                <input id="password_confirmation" name="password_confirmation" class="form-control" type="text" />
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
    function toggleChargeableField(){
        let public_ip = document.getElementById('public_ip');
        if(public_ip.value == 'yes'){
            document.getElementById('chargeable_ip').disabled = false;
        }
        else{
            document.getElementById('chargeable_ip').disabled = true;
        }
    }
    
    function toggleChargeField(){
        let chargeable_ip = document.getElementById('chargeable_ip');
        if(chargeable_ip.value == '1'){
            document.getElementById('charge').disabled = false;
        }
        else{
            document.getElementById('charge').disabled = true;
        }
    }
  </script>
  