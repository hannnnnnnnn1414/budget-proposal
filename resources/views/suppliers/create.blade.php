<form id="createForm" action="{{ route('suppliers.store') }}" method="POST" class="text-start">
    @csrf
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">Insurance Company ID</label><span class="text-danger">*</span>
            <input type="text" class="form-control" name="ins_id" value="{{ old('ins_id') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Insurance Company Name</label><span class="text-danger">*</span>
            <input type="text" class="form-control" name="company" value="{{ old('company') }}" required>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-danger">Save</button>
    </div>
</form>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
