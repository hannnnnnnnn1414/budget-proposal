<!DOCTYPE html>
<html lang="en">

<x-head></x-head>

<body class="g-sidenav-show bg-gray-100">
    <x-sidebar></x-sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
        <x-navbar :notifications="$notifications">
            Submissions</x-navbar>
        <div class="container-fluid ">
            <div class="row">
                <!-- Purpose Card -->
                <div class="col-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0 text-white">Purpose of Submission</h5>
                        </div>
                        <div class="card-body">
                            <form id="mainForm" method="POST" action="{{ route('accounts.store') }}">
                                @csrf
                                <input type="hidden" name="acc_id" value="{{ $account->acc_id }}">
                                <textarea class="form-control" name="purpose" id="purpose" rows="3" placeholder="Enter purpose of submission"
                                    required>{{ old('purpose', session('purpose')) }}</textarea>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0 text-white">Proposal CAPEX</h5>
                        </div>
                        <div class="card-body d-flex align-items-center flex-wrap">
                            <button type="button" class="btn btn-danger me-3" data-bs-toggle="modal"
                                data-bs-target="#pdfUploadModal">
                                <i class="fas fa-file-pdf me-2"></i>Upload PDF
                            </button>
                            <div id="pdfPreviewContainer" class="d-flex flex-wrap align-items-center">
                                @if (session('pdf_attachment'))
                                    @foreach (session('pdf_attachment') as $index => $pdf)
                                        <span class="pdf-preview-item me-3 mb-2">
                                            <span>{{ $pdf['name'] }}</span>
                                            <button type="button" class="btn btn-sm btn-danger ms-2 remove-pdf"
                                                data-index="{{ $index }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </span>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>


                <!-- PDF Upload Modal -->
                <div class="modal fade" id="pdfUploadModal" tabindex="-1" aria-labelledby="pdfUploadModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title text-white" id="pdfUploadModalLabel">Upload Document</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form id="pdfUploadForm" method="POST" action="{{ route('accounts.uploadPdf') }}"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="pdfFile" class="form-label">Select PDF File</label>
                                        <input class="form-control" type="file" id="pdfFile" name="pdf_file"
                                            accept=".pdf" required>
                                    </div>
                                    {{-- <div class="mb-3">
                                        <label for="pdfDescription" class="form-label">Description</label>
                                        <input type="text" class="form-control" id="pdfDescription"
                                            name="pdf_description" placeholder="Enter document description">
                                    </div> --}}
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-danger">Upload</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Items Card -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0 text-white">Items of Submission</h5>
                        </div>
                        <div id="accounts">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="itemsTable">
                                        <thead>
                                            <tr>
                                                <th width="5%">No.</th>
                                                <th>Item</th>
                                                <th>Asset Class</th>
                                                <th>Prioritas</th>
                                                <th>Alasan</th>
                                                <th>Keterangan</th>
                                                <th width="15%">Qty</th>
                                                <th width="15%">Price</th>
                                                <th width="15%">Amount</th>
                                                <th>Workcenter</th>
                                                <th>Department</th>
                                                <th>Month</th>
                                                <th width="10%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (session('temp_data'))
                                                @foreach (session('temp_data') as $index => $data)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $data['itm_id'] }}</td>

                                                        {{-- <td>{{ $items[$data['itm_id']] ?? $data['itm_id'] }}</td> --}}
                                                        <td>{{ $data['asset_class'] }}</td>
                                                        <td>{{ $data['prioritas'] }}</td>
                                                        <td>{{ $data['alasan'] }}</td>
                                                        <td>{{ $data['keterangan'] }}</td>
                                                        <td>{{ $data['quantity'] }}</td>
                                                        <td class="text-end">
                                                            {{ isset($data['price']) ? 'IDR ' . number_format($data['price'], 2) : '-' }}
                                                        </td>
                                                        <td class="text-end">
                                                            @if (isset($data['quantity']) && isset($data['price']))
                                                                {{ 'IDR ' . number_format($data['quantity'] * $data['price'], 2) }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {{ $workcenters[$data['wct_id']] ?? $data['wct_id'] }}
                                                        </td>
                                                        <td>
                                                            {{ $departments[$data['dpt_id']] ?? $data['dpt_id'] }}
                                                        </td>
                                                        <td>{{ $data['month'] ?? '-' }}</td>
                                                        <td class="text-center">
                                                            <form method="POST"
                                                                action="{{ route('accounts.removeTempData', $index) }}"
                                                                style="display:inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger"
                                                                    title="Delete">
                                                                    <i class="fa-solid fa-trash fs-6"></i>
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="9" class="text-center text-muted">No items
                                                        added
                                                        yet
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Cancel Confirmation Modal -->
                                <div class="modal fade" id="cancelConfirmModal" tabindex="-1"
                                    aria-labelledby="cancelConfirmModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content text-center p-4" style="border-radius: 15px;">
                                            <div class="mx-auto mb-3"
                                                style="width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                <i class="fa-solid fa-circle-exclamation fa-2xl"
                                                    style="color: #ff0000;"></i>
                                            </div>
                                            <h5 class="modal-title fw-bold mb-2" id="cancelConfirmModalLabel">
                                                Cancel
                                                Submission</h5>
                                            <p class="mb-4">Are you sure you want to cancel the
                                                submission?<br>All
                                                entered data will be lost.</p>
                                            <div class="d-flex justify-content-center gap-2">
                                                <button type="button" class="btn btn-light px-4"
                                                    data-bs-dismiss="modal">No</button>
                                                <form action="{{ route('accounts.cancel') }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger px-4">Yes</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" id="addItemBtn" class="btn text-white"
                                        style="background-color: #0080ff">
                                        <i class="fas fa-plus me-2"></i>Add Item
                                    </button>
                                    <div class="d-flex">
                                        <button type="button" id="cancelBtn" class="btn btn-secondary me-2"
                                            data-bs-toggle="modal"
                                            data-bs-target="#cancelConfirmModal">Cancel</button>
                                        <button type="submit" form="mainForm" class="btn btn-danger">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Item Modal -->
        <div class="modal fade" id="itemModal" tabindex="-1" aria-labelledby="itemModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white" id="itemModalLabel">Add New Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form id="itemForm" method="POST" action="{{ route('accounts.addTempData') }}">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="acc_id" value="{{ $account->acc_id }}">
                            <input type="hidden" name="purpose" id="modal_purpose"
                                value="{{ old('purpose', session('purpose')) }}">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Item Type</label><span class="text-danger">*</span>
                                        <select name="input_type" id="input_type" class="form-control" required>
                                            <option value="select">Item GID</option>
                                            <option value="manual">Item Non-GID</option>
                                        </select>
                                    </div>
                                    <div class="mb-3" id="select_item_container">
                                        <label class="form-label">Item GID</label><span class="text-danger">*</span>
                                        <select name="itm_id" id="itm_id" class="form-control select2" required>
                                            <option value="">-- Select Item --</option>
                                            @foreach ($items as $itm_id => $item_name)
                                                <option value="{{ $itm_id }}">{{ $itm_id }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3" id="manual_item_container" style="display: none;">
                                        <label class="form-label">Item Non-GID</label><span
                                            class="text-danger">*</span>
                                        <input type="text" name="manual_item" id="manual_item"
                                            class="form-control" placeholder="Enter item name">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Keterangan</label><span class="text-danger">*</span>
                                        <textarea class="form-control" name="keterangan" id="keterangan" placeholder="Keterangan" required>{{ old('keterangan') }}</textarea>
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Quantity</label><span class="text-danger">*</span>
                                        <input type="number" class="form-control" name="quantity" id="quantity"
                                            value="{{ old('quantity') }}" placeholder="Quantity" required>
                                    </div>
                                    {{-- <div class="mb-3">
                                        <label class="form-label">Price</label><span class="text-danger">*</span>
                                        <input type="number" step="0.01" class="form-control" name="price"
                                            id="price" value="{{ old('price') }}" placeholder="Price" required>
                                    </div> --}}
                                    <div class="row mb-3">
                                        <!-- Currency -->
                                        <div class="col-md-6">
                                            <label for="cur_id" class="form-label">Currency <span
                                                    class="text-danger">*</span></label>
                                            <select name="cur_id" id="cur_id" class="form-select" required>
                                                <option value="">Rp</option>
                                                @foreach ($currencies as $cur_id => $currency)
                                                    <option value="{{ $cur_id }}">{{ $currency }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Price -->
                                        <div class="col-md-6">
                                            <label for="price" class="form-label">Price <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" class="form-control" required id="price"
                                                name="price" value="{{ old('price', $submission->price ?? 0) }}"
                                                placeholder="Price" step="0.01" min="0">
                                        </div>
                                    </div>
                                    <div id="currencyInfo" class="form-text text-muted" style="display: none;"></div>

                                    <div class="mb-3">
                                        <label class="form-label">Amount</label><span class="text-danger">*</span>
                                        <input type="text" class="form-control" id="amountDisplay" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <!-- Kolom pertama -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Asset Class</label><span
                                            class="text-danger">*</span>
                                        <select class="form-control" name="asset_class" id="asset_class" required>
                                            <option value="">-- Select Asset Class --</option>
                                            <option value="170">170 - Landright</option>
                                            <option value="171">171 - Infrastructure</option>
                                            <option value="173">173 - Building Improvement</option>
                                            <option value="174">174 - Building Equipment</option>
                                            <option value="175">175 - Machinery Eqp</option>
                                            <option value="176">176 - Accessories</option>
                                            <option value="177">177 - Office Equipment</option>
                                            <option value="178">178 - Transportation</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Kolom kedua -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Prioritas</label><span class="text-danger">*</span>
                                        <select class="form-control" name="prioritas" id="prioritas" required>
                                            <option value="">-- Select Prioritas --</option>
                                            <option value="H">H - High</option>
                                            <option value="M">M - Medium</option>
                                            <option value="L">L - Low</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Kolom ketiga -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Alasan</label><span class="text-danger">*</span>
                                        <select class="form-control" name="alasan" id="alasan" required>
                                            <option value="">-- Select Alasan --</option>
                                            <option value="1">1 - Penambahan</option>
                                            <option value="2">2 - Penggantian</option>
                                            <option value="3">3 - Model Baru</option>
                                            <option value="4">4 - Quality Control</option>
                                            <option value="5">5 - Local Component</option>
                                            <option value="6">6 - Keselamatan Kerja</option>
                                            <option value="7">7 - Peningkatan produk</option>
                                            <option value="8">8 - Others</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                {{-- <div class="col-md-6">

                                    <div class="mb-3">
    <label class="form-label">Description</label><span class="text-danger">*</span>
    <textarea class="form-control" name="description" id="description" placeholder="Description" required readonly></textarea>
</div>
                                </div> --}}
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Workcenter</label>
                                        <select name="wct_id" class="form-control" id="wct_id">
                                            <option value="">-- Workcenter --</option>
                                            @foreach ($workcenters as $wctID => $workcenter)
                                                <option value="{{ $wctID }}" @selected(old('wct_id') == $wctID)>
                                                    {{ $workcenter }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Department</label><span class="text-danger">*</span>
                                        <input type="hidden" name="dpt_id" value="{{ Auth::user()->dept }}">
                                        <input class="form-control"
                                            value="{{ Auth::user()->department->department ?? '-' }}" readonly>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Month</label><span class="text-danger">*</span>
                                        <select class="form-control" name="month" id="month" required>
                                            <option value="">-- Select Month --</option>
                                            @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                                <option value="{{ $month }}" @selected(old('month') === $month)>
                                                    {{ $month }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger">Add Item</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Select2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <!-- Select2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link
            href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
            rel="stylesheet" />

        <script>
            $(document).ready(function() {

                $('#itemModal').on('shown.bs.modal', function() {
                    $('#itm_id').select2({
                        dropdownParent: $('#itemModal'),
                        allowClear: true,
                        placeholder: '-- Select Item --',
                        width: '100%',
                        theme: 'bootstrap-5'

                    });

                    $('#cur_id').select2({
                        dropdownParent: $('#itemModal'),
                        allowClear: true,
                        placeholder: 'Rp',
                        width: '150%',
                        theme: 'bootstrap-5'
                    });

                    // Ubah tinggi Select2 biar sama seperti input
                    $('.select2-selection--single').css({
                        'height': $('#price').outerHeight() + 'px',
                        'display': 'flex',
                        'align-items': 'center'
                    });
                    $('.select2-selection__rendered').css({
                        'line-height': $('#price').outerHeight() + 'px'
                    });

                    $.ajax({
                        url: '{{ route('accounts.getCurrencies') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            currencies = response.currencies;
                            let options = '<option value="">-- Select Currency --</option>';
                            currencies.forEach(currency => {
                                options +=
                                    `<option value="${currency.cur_id}" data-nominal="${currency.nominal}">${currency.currency}</option>`;
                            });
                            $('#cur_id').html(options);
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to load currencies',
                            });
                        }
                    });
                });
                // Toggle input type
                $('#input_type').on('change', function() {
                    if ($(this).val() === 'select') {
                        $('#select_item_container').show();
                        $('#manual_item_container').hide();
                        $('#itm_id').prop('required', true);
                        $('#manual_item').prop('required', false);
                        $('#description').val('').prop('readonly',
                            true); // Clear and make readonly for GID items
                    } else {
                        $('#select_item_container').hide();
                        $('#manual_item_container').show();
                        $('#itm_id').prop('required', false);
                        $('#manual_item').prop('required', true);
                        $('#description').val('').prop('readonly',
                            false); // Clear and make editable for non-GID items
                    }
                });


                // Fetch item name when itm_id is entered
                // $('#itm_id').on('change', function() {
                //     const itm_id = $(this).val().trim();
                //     if (itm_id && $('#input_type').val() === 'select') {
                //         $.ajax({
                //             url: '{{ route('accounts.getItemName') }}',
                //             method: 'POST',
                //             data: {
                //                 itm_id: itm_id,
                //                 _token: '{{ csrf_token() }}'
                //             },
                //             success: function(response) {
                //                 if (response.item) {
                //                     $('#description').val(response.item.item);
                //                 } else {
                //                     $('#description').val('');
                //                     alert('Item not found');
                //                 }
                //             },
                //             error: function() {
                //                 $('#description').val('');
                //                 alert('Error fetching item name');
                //             }
                //         });
                //     }
                // });

                $('#itm_id').on('change', function() {
                    const itm_id = $(this).val().trim();
                    if (itm_id && $('#input_type').val() === 'select') {
                        $.ajax({
                            url: '{{ route('accounts.getItemName') }}',
                            method: 'POST',
                            data: {
                                itm_id: itm_id,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.item && response.item.item) {
                                    $('#description').val(response.item.item).prop('readonly',
                                    true);
                                } else {
                                    $('#itm_id').val('');
                                    $('#description').val('').prop('readonly', true);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Item GID Not Found',
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                $('#itm_id').val('');
                                $('#description').val('').prop('readonly', true);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Item GID Not Found',
                                });
                                console.error('AJAX Error:', status, error, xhr.responseText);
                            }
                        });
                    }
                });


                // Calculate amount
                $('#quantity, #price, #cur_id').on('input', function() {
                    const quantity = parseFloat($('#quantity').val()) || 0;
                    const price = parseFloat($('#price').val()) || 0;
                    const cur_id = $('#cur_id').val();
                    const currency = currencies.find(c => c.cur_id === cur_id);
                    const nominal = currency ? parseFloat(currency.nominal) : 1;
                    const amount = quantity * price * nominal;

                    // Format amount with IDR currency
                    $('#amountDisplay').val('IDR ' + amount.toLocaleString('id-ID', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                });

                // Handle form submission
                $('#itemForm').on('submit', function(e) {
                    const quantity = parseFloat($('#quantity').val()) || 0;
                    const price = parseFloat($('#price').val()) || 0;
                    const cur_id = $('#cur_id').val();
                    const currency = currencies.find(c => c.cur_id === cur_id);
                    const nominal = currency ? parseFloat(currency.nominal) : 1;
                    const amount = quantity * price * nominal;

                    // Create a hidden input for amount just before submission
                    $(this).append(`<input type="hidden" name="amount" value="${amount}">`);
                });

                $('#mainForm').on('submit', function(e) {
                    const accId = $(this).find('input[name="acc_id"]').val();
                    const pdfCount = $('#pdfPreviewContainer .pdf-preview-item').length;

                    if (accId === 'CAPEX' && pdfCount === 0) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Proposal CAPEX is required for this submissions.',
                            confirmButtonColor: '#d33'
                        });
                        return false;
                    }
                });

                // Add Item button click handler
                $('#addItemBtn').click(function() {
                    if ($('#purpose').val().trim() === '') {
                        alert('Please enter the purpose first');
                        $('#purpose').focus();
                        return;
                    }

                    $('#modal_purpose').val($('#purpose').val());
                    var modal = new bootstrap.Modal(document.getElementById('itemModal'));
                    modal.show();
                });

                // Update purpose in modal when changed in main form
                $('#purpose').on('input change', function() {
                    $('#modal_purpose').val($(this).val());
                });

                $('#pdfUploadForm').on('submit', function(e) {
                    e.preventDefault();

                    var formData = new FormData(this);

                    $.ajax({
                        url: $(this).attr('action'),
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                // Update the PDF preview
                                $('#pdfPreviewContainer').empty();
                                response.pdfs.forEach(function(pdf, index) {
                                    $('#pdfPreviewContainer').append(`
                                        <div class="pdf-preview-item mb-2">
                                            <span>${pdf.name}</span>
                                            <button type="button" class="btn btn-sm btn-danger ms-2 remove-pdf" data-index="${index}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    `);
                                });

                                $('#pdfUploadModal').modal('hide');
                                $('#pdfUploadForm')[0].reset();
                            }
                        },
                        error: function(xhr) {
                            alert('Error uploading PDF: ' + xhr.responseJSON.message);
                        }
                    });
                });

                // Handle PDF removal
                $(document).on('click', '.remove-pdf', function() {
                    var index = $(this).data('index');

                    $.ajax({
                        url: '{{ route('accounts.removePdf') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            index: index
                        },
                        success: function(response) {
                            if (response.success) {
                                // Update the PDF preview
                                $('#pdfPreviewContainer').empty();
                                response.pdfs.forEach(function(pdf, index) {
                                    $('#pdfPreviewContainer').append(`
                                        <div class="pdf-preview-item mb-2">
                                            <span>${pdf.name}</span>
                                            <button type="button" class="btn btn-sm btn-danger ms-2 remove-pdf" data-index="${index}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    `);
                                });
                            }
                        },
                        error: function(xhr) {
                            alert('Error removing PDF: ' + xhr.responseJSON.message);
                        }
                    });
                });
            });
        </script>
    </main>
</body>

</html>
