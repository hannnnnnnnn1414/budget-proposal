<!-- resources/views/submissions/edit.blade.php -->
<!DOCTYPE html>
<html lang="en">

<x-head>

</x-head>

<body class="g-sidenav-show bg-gray-100">
    <!-- resources/views/submissions/office-edit.blade.php -->
    <div class="modal-content">
        <div class="modal-header bg-danger">
            <h5 class="modal-title text-white">Update Item</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{ route('submissions.update', $submission->sub_id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <!-- Error messages bisa ditampilkan di sini -->
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Item</label>
                            <select name="itm_id" class="form-control" required id="itm_id">
                                <option value="">-- Select Item --</option>
                                @foreach ($items as $itemID => $item)
                                    <option value="{{ $itemID }}" @selected(old('itm_id') == $itemID || $submission->itm_id == $itemID)>
                                        {{ $item }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label><span class="text-danger">*</span>
                            <textarea class="form-control" name="description" required placeholder="Description">{{ old('description', $submission->description) }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">

                        <div class="mb-3">
                            <label class="form-label">Quantity</label><span class="text-danger">*</span>
                            <input type="number" class="form-control" name="quantity"
                                value="{{ old('quantity', $submission->quantity) }}" required placeholder="Quantity">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price</label><span class="text-danger">*</span>
                            <input type="number" class="form-control" name="price"
                                value="{{ old('price', $submission->price) }}" required placeholder="Price">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Workcenter</label>
                            <select name="wct_id" class="form-control" id="wct_id">
                                <option value="">-- Select Workcenter --</option>
                                @foreach ($workcenters as $wctID => $workcenter)
                                    <option value="{{ $wctID }}" @selected(old('wct_id') == $wctID || $submission->wct_id == $wctID)>
                                        {{ $workcenter }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="mb-3">
                                <label class="form-label">Department</label>
                                <input class="form-control" name="dpt_id" value="{{ Auth::user()->dept }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-danger">Save Changes</button>
            </div>
        </form>
    </div>
    </div>
</body>

</html>
