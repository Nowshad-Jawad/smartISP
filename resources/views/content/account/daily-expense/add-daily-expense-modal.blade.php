<!-- Add New Credit Card Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
      <div class="modal-content p-3 p-md-5">
        <div class="modal-body">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <div class="text-center mb-4">
            <h3 class="mb-2">Add Expense</h3>
          </div>
          <form action="{{route('account-store-daily-expense')}}" class="row g-3" method="POST">
            @csrf
            <div class="col-12">
                <label class="form-label w-100" for="name">Expense Claimant</label>
               <input id="name" name="name" class="form-control" type="text" />
            </div>
            <div class="col-12">
                <label class="form-label" for="category">Category</label>
                <select id="category" name="category" class="select2 form-select">
                    <option value="">Please Select One</option>
                    @foreach($categories as $category)
                        <option value="{{$category->id}}">{{$category->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label w-100" for="amount">Amount</label>
                <input id="amount" name="amount" class="form-control" type="text" />
            </div>
            <div class="col-12">
                <label class="form-label" for="method">Method</label>
                <select id="method" name="method" class="select2 form-select" onchange="toggleTransactionIdField(this)">
                    <option value="">Please Select One</option>
                    <option value="Cash">Cash</option>
                    <option value="Bkash">Bkash</option>
                </select>
            </div>
            <div id="transaction_id_field" class="col-12 d-none">
                <label class="form-label w-100" for="transaction">Transaction Id</label>
                <input id="transaction" name="transaction" class="form-control" type="text" />
            </div>
            <div class="col-12">
                <label class="form-label w-100" for="date">Date</label>
                <input id="date" name="date" class="form-control" type="date" />
            </div>
            <div class="col-12">
                <label class="form-label w-100" for="description">Description</label>
                <textarea name="description" id="description" class="form-control"></textarea>
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
    function toggleTransactionIdField(method){
        if(method.value == 'Bkash'){
            document.getElementById('transaction_id_field').classList.remove('d-none');
        }
        else{
            document.getElementById('transaction_id_field').classList.add('d-none');
        }
    }
  </script>

  