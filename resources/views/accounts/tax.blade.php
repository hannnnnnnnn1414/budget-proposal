<!DOCTYPE html>
<html lang="en">

<x-head></x-head>

<body class="g-sidenav-show bg-gray-100">
    <x-sidebar></x-sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
        <x-navbar :notifications="$notifications">
            Submissions</x-navbar>
        <div class="container-fluid ">
            @if ($acc_id == 'FOHTAXPUB' || $acc_id == 'SGATAXPUB')
                <div class="row">
                    <!-- Purpose Card -->
                    <div class="col-12 mb-4">
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
                                                    <th>Description</th>
                                                    <th width="15%">Price</th> <!-- [MODIFIKASI] Hapus Qty -->
                                                    <th width="15%">Amount</th>
                                                    <th>Workcenter</th>
                                                    <th>Department</th>
                                                    <th>Month</th>
                                                    <th width="10%">Action</th> <!-- [MODIFIKASI] Hapus R/NR -->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (session('temp_data'))
                                                    @foreach (session('temp_data') as $index => $data)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $data['itm_id'] ?? '-' }}</td>
                                                            <!-- [MODIFIKASI] Gunakan itm_id langsung -->
                                                            <td>{{ $data['description'] }}</td>
                                                            <td class="text-end">
                                                                {{ isset($data['price']) ? 'IDR ' . number_format($data['price'], 2) : '-' }}
                                                            </td>
                                                            <td class="text-end">
                                                                @if (isset($data['amount']))
                                                                    {{ 'IDR ' . number_format($data['amount'], 2) }}
                                                                @else
                                                                    -
                                                                @endif
                                                                <!-- [MODIFIKASI] Gunakan amount langsung -->
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
                                                        <td colspan="8" class="text-center text-muted">No items added
                                                            yet</td>
                                                        <!-- [MODIFIKASI] Ubah colspan ke 8 karena Qty dan R/NR dihapus -->
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- ... Cancel Confirmation Modal tetap sama ... -->
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

                <!-- Add Item Modal -->
                <div class="modal fade" id="itemModal" tabindex="-1" aria-labelledby="itemModalLabel"
                    aria-hidden="true">
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
                                                <label class="form-label">Item</label><span class="text-danger">*</span>
                                                <input type="text" name="itm_id" id="itm_id" class="form-control"
                                                    placeholder="Enter item name" required value="{{ old('itm_id') }}">
                                                <!-- [MODIFIKASI] Ganti input_type, Item GID, dan Item Non-GID dengan input text itm_id -->
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Description</label><span
                                                    class="text-danger">*</span>
                                                <textarea class="form-control" name="description" id="description" placeholder="Description" required>{{ old('description') }}</textarea> <!-- [MODIFIKASI] Hapus readonly -->
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row mb-3">
                                                <!-- Currency -->
                                                <div class="col-md-6">
                                                    <label for="cur_id" class="form-label">Currency <span
                                                            class="text-danger">*</span></label>
                                                    <select name="cur_id" id="cur_id" class="form-select"
                                                        required>
                                                        <option value="" data-nominal="1" selected>Rp</option>
                                                        @foreach ($currencies as $cur_id => $currency)
                                                            <option value="{{ $cur_id }}"
                                                                data-nominal="{{ $currency['nominal'] }}">
                                                                {{ $currency['currency'] }}</option>
                                                            <!-- [MODIFIKASI] Gunakan array currency dan nominal -->
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- Price -->
                                                <div class="col-md-6">
                                                    <label for="price" class="form-label">Price <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" required
                                                        id="price" name="price"
                                                        value="{{ old('price', $submission->price ?? 0) }}"
                                                        placeholder="Price" step="0.01" min="0">
                                                </div>
                                            </div>
                                            <div id="currencyInfo" class="form-text text-muted"
                                                style="display: none;"></div>
                                            <!-- [MODIFIKASI] Tambah currencyInfo untuk konversi -->
                                            <div class="mb-3">
                                                <label class="form-label">Amount</label><span
                                                    class="text-danger">*</span>
                                                <input type="text" class="form-control" id="amountDisplay"
                                                    readonly>
                                                <input type="hidden" name="amount" id="amount">
                                                <!-- [MODIFIKASI] Tambah hidden input untuk amount -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Workcenter</label>
                                                <select name="wct_id" id="wct_id" class="form-select">
                                                    <!-- [MODIFIKASI] Gunakan form-select -->
                                                    <option value="">-- Workcenter --</option>
                                                    @foreach ($workcenters as $wctID => $workcenter)
                                                        <option value="{{ $wctID }}"
                                                            @selected(old('wct_id') == $wctID)>
                                                            {{ $workcenter }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Department</label><span
                                                    class="text-danger">*</span>
                                                <input type="hidden" name="dpt_id"
                                                    value="{{ Auth::user()->dept }}">
                                                <input class="form-control"
                                                    value="{{ Auth::user()->department->department ?? '-' }}"
                                                    readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Month</label><span
                                                    class="text-danger">*</span>
                                                <select class="form-control" name="month" id="month" required>
                                                    <option value="">-- Select Month --</option>
                                                    @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                                        <option value="{{ $month }}"
                                                            @selected(old('month') === $month)>
                                                            {{ $month }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-danger">Add Item</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
            <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <link
                href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
                rel="stylesheet" />
            <script>
                $(document).ready(function() {
                    let currencies = [];

                    $('#itemModal').on('shown.bs.modal', function() {
                        $('#cur_id').select2({
                            dropdownParent: $('#itemModal'),
                            allowClear: true,
                            placeholder: '-- Select Currency --',
                            width: '100%',
                            theme: 'bootstrap-5'
                        });

                        $('#wct_id').select2({
                            dropdownParent: $('#itemModal'),
                            allowClear: true,
                            placeholder: '-- Select Workcenter --',
                            width: '100%',
                            theme: 'bootstrap-5'
                        });

                        $('#month').select2({
                            dropdownParent: $('#itemModal'),
                            allowClear: true,
                            placeholder: '-- Select Month --',
                            width: '100%',
                            theme: 'bootstrap-5'
                        });

                        // [MODIFIKASI] Ubah tinggi Select2 agar sama seperti input
                        $('.select2-selection--single').css({
                            'height': $('#price').outerHeight() + 'px',
                            'display': 'flex',
                            'align-items': 'center'
                        });
                        $('.select2-selection__rendered').css({
                            'line-height': $('#price').outerHeight() + 'px'
                        });

                        // [MODIFIKASI] Load currencies via AJAX
                        $.ajax({
                            url: '{{ route('accounts.getCurrencies') }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                currencies = response.currencies || [];
                                let options = '<option value="" data-nominal="1" selected>Rp</option>';
                                currencies.forEach(currency => {
                                    const nominal = currency.nominal ??
                                    1; // [MODIFIKASI] Fallback jika nominal tidak ada
                                    options +=
                                        `<option value="${currency.cur_id}" data-nominal="${nominal}">${currency.currency}</option>`;
                                });
                                $('#cur_id').html(options).trigger('change');
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

                    // [MODIFIKASI] Handle currency change untuk menampilkan info konversi
                    $('#cur_id').on('change', function() {
                        const cur_id = $(this).val();
                        const currency = currencies.find(c => c.cur_id === cur_id);
                        const nominal = currency && currency.nominal ? parseFloat(currency.nominal) :
                        1; // [MODIFIKASI] Validasi nominal
                        const currencyInfo = $('#currencyInfo');

                        if (cur_id && currency) {
                            currencyInfo.text(`1 ${currency.currency} = ${nominal.toLocaleString('id-ID')} IDR`);
                            currencyInfo.show();
                        } else {
                            currencyInfo.text('');
                            currencyInfo.hide();
                        }

                        $('#price').trigger('input');
                    });

                    // [MODIFIKASI] Tambah event untuk memastikan itm_id diubah ke uppercase
                    $('#itm_id').on('input', function() {
                        $(this).val($(this).val().toUpperCase());
                    });

                    // [MODIFIKASI] Hitung amount berdasarkan price dan nominal currency (quantity dihapus)
                    $('#price, #cur_id').on('input', function() {
                        const price = parseFloat($('#price').val()) || 0;
                        const cur_id = $('#cur_id').val();
                        const currency = currencies.find(c => c.cur_id === cur_id);
                        const nominal = currency && currency.nominal ? parseFloat(currency.nominal) :
                        1; // [MODIFIKASI] Validasi nominal
                        const amount = price * nominal;

                        $('#amountDisplay').val('IDR ' + amount.toLocaleString('id-ID', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }));
                        $('#amount').val(amount.toFixed(2)); // [MODIFIKASI] Set hidden input amount
                    });

                    // [MODIFIKASI] Handle form submission untuk menambahkan hidden input amount
                    $('#itemForm').on('submit', function(e) {
                        const price = parseFloat($('#price').val()) || 0;
                        const cur_id = $('#cur_id').val();
                        const currency = currencies.find(c => c.cur_id === cur_id);
                        const nominal = currency && currency.nominal ? parseFloat(currency.nominal) :
                        1; // [MODIFIKASI] Validasi nominal
                        const amount = price * nominal;

                        $(this).find('#amount').val(amount.toFixed(2));
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

                    $('#mainForm').on('submit', function(e) {
                        const itemRows = $('#itemsTable tbody tr').not(':has(td.text-center.text-muted)').length;
                        if (itemRows === 0) {
                            e.preventDefault();
                            Swal.fire({
                                icon: 'error',
                                title: 'No Items Added',
                                text: 'Please add at least one item before saving the submission.',
                            });
                            return false;
                        }
                    });
                });
            </script>
    </main>
</body>

</html>
