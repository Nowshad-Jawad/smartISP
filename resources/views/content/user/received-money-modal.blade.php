<div class="modal fade" id="addNewIPPoolModal_{{$user->id}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
      <div class="modal-content p-3 p-md-5">
        <div class="modal-body">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <div class="text-center mb-4">
            <h3 class="mb-2">Confirm Payment</h3>
          </div>
          <form action="{{route('user-approve-customer', $user->id)}}" class="row g-3" method="POST">
            @csrf
            <div class="col-12 text-center mt-2">
                User: {{$user->full_name}}
            </div>
            <div class="col-12 text-center mt-2">
                Package: {{$user->package->name}} <br>
                {{-- Duration: {{$user->package->durationmeasure}} <br> --}}
                Bill: {{$user->package->price}} <br>
                User Discount: {{$user->discount}}
            </div>
            <div class="col-12 mt-2">
                <label class="form-label w-100" for="amount">Total Amount</label>
                <input id="amount" name="amount" class="form-control" type="text" @if($user->discount != null) value="{{$user->bill - $user->discount}}" @else value="{{$user->bill}}" @endif readonly />
            </div>
            <div class="col-12 mt-2">
                <label class="form-label w-100" for="received_amount">Received Amount</label>
                <input id="received_amount" name="received_amount" class="form-control" type="text" />
            </div>
            <div class="col-12 mt-2">
              <label class="form-label" for="paid_by">Paid By</label>
              <select id="paid_by" name="paid_by" class="select2 form-select">
                  <option value="Bkash">Bkash</option>
                  <option value="Cash">Cash</option>
              </select>
          </div>
          <div class="col-12 mt-2">
              <label class="form-label w-100" for="transaction_id">Transaction Id</label>
              <input id="transaction_id" name="transaction_id" class="form-control" type="text" />
          </div>
            <div class="col-12 mt-2">
                <label class="form-label" for="status">Status</label>
                <select id="status" name="status" class="select2 form-select">
                    <option value="paid" selected>Paid</option>
                    <option value="due">Due</option>
                    <option value="over_paid">Over Paid</option>
                </select>
            </div>
            <div class="col-12 text-center mt-4">
              <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
              <button type="reset" class="btn btn-label-secondary btn-reset" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
            </div>

          </form>
  
          
        </div>
      </div>
    </div>
  </div>