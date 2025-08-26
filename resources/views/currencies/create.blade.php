<form id="createForm" action="{{ route('currencies.store') }}" method="POST" class="text-start">
    @csrf
    <div class="row mb-3">
        <div class="col-md-4">
            <label class="form-label">Currency ID</label><span class="text-danger">*</span>
            <input type="text" class="form-control" name="cur_id" value="{{ old('cur_id') }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Currency Name</label><span class="text-danger">*</span>
            <input type="text" class="form-control" name="currency" value="{{ old('currency') }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Nominal</label><span class="text-danger">*</span>
            <input type="text" class="form-control" name="nominal" value="{{ old('nominal') }}" required>
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
