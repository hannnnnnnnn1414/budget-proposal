<form id="editForm" action="{{ route('dimensions.update', ['dim_id' => $dim_id, 'id' => $item->id]) }}" method="POST" class="text-start">
    @csrf
    @method('PUT')
    <div class="row mb-3">
        @if ($dim_id == 1)
            <div class="col-md-6">
                <label class="form-label">Line of Business ID</label><span class="text-danger">*</span>
                <input type="text" class="form-control" name="lob_id" value="{{ old('lob_id', $item->lob_id) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Line of Business Name</label><span class="text-danger">*</span>
                <input type="text" class="form-control" name="line_business" value="{{ old('line_business', $item->line_business) }}" required>
            </div>
        @elseif ($dim_id == 2)
            <div class="col-md-6">
                <label class="form-label">Department ID</label><span class="text-danger">*</span>
                <input type="text" class="form-control" name="dpt_id" value="{{ old('dpt_id', $item->dpt_id) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Department Name</label><span class="text-danger">*</span>
                <input type="text" class="form-control" name="department" value="{{ old('department', $item->department) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Level</label><span class="text-danger">*</span>
                <input type="number" class="form-control" name="level" value="{{ old('level', $item->level) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Parent</label><span class="text-danger">*</span>
                <input type="number" class="form-control" name="parent" value="{{ old('parent', $item->parent) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Alloc</label>
                <input type="text" class="form-control" name="alloc" value="{{ old('alloc', $item->alloc) }}">
            </div>
        @elseif ($dim_id == 3)
            <div class="col-md-6">
                <label class="form-label">Workcenter ID</label><span class="text-danger">*</span>
                <input type="text" class="form-control" name="wct_id" value="{{ old('wct_id', $item->wct_id) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Workcenter Name</label><span class="text-danger">*</span>
                <input type="text" class="form-control" name="workcenter" value="{{ old('workcenter', $item->workcenter) }}" required>
            </div>
        @elseif ($dim_id == 4)
            <div class="col-md-6">
                <label class="form-label">Budget Code ID</label><span class="text-danger">*</span>
                <input type="text" class="form-control" name="bdc_id" value="{{ old('bdc_id', $item->bdc_id) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Budget Code Name</label><span class="text-danger">*</span>
                <input type="text" class="form-control" name="budget_name" value="{{ old('budget_name', $item->budget_name) }}" required>
            </div>
        @endif
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-danger">Update</button>
    </div>
</form>