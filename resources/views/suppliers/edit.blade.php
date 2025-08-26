<form id="editForm" action="{{ route('suppliers.update', $supplier->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label for="ins_id" class="form-label">Supplier ID</label><span class="text-danger">*</span>
        <input type="text" class="form-control" id="ins_id" name="ins_id" value="{{ old('ins_id', $supplier->ins_id) }}" required>
    </div>
    <div class="mb-3">
        <label for="company" class="form-label">Company Name</label><span class="text-danger">*</span>
        <input type="text" class="form-control" id="company" name="company" value="{{old ('company', $supplier->company) }}" required>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-danger">Update Supplier</button>
    </div>
</form>