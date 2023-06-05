<!-- Add New Credit Card Modal -->
<div class="modal fade" id="addBillCollectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
      <div class="modal-content p-3 p-md-5">
        <div class="modal-body">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <div class="text-center mb-4">
            <h3 class="mb-2">Add Bill</h3>
          </div>
          <form action="" class="row g-3" method="POST">
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
                <input id="monthly_bill" name="monthly_bill" class="form-control" type="text" />
            </div>
            <div class="col-12">
                <label class="form-label w-100" for="received">Received</label>
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
    // document.getElementById('customer_name').value = customer.value;
    axios.get(`bill-collection/get-details/${customer.value}`).then((resp) => {
        if(resp.status == 200){
            console.log(resp.data);
        }
    })
}
</script>

  