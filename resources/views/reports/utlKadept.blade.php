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
                                                    $approval = \App\Models\Approval::where(
                                                        'sub_id',
                                                        $submission->sub_id,
                                                    )
                                                        ->where('approve_by', Auth::user()->npk)
                                                        ->first();
                                                    $directDIC = in_array($submission->dpt_id, [
                                                        '6111',
                                                        '6121',
                                                        '4211',
                                                    ]);
                                                @endphp
                                                <p>Status: <span class="font-bold">
                                                        @if ($submission->status == 1)
                                                            <span class="badge bg-warning">DRAFT</span>
                                                        @elseif ($submission->status == 2)
                                                            <span class="badge bg-secondary">UNDER REVIEW
                                                                KADEP</span>
                                                        @elseif ($submission->status == 3 && !$directDIC)
                                                            <span class="badge"
                                                                style="background-color: #0080ff">APPROVED BY
                                                                KADEPT</span>
                                                        @elseif ($submission->status == 4)
                                                            <span class="badge" style="background-color: #0080ff">
                                                                @if ($directDIC)
                                                                    APPROVED BY KADEPT
                                                                @else
                                                                    Approved by KADIV
                                                                @endif
                                                            </span>
                                                        @elseif ($submission->status == 5)
                                                            <span class="badge"
                                                                style="background-color: #0080ff">APPROVED BY
                                                                DIC</span>
                                                        @elseif ($submission->status == 6)
                                                            <span class="badge"
                                                                style="background-color: #0080ff">APPROVED BY
                                                                PIC BUDGETING</span>
                                                        @elseif ($submission->status == 7)
                                                            <span class="badge"
                                                                style="background-color: #0080ff">APPROVED BY
                                                                KADEP BUDGETING</span>
                                                        @elseif ($submission->status == 8)
                                                            <span class="badge bg-danger">DISAPPROVED BY
                                                                KADEP</span>
                                                        @elseif ($submission->status == 9 && !$directDIC)
                                                            <span class="badge bg-danger">DISAPPROVED BY
                                                                KADIV</span>
                                                        @elseif ($submission->status == 10)
                                                            <span class="badge bg-danger">REQUEST EXPLANATION</span>
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
                                                <p>No submission data available</p>
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
                                    // Check if actions (edit/delete) are allowed
                                    $hasAction = $submissions->contains(function ($submission) {
                                        return in_array($submission->status, [6, 13]);
                                    });

                                    // Define month mappings
                                    $monthMap = [
                                        'JAN' => 'January',
                                        'FEB' => 'February',
                                        'MAR' => 'March',
                                        'APR' => 'April',
                                        'MAY' => 'May',
                                        'JUN' => 'June',
                                        'JUL' => 'July',
                                        'AUG' => 'August',
                                        'SEP' => 'September',
                                        'OCT' => 'October',
                                        'NOV' => 'November',
                                        'DEC' => 'December',
                                        'January' => 'January',
                                        'February' => 'February',
                                        'March' => 'March',
                                        'April' => 'April',
                                        'May' => 'May',
                                        'June' => 'June',
                                        'July' => 'July',
                                        'August' => 'August',
                                        'September' => 'September',
                                        'October' => 'October',
                                        'November' => 'November',
                                        'December' => 'December',
                                        '0' => 'January',
                                        '1' => 'February',
                                        '2' => 'March',
                                        '3' => 'April',
                                        '4' => 'May',
                                        '5' => 'June',
                                        '6' => 'July',
                                        '7' => 'August',
                                        '8' => 'September',
                                        '9' => 'October',
                                        '10' => 'November',
                                        '11' => 'December',
                                    ];

                                    $monthLabels = [
                                        'January' => 'Jan',
                                        'February' => 'Feb',
                                        'March' => 'Mar',
                                        'April' => 'Apr',
                                        'May' => 'May',
                                        'June' => 'Jun',
                                        'July' => 'Jul',
                                        'August' => 'Aug',
                                        'September' => 'Sep',
                                        'October' => 'Oct',
                                        'November' => 'Nov',
                                        'December' => 'Dec',
                                    ];

                                    // Define months array for table headers
                                    $months = [
                                        'January',
                                        'February',
                                        'March',
                                        'April',
                                        'May',
                                        'June',
                                        'July',
                                        'August',
                                        'September',
                                        'October',
                                        'November',
                                        'December',
                                    ];

                                    // Group submissions by itm_id and kwh
                                    $groupedItems = $submissions
                                        ->groupBy(function ($submission) {
                                            // Group by combination yang unik termasuk line_business
                                            return ($submission->itm_id ?? '') .
                                                '-' .
                                                ($submission->kwh ?? '') .
                                                '-' .
                                                ($submission->line_business->line_business ??
                                                    ($submission->lob_id ?? ''));
                                        })
                                        ->map(function ($group) use ($monthMap, $monthLabels) {
                                            $first = $group->first();
                                            $months = [];
                                            $totalAmount = 0;

                                            foreach ($group as $submission) {
                                                $month = isset($monthMap[$submission->month])
                                                    ? $monthMap[$submission->month]
                                                    : null;
                                                if ($month && array_key_exists($month, $monthLabels)) {
                                                    $months[$month] = [
                                                        'amount' => $submission->amount,
                                                        'id' => $submission->id,
                                                        'price' => $submission->price,
                                                        'wct_id' => $submission->wct_id,
                                                        'cur_id' => $submission->cur_id,
                                                        'lob_id' => $submission->lob_id,
                                                    ];
                                                    $totalAmount += $submission->amount;
                                                }
                                            }

                                            return [
                                                'itm_id' => $first->itm_id ?? '-',
                                                'kwh' => $first->kwh ?? '-',
                                                'workcenter' => $first->workcenter
                                                    ? $first->workcenter->workcenter
                                                    : '-',
                                                'department' => $first->dept ? $first->dept->department : '-',
                                                // AMBIL LANGSUNG DARI RELASI line_business
                                                'line_business' => $first->line_business
                                                    ? $first->line_business->line_business
                                                    : $first->lob_id ?? '-',
                                                'sub_id' => $first->sub_id,
                                                'status' => $first->status,
                                                'months' => $months,
                                                'total_amount' => $totalAmount,
                                            ];
                                        });

                                    $grandTotal = $groupedItems->sum('total_amount');
                                @endphp

                                @if (in_array($submission->status, [6, 13]))
                                    <div class="d-flex justify-content-end mb-3">
                                        <button type="button" class="btn btn-danger open-add-item-modal"
                                            data-sub-id="{{ $submission->sub_id }}">
                                            <i class="fa-solid fa-plus me-2"></i>Add Item
                                        </button>
                                    </div>
                                @endif

                                <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                                    <table class="table table-bordered"
                                        style="border-collapse: separate; border-spacing: 0; min-width: 100%;">
                                        <thead class="bg-gray-200 text-center"
                                            style="position: sticky; top: 0; z-index: 100; background-color: #e9ecef;">
                                            <tr>
                                                <th class="text-left border p-2"
                                                    style="position: sticky; left: 0; z-index: 110; background-color: #e9ecef; min-width: 80px; width: 80px;">
                                                    Item</th>
                                                <th class="text-left border p-2"
                                                    style="position: left: 80px; z-index: 110; background-color: #e9ecef; min-width: 80px; width: 80px;">
                                                    KWH</th>
                                                <th class="text-left border p-2"
                                                    style="position: left: 160px; z-index: 110; background-color: #e9ecef; min-width: 120px; width: 120px;">
                                                    Workcenter</th>
                                                <th class="text-left border p-2"
                                                    style="position: left: 280px; z-index: 110; background-color: #e9ecef; min-width: 120px; width: 120px;">
                                                    Department</th>
                                                <th class="text-left border p-2"
                                                    style="position: left: 400px; z-index: 110; background-color: #e9ecef; min-width: 120px; width: 120px;">
                                                    Line of Business</th>
                                                @foreach ($months as $month)
                                                    <th class="text-left border p-2" style="min-width: 100px;">
                                                        {{ $monthLabels[$month] }}</th>
                                                @endforeach
                                                <th class="text-left border p-2" style="min-width: 120px;">Total
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($groupedItems as $item)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="border p-2"
                                                        style="position: sticky; left: 0; z-index: 10; background-color: white; min-width: 80px; width: 80px;">
                                                        {{ $item['itm_id'] }}</td>
                                                    <td class="border p-2"
                                                        style="position: left: 80px; z-index: 10; background-color: white; min-width: 80px; width: 80px;">
                                                        {{ $item['kwh'] }}</td>
                                                    <td class="border p-2"
                                                        style="position: left: 160px; z-index: 10; background-color: white; min-width: 120px; width: 120px;">
                                                        {{ $item['workcenter'] }}</td>
                                                    <td class="border p-2"
                                                        style="position: left: 280px; z-index: 10; background-color: white; min-width: 120px; width: 120px;">
                                                        {{ $item['department'] }}</td>
                                                    <td class="border p-2"
                                                        style="position: left: 400px; z-index: 10; background-color: white; min-width: 120px; width: 120px;">
                                                        {{ $item['line_business'] }}</td>
                                                    @foreach ($months as $month)
                                                        <td class="border p-2 text-center" style="min-width: 100px;">
                                                            @if (isset($item['months'][$month]) && $item['months'][$month]['price'] > 0)
                                                                @php
                                                                    $monthlyData = $item['months'][$month];
                                                                @endphp
                                                                @if ($item['status'] == 6 || $item['status'] == 13)
                                                                    <a href="#" class="editable-month"
                                                                        data-sub-id="{{ $item['sub_id'] }}"
                                                                        data-id="{{ $monthlyData['id'] ?? '' }}"
                                                                        data-month="{{ $month }}"
                                                                        data-price="{{ $monthlyData['price'] ?? 0 }}"
                                                                        data-kwh="{{ $item['kwh'] }}"
                                                                        data-itm-id="{{ $item['itm_id'] }}"
                                                                        data-workcenter="{{ $item['workcenter'] }}"
                                                                        data-workcenter-id="{{ $monthlyData['wct_id'] ?? '' }}"
                                                                        data-currency-id="{{ $monthlyData['cur_id'] ?? '' }}"
                                                                        data-lob-id="{{ $monthlyData['lob_id'] ?? '' }}"
                                                                        title="Klik untuk mengedit data {{ $month }}">
                                                                        Rp
                                                                        {{ number_format($item['months'][$month]['price'], 0, ',', '.') }}
                                                                    </a>
                                                                @else
                                                                    Rp
                                                                    {{ number_format($item['months'][$month]['price'], 0, ',', '.') }}
                                                                @endif
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                    <td class="border p-2" style="min-width: 120px;">
                                                        Rp {{ number_format($item['total_amount'], 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="{{ $hasAction ? 18 : 17 }}"
                                                        class="border p-2 text-center">
                                                        No Submissions found!
                                                    </td>
                                                </tr>
                                            @endforelse
                                            <tr class="bg-gray-100 font-bold">
                                                <td colspan="{{ count($months) + 5 }}" class="border p-2"
                                                    style="position: left: 0; z-index: 10; background-color: #f8f9fa;">
                                                    Total
                                                </td>
                                                <td class="border p-2 text-right">
                                                    Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                                </td>
                                            </tr>
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

        <!-- Modal Edit Data Bulanan -->
        <div id="editMonthModal" class="modal fade" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white">Edit Data Bulanan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editMonthForm" method="POST" action="">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="sub_id" id="edit_month_sub_id">
                            <input type="hidden" name="id" id="edit_month_id">
                            <input type="hidden" name="month" id="edit_month_name">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Bulan</label>
                                    <input type="text" id="display_month" class="form-control" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Item</label>
                                    <input type="text" id="edit_month_itm_id" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">KWH</label>
                                    <input type="number" id="edit_month_kwh" name="kwh" class="form-control"
                                        required min="0">
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_month_price" class="form-label">Harga</label>
                                    <input type="number" name="price" id="edit_month_price" class="form-control"
                                        required min="0" step="0.01">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="edit_month_cur_id" class="form-label">Mata
                                        Uang</label>
                                    <select name="cur_id" id="edit_month_cur_id" class="form-control select"
                                        required>
                                        <option value="">-- Pilih Mata Uang --</option>
                                        @foreach (\App\Models\Currency::orderBy('currency', 'asc')->get() as $currency)
                                            <option value="{{ $currency->cur_id }}"
                                                data-nominal="{{ $currency->nominal }}">
                                                {{ $currency->currency }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_month_amount_display" class="form-label">Jumlah
                                        (IDR)</label>
                                    <input type="text" id="edit_month_amount_display" class="form-control"
                                        readonly>
                                    <input type="hidden" name="amount" id="edit_month_amount">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="edit_month_wct_id" class="form-label">Workcenter</label>
                                    <select name="wct_id" id="edit_month_wct_id" class="form-control select"
                                        required>
                                        <option value="">-- Pilih Workcenter --</option>
                                        @foreach (\App\Models\Workcenter::orderBy('workcenter', 'asc')->get() as $workcenter)
                                            <option value="{{ $workcenter->wct_id }}">
                                                {{ $workcenter->workcenter }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_month_lob_id" class="form-label">Line of
                                        Business</label>
                                    <select name="lob_id" id="edit_month_lob_id" class="form-control select"
                                        required>
                                        <option value="">-- Pilih Line of Business --</option>
                                        @foreach (\App\Models\LineOfBusiness::orderBy('line_business', 'asc')->get() as $line_business)
                                            <option value="{{ $line_business->lob_id }}">
                                                {{ $line_business->line_business }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger me-auto" id="deleteMonthButton">Hapus
                                    Data</button>
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn text-white"
                                    style="background-color: #0080ff;">Perbarui Data</button>
                            </div>
                        </form>
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
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Item <span class="text-danger">*</span></label>
                                        <input type="text" name="itm_id" id="itm_id" class="form-control"
                                            placeholder="Enter Item ID" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="kwh" class="form-label">KWH <span
                                                class="text-danger">*</span></label>
                                        <input type="number" name="kwh" id="kwh" class="form-control"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="wct_id" class="form-label">Workcenter <span
                                                class="text-danger">*</span></label>
                                        <select name="wct_id" id="wct_id" class="form-control select" required>
                                            <option value="">-- Select Workcenter --</option>
                                            @foreach (\App\Models\Workcenter::orderBy('workcenter', 'asc')->get() as $workcenter)
                                                <option value="{{ $workcenter->wct_id }}">
                                                    {{ $workcenter->workcenter }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="cur_id" class="form-label">Currency <span
                                                    class="text-danger">*</span></label>
                                            <select name="cur_id" id="cur_id" class="form-select" required>
                                                <option value="">-- Select Currency --</option>
                                                @foreach (\App\Models\Currency::orderBy('currency', 'asc')->get() as $currency)
                                                    <option value="{{ $currency->cur_id }}"
                                                        data-nominal="{{ $currency->nominal }}">
                                                        {{ $currency->currency }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small id="currencyNote" class="form-text text-muted"
                                                style="display: none;"></small>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="price" class="form-label">Price <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" name="price" id="price" class="form-control"
                                                required min="0" step="0.01">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="amountDisplay" class="form-label">Amount
                                            (IDR)</label>
                                        <input type="text" id="amountDisplay" class="form-control" readonly>
                                        <input type="hidden" name="amount" id="amount">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Department <span
                                                class="text-danger">*</span></label>
                                        <input type="hidden" name="dpt_id" value="{{ Auth::user()->dept }}">
                                        <input class="form-control"
                                            value="{{ Auth::user()->department->department ?? '-' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
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
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="lob_id" class="form-label">Line of Business
                                            <span class="text-danger">*</span></label>
                                        <select name="lob_id" id="lob_id" class="form-control select" required>
                                            <option value="">-- Pilih Line of Business --
                                            </option>
                                            @foreach (\App\Models\LineOfBusiness::orderBy('line_business', 'asc')->get() as $lob)
                                                <option value="{{ $lob->lob_id }}">
                                                    {{ $lob->line_business }}</option>
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
                                        <label class="form-label">Description <span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control" name="description" id="description" placeholder="Description" required></textarea>
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
                // Initialize Select2
                function initializeSelect2($modal) {
                    $modal.find('.select').each(function() {
                        // $(this).select2({
                        //     width: '100%',
                        //     dropdownParent: $modal,
                        //     theme: 'bootstrap-5',
                        //     placeholder: $(this).attr('id') === 'cur_id' ? '-- Select Currency --' : $(
                        //             this).attr('id') === 'wct_id' ? '-- Select Workcenter --' : $(this)
                        //         .attr('id') === 'month' ? '-- Select Month --' :
                        //         '-- Select Line of Business --',
                        //     allowClear: true
                        // });
                    });

                    // Adjust Select2 height
                    $modal.find('.select2-selection--single').css({
                        'height': $modal.find('#price').outerHeight() + 'px',
                        'display': 'flex',
                        'align-items': 'center'
                    });
                    $modal.find('.select2-selection__rendered').css({
                        'line-height': $modal.find('#price').outerHeight() + 'px'
                    });

                    // Destroy Select2 when modal closes
                    $modal.on('hidden.bs.modal', function() {
                        $modal.find('.select').each(function() {
                            if ($(this).data('select2')) {
                                $(this).select2('destroy');
                            }
                        });
                    });
                }

                // Initialize Add Item Modal
                $('#addItemModal').on('shown.bs.modal', function() {
                    $('#addItemForm')[0].reset();
                    $('#amountDisplay').val('');
                    $('#cur_id').val('').trigger('change');
                    $('#currencyNote').text('').hide();
                    initializeSelect2($(this));
                });

                // Calculate amount dynamically for Add Item Modal
                $('#addItemModal').on('input change', '#price, #cur_id', function() {
                    const $priceInput = $('#addItemModal #price');
                    const $currencySelect = $('#addItemModal #cur_id');
                    const $amountDisplay = $('#addItemModal #amountDisplay');
                    const $amountHidden = $('#addItemModal #amount');
                    const $currencyNote = $('#addItemModal #currencyNote');

                    const price = parseFloat($priceInput.val()) || 0;
                    const selectedCurrency = $currencySelect.find('option:selected');
                    const currencyNominal = parseFloat(selectedCurrency.data('nominal')) || 1;
                    const currencyCode = selectedCurrency.text().trim();

                    if (currencyNominal !== 1 && currencyCode) {
                        const formattedNominal = currencyNominal.toLocaleString('id-ID', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                        $currencyNote.text(`1 ${currencyCode} = IDR ${formattedNominal}`).show();
                    } else {
                        $currencyNote.text('').hide();
                    }

                    const amount = price * currencyNominal;
                    $amountDisplay.val('IDR ' + amount.toLocaleString('id-ID', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                    $amountHidden.val(amount.toFixed(2));
                });

                // // Uppercase for itm_id in Add Item Modal
                // $('#addItemModal #itm_id').on('input', function() {
                //     $(this).val($(this).val().toUpperCase());
                // });

                // Handle opening Add Item Modal
                $(document).on('click', '.open-add-item-modal', function(e) {
                    e.preventDefault();
                    var subId = $(this).data('sub-id');
                    var modal = $('#addItemModal');
                    modal.find('#sub_id').val(subId);
                    modal.modal('show');
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

                // Handle Edit Month Modal
                $(document).on('click', '.editable-month', function(e) {
                    e.preventDefault();
                    const subId = $(this).data('sub-id');
                    const id = $(this).data('id');
                    const month = $(this).data('month');
                    const price = $(this).data('price');
                    const kwh = $(this).data('kwh');
                    const itmId = $(this).data('itm-id');
                    const workcenterId = $(this).data('workcenter-id');
                    const currencyId = $(this).data('currency-id');
                    const lobId = $(this).data('lob-id');

                    if (!id) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Data ID tidak ditemukan. Silakan refresh halaman dan coba lagi.',
                            confirmButtonColor: '#d33'
                        });
                        return;
                    }

                    $('#edit_month_sub_id').val(subId);
                    $('#edit_month_id').val(id);
                    $('#edit_month_name').val(month);
                    $('#display_month').val(month);
                    $('#edit_month_itm_id').val(itmId);
                    $('#edit_month_kwh').val(kwh);
                    $('#edit_month_price').val(price);
                    $('#edit_month_wct_id').val(workcenterId || '');
                    $('#edit_month_cur_id').val(currencyId || '');
                    $('#edit_month_lob_id').val(lobId || '');

                    updateMonthAmountDisplay();
                    $('#editMonthForm').attr('action', '/submissions/' + subId + '/id/' + id + '/month/' +
                        encodeURIComponent(month));
                    $('#editMonthModal').modal('show');
                    initializeSelect2($('#editMonthModal'));
                });

                // Calculate amount for Edit Month Modal
                $('#editMonthModal').on('input change', '#edit_month_price, #edit_month_cur_id', function() {
                    updateMonthAmountDisplay();
                });

                function updateMonthAmountDisplay() {
                    const price = parseFloat($('#edit_month_price').val()) || 0;
                    const selectedCurrency = $('#edit_month_cur_id').find('option:selected');
                    const currencyNominal = parseFloat(selectedCurrency.data('nominal')) || 1;
                    const amount = price * currencyNominal;

                    $('#edit_month_amount_display').val('IDR ' + amount.toLocaleString('id-ID', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                    $('#edit_month_amount').val(amount.toFixed(2));
                }

                // Handle Edit Month Form Submission
                $(document).on('submit', '#editMonthForm', function(e) {
                    e.preventDefault();
                    const form = $(this);
                    const url = form.attr('action');

                    $.ajax({
                        url: url,
                        method: 'PUT',
                        data: form.serialize(),
                        success: function(response) {
                            if (response.success) {
                                $('#editMonthModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Data berhasil diperbarui.',
                                    confirmButtonColor: '#3085d6'
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: xhr.responseJSON?.message ||
                                    'Gagal memperbarui data.',
                                confirmButtonColor: '#d33'
                            });
                        }
                    });
                });

                // Handle Delete Month
                $(document).on('click', '#deleteMonthButton', function() {
                    const subId = $('#edit_month_sub_id').val();
                    const id = $('#edit_month_id').val();
                    const month = $('#edit_month_name').val();

                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Data untuk bulan ini akan dihapus!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '/submissions/' + subId + '/id/' + id + '/month/' +
                                    encodeURIComponent(month),
                                method: 'DELETE',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    if (response.success) {
                                        $('#editMonthModal').modal('hide');
                                        Swal.fire('Terhapus!', 'Data berhasil dihapus.',
                                            'success').then(() => {
                                            location.reload();
                                        });
                                    }
                                },
                                error: function(xhr) {
                                    Swal.fire('Error!', xhr.responseJSON?.message ||
                                        'Gagal menghapus data.', 'error');
                                }
                            });
                        }
                    });
                });

                // Handle Edit Item Modal
                $(document).on('click', '.open-edit-modal', function(e) {
                    e.preventDefault();
                    var subId = $(this).data('id');
                    var itmId = $(this).data('itm-id');
                    var modal = $('#editModal');

                    modal.find('.modal-content').html(`
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white">Edit Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center py-4">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            `);
                    modal.modal('show');

                    $.get('/submissions/' + subId + '/id/' + itmId + '/edit', function(data) {
                        const itemData = {
                            itm_id: data.itm_id || '',
                            kwh: data.kwh || 0,
                            cur_id: data.cur_id || '',
                            price: data.price || 0,
                            amount: parseFloat(data.amount) || 0,
                            dpt_id: data.dpt_id || '',
                            wct_id: data.wct_id || '',
                            month: data.month || '',
                            acc_id: data.acc_id || '',
                            purpose: data.purpose || '',
                            department: data.department || '-',
                            lob_id: data.lob_id || ''
                        };

                        modal.find('.modal-content').html(`
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white">Edit Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editItemForm" method="POST" action="/submissions/${subId}/id/${itmId}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="sub_id" value="${subId}">
                            <input type="hidden" name="acc_id" value="${itemData.acc_id}">
                            <input type="hidden" name="purpose" value="${itemData.purpose}">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Item <span class="text-danger">*</span></label>
                                        <input type="text" name="itm_id" id="itm_id" class="form-control" placeholder="Enter item name" required value="${itemData.itm_id}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="kwh" class="form-label">KWH <span class="text-danger">*</span></label>
                                        <input type="number" name="kwh" id="kwh" class="form-control" required min="0" value="${itemData.kwh}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="wct_id" class="form-label">Workcenter <span class="text-danger">*</span></label>
                                        <select name="wct_id" id="wct_id" class="form-control select" required>
                                            <option value="">-- Select Workcenter --</option>
                                            @foreach (\App\Models\Workcenter::orderBy('workcenter', 'asc')->get() as $workcenter)
                                                <option value="{{ $workcenter->wct_id }}" ${itemData.wct_id === '{{ $workcenter->wct_id }}' ? 'selected' : ''}>
                                                    {{ $workcenter->workcenter }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="cur_id" class="form-label">Currency <span class="text-danger">*</span></label>
                                            <select name="cur_id" id="cur_id" class="form-control select" required>
                                                <option value="">-- Select Currency --</option>
                                                @foreach (\App\Models\Currency::orderBy('currency', 'asc')->get() as $currency)
                                                    <option value="{{ $currency->cur_id }}" data-nominal="{{ $currency->nominal }}" ${itemData.cur_id === '{{ $currency->cur_id }}' ? 'selected' : ''}>
                                                        {{ $currency->currency }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small id="currencyNote" class="form-text text-muted" style="display: none;"></small>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                            <input type="number" name="price" id="price" class="form-control" required min="0" step="0.01" value="${itemData.price}">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="amountDisplay" class="form-label">Amount (IDR)</label>
                                        <input type="text" id="amountDisplay" class="form-control" readonly value="IDR ${itemData.amount.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}">
                                        <input type="hidden" name="amount" id="amount" value="${itemData.amount.toFixed(2)}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Department <span class="text-danger">*</span></label>
                                        <input type="hidden" name="dpt_id" value="${itemData.dpt_id}">
                                        <input class="form-control" value="${itemData.department}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="month" class="form-label">Month <span class="text-danger">*</span></label>
                                        <select class="form-control select" name="month" id="month" required>
                                            <option value="">-- Select Month --</option>
                                            @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                                <option value="{{ $month }}" ${itemData.month === '{{ $month }}' ? 'selected' : ''}>{{ $month }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="lob_id" class="form-label">Line of Business <span class="text-danger">*</span></label>
                                        <select name="lob_id" id="lob_id" class="form-control select" required>
                                            <option value="">-- Select Line of Business --</option>
                                            @foreach (\App\Models\LineOfBusiness::orderBy('line_business', 'asc')->get() as $lob)
                                                <option value="{{ $lob->lob_id }}" ${itemData.lob_id === '{{ $lob->lob_id }}' ? 'selected' : ''}>
                                                    {{ $lob->line_business }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn text-white" style="background-color: #0080ff;">Update Item</button>
                            </div>
                        </form>
                    </div>
                `);

                        initializeSelect2(modal);
                        modal.find('#price, #cur_id').on('input change', function() {
                            const $priceInput = modal.find('#price');
                            const $currencySelect = modal.find('#cur_id');
                            const $amountDisplay = modal.find('#amountDisplay');
                            const $amountHidden = modal.find('#amount');
                            const $currencyNote = modal.find('#currencyNote');

                            const price = parseFloat($priceInput.val()) || 0;
                            const selectedCurrency = $currencySelect.find('option:selected');
                            const currencyNominal = parseFloat(selectedCurrency.data(
                                'nominal')) || 1;
                            const currencyCode = selectedCurrency.text().trim();

                            if (currencyNominal !== 1 && currencyCode) {
                                const formattedNominal = currencyNominal.toLocaleString(
                                    'id-ID', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                $currencyNote.text(
                                    `1 ${currencyCode} = IDR ${formattedNominal}`).show();
                            } else {
                                $currencyNote.text('').hide();
                            }

                            const amount = price * currencyNominal;
                            $amountDisplay.val('IDR ' + amount.toLocaleString('id-ID', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }));
                            $amountHidden.val(amount.toFixed(2));
                        });

                        modal.find('#itm_id').on('input', function() {
                            $(this).val($(this).val().toUpperCase());
                        });
                    }).fail(function(xhr) {
                        modal.find('.modal-content').html(`
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white">Edit Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            Gagal memuat form edit: ${xhr.responseJSON?.message || 'Unknown error'}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                `);
                    });
                });

                // Handle Edit Item Form Submission
                $(document).on('submit', '#editItemForm', function(e) {
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
                                    text: 'Item updated successfully.',
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

                // Handle History Modal
                $(document).on('click', '.open-history-modal', function(e) {
                    e.preventDefault();
                    var subId = $(this).data('id');
                    var modal = $('#historyModal');
                    modal.find('.modal-body').html(
                        '<div class="text-center py-4"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>'
                    );
                    modal.modal('show');
                    $.get('/approvals/history/' + subId)
                        .done(function(data) {
                            modal.find('.modal-body').html(data);
                        })
                        .fail(function() {
                            modal.find('.modal-body').html(
                                '<div class="alert alert-danger">Failed to load approval history</div>');
                        });
                });

                // Handle Add Remark Modal
                $(document).on('click', '.open-add-remark-modal', function(e) {
                    e.preventDefault();
                    var subId = $(this).data('id');
                    var modal = $('#addRemarkModal');
                    modal.find('#sub_id').val(subId);
                    modal.find('#remark').val('');
                    $.get('/remarks/get-remarks/' + subId, function(response) {
                        if (response.remarks && response.remarks.length > 0) {
                            modal.find('#remark').val(response.remarks[0].remark);
                        }
                    }).fail(function() {
                        console.log('Failed to load remarks');
                    });
                    modal.modal('show');
                });

                // Handle Add Remark Form Submission
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

                // Handle Remarks History Modal
                $(document).on('click', '.open-historyremark-modal', function(e) {
                    e.preventDefault();
                    var subId = $(this).data('id');
                    var modal = $('#historyremarkModal');
                    modal.find('.modal-body').html(
                        '<div class="text-center py-4"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>'
                    );
                    modal.modal('show');
                    $.get('/remarks/remark/' + subId)
                        .done(function(data) {
                            modal.find('.modal-body').html(data);
                        })
                        .fail(function() {
                            modal.find('.modal-body').html(
                                '<div class="alert alert-danger">Failed to load remarks history</div>');
                        });
                });

                // Handle Delete Button
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
                                        Swal.fire('Deleted!', response.message, 'success')
                                            .then(() => {
                                                location.reload();
                                            });
                                    } else {
                                        Swal.fire('Error!', response.message, 'error');
                                    }
                                },
                                error: function(xhr) {
                                    Swal.fire('Error!', xhr.responseJSON.message ||
                                        'Something went wrong', 'error');
                                }
                            });
                        }
                    });
                });

                // Handle Send Form Submission
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

                // Handle Approve Form Submission
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

                // Handle Disapprove Form Submission
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
`
