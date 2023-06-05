<!-- Add New Credit Card Modal -->
<div class="modal fade" id="editCategoryModal_{{$category->id}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
      <div class="modal-content p-3 p-md-5">
        <div class="modal-body">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <div class="text-center mb-4">
            <h3 class="mb-2">Edit Category</h3>
          </div>
          <form action="{{route('account-update-category', $category->id)}}" class="row g-3" method="POST">
            @csrf
            @method('PUT')
            <div class="col-12">
                <label class="form-label w-100" for="name">Name</label>
                <input id="name" name="name" class="form-control" type="text" value="{{$category->name}}" />
           </div>
            <div class="col-12">
                <label class="form-label" for="type">Type</label>
                <select id="type" name="type" class="select2 form-select">
                    <option value="">Please Select One</option>
                    <option value="Income" @if($category->type == 'Income') selected @endif>Income</option>
                    <option value="Expense" @if($category->type == 'Expense') selected @endif>Expense</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label w-100" for="status">Status</label>
                <input id="status" name="status" type="checkbox" @if($category->status == true) checked @endif />
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

  