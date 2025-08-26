<form id="createForm" action="{{ route('dimensions.store', ['dim_id' => $dim_id]) }}" method="POST" class="text-start">
    @csrf
    <div class="row mb-3">
        @if ($dim_id == 1)
            <div class="col-md-6">
                <label class="form-label">Line of Business ID</label><span class="text-danger">*</span>
                <input type="text" class="form-control" name="lob_id" value="{{ old('lob_id') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Line of Business Name</label><span class="text-danger">*</span>
                <input type="text" class="form-control" name="line_business" value="{{ old('line_business') }}"
                    required>
            </div>
        @elseif ($dim_id == 2)
            <div class="col-md-6">
                <label class="form-label">Department ID</label><span class="text-danger">*</span>
                <input type="text" class="form-control" name="dpt_id" value="{{ old('dpt_id') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Department Name</label><span class="text-danger">*</span>
                <input type="text" class="form-control" name="department" value="{{ old('department') }}" required>
            </div>
            <div class="col-md-4 mt-4">
                <label class="form-label">Level</label><span class="text-danger">*</span>
                <input type="number" class="form-control" name="level" value="{{ old('level') }}" required>
            </div>
            <div class="col-md-4 mt-4">
                <label class="form-label">Parent</label><span class="text-danger">*</span>
                <input type="number" class="form-control" name="parent" value="{{ old('parent') }}" required>
            </div>
            <div class="col-md-4 mt-4">
                <label class="form-label">Alloc</label>
                <input type="text" class="form-control" name="alloc" value="{{ old('alloc') }}">
            </div>
        @elseif ($dim_id == 3)
            <div class="col-md-6">
                <label class="form-label">Workcenter ID</label><span class="text-danger">*</span>
                <input type="text" class="form-control" name="wct_id" value="{{ old('wct_id') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Workcenter Name</label><span class="text-danger">*</span>
                <input type="text" class="form-control" name="workcenter" value="{{ old('workcenter') }}" required>
            </div>
        @elseif ($dim_id == 4)
            <div class="col-md-6">
                <label class="form-label">Budget Code ID</label><span class="text-danger">*</span>
                <input type="text" class="form-control" name="bdc_id" value="{{ old('bdc_id') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Budget Code Name</label><span class="text-danger">*</span>
                <input type="text" class="form-control" name="budget_name" value="{{ old('budget_name') }}" required>
            </div>
        @endif
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
