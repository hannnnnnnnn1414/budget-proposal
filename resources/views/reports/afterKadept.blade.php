<!DOCTYPE html>
<html lang="en">

<x-head>


</x-head>

<body class="g-sidenav-show bg-gray-100">
    <x-sidebar></x-sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
        <x-navbar :notifications="$notifications">
            Submissions</x-navbar>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-danger">
                            <h4 style="font-weight: bold;" class="text-white"><i
                                    class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>PROPOSAL DETAIL
                                {{ $account_name }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card-body">
                                        <div class="card-header bg-secondary text-white py-2 px-2">
                                            <h6 class="mb-0 text-white">Approval Status</h5>
                                        </div>
                                        <!-- Approval Status -->
                                        <div class="bg-green-100 p-4 rounded shadow mb-4">

                                            @if ($submissions->isNotEmpty())
                                                @php
                                                    $submission = $submissions->first();
                                                    // Fetch the approval record for the submission where approve_by matches the logged-in user's npk
$approval = \App\Models\Approval::where(
    'sub_id',
    $submission->sub_id,
)
    ->where('approve_by', Auth::user()->npk)
                                                        ->first();
                                                @endphp
                                                <p>Status: <span class="font-bold">
                                                        @if ($submission->status == 6)
                                                            <span class="badge bg-warning">REQUIRES APPROVAL
                                                            </span>
                                                        @elseif ($submission->status == 7)
                                                            <span class="badge"
                                                                style="background-color: #0080ff">APPROVED
                                                                BY
                                                                KADEP BUDGETING</span>
                                                        @elseif ($submission->status == 8)
                                                            <span class="badge bg-danger">DISAPPROVED BY
                                                                KADEP</span>
                                                        @elseif ($submission->status == 9)
                                                            <span class="badge bg-danger">DISAPPROVED BY
                                                                KADIV</span>
                                                        @elseif ($submission->status == 10)
                                                            <span class="badge bg-danger">DISAPPROVED BY DIC</span>
                                                        @elseif ($submission->status == 11)
                                                            <span class="badge bg-danger">DISAPPROVED BY PIC
                                                                BUDGETING</span>
                                                        @elseif ($submission->status == 12)
                                                            <span class="badge bg-danger">DISAPPROVED BY KADEP
                                                                BUDGETING</span>
                                                        @else
                                                            <span class="badge bg-danger">REJECTED</span>
                                                        @endif
                                                    </span></p>
                                                <p>Date:
                                                    {{ $approval ? $approval->created_at->format('d-m-Y H:i') : '-' }}
                                                </p>
                                                <div class="mt-4 flex space-x-2">
                                                    <button type="button" class="btn btn-danger open-history-modal"
                                                        data-id="{{ $submission->sub_id }}">History
                                                        Approval</button>
                                                </div>
                                            @else
                                                <p><strong>Remark: -</strong></p>
                                                <p><strong>Date: -</strong></p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card-body">
                                        <div class="card-header bg-secondary text-white py-2 px-2">
                                            <h6 class="mb-0 text-white">Remark</h6>
                                        </div>
                                        <div class="bg-white p-4 rounded shadow mb-4">
                                            @php
                                                $remarks = \App\Models\Remarks::where(
                                                    'sub_id',
                                                    $submission->sub_id ?? '',
                                                )
                                                    ->where('remark_by', Auth::user()->npk)
                                                    ->where('remark_type', 'remark')
                                                    ->with('user')
                                                    ->get();
                                            @endphp
                                            @if ($remarks->isNotEmpty())
                                                @php $remark = $remarks->first(); @endphp
                                                @foreach ($remarks as $remark)
                                                    <div class="mb-3">
                                                        <p><strong>Remark:</strong> <span
                                                                class="font-bold">{{ $remark->remark }}</span></p>
                                                        {{-- <p><strong>By:</strong>
                                                                {{ $remark->user ? $remark->user->name : 'Unknown User' }}
                                                                (NPK: {{ $remark->remark_by }})</p> --}}
                                                        <p><strong>Date:</strong>
                                                            {{ $remark->created_at->format('d-m-Y H:i') }}</p>
                                                    </div>
                                                @endforeach
                                            @else
                                                <p><strong>Remark: -</strong></p>
                                                <p><strong>Date: -</strong></p>
                                            @endif
                                            <div class="mt-4 flex space-x-2">
                                                <button type="button" class="btn open-add-remark-modal text-white"
                                                    style="background-color: #0080ff;"
                                                    data-id="{{ $submission->sub_id ?? '' }}">Add Remark</button>
                                                <button type="button" class="btn btn-danger open-historyremark-modal"
                                                    data-id="{{ $submission->sub_id ?? '' }}">View
                                                    Remarks</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-header bg-secondary text-white py-2 px-2">
                                <h6 class="mb-0 text-white">Item of Purchase</h5>
                            </div>
                            <!-- Item Table -->
                            <div class="bg-white p-4 rounded shadow mb-4">
                                @php
                                    $hasAction = $submissions->contains(function ($submission) {
                                        return $submission->status == 6;
                                    });
                                @endphp
                                @if ($submission->status == 6)
                                    <div class="d-flex justify-content-end mb-3">
                                        <button type="button" class="btn btn-danger open-add-item-modal"
                                            data-sub-id="{{ $submission->sub_id }}">
                                            <i class="fa-solid fa-plus me-2"></i>Add Item
                                        </button>
                                    </div>
                                @endif
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="bg-gray-200 text-center">
                                            <tr>
                                                <th class="text-left border p-2">Item</th>
                                                <th class="text-left border p-2">Customer</th>
                                                <th class="text-left border p-2">Qty</th>
                                                <th class="text-left border p-2">Price</th>
                                                <th class="text-left border p-2">Amount</th>
                                                <th class="text-left border p-2">Workcenter</th>
                                                <th class="text-left border p-2">Department</th>
                                                <th class="text-left border p-2">Month</th>
                                                <th class="text-left border p-2">R/NR</th>
                                                @if ($hasAction)
                                                    <th class="text-left border p-2">Action</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($submissions as $submission)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="border p-2">
                                                        {{ $submission->item != null ? $submission->item->itm_id : $submission->itm_id ?? '' }}
                                                    </td>
                                                    <td class="border p-2">{{ $submission->customer }}</td>
                                                    <td class="border p-2">{{ $submission->quantity }}</td>
                                                    <td class="border p-2">Rp
                                                        {{ number_format($submission->price, 0, ',', '.') }}</td>
                                                    <td class="border p-2">Rp
                                                        {{ number_format($submission->amount, 0, ',', '.') }}</td>
                                                    <td class="border p-2">
                                                        {{ $submission->workcenter != null ? $submission->workcenter->workcenter : '' }}
                                                    </td>
                                                    <td class="border p-2">
                                                        {{ $submission->dept != null ? $submission->dept->department : '' }}
                                                    </td>
                                                    <td class="border p-2">{{ $submission->month }}</td>
                                                    <td class="border p-2">
                                                        {{ $submission->budget != null ? $submission->budget->budget_name : '' }}
                                                    </td>
                                                    @if ($hasAction)
                                                        <td class="border p-2">
                                                            @if ($submission->status == 6)
                                                                <a href="#" data-id="{{ $submission->sub_id }}"
                                                                    data-itm-id="{{ $submission->id }}"
                                                                    class="inline-flex items-center justify-center p-2 text-red-600 hover:text-blue-800 open-edit-modal"
                                                                    title="Update">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <form
                                                                    action="{{ route('submissions.delete', ['sub_id' => $submission->sub_id, 'id' => $submission->id]) }}"
                                                                    method="POST" class="delete-form"
                                                                    data-item-count="{{ count($submissions) }}"
                                                                    style="display:inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="button" class="btn-delete"
                                                                        style="background: transparent; border: none; padding: 0; margin: 0; cursor: pointer;"
                                                                        title="Delete">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </td>
                                                    @endif
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="border p-2 text-center">
                                                        No
                                                        Submissions found!</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <br>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <button onclick="history.back()" type="button" class="btn btn-secondary me-2">
                                    <i class="fa-solid fa-arrow-left me-2"></i>Back</button>
                                <div class="d-flex gap-3">
                                    @if ($submission->status == 6)
                                        <form action="{{ route('submissions.submit', $submission->sub_id) }}"
                                            method="POST" class="approve-form">
                                            @csrf
                                            <button type="submit" class="btn text-white"
                                                style="background-color: #0080ff;">
                                                <i class="fa-solid fa-check me-2"></i> Approved
                                            </button>
                                        </form>
                                        <form action="{{ route('submissions.disapprove', $submission->sub_id) }}"
                                            method="POST" class="disapprove-form">
                                            @csrf
                                            <button type="submit" class="btn btn-secondary">
                                                <i class="fa-solid fa-xmark me-2"></i>DISAPPROVED
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- Add Item Modal -->
        <div id="addItemModal" class="modal fade" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white">Add New Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addItemForm" method="POST"
                            action="{{ route('submissions.add-item', $submission->sub_id) }}">
                            @csrf
                            <input type="hidden" name="sub_id" id="sub_id" value="{{ $submission->sub_id }}">
                            <input type="hidden" name="acc_id" id="acc_id"
                                value="{{ $submissions->first()->acc_id ?? '' }}">
                            <input type="hidden" name="purpose" id="purpose"
                                value="{{ $submission->purpose ?? '' }}">

                            <!-- Two-Column Layout for Six Fields -->
                            <div class="row">
                                <!-- Left Column -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Input Type <span
                                                class="text-danger">*</span></label>
                                        <select name="input_type" id="input_type" class="form-control select"
                                            required>
                                            <option value="select">Item GID</option>
                                            <option value="manual">Item Non-GID</option>
                                        </select>
                                    </div>
                                    <div class="mb-3" id="select_item_container">
                                        <label class="form-label">Item GID <span class="text-danger">*</span></label>
                                        <input type="text" name="itm_id" id="itm_id" class="form-control"
                                            placeholder="Enter Item GID" required>
                                    </div>
                                    <div class="mb-3" id="manual_item_container" style="display: none;">
                                        <label class="form-label">Item Non-GID <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="manual_item" id="manual_item"
                                            class="form-control" placeholder="Enter item name">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Customer <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="customer" id="customer" placeholder="Customer" required></textarea>
                                    </div>
                                </div>
                                <!-- Right Column -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="quantity" class="form-label">Quantity</label>
                                        <input type="number" name="quantity" id="quantity" class="form-control"
                                            required min="1" step="1">
                                    </div>
                                    <!-- Price -->
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price (IDR)</label>
                                        <input type="number" name="price" id="price" class="form-control"
                                            required min="0" step="0.01">
                                    </div>

                                    <!-- Workcenter -->
                                    <div class="mb-3">
                                        <label for="amountDisplay" class="form-label">Amount (IDR)</label>
                                        <input type="text" id="amountDisplay" class="form-control" readonly>
                                        <input type="hidden" name="amount" id="amount">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <!-- Single-Column Layout for Remaining Fields -->
                                <!-- Department -->
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Department <span
                                                class="text-danger">*</span></label>
                                        <input type="hidden" name="dpt_id" value="{{ $submission->dpt_id }}">
                                        <input class="form-control"
                                            value="{{ $submission->dept->department ?? '-' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">

                                    <!-- Budget (R/NR) -->
                                    <div class="mb-3">
                                        <label for="wct_id" class="form-label">Workcenter</label>
                                        <select name="wct_id" id="wct_id" class="form-control select" required>
                                            <option value="">-- Select Workcenter --</option>
                                            @foreach (\App\Models\Workcenter::orderBy('workcenter', 'asc')->get() as $workcenter)
                                                <option value="{{ $workcenter->wct_id }}">
                                                    {{ $workcenter->workcenter }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Month -->
                                <div class="col-md-3">

                                    <div class="mb-3">
                                        <label for="month" class="form-label">Month <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control select" name="month" id="month" required>
                                            <option value="">-- Select Month --</option>
                                            @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                                <option value="{{ $month }}" @selected(old('month') === $month)>
                                                    {{ $month }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">

                                    <!-- Budget (R/NR) -->
                                    <div class="mb-3">
                                        <label for="bdc_id" class="form-label">Budget (R/NR)</label>
                                        <select name="bdc_id" id="bdc_id" class="form-control select" required>
                                            <option value="">-- Select Budget Code --</option>
                                            @foreach (\App\Models\BudgetCode::orderBy('budget_name', 'asc')->get() as $budget)
                                                <option value="{{ $budget->bdc_id }}">{{ $budget->budget_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn text-white" style="background-color: #0080ff;">Add
                                    Item</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Container -->
        <div id="editModal" class="modal fade" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <!-- Konten modal akan dimuat di sini -->
            </div>
        </div>
        <div id="historyModal" class="modal fade" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white">Approval History</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- History content will be loaded here via AJAX -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add Remark Modal -->
        <div id="addRemarkModal" class="modal fade" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add/Edit Remark</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addRemarkForm" method="POST" action="{{ route('remarks.store') }}">
                            @csrf
                            <input type="hidden" name="sub_id" id="remark_sub_id" value="">
                            <div class="mb-3">
                                <label for="remark_text" class="form-label">Remark</label>
                                <textarea class="form-control" id="remark_text" name="remark" rows="4" placeholder="Enter your remark"
                                    required></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn text-white"
                                    style="background-color: #0080ff;">Submit Remark</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- View Remarks Modal -->
        <div id="historyremarkModal" class="modal fade" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white">Remarks History</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- History content will be loaded here via AJAX -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $(document).ready(function() {
                // Initialize Select2 for all select elements
                $('.select').select({
                    width: '100%',
                    dropdownParent: $('#addItemModal, #editModal')
                });

                // Toggle input type for Add Item Modal
                $('#addItemModal #input_type').on('change', function() {
                    if ($(this).val() === 'select') {
                        $('#addItemModal #select_item_container').show();
                        $('#addItemModal #manual_item_container').hide();
                        $('#addItemModal #itm_id').prop('required', true);
                        $('#addItemModal #manual_item').prop('required', false);
                        $('#addItemModal #description').val('');
                    } else {
                        $('#addItemModal #select_item_container').hide();
                        $('#addItemModal #manual_item_container').show();
                        $('#addItemModal #itm_id').prop('required', false);
                        $('#addItemModal #manual_item').prop('required', true);
                        $('#addItemModal #description').val('');
                    }
                });

                $('#addItemModal #itm_id').on('input.uppercase', function() {
                    $(this).val($(this).val().toUpperCase());
                });

                $('#addItemModal #itm_id').on('blur', function() {
                    const itm_id = $(this).val().trim();
                    if (itm_id && $('#addItemModal #input_type').val() === 'select') {
                        $.ajax({
                            url: '{{ route('accounts.getItemName') }}',
                            method: 'POST',
                            data: {
                                itm_id: itm_id,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.item) {
                                    $('#addItemModal #description').val(response.item.item);
                                } else {
                                    $('#addItemModal #itm_id').val('');
                                    $('#addItemModal #description').val('');
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Item GID Not Found',
                                    });
                                }
                            },
                            error: function() {
                                $('#addItemModal #itm_id').val('');
                                $('#addItemModal #description').val('');
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Item GID Not Found',
                                });
                                console.error('AJAX Error:', status, error, xhr
                                    .responseText);
                            }
                        });
                    }
                });

                // Calculate amount dynamically for Add Item Modal
                $('#addItemModal').on('input', '#quantity, #price', function() {
                    const quantity = parseFloat($('#addItemModal #quantity').val()) || 0;
                    const price = parseFloat($('#addItemModal #price').val()) || 0;
                    const amount = quantity * price;

                    $('#addItemModal #amountDisplay').val('IDR ' + amount.toLocaleString('id-ID', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                    $('#addItemModal #amount').val(amount.toFixed(2));
                });

                // Handle opening the Add Item modal
                $(document).on('click', '.open-add-item-modal', function(e) {
                    e.preventDefault();
                    var subId = $(this).data('sub-id');
                    var modal = $('#addItemModal');

                    // Set the sub_id in the form
                    modal.find('#sub_id').val(subId);
                    modal.modal('show');

                    // Initialize Select2 in the modal
                    modal.find('.select').select({
                        width: '100%',
                        dropdownParent: modal
                    });

                    // Reset form fields
                    modal.find('#addItemForm')[0].reset();
                    modal.find('#amountDisplay').val('');
                    modal.find('#input_type').val('select').trigger('change'); // Reset to select
                });

                // Handle Add Item form submission
                $(document).on('submit', '#addItemForm', function(e) {
                    e.preventDefault();
                    var form = $(this);

                    $.ajax({
                        url: form.attr('action'),
                        method: form.attr('method'),
                        data: form.serialize(),
                        success: function(response) {
                            if (response.success) {
                                $('#addItemModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Item added successfully.',
                                    confirmButtonColor: '#3085d6'
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = xhr.responseJSON.message || 'Failed to add item.';
                            if (xhr.status === 422 && xhr.responseJSON.errors) {
                                errorMessage = Object.values(xhr.responseJSON.errors).flat().join(
                                    '\n');
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: errorMessage,
                                confirmButtonColor: '#d33'
                            });
                        }
                    });
                });

                // Handle Edit modal loading
                $(document).on('click', '.open-edit-modal', function(e) {
                    e.preventDefault();
                    var subId = $(this).data('id');
                    var itmId = $(this).data('itm-id');
                    var modal = $('#editModal');

                    // Load modal content via AJAX
                    $.get('/submissions/' + subId + '/id/' + itmId + '/edit', function(data) {
                        modal.find('.modal-dialog').html(data);
                        modal.modal('show');

                        // Initialize Select2 in the edit modal
                        modal.find('.select').select({
                            width: '100%',
                            dropdownParent: modal
                        });
                    }).fail(function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to load edit form.',
                            confirmButtonColor: '#d33'
                        });
                    });
                });

                // Handle Edit form submission
                $(document).on('submit', '#editModal form', function(e) {
                    e.preventDefault();
                    var form = $(this);

                    $.ajax({
                        url: form.attr('action'),
                        method: form.attr('method'),
                        data: form.serialize(),
                        success: function(response) {
                            if (response.success) {
                                $('#editModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Data has been updated successfully.',
                                    confirmButtonColor: '#3085d6'
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = xhr.responseJSON.message || 'Failed to update item.';
                            if (xhr.status === 422 && xhr.responseJSON.errors) {
                                errorMessage = Object.values(xhr.responseJSON.errors).flat().join(
                                    '\n');
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: errorMessage,
                                confirmButtonColor: '#d33'
                            });
                        }
                    });
                });

                // Handle History modal loading
                $(document).on('click', '.open-history-modal', function(e) {
                    e.preventDefault();
                    var subId = $(this).data('id');
                    var modal = $('#historyModal');

                    // Show loading state
                    modal.find('.modal-body').html(
                        '<div class="text-center py-4"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>'
                    );
                    modal.modal('show');

                    // Load history content
                    $.get('/approvals/history/' + subId)
                        .done(function(data) {
                            modal.find('.modal-body').html(data);
                        })
                        .fail(function() {
                            modal.find('.modal-body').html(
                                '<div class="alert alert-danger">Failed to load approval history</div>'
                            );
                        });
                });

                // Handle opening the Add Remark modal
                // Handle opening the Add/Edit Remark modal
                $(document).on('click', '.open-add-remark-modal', function(e) {
                    e.preventDefault();
                    var subId = $(this).data('id');
                    var modal = $('#addRemarkModal');

                    // Set the sub_id in the form
                    modal.find('#remark_sub_id').val(subId);

                    // Clear the textarea first
                    modal.find('#remark_text').val('');

                    // Get existing remark if any
                    $.get('/remarks/get-remarks/' + subId, function(response) {
                        if (response.remarks && response.remarks.length > 0) {
                            modal.find('#remark_text').val(response.remarks[0].remark);
                        }
                    }).fail(function() {
                        console.log('Failed to load remarks');
                    });

                    modal.modal('show');
                });
                // Handle Add Remark form submission
                $(document).on('submit', '#addRemarkForm', function(e) {
                    e.preventDefault();
                    var form = $(this);

                    $.ajax({
                        url: form.attr('action'),
                        method: form.attr('method'),
                        data: form.serialize(),
                        success: function(response) {
                            if (response.success) {
                                $('#addRemarkModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Remark added successfully.',
                                    confirmButtonColor: '#3085d6'
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = xhr.responseJSON.message || 'Failed to add remark.';
                            if (xhr.status === 422 && xhr.responseJSON.errors) {
                                errorMessage = Object.values(xhr.responseJSON.errors).flat().join(
                                    '\n');
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: errorMessage,
                                confirmButtonColor: '#d33'
                            });
                        }
                    });
                });

                // Handle Remarks History modal loading
                $(document).on('click', '.open-historyremark-modal', function(e) {
                    e.preventDefault();
                    var subId = $(this).data('id');
                    var modal = $('#historyremarkModal');

                    // Show loading state
                    modal.find('.modal-body').html(
                        '<div class="text-center py-4"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>'
                    );
                    modal.modal('show');

                    // Load remarks history content
                    $.get('/remarks/remark/' + subId)
                        .done(function(data) {
                            modal.find('.modal-body').html(data);
                        })
                        .fail(function() {
                            modal.find('.modal-body').html(
                                '<div class="alert alert-danger">Failed to load remarks history</div>'
                            );
                        });
                });

                // Handle Delete form submission
                $(document).on('click', '.btn-delete', function() {
                    const form = $(this).closest('form');
                    const itemCount = form.data('item-count');

                    if (itemCount <= 1) {
                        Swal.fire({
                            title: 'Warning!',
                            text: 'There must be at least one item in the submission. You cannot delete the last item.',
                            icon: 'warning',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#d33'
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: form.attr('action'),
                                method: form.attr('method'),
                                data: form.serialize(),
                                success: function(response) {
                                    if (response.success) {
                                        Swal.fire(
                                            'Deleted!',
                                            response.message,
                                            'success'
                                        ).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire(
                                            'Error!',
                                            response.message,
                                            'error'
                                        );
                                    }
                                },
                                error: function(xhr) {
                                    Swal.fire(
                                        'Error!',
                                        xhr.responseJSON.message ||
                                        'Something went wrong',
                                        'error'
                                    );
                                }
                            });
                        }
                    });
                });

                // Handle Send form submission
                $(document).on('submit', '.send-form', function(e) {
                    e.preventDefault();
                    var form = $(this);

                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'Do you want to send this submission?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, send it!',
                        cancelButtonText: 'No, cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: form.attr('action'),
                                method: form.attr('method'),
                                data: form.serialize(),
                                success: function(response, status, xhr) {
                                    if (xhr.status === 200 || xhr.status === 302) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success!',
                                            text: 'Submission sent successfully.',
                                            confirmButtonColor: '#3085d6'
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error!',
                                            text: 'Failed to send submission.',
                                            confirmButtonColor: '#d33'
                                        });
                                    }
                                },
                                error: function(xhr) {
                                    let errorMessage = 'Something went wrong.';
                                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                        errorMessage = Object.values(xhr.responseJSON
                                            .errors).flat().join(' ');
                                    } else if (xhr.responseJSON?.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: errorMessage,
                                        confirmButtonColor: '#d33'
                                    });
                                }
                            });
                        }
                    });
                });

                // Handle Approve form submission
                $(document).on('submit', '.approve-form', function(e) {
                    e.preventDefault();
                    var form = $(this);

                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'Do you want to approve this submission?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, approve it!',
                        cancelButtonText: 'No, cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: form.attr('action'),
                                method: form.attr('method'),
                                data: form.serialize(),
                                success: function(response, status, xhr) {
                                    if (xhr.status === 200 || xhr.status === 302) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success!',
                                            text: 'Submission approved successfully.',
                                            confirmButtonColor: '#3085d6'
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error!',
                                            text: 'Failed to approve submission.',
                                            confirmButtonColor: '#d33'
                                        });
                                    }
                                },
                                error: function(xhr) {
                                    let errorMessage = 'Something went wrong.';
                                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                        errorMessage = Object.values(xhr.responseJSON
                                            .errors).flat().join(' ');
                                    } else if (xhr.responseJSON?.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: errorMessage,
                                        confirmButtonColor: '#d33'
                                    });
                                }
                            });
                        }
                    });
                });

                // Handle Disapprove form submission
                $(document).on('submit', '.disapprove-form', function(e) {
                    e.preventDefault();
                    var form = $(this);

                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'Do you want to disapprove this submission?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, disapprove it!',
                        cancelButtonText: 'No, cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: form.attr('action'),
                                method: form.attr('method'),
                                data: form.serialize(),
                                success: function(response, status, xhr) {
                                    if (xhr.status === 200 || xhr.status === 302) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success!',
                                            text: 'Submission disapproved successfully.',
                                            confirmButtonColor: '#3085d6'
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error!',
                                            text: 'Failed to disapprove submission.',
                                            confirmButtonColor: '#d33'
                                        });
                                    }
                                },
                                error: function(xhr) {
                                    let errorMessage = 'Something went wrong.';
                                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                        errorMessage = Object.values(xhr.responseJSON
                                            .errors).flat().join(' ');
                                    } else if (xhr.responseJSON?.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: errorMessage,
                                        confirmButtonColor: '#d33'
                                    });
                                }
                            });
                        }
                    });
                });
            });
        </script>
        <x-footer></x-footer>
    </main>
</body>

</html>
