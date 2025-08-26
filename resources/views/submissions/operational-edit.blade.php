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
        <form id="updateSubmissionForm" action="{{ route('submissions.update', $submission->sub_id) }}" method="POST">
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
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" placeholder="Description">{{ old('description', $submission->description) }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" class="form-control" name="price"
                                value="{{ old('price', $submission->price) }}" placeholder="Price">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Work Center</label>
                            <select name="wct_id" class="form-control" required id="wct_id">
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
                                <input type="hidden" name="dpt_id" value="{{ Auth::user()->dept }}">
                                <input class="form-control" value="{{ Auth::user()->department->department ?? '-' }}"
                                    readonly>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $('#updateSubmissionForm').on('submit', function(e) {
            e.preventDefault(); // Cegah submit default

            const form = $(this);
            const url = form.attr('action');
            const data = form.serialize();

            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    // Modal ditutup, SweetAlert muncul
                    $('.modal').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Data has been updated successfully',
                        confirmButtonColor: '#d33',
                    }).then(() => {
                        location.reload(); // Optional: reload halaman jika butuh refresh data
                    });
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON?.errors;
                    let message = 'Something went wrong';

                    if (errors) {
                        message = Object.values(errors).flat().join('\n');
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: message,
                        confirmButtonColor: '#d33',
                    });
                }
            });
        });
    </script>

</body>

</html>
