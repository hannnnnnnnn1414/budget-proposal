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
        <form id="updateSubmissionForm"
            action="{{ route('submissions.update', ['sub_id' => $submission->sub_id, 'id' => $submission->id]) }}"
            method="POST">
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
                            <label class="form-label">Input Type <span class="text-danger">*</span></label>
                            <select name="input_type" id="input_type" class="form-control select" required>
                                <option value="select"
                                    {{ old('input_type', \App\Models\Item::where('itm_id', $submission->itm_id)->exists() ? 'select' : 'manual') == 'select' ? 'selected' : '' }}>
                                    Item GID</option>
                                <option value="manual"
                                    {{ old('input_type', \App\Models\Item::where('itm_id', $submission->itm_id)->exists() ? 'select' : 'manual') == 'manual' ? 'selected' : '' }}>
                                    Item Non-GID</option>
                            </select>
                        </div>
                        <div class="mb-3" id="select_item_container"
                            style="{{ old('input_type', \App\Models\Item::where('itm_id', $submission->itm_id)->exists() ? 'select' : 'manual') == 'select' ? '' : 'display: none;' }}">
                            <label class="form-label">Item GID <span class="text-danger">*</span></label>
                            <select name="itm_id" id="itm_id" class="form-control select2"
                                {{ old('input_type', \App\Models\Item::where('itm_id', $submission->itm_id)->exists() ? 'select' : 'manual') == 'select' ? 'required' : '' }}>
                                <option value="">-- Select Item --</option>
                                @foreach ($items as $itm_id => $item)
                                    <option value="{{ $itm_id }}" data-name="{{ $item }}"
                                        {{ old('itm_id', $submission->itm_id) == $itm_id ? 'selected' : '' }}>
                                        {{ $itm_id }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3" id="manual_item_container"
                            style="{{ old('input_type', \App\Models\Item::where('itm_id', $submission->itm_id)->exists() ? 'select' : 'manual') == 'manual' ? '' : 'display: none;' }}">
                            <label class="form-label">Item Non-GID <span class="text-danger">*</span></label>
                            <input type="text" name="manual_item" id="manual_item" class="form-control"
                                placeholder="Enter item name"
                                value="{{ old('manual_item', \App\Models\Item::where('itm_id', $submission->itm_id)->exists() ? '' : $submission->itm_id) }}"
                                {{ old('input_type', \App\Models\Item::where('itm_id', $submission->itm_id)->exists() ? 'select' : 'manual') == 'manual' ? 'required' : '' }}>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="description" id="description" placeholder="Description" required>{{ old('description', $submission->description) }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Quantity</label><span class="text-danger">*</span>
                            <input type="number" class="form-control" required id="quantity" name="quantity"
                                value="{{ old('quantity', $submission->quantity) }}" placeholder="Quantity">
                        </div>
                        <div class="mb-3">
                            <div class="row g-2">
                                <!-- Currency (kiri) -->
                                <div class="col-md-6">
                                    <label for="cur_id" class="form-label">Currency <span
                                            class="text-danger">*</span></label>
                                    <select name="cur_id" id="cur_id" class="form-control select" required>
                                        <option value="">-- Select Currency --</option>
                                        @foreach (\App\Models\Currency::orderBy('currency', 'asc')->get() as $currency)
                                            <option value="{{ $currency->cur_id }}"
                                                data-nominal="{{ $currency->nominal }}"
                                                {{ old('cur_id', 'IDR') == $currency->cur_id ? 'selected' : '' }}>
                                                {{ $currency->currency }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small id="currencyNote" class="form-text text-muted"
                                        style="display: none;"></small>
                                </div>

                                <!-- Price (kanan) -->
                                <div class="col-md-6">
                                    <label for="price" class="form-label">Price <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" required id="price" name="price"
                                        value="{{ old('price', $submission->price ?? 0) }}" placeholder="Price"
                                        step="0.01" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount</label><span class="text-danger">*</span>
                            <input type="text" class="form-control" id="amountDisplay"
                                value="IDR {{ number_format($submission->amount ?? 0, 2, ',', '.') }}"
                                placeholder="Amount" readonly>
                            <input type="hidden" name="amount" id="amount" value="{{ $submission->amount ?? 0 }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Unit</label><span class="text-danger">*</span>
                            <select name="unit" class="form-control" required>
                                <option value="">-- Select Unit --</option>
                                <option value="LOT" @selected(old('unit', $submission->unit) == 'LOT')>LOT</option>
                                <option value="PCS" @selected(old('unit', $submission->unit) == 'PCS')>PCS</option>
                                <option value="KG" @selected(old('unit', $submission->unit) == 'KG')>KG</option>
                                <option value="M" @selected(old('unit', $submission->unit) == 'M')>M</option>
                                <option value="CM" @selected(old('unit', $submission->unit) == 'CM')>CM</option>
                                <option value="UNIT" @selected(old('unit', $submission->unit) == 'UNIT')>UNIT</option>
                                <option value="SET" @selected(old('unit', $submission->unit) == 'SET')>SET</option>
                                <option value="BOX" @selected(old('unit', $submission->unit) == 'BOX')>BOX</option>
                                <option value="PACK" @selected(old('unit', $submission->unit) == 'PACK')>PACK</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
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
                    <div class="col-md-4">
                        <div class="mb-3">
                            <div class="mb-3">
                                <label class="form-label">Department</label><span class="text-danger">*</span>
                                <input type="hidden" name="dpt_id" value="{{ Auth::user()->dept }}">
                                <input class="form-control" value="{{ Auth::user()->department->department ?? '-' }}"
                                    readonly>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Month</label><span class="text-danger">*</span>
                            <select name="month" class="form-control" required>
                                <option value="">-- Select Month --</option>
                                @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                    <option value="{{ $month }}" @selected(old('month', $submission->month) == $month)>
                                        {{ $month }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">R/NR</label><span class="text-danger">*</span>
                            <select name="bdc_id" class="form-control" required id="bdc_id">
                                <option value="">-- Select R/NR --</option>
                                @foreach ($budgets as $bdcID => $budget)
                                    <option value="{{ $bdcID }}" @selected(old('bdc_id') == $bdcID || $submission->bdc_id == $bdcID)>
                                        {{ $budget }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Line Of Business</label><span class="text-danger">*</span>
                            <select name="lob_id" class="form-control" required id="lob_id">
                                <option value="">-- Select R/NR --</option>
                                @foreach ($line_businesses as $lobID => $line_business)
                                    <option value="{{ $lobID }}" @selected(old('lob_id') == $lobID || $submission->lob_id == $lobID)>
                                        {{ $line_business }}
                                    </option>
                                @endforeach
                            </select>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Store original values
            let originalItmId = '{{ old('itm_id', $submission->itm_id) }}';
            let originalManualItem =
                '{{ old('manual_item', \App\Models\Item::where('itm_id', $submission->itm_id)->exists() ? '' : $submission->itm_id) }}';
            let originalDescription = '{{ old('description', $submission->description) }}';

            // Initialize Select2 for select elements
            function initializeModal() {
                const $modal = $('#editModal');
                // Initialize Select2 for itm_id with search enabled
                $modal.find('#itm_id').select2({
                    width: '100%',
                    dropdownParent: $modal,
                    placeholder: '-- Select Item --',
                    allowClear: true,
                    theme: 'bootstrap-5',
                    minimumResultsForSearch: 1 // Enable search even with few options
                });

                $modal.find('#cur_id').select2({
                    width: '100%',
                    dropdownParent: $modal,
                    placeholder: '-- Select Currency --',
                    allowClear: true,
                    theme: 'bootstrap-5',
                    minimumResultsForSearch: 1
                });

                // Adjust Select2 height to match other inputs
                $modal.find('.select2-selection--single').css({
                    'height': $modal.find('#price').outerHeight() + 'px',
                    'display': 'flex',
                    'align-items': 'center'
                });
                $modal.find('.select2-selection__rendered').css({
                    'line-height': $modal.find('#price').outerHeight() + 'px'
                });

                // Toggle input type visibility and handle description
                $modal.find('#input_type').off('change').on('change', function() {
                    const $selectContainer = $modal.find('#select_item_container');
                    const $manualContainer = $modal.find('#manual_item_container');
                    const $itmId = $modal.find('#itm_id');
                    const $manualItem = $modal.find('#manual_item');
                    const $description = $modal.find('#description');

                    if ($(this).val() === 'select') {
                        $selectContainer.show();
                        $manualContainer.hide();
                        $itmId.prop('required', true);
                        $manualItem.prop('required', false);
                        $manualItem.val('');
                        $description.val(originalDescription); // Retain original description
                        // $itmId.val(originalItmId).trigger('change'); // Restore original itm_id
                    } else {
                        $selectContainer.hide();
                        $manualContainer.show();
                        $itmId.prop('required', false);
                        $manualItem.prop('required', true);
                        $manualItem.val(originalManualItem);
                        $description.val(originalDescription || originalManualItem);
                        $itmId.val('').trigger('change'); // Clear Select2
                    }
                });

                // Update description when item is selected from dropdown
                // $modal.find('#itm_id').off('change').on('change', function() {
                //     const selectedOption = $(this).find('option:selected');
                //     console.log("Selected value:", $(this).val());
                //     console.log("Selected text:", selectedOption.text());
                //     const itemName = selectedOption.data('name');
                //     const $description = $modal.find('#description'); // ✅ FIX di sini
                //     $description.val(itemName || '');
                // });

                $('#itm_id').on('change', function() {
                    const selectedValue = $(this).val();
                    const selectedOption = $(this).find('option:selected');
                    const itemName = selectedOption.data('name');
                    const $description = $('#description');

                    // Set nilai ke form berdasarkan pilihan baru
                    if (selectedValue) {
                        $('#itm_id').val(selectedValue).trigger(
                        'change.select2'); // Pastikan Select2 diperbarui
                        $description.val(itemName || '');
                    } else {
                        $description.val('');
                    }

                    // Simpan nilai terpilih sebagai referensi
                    originalItmId = selectedValue;
                });

                // Calculate and update amount display
                //                 function calculateAmount() {
                //                     const $quantityInput = $modal.find('#quantity');
                //                     const $priceInput = $modal.find('#price');
                //                     const $currencySelect = $modal.find('#cur_id');
                //                     const $amountDisplay = $modal.find('#amountDisplay');
                //                     const $amountHidden = $modal.find('#amount');

                //                     if (!$quantityInput.length || !$priceInput.length || !$currencySelect.length || !$amountDisplay.length || !$amountHidden
                //                         .length) {
                //                         console.error('Missing input elements in #editModal');
                //                         return;
                //                     }

                //                     const quantity = parseFloat($quantityInput.val()) || 0;
                //                     const price = parseFloat($priceInput.val()) || 0;
                // const currencyNominal = parseFloat($currencySelect.find('option:selected').data('nominal')) || 1; // Default to 1 for IDR or no selection
                //                     const amount = quantity * price * currencyNominal;

                //                     $amountDisplay.val('IDR ' + amount.toLocaleString('id-ID', {
                //                         minimumFractionDigits: 2,
                //                         maximumFractionDigits: 2
                //                     }));
                //                     $amountHidden.val(amount.toFixed(2));
                //                 }

                //                 // Bind input event for quantity and price
                //                 $modal.find('#quantity, #price, #cur_id').off('input.calculateAmount change.calculateAmount').on('input.calculateAmount change.calculateAmount',
                //                     calculateAmount);

                //                 // Trigger initial calculation
                //                 calculateAmount();

                //                 // Trigger initial input_type change to set correct visibility
                //                 // $modal.find('#input_type').trigger('change');

                //                 // Trigger item description update if an item is already selected
                //                 if ($modal.find('#input_type').val() === 'select' && $modal.find('#itm_id').val()) {
                //                     $modal.find('#itm_id').trigger('change');
                //                 }
                function updateCurrencyNoteAndAmount() {
                    const $currencySelect = $modal.find('#cur_id');
                    const $currencyNote = $modal.find('#currencyNote');
                    const $quantityInput = $modal.find('#quantity');
                    const $priceInput = $modal.find('#price');
                    const $amountDisplay = $modal.find('#amountDisplay');
                    const $amountHidden = $modal.find('#amount');

                    if (!$quantityInput.length || !$priceInput.length || !$currencySelect.length || !$amountDisplay
                        .length || !$amountHidden.length) {
                        console.error('Missing input elements in #editModal');
                        return;
                    }

                    const selectedCurrency = $currencySelect.find('option:selected');
                    const currencyNominal = parseFloat(selectedCurrency.data('nominal')) || 1;
                    const currencyCode = selectedCurrency.text().trim();

                    // Update conversion note
                    if (currencyNominal !== 1 && currencyCode) {
                        const formattedNominal = currencyNominal.toLocaleString('id-ID', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                        $currencyNote.text(`1 ${currencyCode} = IDR ${formattedNominal}`).show();
                    } else {
                        $currencyNote.text('').hide();
                    }

                    // Calculate amount
                    const quantity = parseFloat($quantityInput.val()) || 0;
                    const price = parseFloat($priceInput.val()) || 0;
                    const amount = quantity * price * currencyNominal;

                    $amountDisplay.val('IDR ' + amount.toLocaleString('id-ID', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                    $amountHidden.val(amount.toFixed(2));
                }

                // Bind input and change events for quantity, price, and currency
                $modal.find('#quantity, #price, #cur_id').off('input.calculateAmount change.calculateAmount').on(
                    'input.calculateAmount change.calculateAmount', updateCurrencyNoteAndAmount);

                // Trigger initial calculation and note update
                updateCurrencyNoteAndAmount();

                // Trigger initial input_type change to set correct visibility
                $modal.find('#input_type').trigger('change');

                // Trigger item description update if an item is already selected
                if ($modal.find('#input_type').val() === 'select' && $modal.find('#itm_id').val()) {
                    $modal.find('#itm_id').trigger('change');
                }
            }

            // Handle form submission
            $('#updateSubmissionForm').off('submit').on('submit', function(e) {
                e.preventDefault();
                const $form = $(this);
                const url = $form.attr('action');
                const data = $form.serialize();
                const $submitButton = $form.find('button[type="submit"]');

                // Disable submit button to prevent multiple submissions
                $submitButton.prop('disabled', true);

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('.modal').modal('hide');
                        // Swal.fire({
                        //     icon: 'success',
                        //     title: 'Success',
                        //     text: response.message || 'Data has been updated successfully',
                        //     confirmButtonColor: '#3085d6',
                        // }).then(() => {
                        //     location.reload();
                        // });
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON?.errors;
                        let message = xhr.responseJSON?.message || 'Something went wrong';

                        if (errors) {
                            message = Object.values(errors).flat().join('\n');
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: message,
                            confirmButtonColor: '#d33',
                        });
                    },
                    complete: function() {
                        // Re-enable submit button after request completes
                        $submitButton.prop('disabled', false);
                    }
                });
            });

            // Initialize modal when shown
            $('#editModal').on('shown.bs.modal', function() {
                initializeModal();
                const initialValue = $('#itm_id').val();
                if (initialValue) {
                    $('#itm_id').trigger('change'); // Trigger change untuk set deskripsi awal
                }
            });

            // Initial call
            initializeModal();
        });
    </script>
</body>

</html>
