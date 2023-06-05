<div class="modal fade" id="addInvoiceModal_{{$user->id}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
      <div class="modal-content p-3 p-md-5">
        <div class="modal-body">
            
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="text-center mb-4">
                <h3 class="mb-2">Add Invoice</h3>
            </div>
            <form action="{{route('user-store-invoice')}}" class="row g-3" method="POST">
                @csrf
                <div class="col-12 text-center mt-2">
                    Package: {{$user->package->name}} <br>
                    {{-- Duration: {{$user->package->durationmeasure}} <br> --}}
                    Bill: {{$user->package->price}} <br>
                    User Discount: {{$user->discount}}
                </div>
                <div class="col-12 mt-2">
                    <label class="form-label" for="user_id">User Name</label>
                    <select id="user_id" name="user_id" class="select2 form-select">
                        <option value="">Please Select One</option>
                        @foreach($users as $u)
                            <option value="{{$u->id}}" @if($u->id == $user->id) selected @endif>{{$user->full_name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 mt-2">
                    <label class="form-label" for="invoice_for">Invoice For</label>
                    <select id="invoice_for" name="invoice_for" class="select2 form-select" onchange="addBill(this, {{$user->bill}}, {{$user->discount}})">
                        <option value="">Please Select One</option>
                        <option value="monthly_bill">Monthly Bill</option>
                        <option value="add_balance">Add Balance</option>
                        <option value="connection_fee">Connection Fee</option>
                    </select>
                </div>
                <div class="col-12 mt-2">
                    <label class="form-label w-100" for="expire_date">Expire Date</label>
                    <input id="expire_date" name="expire_date" class="form-control" type="text" @if($user->id_in_mkt != null) value="{{$user->billing_date}}" @else value="{{$user->connection_date}}" @endif/>
                </div>
                <div class="col-12 mt-2">
                    <label class="form-label w-100" for="amount">Total Amount</label>
                    <input id="amount" name="amount" class="form-control" type="text" />
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

<script>
    function addBill(event, price, discount){
        if(event.value == 'monthly_bill'){
            if(discount != null){
                document.getElementById('amount').value = (price - discount)
            }
            else{
                document.getElementById('amount').value = price
            }
        }
    }
</script>