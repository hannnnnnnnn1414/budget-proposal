<!DOCTYPE html>
<html lang="en">

<x-head></x-head>

<body class="g-sidenav-show bg-gray-100">
    <x-sidebar></x-sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
        <x-navbar :notifications="$notifications">
            Submissions</x-navbar>
        <div class="container-fluid ">
            @if ($acc_id == 'FOHREPAIR')
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
                                                    <!-- [MODIFIKASI] Hapus kolom Unit -->
                                                    <!-- [MODIFIKASI] Hapus kolom Quantity -->
                                                    <th width="15%">Price</th>
                                                    <th width="15%">Amount</th>
                                                    <th>Workcenter</th>
                                                    <th>Department</th>
                                                    <th>Month</th>
                                                    <th>R/NR</th>
                                                    <th>Line Of Business</th>
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
                                                            <td>{{ $data['description'] }}</td>
                                                            <!-- [MODIFIKASI] Hapus kolom unit -->
                                                            <!-- [MODIFIKASI] Hapus kolom quantity -->
                                                            <td class="text-end">
                                                                {{ isset($data['price']) ? 'IDR ' . number_format($data['price'], 2) : '-' }}
                                                            </td>
                                                            <td class="text-end">
                                                                <!-- [MODIFIKASI] Amount hanya berdasarkan price karena quantity dihapus -->
                                                                {{ isset($data['amount']) ? 'IDR ' . number_format($data['amount'], 2) : '-' }}
                                                            </td>
                                                            <td>
                                                                {{ $workcenters[$data['wct_id']] ?? $data['wct_id'] }}
                                                            </td>
                                                            <td>
                                                                {{ $departments[$data['dpt_id']] ?? $data['dpt_id'] }}
                                                            </td>
                                                            <td>{{ $data['month'] ?? '-' }}</td>
                                                            <td>
                                                                {{ $budget_codes[$data['bdc_id']] ?? $data['bdc_id'] }}
                                                            </td>
                                                            <td>
                                                                {{ $line_business[$data['lob_id']] ?? $data['lob_id'] }}
                                                            </td>
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
                                                    <!-- [MODIFIKASI] Sesuaikan colspan karena kolom Unit dan Quantity dihapus -->
                                                    <tr>
                                                        <td colspan="9" class="text-center text-muted">No items added
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
                                                <h5 class="modal-title fw-bold mb-2" id="cancelConfirmModalLabel">Cancel
                                                    Submission</h5>
                                                <p class="mb-4">Are you sure you want to cancel the submission?<br>All
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
            @elseif($acc_id == 'SGAREPAIR')
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
                                                    <!-- [MODIFIKASI] Hapus kolom Quantity -->
                                                    <th width="15%">Price</th>
                                                    <th width="15%">Amount</th>
                                                    <th>Workcenter</th>
                                                    <th>Department</th>
                                                    <th>Month</th>
                                                    <th>R/NR</th>
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
                                                            <td>{{ $data['description'] }}</td>
                                                            <!-- [MODIFIKASI] Hapus kolom quantity -->
                                                            <td class="text-end">
                                                                {{ isset($data['price']) ? 'IDR ' . number_format($data['price'], 2) : '-' }}
                                                            </td>
                                                            <td class="text-end">
                                                                <!-- [MODIFIKASI] Amount hanya berdasarkan price karena quantity dihapus -->
                                                                {{ isset($data['amount']) ? 'IDR ' . number_format($data['amount'], 2) : '-' }}
                                                            </td>
                                                            <td>
                                                                {{ $workcenters[$data['wct_id']] ?? $data['wct_id'] }}
                                                            </td>
                                                            <td>
                                                                {{ $departments[$data['dpt_id']] ?? $data['dpt_id'] }}
                                                            </td>
                                                            <td>{{ $data['month'] ?? '-' }}</td>
                                                            <td>
                                                                {{ $budget_codes[$data['bdc_id']] ?? $data['bdc_id'] }}
                                                            </td>
                                                            <td class="text-center">
                                                                <form method="POST"
                                                                    action="{{ route('accounts.removeTempData', $index) }}"
                                                                    style="display:inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-danger" title="Delete">
                                                                        <i class="fa-solid fa-trash fs-6"></i>
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <!-- [MODIFIKASI] Sesuaikan colspan karena kolom Quantity dihapus -->
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
                                                        <button type="submit"
                                                            class="btn btn-danger px-4">Yes</button>
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
                                            <button type="submit" form="mainForm"
                                                class="btn btn-danger">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @if ($acc_id == 'FOHREPAIR')
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
                                        <!-- [MODIFIKASI] Hapus field Item Type -->
                                        <!-- [MODIFIKASI] Hapus field Item GID -->
                                        <!-- [MODIFIKASI] Hapus field Item Non-GID -->
                                        <div class="mb-3">
                                            <label class="form-label">Item</label><span class="text-danger">*</span>
                                            <input type="text" name="itm_id" id="itm_id" class="form-control"
                                                placeholder="Enter item ID" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Description</label><span
                                                class="text-danger">*</span>
                                            <!-- [MODIFIKASI] Hapus readonly dari textarea description -->
                                            <textarea class="form-control" name="description" id="description" placeholder="Description" required>{{ old('description') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- [MODIFIKASI] Hapus field Quantity -->
                                        <div class="row mb-3">
                                            <!-- Currency -->
                                            <div class="col-md-6">
                                                <label for="cur_id" class="form-label">Currency <span
                                                        class="text-danger">*</span></label>
                                                <select name="cur_id" id="cur_id" class="form-select" required>
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
                                                <input type="number" class="form-control" required id="price"
                                                    name="price"
                                                    value="{{ old('price', $submission->price ?? 0) }}"
                                                    placeholder="Price" step="0.01" min="0">
                                            </div>
                                        </div>
                                        <div id="currencyInfo" class="form-text text-muted" style="display: none;">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Amount</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="amountDisplay" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <!-- [MODIFIKASI] Hapus field Unit -->
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Workcenter</label>
                                            <select name="wct_id" class="form-control" required id="wct_id">
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
                                            <label class="form-label">Department</label><span
                                                class="text-danger">*</span>
                                            <input type="hidden" name="dpt_id" value="{{ Auth::user()->dept }}">
                                            <input class="form-control"
                                                value="{{ Auth::user()->department->department ?? '-' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
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
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">R/NR</label><span class="text-danger">*</span>
                                            <select name="bdc_id" class="form-control" required id="bdc_id">
                                                <option value="">-- R/NR --</option>
                                                @foreach ($budget_codes as $bdcID => $budget_code)
                                                    <option value="{{ $bdcID }}" @selected(old('bdc_id') == $bdcID)>
                                                        {{ $budget_code }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Line Of Business</label><span
                                                class="text-danger">*</span>
                                            <select name="lob_id" class="form-control" required id="lob_id">
                                                <option value="">-- Line Of Business --</option>
                                                @foreach ($line_business as $lobID => $line_businesses)
                                                    <option value="{{ $lobID }}" @selected(old('lob_id') == $lobID)>
                                                        {{ $line_businesses }}
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
        @elseif ($acc_id == 'SGAREPAIR')
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
                                        <!-- [MODIFIKASI] Hapus field Item Type -->
                                        <!-- [MODIFIKASI] Hapus field Item GID -->
                                        <!-- [MODIFIKASI] Hapus field Item Non-GID -->
                                        <div class="mb-3">
                                            <label class="form-label">Item</label><span class="text-danger">*</span>
                                            <input type="text" name="itm_id" id="itm_id" class="form-control"
                                                placeholder="Enter item ID" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Description</label><span
                                                class="text-danger">*</span>
                                            <!-- [MODIFIKASI] Hapus readonly dari textarea description -->
                                            <textarea class="form-control" name="description" id="description" placeholder="Description" required>{{ old('description') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- [MODIFIKASI] Hapus field Quantity -->
                                        <div class="row mb-3">
                                            <!-- Currency -->
                                            <div class="col-md-6">
                                                <label for="cur_id" class="form-label">Currency <span
                                                        class="text-danger">*</span></label>
                                                <select name="cur_id" id="cur_id" class="form-select" required>
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
                                                <input type="number" class="form-control" required id="price"
                                                    name="price"
                                                    value="{{ old('price', $submission->price ?? 0) }}"
                                                    placeholder="Price" step="0.01" min="0">
                                            </div>
                                        </div>
                                        <div id="currencyInfo" class="form-text text-muted" style="display: none;">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Amount</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="amountDisplay" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <!-- [MODIFIKASI] Hapus field Unit -->
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Workcenter</label>
                                            <select name="wct_id" class="form-control" required id="wct_id">
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
                                            <label class="form-label">Department</label><span
                                                class="text-danger">*</span>
                                            <input type="hidden" name="dpt_id" value="{{ Auth::user()->dept }}">
                                            <input class="form-control"
                                                value="{{ Auth::user()->department->department ?? '-' }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
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
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">R/NR</label><span class="text-danger">*</span>
                                            <select name="bdc_id" class="form-control" required id="bdc_id">
                                                <option value="">-- R/NR --</option>
                                                @foreach ($budget_codes as $bdcID => $budget_code)
                                                    <option value="{{ $bdcID }}" @selected(old('bdc_id') == $bdcID)>
                                                        {{ $budget_code }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Line Of Business</label><span
                                                class="text-danger">*</span>
                                            <select name="lob_id" class="form-control" required id="lob_id">
                                                <option value="">-- Line Of Business --</option>
                                                @foreach ($line_business as $lobID => $line_businesses)
                                                    <option value="{{ $lobID }}" @selected(old('lob_id') == $lobID)>
                                                        {{ $line_businesses }}
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
                let currencies = [];

                $('#itemModal').on('shown.bs.modal', function() {
                    // Inisialisasi Select2 untuk semua dropdown
                    $('.select').select2({ // [MODIFIKASI] Inisialisasi Select2 untuk semua elemen dengan class select
                        dropdownParent: $('#itemModal'),
                        allowClear: true,
                        placeholder: function() {
                            return $(this).attr('id') === 'cur_id' ? '-- Select Currency --' :
                                $(this).attr('id') === 'wct_id' ? '-- Workcenter --' :
                                $(this).attr('id') === 'month' ? '-- Select Month --' :
                                $(this).attr('id') === 'bdc_id' ? '-- R/NR --' :
                                // [MODIFIKASI] Placeholder untuk bdc_id
                                $(this).attr('id') === 'lob_id' ? '-- Line Of Business --' :
                                '-- Select --';
                        },
                        width: '100%',
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

                    // [MODIFIKASI] Hapus inisialisasi Select2 spesifik untuk cur_id karena sudah ditangani di atas
                    // $('#cur_id').select2({
                    //     dropdownParent: $('#itemModal'),
                    //     allowClear: true,
                    //     placeholder: '-- Select Currency --',
                    //     width: '100%',
                    //     theme: 'bootstrap-5'
                    // });

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
                                options +=
                                    `<option value="${currency.cur_id}" data-nominal="${currency.nominal}">${currency.currency}</option>`;
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

                $('#cur_id').on('change', function() {
                    const cur_id = $(this).val();
                    const currency = currencies.find(c => c.cur_id === cur_id);
                    const nominal = currency ? parseFloat(currency.nominal) : 1;
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

                $('#itm_id').on('input', function() {
                    $(this).val($(this).val().toUpperCase());
                });

                // Calculate amount
                $('#price, #cur_id').on('input', function() {
                    const price = parseFloat($('#price').val()) || 0;
                    const cur_id = $('#cur_id').val();
                    const currency = currencies.find(c => c.cur_id === cur_id);
                    const nominal = currency ? parseFloat(currency.nominal) : 1;
                    const amount = price * nominal;

                    $('#amountDisplay').val('IDR ' + amount.toLocaleString('id-ID', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                });

                // Handle form submission
                $('#itemForm').on('submit', function(e) {
                    // [MODIFIKASI] Validasi bdc_id sebelum submit
                    const bdc_id = $('#bdc_id').val();
                    if (!bdc_id) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Budget (R/NR) is required.',
                        });
                        return false;
                    }

                    const price = parseFloat($('#price').val()) || 0;
                    const cur_id = $('#cur_id').val();
                    const currency = currencies.find(c => c.cur_id === cur_id);
                    const nominal = currency ? parseFloat(currency.nominal) : 1;
                    const amount = price * nominal;

                    $(this).append(`<input type="hidden" name="amount" value="${amount}">`);
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

                // [MODIFIKASI] Destroy Select2 saat modal ditutup untuk mencegah bug
                $('#itemModal').on('hidden.bs.modal', function() {
                    $('.select').each(function() {
                        if ($(this).data('select2')) {
                            $(this).select2('destroy');
                        }
                    });
                });
            });
        </script>
    </main>
</body>

</html>
