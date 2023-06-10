<!-- Add New Credit Card Modal -->
<div class="modal fade" id="addNewApiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
      <div class="modal-content p-3 p-md-5">
        <div class="modal-body">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <div class="text-center mb-4">
            <h3 class="mb-2">Add New API</h3>
          </div>
          <form action="{{route('sms-store-sms-api')}}" class="row g-3" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="name">Name</label>
                <select id="name" name="name" class="select2 form-select">
                    <option value="">Select Gateway</option>
                    <option value="Brillent">Brillent</option>
                    <option value="REVE System">REVE System</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" for="api">API URL</label>
                <input type="text" class="form-control" id="api" name="api" placeholder="API URL" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="api_key">API KEY</label>
                <input type="text" class="form-control" id="api_key" name="api_key" placeholder="API KEY" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="secret_key">Secret Key</label>
                <input type="text" class="form-control" id="secret_key" name="secret_key" placeholder="Secret Key" />
            </div>
            <div class="mb-3">
                <label class="form-label" for="caller_id">Caller ID</label>
                <input type="text" class="form-control" id="caller_id" name="caller_id" placeholder="Caller ID" />
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

  