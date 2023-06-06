<!-- Add New Credit Card Modal -->
<div class="modal fade" id="addBillCollectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
      <div class="modal-content p-3 p-md-5">
        <div class="modal-body">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <div class="text-center mb-4">
            <h3 class="mb-2">Add Bill</h3>
          </div>
          <form action="{{route('account-store-bill-collection')}}" class="row g-3" method="POST">
            @csrf
            <div class="col-12">
                <label class="form-label" for="customer">Customer Id</label>
                <select id="customer" name="customer" class="select2 form-select" onchange="addCustomerId(this)">
                    <option value="">Please Select One</option>
                    @foreach($customers as $customer)
                        <option value="{{$customer->id}}">{{$customer->username}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label w-100" for="customer_name">Customer Name</label>
                <input id="customer_name" name="customer_name" class="form-control" type="text" readonly />
            </div>
            <div class="col-12">
                <label class="form-label" for="method">Method</label>
                <select id="method" name="method" class="select2 form-select">
                    <option value="">Please Select One</option>
                    <option value="Cash">Cash</option>
                    <option value="Bkash">Bkash</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label w-100" for="monthly_bill">Monthly Bill</label>
                <input id="monthly_bill" name="monthly_bill" class="form-control" type="text" readonly />
                <p id="current_balance" class="h6 d-none"><strong>Current Wallet Balance: </strong> <mark id="wallet_balance"></mark></p>
            </div>
            <div class="col-12">
                <div class="d-flex justify-content-between">
                    <label class="form-label" for="received">Received</label>
                    <div id="add_from_wallet_field" class="d-none">
                        <input type="checkbox" name="add_wallet_balance" id="add_wallet_balance" onchange="addWalletBalance(this)">
                        <label for="add_wallet_balance">Add From Customer Wallet</label>
                    </div>
                </div>
                <input id="received" name="received" class="form-control" type="text" />
            </div>
            <div class="col-12">
                <label class="form-label" for="manager">Received By</label>
                <select id="manager" name="manager" class="select2 form-select">
                    <option value="">Please Select One</option>
                    @foreach($managers as $manager)
                        <option value="{{$manager->id}}">{{$manager->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label w-100" for="issue_date">Issue Date</label>
                <input id="issue_date" name="issue_date" class="form-control" type="date" />
            </div>
            <div class="col-12">
                <label class="form-label w-100" for="note">Note</label>
                <textarea name="note" id="note" class="form-control"></textarea>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js" integrity="sha512-uMtXmF28A2Ab/JJO2t/vYhlaa/3ahUOgj1Zf27M5rOo8/+fcTUVH0/E0ll68njmjrLqOBjXM3V9NiPFL5ywWPQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
function addCustomerId(customer){
    axios({
        method: 'post',
        url: 'bill-collection/get-details/',
        data: {
            customer: customer.value
        }
    }).then((resp) => {
        if(resp.status == 200){
            document.getElementById('customer_name').value = resp.data.customer.full_name

            if(resp.data.customer.discount == null){
                document.getElementById('monthly_bill').value = resp.data.customer.bill
            }
            else{
                document.getElementById('monthly_bill').value = (parseInt(resp.data.customer.bill) - parseInt(resp.data.customer.discount))
            }

            if(resp.data.customer.bill <= resp.data.customer.wallet){
                document.getElementById('current_balance').classList.remove('d-none')
                document.getElementById('wallet_balance').innerHTML = resp.data.customer.wallet 
                document.getElementById('add_from_wallet_field').classList.remove('d-none')
            }
            else{
                document.getElementById('current_balance').classList.add('d-none')
                document.getElementById('wallet_balance').innerHTML = resp.data.customer.wallet 
                document.getElementById('add_from_wallet_field').classList.add('d-none')
            }         
        }
    })
}

function addWalletBalance(wallet){
    if(wallet.checked == true){
        let received_field = document.getElementById('received');
        let monthly_bill = document.getElementById('monthly_bill');
        received_field.value = monthly_bill.value;
    }
    else{
        let received_field = document.getElementById('received');
        received_field.value = null;
    }
}
</script>

  