<!-- Add New Credit Card Modal -->
<div class="modal fade" id="addNewTemplateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
      <div class="modal-content p-3 p-md-5">
        <div class="modal-body">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="text-center mb-4">
                <h3 class="mb-2">Add New API</h3>
            </div>
            <form action="{{route('sms-store-sms-template')}}" class="row g-3" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Full Name" />
                </div>
                <div class="mb-3">
                    <label class="form-label" for="api">Select API</label>
                    <select id="api" name="api" class="select2 form-select">
                        <option value="">Please Select One</option>
                        @foreach($sms_apis as $api)
                            <option value="{{$api->id}}">{{$api->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="template_for">Template For</label>
                    <select id="template_for" name="template_for" class="select2 form-select">
                        <option value="">Please Select One</option>
                        <option value="welcome_sms">Welcome SMS</option>
                        <option value="invoice_create">Invoice Create</option>
                        <option value="invoice_payment">Invoice Payment</option>
                        <option value="account_expire">Account Expire</option>
                        <option value="package_change">Package Change</option>
                        <option value="ticket_accept">Ticket Accept</option>
                        <option value="ticket_pending">Ticket Pending</option>
                        <option value="ticket_success">Ticket Success</option>
                        <option value="assign_ticket_to_support">Assign Ticket To Support</option>
                        <option value="user_info">User Info</option>
                        <option value="update_balance">Update Balance</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="template">Template</label>
                    <textarea name="template" id="template" cols="30" rows="10" class="form-control">Dear {user_name}, Your internet due bill= {due_amount}tk. Please pay by Cash/bKash/Rocket. Your Ref ID={customer_user_id}. For query call: 09639-949494 Thanks -{company_name}
                    </textarea>
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

  