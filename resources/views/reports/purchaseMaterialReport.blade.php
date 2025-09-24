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
            <div class="row my-4">
                <div class="col-12">
                    <div class="card">
                        @if (session('sect') === 'Kadept')
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
                                                                        APPROVED BY KADIV
                                                                    @endif
                                                                </span>
                                                            @elseif ($submission->status == 5)
                                                                <span class="badge"
                                                                    style="background-color: #0080ff">APPROVED
                                                                    BY
                                                                    DIC</span>
                                                            @elseif ($submission->status == 6)
                                                                <span class="badge"
                                                                    style="background-color: #0080ff">APPROVED
                                                                    BY
                                                                    PIC BUDGETING</span>
                                                            @elseif ($submission->status == 7)
                                                                <span class="badge"
                                                                    style="background-color: #0080ff">APPROVED
                                                                    BY
                                                                    KADEP BUDGETING</span>
                                                            @elseif ($submission->status == 8)
                                                                <span class="badge bg-danger">DISAPPROVED BY
                                                                    KADEP</span>
                                                            @elseif ($submission->status == 9 && !$directDIC)
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
                                                    <button type="button"
                                                        class="btn btn-danger open-historyremark-modal"
                                                        data-id="{{ $submission->sub_id ?? '' }}">View Remarks</button>
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
                                            return in_array($submission->status, [2, 9]);
                                        });

                                        // Pemetaan bulan seperti di kode Staff
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

                                        // Kelompokkan submissions berdasarkan itm_id dan description
                                        $groupedItems = $submissions
                                            ->groupBy(function ($submission) {
                                                return ($submission->itm_id ?? '-') .
                                                    '-' .
                                                    ($submission->description ?? '-');
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
                                                        $months[$month] = $submission->price;
                                                        $totalAmount += $submission->price;
                                                    }
                                                }

                                                return [
                                                    'itm_id' => $first->itm_id ?? '-',
                                                    'description' => $first->description ?? '-',
                                                    'bdc_id' => $first->bdc_id ?? '-',
                                                    'business_partner' => $first->business_partner ?? '-',
                                                    'lob_id' => $first->lob_id ?? '-',
                                                    'price' => $first->price ?? 0,
                                                    'amount' => $totalAmount,
                                                    'workcenter' => $first->workcenter
                                                        ? $first->workcenter->workcenter
                                                        : '-',
                                                    'department' => $first->dept ? $first->dept->department : '-',
                                                    'months' => $months,
                                                    'sub_id' => $first->sub_id,
                                                    'id' => $first->id,
                                                    'status' => $first->status,
                                                ];
                                            });

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
                                        $grandTotal = $groupedItems->sum('amount');
                                    @endphp
                                    @if (in_array($submission->status, [2, 9]))
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
                                                        style="position: sticky; left: 80px; z-index: 110; background-color: #e9ecef; min-width: 180px; width: 180px;">
                                                        Description</th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 260px; z-index: 110; background-color: #e9ecef; min-width: 80px; width: 80px;">
                                                        R/NR</th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 340px; z-index: 110; background-color: #e9ecef; min-width: 120px; width: 120px;">
                                                        Business Partner</th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 460px; z-index: 110; background-color: #e9ecef; min-width: 120px; width: 120px;">
                                                        Line Of Business</th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 580px; z-index: 110; background-color: #e9ecef; min-width: 120px; width: 120px;">
                                                        Workcenter</th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 700px; z-index: 110; background-color: #e9ecef; min-width: 120px; width: 120px;">
                                                        Department</th>
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
                                                            style="position: sticky; left: 80px; z-index: 10; background-color: white; min-width: 180px; width: 180px;">
                                                            {{ $item['description'] }}</td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 260px; z-index: 10; background-color: white; min-width: 80px; width: 80px;">
                                                            {{ isset($budget_name[$item['bdc_id']]) ? $budget_name[$item['bdc_id']] : $item['bdc_id'] ?? '-' }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 340px; z-index: 10; background-color: white; min-width: 120px; width: 120px;">
                                                            {{ $item['business_partner'] }}</td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 460px; z-index: 10; background-color: white; min-width: 120px; width: 120px;">
                                                            {{ $line_businesses->firstWhere('line_business', $item['lob_id'])['line_business'] ?? ($item['lob_id'] ?? '-') }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 580px; z-index: 10; background-color: white; min-width: 120px; width: 120px;">
                                                            {{ $item['workcenter'] }}</td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 700px; z-index: 10; background-color: white; min-width: 120px; width: 120px;">
                                                            {{ $item['department'] }}</td>
                                                        @foreach ($months as $month)
                                                            <td class="border p-2 text-center"
                                                                style="min-width: 100px;">
                                                                @if (isset($item['months'][$month]) && $item['months'][$month] > 0)
                                                                    @php
                                                                        $monthlyData = $submissions->first(function (
                                                                            $submission,
                                                                        ) use ($month, $item, $monthMap) {
                                                                            return $monthMap[$submission->month] ===
                                                                                $month &&
                                                                                $submission->itm_id ===
                                                                                    $item['itm_id'] &&
                                                                                $submission->description ===
                                                                                    $item['description'];
                                                                        });
                                                                    @endphp
                                                                    @if ($item['status'] == 2 || $item['status'] == 9)
                                                                        <a href="#" class="editable-month"
                                                                            data-sub-id="{{ $item['sub_id'] }}"
                                                                            data-id="{{ $monthlyData->id ?? '' }}"
                                                                            data-month="{{ $month }}"
                                                                            data-price="{{ $monthlyData->price ?? $item['months'][$month] }}"
                                                                            data-itm-id="{{ $item['itm_id'] }}"
                                                                            data-description="{{ $item['description'] }}"
                                                                            data-bdc-id="{{ $monthlyData->bdc_id ?? $item['bdc_id'] }}"
                                                                            data-business-partner="{{ $monthlyData->business_partner ?? $item['business_partner'] }}"
                                                                            data-lob-id="{{ $monthlyData->lob_id ?? $item['lob_id'] }}"
                                                                            data-workcenter="{{ $monthlyData->workcenter->workcenter ?? $item['workcenter'] }}"
                                                                            data-workcenter-id="{{ $monthlyData->wct_id ?? '' }}"
                                                                            data-currency-id="{{ $monthlyData->cur_id ?? '' }}"
                                                                            title="Klik untuk mengedit data {{ $month }}">
                                                                            IDR
                                                                            {{ number_format($item['months'][$month], 2, ',', '.') }}
                                                                        </a>
                                                                    @else
                                                                        IDR
                                                                        {{ number_format($item['months'][$month], 2, ',', '.') }}
                                                                    @endif
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                        <td class="border p-2">IDR
                                                            {{ number_format($item['amount'], 2, ',', '.') }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="{{ $hasAction ? 20 : 19 }}"
                                                            class="border p-2 text-center">No Submissions found!</td>
                                                    </tr>
                                                @endforelse
                                                <tr class="bg-gray-100 font-bold">
                                                    <td colspan="7" class="border p-2 text-right"
                                                        style="position: sticky; left: 0; z-index: 10; background-color: #f8f9fa;">
                                                        Total</td>
                                                    @foreach ($months as $month)
                                                        <td class="border p-2"></td>
                                                    @endforeach
                                                    <td class="border p-2">IDR
                                                        {{ number_format($grandTotalAll, 2, ',', '.') }}</td>
                                                    @if ($hasAction)
                                                        <td class="border p-2"></td>
                                                    @endif
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    @if ($budgetPlans->hasPages())
                                        <div class="d-flex justify-content-center mt-4">
                                            {{ $budgetPlans->onEachSide(1)->links('pagination::bootstrap-5') }}
                                        </div>
                                    @endif
                                    <br>
                                </div>
                                <div class="d-flex justify-content-between mt-4">
                                    <button onclick="history.back()" type="button" class="btn btn-secondary me-2">
                                        <i class="fa-solid fa-arrow-left me-2"></i>Back</button>
                                    <div class="d-flex gap-3">
                                        @if (in_array($submission->status, [2, 9]))
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
                        @elseif(session('sect') === 'Kadiv')
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
                                                            @if ($submission->status == 3)
                                                                <span class="badge bg-warning">REQUIRES APPROVAL</span>
                                                            @elseif ($submission->status == 4)
                                                                <span class="badge"
                                                                    style="background-color: #0080ff">>APPROVED
                                                                    BY
                                                                    KADIV</span>
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
                                                        <button type="button"
                                                            class="btn btn-danger open-history-modal"
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
                                                    <button type="button"
                                                        class="btn open-add-remark-modal text-white"
                                                        style="background-color: #0080ff;"
                                                        data-id="{{ $submission->sub_id ?? '' }}">Add Remark</button>
                                                    <button type="button"
                                                        class="btn btn-danger open-historyremark-modal"
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
                                            return in_array($submission->status, [3, 10]);
                                        });

                                        // Pemetaan bulan seperti di kode Staff
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

                                        // Kelompokkan submissions berdasarkan itm_id dan description
                                        $groupedItems = $submissions
                                            ->groupBy(function ($submission) {
                                                return ($submission->itm_id ?? '-') .
                                                    '-' .
                                                    ($submission->description ?? '-');
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
                                                        $months[$month] = $submission->price;
                                                        $totalAmount += $submission->price;
                                                    }
                                                }

                                                return [
                                                    'itm_id' => $first->itm_id ?? '-',
                                                    'description' => $first->description ?? '-',
                                                    'bdc_id' => $first->bdc_id ?? '-',
                                                    'business_partner' => $first->business_partner ?? '-',
                                                    'lob_id' => $first->lob_id ?? '-',
                                                    'price' => $first->price ?? 0,
                                                    'amount' => $totalAmount,
                                                    'workcenter' => $first->workcenter
                                                        ? $first->workcenter->workcenter
                                                        : '-',
                                                    'department' => $first->dept ? $first->dept->department : '-',
                                                    'months' => $months,
                                                    'sub_id' => $first->sub_id,
                                                    'id' => $first->id,
                                                    'status' => $first->status,
                                                ];
                                            });

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
                                        $grandTotal = $groupedItems->sum('amount');
                                    @endphp
                                    @if (in_array($submission->status, [3, 10]))
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
                                                        style="position: sticky; left: 80px; z-index: 110; background-color: #e9ecef; min-width: 180px; width: 180px;">
                                                        Description</th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 260px; z-index: 110; background-color: #e9ecef; min-width: 80px; width: 80px;">
                                                        R/NR</th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 340px; z-index: 110; background-color: #e9ecef; min-width: 120px; width: 120px;">
                                                        Business Partner</th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 460px; z-index: 110; background-color: #e9ecef; min-width: 120px; width: 120px;">
                                                        Line Of Business</th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 580px; z-index: 110; background-color: #e9ecef; min-width: 120px; width: 120px;">
                                                        Workcenter</th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 700px; z-index: 110; background-color: #e9ecef; min-width: 120px; width: 120px;">
                                                        Department</th>
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
                                                            style="position: sticky; left: 80px; z-index: 10; background-color: white; min-width: 180px; width: 180px;">
                                                            {{ $item['description'] }}</td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 260px; z-index: 10; background-color: white; min-width: 80px; width: 80px;">
                                                            {{ isset($budget_name[$item['bdc_id']]) ? $budget_name[$item['bdc_id']] : $item['bdc_id'] ?? '-' }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 340px; z-index: 10; background-color: white; min-width: 120px; width: 120px;">
                                                            {{ $item['business_partner'] }}</td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 460px; z-index: 10; background-color: white; min-width: 120px; width: 120px;">
                                                            {{ $line_businesses->firstWhere('line_business', $item['lob_id'])['line_business'] ?? ($item['lob_id'] ?? '-') }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 580px; z-index: 10; background-color: white; min-width: 120px; width: 120px;">
                                                            {{ $item['workcenter'] }}</td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 700px; z-index: 10; background-color: white; min-width: 120px; width: 120px;">
                                                            {{ $item['department'] }}</td>
                                                        @foreach ($months as $month)
                                                            <td class="border p-2 text-center"
                                                                style="min-width: 100px;">
                                                                @if (isset($item['months'][$month]) && $item['months'][$month] > 0)
                                                                    @php
                                                                        $monthlyData = $submissions->first(function (
                                                                            $submission,
                                                                        ) use ($month, $item, $monthMap) {
                                                                            return $monthMap[$submission->month] ===
                                                                                $month &&
                                                                                $submission->itm_id ===
                                                                                    $item['itm_id'] &&
                                                                                $submission->description ===
                                                                                    $item['description'];
                                                                        });
                                                                    @endphp
                                                                    @if ($item['status'] == 3 || $item['status'] == 10)
                                                                        <a href="#" class="editable-month"
                                                                            data-sub-id="{{ $item['sub_id'] }}"
                                                                            data-id="{{ $monthlyData->id ?? '' }}"
                                                                            data-month="{{ $month }}"
                                                                            data-price="{{ $monthlyData->price ?? $item['months'][$month] }}"
                                                                            data-itm-id="{{ $item['itm_id'] }}"
                                                                            data-description="{{ $item['description'] }}"
                                                                            data-bdc-id="{{ $monthlyData->bdc_id ?? $item['bdc_id'] }}"
                                                                            data-business-partner="{{ $monthlyData->business_partner ?? $item['business_partner'] }}"
                                                                            data-lob-id="{{ $monthlyData->lob_id ?? $item['lob_id'] }}"
                                                                            data-workcenter="{{ $monthlyData->workcenter->workcenter ?? $item['workcenter'] }}"
                                                                            data-workcenter-id="{{ $monthlyData->wct_id ?? '' }}"
                                                                            data-currency-id="{{ $monthlyData->cur_id ?? '' }}"
                                                                            title="Klik untuk mengedit data {{ $month }}">
                                                                            IDR
                                                                            {{ number_format($item['months'][$month], 2, ',', '.') }}
                                                                        </a>
                                                                    @else
                                                                        IDR
                                                                        {{ number_format($item['months'][$month], 2, ',', '.') }}
                                                                    @endif
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                        <td class="border p-2">IDR
                                                            {{ number_format($item['amount'], 2, ',', '.') }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="{{ $hasAction ? 20 : 19 }}"
                                                            class="border p-2 text-center">No Submissions found!</td>
                                                    </tr>
                                                @endforelse
                                                <tr class="bg-gray-100 font-bold">
                                                    <td colspan="7" class="border p-2 text-right"
                                                        style="position: sticky; left: 0; z-index: 10; background-color: #f8f9fa;">
                                                        Total</td>
                                                    @foreach ($months as $month)
                                                        <td class="border p-2"></td>
                                                    @endforeach
                                                    <td class="border p-2">IDR
                                                        {{ number_format($grandTotalAll, 2, ',', '.') }}</td>
                                                    @if ($hasAction)
                                                        <td class="border p-2"></td>
                                                    @endif
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    @if ($budgetPlans->hasPages())
                                        <div class="d-flex justify-content-center mt-4">
                                            {{ $budgetPlans->onEachSide(1)->links('pagination::bootstrap-5') }}
                                        </div>
                                    @endif
                                    <br>
                                </div>
                                <div class="d-flex justify-content-between mt-4">
                                    <button onclick="history.back()" type="button"
                                        class="btn btn-secondary me-2">Back</button>
                                </div>
                            </div>
                        @elseif(session('sect') === 'DIC')
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
$directDIC = in_array($submission->dpt_id, [
    '6111',
    '6121',
    '4211',
                                                        ]);

                                                    @endphp
                                                    <p>Status: <span class="font-bold">
                                                            @if ($submission->status == 4)
                                                                <span class="badge bg-warning">REQUIRES APPROVAL</span>
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
                                                        <button type="button"
                                                            class="btn btn-danger open-history-modal"
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
                                                    <button type="button"
                                                        class="btn open-add-remark-modal text-white"
                                                        style="background-color: #0080ff;"
                                                        data-id="{{ $submission->sub_id ?? '' }}">Add Remark</button>
                                                    <button type="button"
                                                        class="btn btn-danger open-historyremark-modal"
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
                                            return in_array($submission->status, [4, 11]);
                                        });

                                        // Pemetaan bulan seperti di kode Staff
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

                                        // Kelompokkan submissions berdasarkan itm_id dan description
                                        $groupedItems = $submissions
                                            ->groupBy(function ($submission) {
                                                return ($submission->itm_id ?? '-') .
                                                    '-' .
                                                    ($submission->description ?? '-');
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
                                                        $months[$month] = $submission->price;
                                                        $totalAmount += $submission->price;
                                                    }
                                                }

                                                return [
                                                    'itm_id' => $first->itm_id ?? '-',
                                                    'description' => $first->description ?? '-',
                                                    'bdc_id' => $first->bdc_id ?? '-',
                                                    'business_partner' => $first->business_partner ?? '-',
                                                    'lob_id' => $first->lob_id ?? '-',
                                                    'price' => $first->price ?? 0,
                                                    'amount' => $totalAmount,
                                                    'workcenter' => $first->workcenter
                                                        ? $first->workcenter->workcenter
                                                        : '-',
                                                    'department' => $first->dept ? $first->dept->department : '-',
                                                    'months' => $months,
                                                    'sub_id' => $first->sub_id,
                                                    'id' => $first->id,
                                                    'status' => $first->status,
                                                ];
                                            });

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
                                        $grandTotal = $groupedItems->sum('amount');
                                    @endphp
                                    @if (in_array($submission->status, [4, 11]))
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
                                                        style="position: sticky; left: 80px; z-index: 110; background-color: #e9ecef; min-width: 180px; width: 180px;">
                                                        Description</th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 260px; z-index: 110; background-color: #e9ecef; min-width: 80px; width: 80px;">
                                                        R/NR</th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 340px; z-index: 110; background-color: #e9ecef; min-width: 120px; width: 120px;">
                                                        Business Partner</th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 460px; z-index: 110; background-color: #e9ecef; min-width: 120px; width: 120px;">
                                                        Line Of Business</th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 580px; z-index: 110; background-color: #e9ecef; min-width: 120px; width: 120px;">
                                                        Workcenter</th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 700px; z-index: 110; background-color: #e9ecef; min-width: 120px; width: 120px;">
                                                        Department</th>
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
                                                            style="position: sticky; left: 80px; z-index: 10; background-color: white; min-width: 180px; width: 180px;">
                                                            {{ $item['description'] }}</td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 260px; z-index: 10; background-color: white; min-width: 80px; width: 80px;">
                                                            {{ isset($budget_name[$item['bdc_id']]) ? $budget_name[$item['bdc_id']] : $item['bdc_id'] ?? '-' }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 340px; z-index: 10; background-color: white; min-width: 120px; width: 120px;">
                                                            {{ $item['business_partner'] }}</td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 460px; z-index: 10; background-color: white; min-width: 120px; width: 120px;">
                                                            {{ $line_businesses->firstWhere('line_business', $item['lob_id'])['line_business'] ?? ($item['lob_id'] ?? '-') }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 580px; z-index: 10; background-color: white; min-width: 120px; width: 120px;">
                                                            {{ $item['workcenter'] }}</td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 700px; z-index: 10; background-color: white; min-width: 120px; width: 120px;">
                                                            {{ $item['department'] }}</td>
                                                        @foreach ($months as $month)
                                                            <td class="border p-2 text-center"
                                                                style="min-width: 100px;">
                                                                @if (isset($item['months'][$month]) && $item['months'][$month] > 0)
                                                                    @php
                                                                        $monthlyData = $submissions->first(function (
                                                                            $submission,
                                                                        ) use ($month, $item, $monthMap) {
                                                                            return $monthMap[$submission->month] ===
                                                                                $month &&
                                                                                $submission->itm_id ===
                                                                                    $item['itm_id'] &&
                                                                                $submission->description ===
                                                                                    $item['description'];
                                                                        });
                                                                    @endphp
                                                                    @if ($item['status'] == 4 || $item['status'] == 11)
                                                                        <a href="#" class="editable-month"
                                                                            data-sub-id="{{ $item['sub_id'] }}"
                                                                            data-id="{{ $monthlyData->id ?? '' }}"
                                                                            data-month="{{ $month }}"
                                                                            data-price="{{ $monthlyData->price ?? $item['months'][$month] }}"
                                                                            data-itm-id="{{ $item['itm_id'] }}"
                                                                            data-description="{{ $item['description'] }}"
                                                                            data-bdc-id="{{ $monthlyData->bdc_id ?? $item['bdc_id'] }}"
                                                                            data-business-partner="{{ $monthlyData->business_partner ?? $item['business_partner'] }}"
                                                                            data-lob-id="{{ $monthlyData->lob_id ?? $item['lob_id'] }}"
                                                                            data-workcenter="{{ $monthlyData->workcenter->workcenter ?? $item['workcenter'] }}"
                                                                            data-workcenter-id="{{ $monthlyData->wct_id ?? '' }}"
                                                                            data-currency-id="{{ $monthlyData->cur_id ?? '' }}"
                                                                            title="Klik untuk mengedit data {{ $month }}">
                                                                            IDR
                                                                            {{ number_format($item['months'][$month], 2, ',', '.') }}
                                                                        </a>
                                                                    @else
                                                                        IDR
                                                                        {{ number_format($item['months'][$month], 2, ',', '.') }}
                                                                    @endif
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                        <td class="border p-2">IDR
                                                            {{ number_format($item['amount'], 2, ',', '.') }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="{{ $hasAction ? 20 : 19 }}"
                                                            class="border p-2 text-center">No Submissions found!</td>
                                                    </tr>
                                                @endforelse
                                                <tr class="bg-gray-100 font-bold">
                                                    <td colspan="7" class="border p-2 text-right"
                                                        style="position: sticky; left: 0; z-index: 10; background-color: #f8f9fa;">
                                                        Total</td>
                                                    @foreach ($months as $month)
                                                        <td class="border p-2"></td>
                                                    @endforeach
                                                    <td class="border p-2">IDR
                                                        {{ number_format($grandTotalAll, 2, ',', '.') }}</td>
                                                    @if ($hasAction)
                                                        <td class="border p-2"></td>
                                                    @endif
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    @if ($budgetPlans->hasPages())
                                        <div class="d-flex justify-content-center mt-4">
                                            {{ $budgetPlans->onEachSide(1)->links('pagination::bootstrap-5') }}
                                        </div>
                                    @endif
                                    <br>
                                </div>
                                <div class="d-flex justify-content-between mt-4">
                                    <button onclick="history.back()" type="button"
                                        class="btn btn-secondary me-2">Back</button>
                                </div>
                            </div>
                        @elseif (session('sect') === 'PIC' && session('dept') === '6121')
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
                                                                <span class="badge"
                                                                    style="background-color: #0080ff">
                                                                    @if ($directDIC)
                                                                        APPROVED BY KADEPT
                                                                    @else
                                                                        APPROVED BY KADIV
                                                                    @endif
                                                                </span>
                                                            @elseif ($submission->status == 5)
                                                                <span class="badge"
                                                                    style="background-color: #0080ff">APPROVED
                                                                    BY
                                                                    DIC</span>
                                                            @elseif ($submission->status == 6)
                                                                <span class="badge"
                                                                    style="background-color: #0080ff">APPROVED
                                                                    BY
                                                                    PIC BUDGETING</span>
                                                            @elseif ($submission->status == 7)
                                                                <span class="badge"
                                                                    style="background-color: #0080ff">APPROVED
                                                                    BY
                                                                    KADEP BUDGETING</span>
                                                            @elseif ($submission->status == 8)
                                                                <span class="badge bg-danger">DISAPPROVED BY
                                                                    KADEP</span>
                                                            @elseif ($submission->status == 9 && !$directDIC)
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
                                                        <button type="button"
                                                            class="btn btn-danger open-history-modal"
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
                                                                    class="font-bold">{{ $remark->remark }}</span>
                                                            </p>
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
                                                    <button type="button"
                                                        class="btn open-add-remark-modal text-white"
                                                        style="background-color: #0080ff;"
                                                        data-id="{{ $submission->sub_id ?? '' }}">Add Remark</button>
                                                    <button type="button"
                                                        class="btn btn-danger open-historyremark-modal"
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
                                            return in_array($submission->status, [5, 12]);
                                        });

                                        // Pemetaan bulan seperti di kode Staff
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

                                        // Kelompokkan submissions berdasarkan itm_id dan description
                                        $groupedItems = $submissions
                                            ->groupBy(function ($submission) {
                                                return ($submission->itm_id ?? '-') .
                                                    '-' .
                                                    ($submission->description ?? '-');
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
                                                        $months[$month] = $submission->price;
                                                        $totalAmount += $submission->price;
                                                    }
                                                }

                                                return [
                                                    'itm_id' => $first->itm_id ?? '-',
                                                    'description' => $first->description ?? '-',
                                                    'bdc_id' => $first->bdc_id ?? '-',
                                                    'business_partner' => $first->business_partner ?? '-',
                                                    'lob_id' => $first->lob_id ?? '-',
                                                    'price' => $first->price ?? 0,
                                                    'amount' => $totalAmount,
                                                    'workcenter' => $first->workcenter
                                                        ? $first->workcenter->workcenter
                                                        : '-',
                                                    'department' => $first->dept ? $first->dept->department : '-',
                                                    'months' => $months,
                                                    'sub_id' => $first->sub_id,
                                                    'id' => $first->id,
                                                    'status' => $first->status,
                                                ];
                                            });

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
                                        $grandTotal = $groupedItems->sum('amount');
                                    @endphp
                                    @if (in_array($submission->status, [5, 12]))
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
                                                        style="position: sticky; left: 80px; z-index: 110; background-color: #e9ecef; min-width: 180px; width: 180px;">
                                                        Description</th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 260px; z-index: 110; background-color: #e9ecef; min-width: 80px; width: 80px;">
                                                        R/NR</th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 340px; z-index: 110; background-color: #e9ecef; min-width: 120px; width: 120px;">
                                                        Business Partner</th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 460px; z-index: 110; background-color: #e9ecef; min-width: 120px; width: 120px;">
                                                        Line Of Business</th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 580px; z-index: 110; background-color: #e9ecef; min-width: 120px; width: 120px;">
                                                        Workcenter</th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 700px; z-index: 110; background-color: #e9ecef; min-width: 120px; width: 120px;">
                                                        Department</th>
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
                                                            style="position: sticky; left: 80px; z-index: 10; background-color: white; min-width: 180px; width: 180px;">
                                                            {{ $item['description'] }}</td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 260px; z-index: 10; background-color: white; min-width: 80px; width: 80px;">
                                                            {{ isset($budget_name[$item['bdc_id']]) ? $budget_name[$item['bdc_id']] : $item['bdc_id'] ?? '-' }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 340px; z-index: 10; background-color: white; min-width: 120px; width: 120px;">
                                                            {{ $item['business_partner'] }}</td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 460px; z-index: 10; background-color: white; min-width: 120px; width: 120px;">
                                                            {{ $line_businesses->firstWhere('line_business', $item['lob_id'])['line_business'] ?? ($item['lob_id'] ?? '-') }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 580px; z-index: 10; background-color: white; min-width: 120px; width: 120px;">
                                                            {{ $item['workcenter'] }}</td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 700px; z-index: 10; background-color: white; min-width: 120px; width: 120px;">
                                                            {{ $item['department'] }}</td>
                                                        @foreach ($months as $month)
                                                            <td class="border p-2 text-center"
                                                                style="min-width: 100px;">
                                                                @if (isset($item['months'][$month]) && $item['months'][$month] > 0)
                                                                    @php
                                                                        $monthlyData = $submissions->first(function (
                                                                            $submission,
                                                                        ) use ($month, $item, $monthMap) {
                                                                            return $monthMap[$submission->month] ===
                                                                                $month &&
                                                                                $submission->itm_id ===
                                                                                    $item['itm_id'] &&
                                                                                $submission->description ===
                                                                                    $item['description'];
                                                                        });
                                                                    @endphp
                                                                    @if ($item['status'] == 5 || $item['status'] == 12)
                                                                        <a href="#" class="editable-month"
                                                                            data-sub-id="{{ $item['sub_id'] }}"
                                                                            data-id="{{ $monthlyData->id ?? '' }}"
                                                                            data-month="{{ $month }}"
                                                                            data-price="{{ $monthlyData->price ?? $item['months'][$month] }}"
                                                                            data-itm-id="{{ $item['itm_id'] }}"
                                                                            data-description="{{ $item['description'] }}"
                                                                            data-bdc-id="{{ $monthlyData->bdc_id ?? $item['bdc_id'] }}"
                                                                            data-business-partner="{{ $monthlyData->business_partner ?? $item['business_partner'] }}"
                                                                            data-lob-id="{{ $monthlyData->lob_id ?? $item['lob_id'] }}"
                                                                            data-workcenter="{{ $monthlyData->workcenter->workcenter ?? $item['workcenter'] }}"
                                                                            data-workcenter-id="{{ $monthlyData->wct_id ?? '' }}"
                                                                            data-currency-id="{{ $monthlyData->cur_id ?? '' }}"
                                                                            title="Klik untuk mengedit data {{ $month }}">
                                                                            IDR
                                                                            {{ number_format($item['months'][$month], 2, ',', '.') }}
                                                                        </a>
                                                                    @else
                                                                        IDR
                                                                        {{ number_format($item['months'][$month], 2, ',', '.') }}
                                                                    @endif
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                        <td class="border p-2">IDR
                                                            {{ number_format($item['amount'], 2, ',', '.') }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="{{ $hasAction ? 20 : 19 }}"
                                                            class="border p-2 text-center">No Submissions found!</td>
                                                    </tr>
                                                @endforelse
                                                <tr class="bg-gray-100 font-bold">
                                                    <td colspan="7" class="border p-2 text-right"
                                                        style="position: sticky; left: 0; z-index: 10; background-color: #f8f9fa;">
                                                        Total</td>
                                                    @foreach ($months as $month)
                                                        <td class="border p-2"></td>
                                                    @endforeach
                                                    <td class="border p-2">IDR
                                                        {{ number_format($grandTotalAll, 2, ',', '.') }}</td>
                                                    @if ($hasAction)
                                                        <td class="border p-2"></td>
                                                    @endif
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    @if ($budgetPlans->hasPages())
                                        <div class="d-flex justify-content-center mt-4">
                                            {{ $budgetPlans->onEachSide(1)->links('pagination::bootstrap-5') }}
                                        </div>
                                    @endif
                                    <br>
                                </div>
                                <div class="d-flex justify-content-between mt-4">
                                    <button onclick="history.back()" type="button" class="btn btn-secondary me-2">
                                        <i class="fa-solid fa-arrow-left me-2"></i>Back</button>
                                    <div class="d-flex gap-3">
                                        @if (in_array($submission->status, [5, 12]))
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
                        @else
                            <div class="card-header bg-danger">
                                <h4 style="font-weight: bold;" class="text-white"><i
                                        class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>PROPOSAL DETAIL
                                    {{ $account_name }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="card-header rounded-0 col-md-6 bg-secondary text-white py-2 px-2">
                                    <h6 class="mb-0 text-white">Approval Status</h6>
                                    <!-- [MODIFIKASI] Perbaiki tag penutup -->
                                </div>
                                <!-- Approval Status -->
                                <div class="bg-green-100 col-md-6 p-4 rounded-0 shadow mb-4">
                                    @if ($submissions->isNotEmpty())
                                        @php
                                            $submission = $submissions->first();
                                            // Fetch the approval record for the submission where approve_by matches the logged-in user's npk
$approval = \App\Models\Approval::where('sub_id', $submission->sub_id)
    ->where('approve_by', Auth::user()->npk)
    ->first();
$directDIC = in_array($submission->dpt_id, ['6111', '6121', '4211']);
                                        @endphp
                                        <p>Status: <span class="font-bold">
                                                @if ($submission->status == 1)
                                                    <span class="badge bg-warning">DRAFT</span>
                                                @elseif ($submission->status == 2)
                                                    <span class="badge bg-secondary">UNDER REVIEW KADEP</span>
                                                @elseif ($submission->status == 3 && !$directDIC)
                                                    <span class="badge" style="background-color: #0080ff">APPROVED BY
                                                        KADEPT</span>
                                                @elseif ($submission->status == 4)
                                                    <span class="badge" style="background-color: #0080ff">
                                                        @if ($directDIC)
                                                            APPROVED BY KADEPT
                                                        @else
                                                            APPROVED BY KADIV
                                                        @endif
                                                    </span>
                                                @elseif ($submission->status == 5)
                                                    <span class="badge" style="background-color: #0080ff">APPROVED BY
                                                        DIC</span>
                                                @elseif ($submission->status == 6)
                                                    <span class="badge" style="background-color: #0080ff">APPROVED BY
                                                        PIC BUDGETING</span>
                                                @elseif ($submission->status == 7)
                                                    <span class="badge" style="background-color: #0080ff">APPROVED BY
                                                        KADEP BUDGETING</span>
                                                @elseif ($submission->status == 8)
                                                    <span class="badge bg-danger">DISAPPROVED BY KADEP</span>
                                                @elseif ($submission->status == 9 && !$directDIC)
                                                    <span class="badge bg-danger">DISAPPROVED BY KADIV</span>
                                                @elseif ($submission->status == 10)
                                                    <span class="badge bg-danger">DISAPPROVED BY DIC</span>
                                                @elseif ($submission->status == 11)
                                                    <span class="badge bg-danger">DISAPPROVED BY PIC BUDGETING</span>
                                                @elseif ($submission->status == 12)
                                                    <span class="badge bg-danger">DISAPPROVED BY KADEP BUDGETING</span>
                                                @else
                                                    <span class="badge bg-danger">REJECTED</span>
                                                @endif
                                            </span></p>
                                        <p>Date: {{ $approval ? $approval->created_at->format('d-m-Y H:i') : '-' }}
                                        </p>

                                        <div class="mt-4 flex space-x-2">
                                            <button type="button" class="btn btn-danger open-history-modal"
                                                data-id="{{ $submission->sub_id }}">History Approval</button>
                                        </div>
                                    @else
                                        <p>No submission data available</p>
                                    @endif
                                </div>
                                <div class="card-header bg-secondary text-white py-2 px-2">
                                    <h6 class="mb-0 text-white">Item of Purchase</h6>
                                    <!-- [MODIFIKASI] Perbaiki tag penutup -->
                                </div>
                                <!-- Item Table -->
                                <div class="bg-white p-4 rounded shadow mb-4">
                                    @php
                                        $hasAction = $submissions->contains(function ($submission) {
                                            return in_array($submission->status, [1, 8]);
                                        });

                                        // Pemetaan bulan seperti di businessReport.blade.php
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

                                        // Kelompokkan submissions berdasarkan itm_id dan description
                                        $groupedItems = $submissions
                                            ->groupBy(function ($submission) {
                                                return ($submission->itm_id ?? '-') .
                                                    '-' .
                                                    ($submission->description ?? '-');
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
                                                        $months[$month] = $submission->price;
                                                        $totalAmount += $submission->price;
                                                    }
                                                }

                                                return [
                                                    'itm_id' => $first->itm_id ?? '-',
                                                    'description' => $first->description ?? '-',
                                                    'bdc_id' => $first->bdc_id ?? '-',
                                                    'business_partner' => $first->business_partner ?? '-',
                                                    'lob_id' => $first->lob_id ?? '-',
                                                    'price' => $first->price ?? 0,
                                                    'amount' => $totalAmount,
                                                    'workcenter' => $first->workcenter
                                                        ? $first->workcenter->workcenter
                                                        : '-',
                                                    'department' => $first->dept ? $first->dept->department : '-',
                                                    'months' => $months,
                                                    'sub_id' => $first->sub_id,
                                                    'id' => $first->id,
                                                    'status' => $first->status,
                                                ];
                                            });

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
                                        $grandTotal = $groupedItems->sum('amount');
                                    @endphp
                                    @if (in_array($submission->status, [1, 8]))
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
                                                        style="position: sticky; left: 80px; z-index: 110; background-color: #e9ecef; min-width: 180px; width: 180px;">
                                                        Description</th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 260px; z-index: 110; background-color: #e9ecef; min-width: 80px; width: 80px;">
                                                        R/NR</th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 340px; z-index: 110; background-color: #e9ecef; min-width: 120px; width: 120px;">
                                                        Business Partner</th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 460px; z-index: 110; background-color: #e9ecef; min-width: 120px; width: 120px;">
                                                        Line Of Business</th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 580px; z-index: 110; background-color: #e9ecef; min-width: 120px; width: 120px;">
                                                        Workcenter</th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 700px; z-index: 110; background-color: #e9ecef; min-width: 120px; width: 120px;">
                                                        Department</th>
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
                                                            style="position: sticky; left: 80px; z-index: 10; background-color: white; min-width: 180px; width: 180px;">
                                                            {{ $item['description'] }}</td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 260px; z-index: 10; background-color: white; min-width: 80px; width: 80px;">
                                                            {{ isset($budget_name[$item['bdc_id']]) ? $budget_name[$item['bdc_id']] : $item['bdc_id'] ?? '-' }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 340px; z-index: 10; background-color: white; min-width: 120px; width: 120px;">
                                                            {{ $item['business_partner'] }}</td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 460px; z-index: 10; background-color: white; min-width: 120px; width: 120px;">
                                                            {{ $line_businesses->firstWhere('line_business', $item['lob_id'])['line_business'] ?? ($item['lob_id'] ?? '-') }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 580px; z-index: 10; background-color: white; min-width: 120px; width: 120px;">
                                                            {{ $item['workcenter'] }}</td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 700px; z-index: 10; background-color: white; min-width: 120px; width: 120px;">
                                                            {{ $item['department'] }}</td>
                                                        @foreach ($months as $month)
                                                            <td class="border p-2 text-center"
                                                                style="min-width: 100px;">
                                                                @if (isset($item['months'][$month]) && $item['months'][$month] > 0)
                                                                    @php
                                                                        $monthlyData = $submissions->first(function (
                                                                            $submission,
                                                                        ) use ($month, $item, $monthMap) {
                                                                            return $monthMap[$submission->month] ===
                                                                                $month &&
                                                                                $submission->itm_id ===
                                                                                    $item['itm_id'] &&
                                                                                $submission->description ===
                                                                                    $item['description'];
                                                                        });
                                                                    @endphp
                                                                    @if ($item['status'] == 1)
                                                                        <a href="#" class="editable-month"
                                                                            data-sub-id="{{ $item['sub_id'] }}"
                                                                            data-id="{{ $monthlyData->id ?? '' }}"
                                                                            data-month="{{ $month }}"
                                                                            data-price="{{ $monthlyData->price ?? $item['months'][$month] }}"
                                                                            data-itm-id="{{ $item['itm_id'] }}"
                                                                            data-description="{{ $item['description'] }}"
                                                                            data-bdc-id="{{ $monthlyData->bdc_id ?? $item['bdc_id'] }}"
                                                                            data-business-partner="{{ $monthlyData->business_partner ?? $item['business_partner'] }}"
                                                                            data-lob-id="{{ $monthlyData->lob_id ?? $item['lob_id'] }}"
                                                                            data-workcenter="{{ $monthlyData->workcenter->workcenter ?? $item['workcenter'] }}"
                                                                            data-workcenter-id="{{ $monthlyData->wct_id ?? '' }}"
                                                                            data-currency-id="{{ $monthlyData->cur_id ?? '' }}"
                                                                            title="Klik untuk mengedit data {{ $month }}">
                                                                            IDR
                                                                            {{ number_format($item['months'][$month], 2, ',', '.') }}
                                                                        </a>
                                                                    @else
                                                                        IDR
                                                                        {{ number_format($item['months'][$month], 2, ',', '.') }}
                                                                    @endif
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                        <td class="border p-2">IDR
                                                            {{ number_format($item['amount'], 2, ',', '.') }}</td>
                                                    @empty
                                                    <tr>
                                                        <td colspan="{{ $hasAction ? 19 : 18 }}"
                                                            class="border p-2 text-center">No Submissions found!</td>
                                                    </tr>
                                                @endforelse



                                                <tr class="bg-gray-100 font-bold">
                                                    <td colspan="7" class="border p-2 text-right"
                                                        style="position: sticky; left: 0; z-index: 10; background-color: #f8f9fa;">
                                                        Total</td>
                                                    @foreach ($months as $month)
                                                        <td class="border p-2"></td>
                                                    @endforeach
                                                    <td class="border p-2">IDR
                                                        {{ number_format($grandTotalAll, 2, ',', '.') }}</td>
                                                    @if ($hasAction)
                                                        <td class="border p-2"></td>
                                                    @endif
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    @if ($budgetPlans->hasPages())
                                        <div class="d-flex justify-content-center mt-4">
                                            {{ $budgetPlans->onEachSide(1)->links('pagination::bootstrap-5') }}
                                        </div>
                                    @endif
                                    <br>
                                </div>
                                <div class="d-flex justify-content-between mt-4">
                                    <button onclick="history.back()" type="button"
                                        class="btn btn-secondary me-2">Back</button>
                                    <div class="d-flex">
                                        @if (in_array($submission->status, [1, 8]))
                                            <form action="{{ route('submissions.submit', $submission->sub_id) }}"
                                                method="POST" class="send-form">
                                                @csrf
                                                <button type="submit" class="btn text-white"
                                                    style="background-color: #0080ff;">
                                                    <i class="fas fa-paper-plane mr-2"></i> SEND
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

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
                                                    <input type="text" id="display_month" class="form-control"
                                                        readonly>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Item</label>
                                                    <input type="text" id="edit_month_itm_id" class="form-control"
                                                        readonly>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Description</label>
                                                    <textarea id="edit_month_description" class="form-control" readonly></textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Budget Code</label>
                                                    <select name="bdc_id" id="edit_month_bdc_id"
                                                        class="form-control select" required>
                                                        @foreach (\App\Models\BudgetCode::orderBy('budget_name', 'asc')->get() as $budgetCode)
                                                            <option value="{{ $budgetCode->bdc_id }}">
                                                                {{ $budgetCode->budget_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Business Partner</label>
                                                    <input type="text" name="business_partner"
                                                        id="edit_month_business_partner" class="form-control"
                                                        required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Line Of Business</label>
                                                    <select name="lob_id" id="edit_month_lob_id"
                                                        class="form-control select" required>
                                                        @foreach ($line_businesses as $lob_id => $line)
                                                            <option value="{{ $line }}">{{ $line }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="edit_month_cur_id" class="form-label">Mata
                                                        Uang</label>
                                                    <select name="cur_id" id="edit_month_cur_id"
                                                        class="form-control select" required>
                                                        <option value="" data-nominal="1">Rp</option>
                                                        @foreach (\App\Models\Currency::orderBy('currency', 'asc')->get() as $currency)
                                                            <option value="{{ $currency->cur_id }}"
                                                                data-nominal="{{ $currency->nominal }}">
                                                                {{ $currency->currency }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="edit_month_price" class="form-label">Harga</label>
                                                    <input type="number" name="price" id="edit_month_price"
                                                        class="form-control" required min="0" step="0.01">
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="edit_month_amount_display" class="form-label">Jumlah
                                                        (IDR)</label>
                                                    <input type="text" id="edit_month_amount_display"
                                                        class="form-control" readonly>
                                                    <input type="hidden" name="amount" id="edit_month_amount">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="edit_month_wct_id"
                                                        class="form-label">Workcenter</label>
                                                    <select name="wct_id" id="edit_month_wct_id"
                                                        class="form-control select" required>
                                                        @foreach (\App\Models\Workcenter::orderBy('workcenter', 'asc')->get() as $workcenter)
                                                            <option value="{{ $workcenter->wct_id }}">
                                                                {{ $workcenter->workcenter }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger me-auto"
                                                    id="deleteMonthButton">Hapus Data</button>
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
                                        <h5 class="modal-title text-white">Tambah Item Baru</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="addItemForm" method="POST"
                                            action="{{ route('submissions.add-item', $submission->sub_id) }}">
                                            @csrf
                                            <input type="hidden" name="sub_id" id="sub_id"
                                                value="{{ $submission->sub_id }}">
                                            <input type="hidden" name="acc_id" id="acc_id"
                                                value="{{ $submissions->first()->acc_id ?? '' }}">
                                            <input type="hidden" name="purpose" id="purpose"
                                                value="{{ $submission->purpose ?? '' }}">

                                            <!-- Two-Column Layout -->
                                            <div class="row">
                                                <!-- Left Column -->
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Budget Code <span
                                                                class="text-danger">*</span></label>
                                                        <!-- [MODIFIKASI] Ganti name dengan budget_name -->
                                                        <select name="bdc_id" id="bdc_id"
                                                            class="form-control select" required>
                                                            @foreach (\App\Models\BudgetCode::orderBy('budget_name', 'asc')->get() as $budgetCode)
                                                                <option value="{{ $budgetCode->bdc_id }}"
                                                                    @selected(old('bdc_id') === $budgetCode->bdc_id)>
                                                                    {{ $budgetCode->budget_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Business Partner <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" name="business_partner"
                                                            id="business_partner" class="form-control"
                                                            placeholder="Masukkan business partner" required
                                                            value="{{ old('business_partner') }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Line Of Business <span
                                                                class="text-danger">*</span></label>
                                                        <select name="lob_id" id="lob_id"
                                                            class="form-control select" required>
                                                            @foreach ($line_businesses as $lob_id => $line)
                                                                <option value="{{ $lob_id }}"
                                                                    @selected(old('lob_id') == $line)>{{ $line }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Item <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" name="itm_id" id="itm_id"
                                                            class="form-control" placeholder="Masukkan item" required
                                                            value="{{ old('itm_id') }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Description <span
                                                                class="text-danger">*</span></label>
                                                        <textarea class="form-control" name="description" id="description" placeholder="Masukkan deskripsi" required>{{ old('description') }}</textarea>
                                                    </div>
                                                </div>
                                                <!-- Right Column -->
                                                <div class="col-md-6">
                                                    <div class="row mb-3">
                                                        <!-- Currency -->
                                                        <div class="col-md-6">
                                                            <label for="cur_id" class="form-label">Mata Uang <span
                                                                    class="text-danger">*</span></label>
                                                            <select name="cur_id" id="cur_id"
                                                                class="form-select select" required>
                                                                <option value="" data-nominal="1" selected>Rp
                                                                </option>
                                                                @foreach (\App\Models\Currency::orderBy('currency', 'asc')->get() as $currency)
                                                                    <option value="{{ $currency->cur_id }}"
                                                                        data-nominal="{{ $currency->nominal }}">
                                                                        {{ $currency->currency }}</option>
                                                                @endforeach
                                                            </select>
                                                            <small id="currencyNote" class="form-text text-muted"
                                                                style="display: none;"></small>
                                                        </div>
                                                        <!-- Price -->
                                                        <div class="col-md-6">
                                                            <label for="price" class="form-label">Harga <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="number" name="price" id="price"
                                                                class="form-control" required min="0"
                                                                step="0.01" value="{{ old('price') }}">
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="amountDisplay" class="form-label">Jumlah
                                                            (IDR)</label>
                                                        <input type="text" id="amountDisplay" class="form-control"
                                                            readonly>
                                                        <input type="hidden" name="amount" id="amount">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="wct_id" class="form-label">Workcenter</label>
                                                        <select name="wct_id" id="wct_id"
                                                            class="form-control select" required>
                                                            @foreach (\App\Models\Workcenter::orderBy('workcenter', 'asc')->get() as $workcenter)
                                                                <option value="{{ $workcenter->wct_id }}">
                                                                    {{ $workcenter->workcenter }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Departemen <span
                                                                class="text-danger">*</span></label>
                                                        <input type="hidden" name="dpt_id"
                                                            value="{{ $submission->dpt_id }}">
                                                        <input class="form-control"
                                                            value="{{ $submission->dept->department ?? '-' }}"
                                                            readonly>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="month" class="form-label">Bulan <span
                                                                class="text-danger">*</span></label>
                                                        <select class="form-control select" name="month"
                                                            id="month" required>
                                                            @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                                                <option value="{{ $month }}"
                                                                    @selected(old('month') === $month)>{{ $month }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Tutup</button>
                                                <button type="submit" class="btn text-white"
                                                    style="background-color: #0080ff;">Tambah Item</button>
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
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Add Remark Modal -->
                        <div id="addRemarkModal" class="modal fade" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Add Remark</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="addRemarkForm" method="POST"
                                            action="{{ route('remarks.store') }}">
                                            @csrf
                                            <input type="hidden" name="sub_id" id="sub_id" value="">
                                            <div class="mb-3">
                                                <label for="remark" class="form-label">Remark</label>
                                                <textarea class="form-control" id="remark" name="remark" rows="4" placeholder="Enter your remark"
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
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
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
                                // Handle klik pada data bulanan
                                $(document).on('click', '.editable-month', function(e) {
                                    e.preventDefault();

                                    const subId = $(this).data('sub-id');
                                    const id = $(this).data('id');
                                    const month = $(this).data('month');
                                    const price = $(this).data('price');
                                    const itmId = $(this).data('itm-id');
                                    const description = $(this).data('description');
                                    const bdcId = $(this).data('bdc-id');
                                    const businessPartner = $(this).data('business-partner');
                                    const lobId = $(this).data('lob-id');
                                    const workcenter = $(this).data('workcenter');
                                    const workcenterId = $(this).data('workcenter-id');
                                    const currencyId = $(this).data('currency-id');

                                    if (!id) {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error!',
                                            text: 'Data ID tidak ditemukan. Silakan refresh halaman dan coba lagi.',
                                            confirmButtonColor: '#d33'
                                        });
                                        return;
                                    }

                                    // Isi form modal
                                    $('#edit_month_sub_id').val(subId);
                                    $('#edit_month_id').val(id);
                                    $('#edit_month_name').val(month);
                                    $('#display_month').val(month);
                                    $('#edit_month_itm_id').val(itmId);
                                    $('#edit_month_description').val(description);
                                    $('#edit_month_bdc_id').val(bdcId).trigger('change');
                                    $('#edit_month_business_partner').val(businessPartner);
                                    $('#edit_month_lob_id').val(lobId).trigger('change');
                                    $('#edit_month_price').val(price);
                                    $('#edit_month_wct_id').val(workcenterId).trigger('change');
                                    $('#edit_month_cur_id').val(currencyId).trigger('change');

                                    // Hitung amount
                                    updateMonthAmountDisplay();

                                    // Set action form
                                    const baseUrl = "{{ url('/') }}";
                                    $('#editMonthForm').attr('action', '/submissions/' + subId + '/id/' + id + '/month/' +
                                        encodeURIComponent(month));

                                    // Tampilkan modal
                                    $('#editMonthModal').modal('show');
                                });

                                // Hitung amount untuk modal edit bulanan
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

                                // Handle submit form edit bulanan
                                $(document).on('submit', '#editMonthForm', function(e) {
                                    e.preventDefault();
                                    const form = $(this);
                                    const formData = form.serialize();
                                    const url = form.attr('action');

                                    $.ajax({
                                        url: baseUrl + '/submissions/' + subId + '/id/' + id +
                                            '/month/' + month,
                                        method: 'PUT',
                                        data: formData,
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

                                // Handle approval form submission with SweetAlert
                                $(document).on('submit', '.approve-form', function(e) {
                                    e.preventDefault();
                                    var form = $(this);

                                    Swal.fire({
                                        title: 'Apakah Anda yakin?',
                                        text: 'Apakah Anda ingin menyetujui pengajuan ini?',
                                        icon: 'question',
                                        showCancelButton: true,
                                        confirmButtonColor: '#3085d6',
                                        cancelButtonColor: '#d33',
                                        confirmButtonText: 'Ya, setujui!',
                                        cancelButtonText: 'Batal'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            $.ajax({
                                                url: form.attr('action'),
                                                method: form.attr('method'),
                                                data: form.serialize(),
                                                success: function(response, status, xhr) {
                                                    console.log('Approve Success Response:', response);
                                                    console.log('Approve Status Code:', xhr.status);
                                                    if (xhr.status === 200 || xhr.status === 302) {
                                                        Swal.fire({
                                                            icon: 'success',
                                                            title: 'Berhasil!',
                                                            text: 'Pengajuan berhasil disetujui.',
                                                            confirmButtonColor: '#3085d6'
                                                        }).then(() => {
                                                            location.reload();
                                                        });
                                                    } else {
                                                        Swal.fire({
                                                            icon: 'error',
                                                            title: 'Error!',
                                                            text: 'Gagal menyetujui pengajuan.',
                                                            confirmButtonColor: '#d33'
                                                        });
                                                    }
                                                },
                                                error: function(xhr) {
                                                    console.log('Approve Error Response:', xhr);
                                                    let errorMessage = 'Terjadi kesalahan.';
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

                                // Handle disapproval form submission with SweetAlert
                                $(document).on('submit', '.disapprove-form', function(e) {
                                    e.preventDefault();
                                    var form = $(this);

                                    Swal.fire({
                                        title: 'Apakah Anda yakin?',
                                        text: 'Apakah Anda ingin menolak pengajuan ini?',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#3085d6',
                                        cancelButtonColor: '#d33',
                                        confirmButtonText: 'Ya, tolak!',
                                        cancelButtonText: 'Batal'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            $.ajax({
                                                url: form.attr('action'),
                                                method: form.attr('method'),
                                                data: form.serialize(),
                                                success: function(response, status, xhr) {
                                                    console.log('Disapprove Success Response:', response);
                                                    console.log('Disapprove Status Code:', xhr.status);
                                                    if (xhr.status === 200 || xhr.status === 302) {
                                                        Swal.fire({
                                                            icon: 'success',
                                                            title: 'Berhasil!',
                                                            text: 'Pengajuan berhasil ditolak.',
                                                            confirmButtonColor: '#3085d6'
                                                        }).then(() => {
                                                            location.reload();
                                                        });
                                                    } else {
                                                        Swal.fire({
                                                            icon: 'error',
                                                            title: 'Error!',
                                                            text: 'Gagal menolak pengajuan.',
                                                            confirmButtonColor: '#d33'
                                                        });
                                                    }
                                                },
                                                error: function(xhr) {
                                                    console.log('Disapprove Error Response:', xhr);
                                                    let errorMessage = 'Terjadi kesalahan.';
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

                                // Handle hapus data bulanan
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
                                            const url = '/submissions/' + subId + '/id/' + id + '/month/' + month;
                                            $.ajax({
                                                url: baseUrl + '/submissions/' + subId + '/id/' + id +
                                                    '/month/' + month,
                                                method: 'DELETE',
                                                data: {
                                                    _token: '{{ csrf_token() }}'
                                                },
                                                success: function(response) {
                                                    if (response.success) {
                                                        $('#editMonthModal').modal('hide');
                                                        Swal.fire(
                                                            'Terhapus!',
                                                            'Data berhasil dihapus.',
                                                            'success'
                                                        ).then(() => {
                                                            location.reload();
                                                        });
                                                    }
                                                },
                                                error: function(xhr) {
                                                    Swal.fire(
                                                        'Error!',
                                                        xhr.responseJSON?.message ||
                                                        'Gagal menghapus data.',
                                                        'error'
                                                    );
                                                }
                                            });
                                        }
                                    });
                                });
                            });

                            // // Inisialisasi Select2 untuk semua elemen dengan class 'select'
                            // $('.select').select2({

                            // });

                            // Event saat modal tambah item ditampilkan
                            $('#addItemModal').on('shown.bs.modal', function() {
                                // // Inisialisasi Select2 untuk field di modal tambah
                                // $('#bdc_id, #lob_id, #cur_id, #wct_id, #month')
                                //     .select2({ // [MODIFIKASI] Ganti #rnr dengan #bdc_id

                                //     });

                                // // Sesuaikan tinggi Select2 agar sejajar dengan input lain
                                // $('.select2-selection--single').css({
                                //     'height': $('#addItemModal #price').outerHeight() + 'px',
                                //     'display': 'flex',
                                //     'align-items': 'center'
                                // });
                                // $('.select2-selection__rendered').css({
                                //     'line-height': $('#addItemModal #price').outerHeight() + 'px'
                                // });

                                // Reset form saat modal dibuka
                                $('#addItemForm')[0].reset();
                                $('#amountDisplay').val('');
                                $('#bdc_id, #lob_id, #cur_id, #wct_id, #month').val('').trigger(
                                    'change'); // [MODIFIKASI] Reset bdc_id dan lob_id
                            });

                            // Event saat modal edit item ditampilkan
                            $('#editModal').on('shown.bs.modal', function() {
                                // Inisialisasi Select2 untuk field di modal edit
                                // $('#edit_bdc_id, #edit_lob_id, #edit_cur_id, #edit_wct_id, #edit_month')
                                //     .select2({ // [MODIFIKASI] Ganti edit_rnr dengan edit_bdc_id

                                //     });

                                // // Sesuaikan tinggi Select2 agar sejajar dengan input lain
                                // $('.select2-selection--single').css({
                                //     'height': $('#editModal #edit_price').outerHeight() + 'px',
                                //     'display': 'flex',
                                //     'align-items': 'center'
                                // });
                                // $('.select2-selection__rendered').css({
                                //     'line-height': $('#editModal #edit_price').outerHeight() + 'px'
                                // });
                            });

                            // Event untuk mengubah itm_id menjadi uppercase di modal tambah
                            $('#addItemModal #itm_id').on('input', function() { // [MODIFIKASI] Tambah uppercase untuk itm_id
                                $(this).val($(this).val().toUpperCase());
                            });

                            // Event untuk mengubah itm_id menjadi uppercase di modal edit
                            $('#editModal #edit_itm_id').on('input', function() { // [MODIFIKASI] Tambah uppercase untuk edit_itm_id
                                $(this).val($(this).val().toUpperCase());
                            });

                            // Hitung amount secara dinamis untuk modal tambah item
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

                                // Update catatan konversi mata uang
                                if (currencyNominal !== 1 && currencyCode) {
                                    const formattedNominal = currencyNominal.toLocaleString('id-ID', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                    $currencyNote.text(`1 ${currencyCode} = IDR ${formattedNominal}`).show();
                                } else {
                                    $currencyNote.text('').hide();
                                }

                                // Hitung amount tanpa quantity
                                const amount = price * currencyNominal;

                                $amountDisplay.val('IDR ' + amount.toLocaleString('id-ID', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                }));
                                $amountHidden.val(amount.toFixed(2));
                            });

                            // Hitung amount secara dinamis untuk modal edit item
                            $('#editModal').on('input change', '#edit_price, #edit_cur_id', function() {
                                const $priceInput = $('#editModal #edit_price');
                                const $currencySelect = $('#editModal #edit_cur_id');
                                const $amountDisplay = $('#editModal #edit_amountDisplay');
                                const $amountHidden = $('#editModal #edit_amount');
                                const $currencyNote = $('#editModal #edit_currencyNote');

                                const price = parseFloat($priceInput.val()) || 0;
                                const selectedCurrency = $currencySelect.find('option:selected');
                                const currencyNominal = parseFloat(selectedCurrency.data('nominal')) || 1;
                                const currencyCode = $currencySelect.text().trim();

                                // Update catatan konversi mata uang
                                if (currencyNominal !== 1 && currencyCode) {
                                    const formattedNominal = currencyNominal.toLocaleString('id-ID', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                    $currencyNote.text(`1 ${currencyCode} = IDR ${formattedNominal}`).show();
                                } else {
                                    $currencyNote.text('').hide();
                                }

                                // Hitung amount tanpa quantity
                                const amount = price * currencyNominal;

                                $amountDisplay.val('IDR ' + amount.toLocaleString('id-ID', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                }));
                                $amountHidden.val(amount.toFixed(2));
                            });

                            // Event untuk membuka modal tambah item
                            $(document).on('click', '.open-add-item-modal', function(e) {
                                e.preventDefault();
                                const subId = $(this).data('sub-id');
                                $('#addItemModal').find('#sub_id').val(subId);
                                $('#addItemModal').modal('show');
                            });

                            // Event untuk membuka modal edit item
                            $(document).on('click', '.open-edit-modal', function(e) {
                                e.preventDefault();
                                const subId = $(this).data('id');
                                const itmId = $(this).data('itm-id');

                                $.ajax({
                                    url: baseUrl + '/submissions/' + subId + '/id/' + id +
                                        '/month/' + month,
                                    method: 'GET',
                                    success: function(data) {
                                        console.log('Data diterima dari server untuk edit:',
                                            data); // [MODIFIKASI] Tambah logging untuk debug data

                                        const modal = $('#editModal');
                                        modal.find('.modal-dialog').html(`
                    <div class="modal-content">
                        <div class="modal-header bg-danger">
                            <h5 class="modal-title text-white">Edit Item</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editItemForm" method="POST" action="/submissions/${subId}/id/${itmId}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="sub_id" value="${subId}">
                                <input type="hidden" name="acc_id" value="${data.acc_id}">
                                <input type="hidden" name="purpose" value="${data.purpose}">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Budget Code <span class="text-danger">*</span></label>
                                            <select name="bdc_id" id="edit_bdc_id" class="form-control select" required>
                                                @foreach (\App\Models\BudgetCode::orderBy('budget_name', 'asc')->get() as $budgetCode)
                                                    <option value="{{ $budgetCode->bdc_id }}" ${data.bdc_id === '{{ $budgetCode->bdc_id }}' ? 'selected' : ''}>{{ $budgetCode->budget_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Business Partner <span class="text-danger">*</span></label>
                                            <input type="text" name="business_partner" id="edit_business_partner" class="form-control" value="${data.business_partner ? data.business_partner.replace(/"/g, '&quot;') : ''}" required> <!-- [MODIFIKASI] Escape karakter kutip untuk business_partner -->
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Line Of Business <span class="text-danger">*</span></label>
                                            <select name="lob_id" id="edit_lob_id" class="form-control select" required>
                                                @foreach ($line_businesses as $lob_id => $line)
                                                    <option value="{{ $line }}" ${data.lob_id === '{{ $lob_id }}' ? 'selected' : ''}>{{ $line }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Item <span class="text-danger">*</span></label>
                                            <input type="text" name="itm_id" id="edit_itm_id" class="form-control" value="${data.itm_id ? data.itm_id.replace(/"/g, '&quot;') : ''}" required> <!-- [MODIFIKASI] Escape karakter kutip untuk itm_id -->
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Description <span class="text-danger">*</span></label>
                                            <textarea class="form-control" name="description" id="edit_description" required>${data.description ? data.description.replace(/"/g, '&quot;') : ''}</textarea> <!-- [MODIFIKASI] Escape karakter kutip untuk description -->
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="edit_cur_id" class="form-label">Mata Uang <span class="text-danger">*</span></label>
                                                <select name="cur_id" id="edit_cur_id" class="form-select select" required>
                                                    <option value="" data-nominal="1" ${data.cur_id === '' ? 'selected' : ''}>Rp</option>
                                                    @foreach (\App\Models\Currency::orderBy('currency', 'asc')->get() as $currency)
                                                        <option value="{{ $currency->cur_id }}" data-nominal="{{ $currency->nominal }}" ${data.cur_id === '{{ $currency->cur_id }}' ? 'selected' : ''}>
                                                            {{ $currency->currency }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small id="edit_currencyNote" class="form-text text-muted" style="display: none;"></small>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="edit_price" class="form-label">Harga <span class="text-danger">*</span></label>
                                                <input type="number" name="price" id="edit_price" class="form-control" required min="0" step="0.01" value="${data.price}">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="edit_amountDisplay" class="form-label">Jumlah (IDR)</label>
                                            <input type="text" id="edit_amountDisplay" class="form-control" readonly value="IDR ${data.amount.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}">
                                            <input type="hidden" name="amount" id="edit_amount" value="${data.amount.toFixed(2)}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="edit_wct_id" class="form-label">Workcenter</label>
                                            <select name="wct_id" id="edit_wct_id" class="form-control select" required>
                                                @foreach (\App\Models\Workcenter::orderBy('workcenter', 'asc')->get() as $workcenter)
                                                    <option value="{{ $workcenter->wct_id }}" ${data.wct_id === '{{ $workcenter->wct_id }}' ? 'selected' : ''}>
                                                        {{ $workcenter->workcenter }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Departemen <span class="text-danger">*</span></label>
                                            <input type="hidden" name="dpt_id" value="${data.dpt_id}">
                                            <input class="form-control" value="${data.department || '-'}" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label for="edit_month" class="form-label">Bulan <span class="text-danger">*</span></label>
                                            <select class="form-control select" name="month" id="edit_month" required>
                                                @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                                    <option value="{{ $month }}" ${data.month === '{{ $month }}' ? 'selected' : ''}>{{ $month }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    <button type="submit" class="btn text-white" style="background-color: #0080ff;">Perbarui Item</button>
                                </div>
                            </form>
                        </div>
                    </div>
                `);

                                        // [MODIFIKASI] Tambah logging untuk memastikan business_partner terisi
                                        console.log('Mengisi business_partner:', data.business_partner);

                                        $('#editModal').modal('show');
                                    },
                                    error: function(xhr) {
                                        console.error('Gagal memuat data edit:', xhr
                                            .responseJSON); // [MODIFIKASI] Tambah logging untuk error
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error!',
                                            text: xhr.responseJSON?.message ||
                                                'Gagal memuat form edit.',
                                            confirmButtonColor: '#d33'
                                        });
                                    }
                                });
                            });

                            // Event untuk tombol delete
                            $(document).on('click', '.btn-delete', function() {
                                const form = $(this).closest('form');
                                const itemCount = form.data('item-count');

                                if (itemCount <= 1) {
                                    Swal.fire({
                                        title: 'Peringatan!',
                                        text: 'Harus ada setidaknya satu item dalam pengajuan. Anda tidak dapat menghapus item terakhir.',
                                        icon: 'warning',
                                        confirmButtonText: 'OK',
                                        confirmButtonColor: '#d33'
                                    });
                                    return;
                                }

                                Swal.fire({
                                    title: 'Apakah Anda yakin?',
                                    text: "Anda tidak akan dapat mengembalikan ini!",
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Ya, hapus!',
                                    cancelButtonText: 'Batal'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $.ajax({
                                            url: form.attr('action'),
                                            method: form.attr('method'),
                                            data: form.serialize(),
                                            success: function(response) {
                                                if (response.success) {
                                                    Swal.fire(
                                                        'Terhapus!',
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
                                                    xhr.responseJSON.message || 'Terjadi kesalahan',
                                                    'error'
                                                );
                                            }
                                        });
                                    }
                                });
                            });

                            // Event untuk submit form tambah item
                            $(document).on('submit', '#addItemForm', function(e) {
                                e.preventDefault();
                                var form = $(this);

                                // Validasi frontend untuk memastikan semua field wajib diisi
                                if (!form.find('#bdc_id').val() || !form.find('#lob_id').val() || !form.find(
                                        '#business_partner').val()) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: 'Harap isi semua field wajib (Budget Code, Line Of Business, Business Partner).',
                                        confirmButtonColor: '#d33'
                                    });
                                    return;
                                }

                                $.ajax({
                                    url: form.attr('action'),
                                    method: form.attr('method'),
                                    data: form.serialize(),
                                    success: function(response) {
                                        if (response.success) {
                                            $('#addItemModal').modal('hide');
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Berhasil!',
                                                text: 'Item berhasil ditambahkan.',
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
                                            text: xhr.responseJSON.message || 'Gagal menambahkan item.',
                                            confirmButtonColor: '#d33'
                                        });
                                    }
                                });
                            });

                            // Event untuk submit form edit item
                            $(document).on('submit', '#editItemForm', function(e) {
                                e.preventDefault();
                                var form = $(this);

                                // Validasi frontend untuk memastikan semua field wajib diisi
                                if (!form.find('#edit_bdc_id').val() || !form.find('#edit_lob_id').val() || !form.find(
                                        '#edit_business_partner').val()) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: 'Harap isi semua field wajib (Budget Code, Line Of Business, Business Partner).',
                                        confirmButtonColor: '#d33'
                                    });
                                    return;
                                }

                                $.ajax({
                                    url: form.attr('action'),
                                    method: form.attr('method'),
                                    data: form.serialize(),
                                    success: function(response) {
                                        if (response.success) {
                                            $('#editModal').modal('hide');
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Berhasil!',
                                                text: 'Item berhasil diperbarui.',
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
                                            text: xhr.responseJSON.message || 'Gagal memperbarui item.',
                                            confirmButtonColor: '#d33'
                                        });
                                    }
                                });
                            });

                            // Event untuk membuka modal history approval
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
                                            '<div class="alert alert-danger">Gagal memuat riwayat persetujuan</div>'
                                        );
                                    });
                            });

                            // Event untuk membuka modal tambah remark
                            $(document).on('click', '.open-add-remark-modal', function(e) {
                                e.preventDefault();
                                var subId = $(this).data('id');
                                var modal = $('#addRemarkModal');

                                modal.find('#sub_id').val(subId);
                                modal.modal('show');
                            });

                            // Event untuk submit form tambah remark
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
                                            location.reload();
                                        }
                                    },
                                    error: function(xhr) {
                                        form.find('.modal-body').prepend(
                                            '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                            (xhr.responseJSON.message || 'Gagal menambahkan remark') +
                                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'
                                        );
                                    }
                                });
                            });

                            // Event untuk membuka modal riwayat remark
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
                                            '<div class="alert alert-danger">Gagal memuat riwayat remark</div>'
                                        );
                                    });
                            });

                            // Kode yang dinonaktifkan
                            // $('#addItemModal').on('input', '#quantity, #price', function() {
                            //     const quantity = parseFloat($('#quantity').val()) || 0;
                            //     const price = parseFloat($('#price').val()) || 0;
                            //     const amount = quantity * price;
                            //
                            //     $('#amountDisplay').val('IDR ' + amount.toLocaleString('id-ID', {
                            //         minimumFractionDigits: 2,
                            //         maximumFractionDigits: 2
                            //     }));
                            //     $('#amount').val(amount);
                            // });

                            // $('#quantity, #price').on('input', function() {
                            //     const quantity = parseFloat($('#quantity').val()) || 0;
                            //     const price = parseFloat($('#price').val()) || 0;
                            //     const amount = quantity * price;
                            //
                            //     // Format amount with IDR currency
                            //     $('#amountDisplay').val('IDR ' + amount.toLocaleString('id-ID', {
                            //         minimumFractionDigits: 2,
                            //         maximumFractionDigits: 2
                            //     }));
                            // });

                            // $('#addItemModal #input_type').on('change', function() {
                            //     if ($(this).val() === 'select') {
                            //         $('#addItemModal #select_item_container').show();
                            //         $('#addItemModal #manual_item_container').hide();
                            //         $('#addItemModal #itm_id').prop('required', true);
                            //         $('#addItemModal #manual_item').prop('required', false);
                            //         $('#addItemModal #description').val('');
                            //     } else {
                            //         $('#addItemModal #select_item_container').hide();
                            //         $('#addItemModal #manual_item_container').show();
                            //         $('#addItemModal #itm_id').prop('required', false);
                            //         $('#addItemModal #manual_item').prop('required', true);
                            //         $('#addItemModal #description').val('');
                            //     }
                            // });

                            // $('#addItemModal #itm_id').on('input.uppercase', function() {
                            //     $(this).val($(this).val().toUpperCase());
                            // });

                            // $('#addItemModal #itm_id').on('change', function() {
                            //     const itm_id = $(this).val().trim();
                            //     if (itm_id && $('#addItemModal #input_type').val() === 'select') {
                            //         $.ajax({
                            //             url: '{{ route('accounts.getItemName') }}',
                            //             method: 'POST',
                            //             data: {
                            //                 itm_id: itm_id,
                            //                 _token: '{{ csrf_token() }}'
                            //             },
                            //             success: function(response) {
                            //                 if (response.item) {
                            //                     $('#addItemModal #description').val(response.item.item);
                            //                 } else {
                            //                     $('#addItemModal #itm_id').val('');
                            //                     $('#addItemModal #description').val('');
                            //                     Swal.fire({
                            //                         icon: 'error',
                            //                         title: 'Error',
                            //                         text: 'Item GID Not Found',
                            //                     });
                            //                 }
                            //             },
                            //             error: function() {
                            //                 $('#addItemModal #itm_id').val('');
                            //                 $('#addItemModal #description').val('');
                            //                 Swal.fire({
                            //                     icon: 'error',
                            //                     title: 'Error',
                            //                     text: 'Item GID Not Found',
                            //                 });
                            //                 console.error('AJAX Error:', status, error, xhr.responseText);
                            //             }
                            //         });
                            //     }
                            // });

                            $(document).on('submit', '.send-form', function(e) {
                                e.preventDefault();
                                var form = $(this);

                                Swal.fire({
                                    title: 'Apakah Anda yakin?',
                                    text: 'Apakah Anda ingin mengirim pengajuan ini?',
                                    icon: 'question',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Ya, kirim!',
                                    cancelButtonText: 'Batal'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $.ajax({
                                            url: form.attr('action'),
                                            method: form.attr('method'),
                                            data: form.serialize(),
                                            success: function(response, status, xhr) {
                                                console.log('Success Response:', response);
                                                console.log('Status Code:', xhr.status);
                                                if (xhr.status === 200 || xhr.status === 302) {
                                                    Swal.fire({
                                                        icon: 'success',
                                                        title: 'Berhasil!',
                                                        text: 'Pengajuan berhasil dikirim.',
                                                        confirmButtonColor: '#3085d6'
                                                    }).then(() => {
                                                        location.reload();
                                                    });
                                                } else {
                                                    Swal.fire({
                                                        icon: 'error',
                                                        title: 'Error!',
                                                        text: 'Gagal mengirim pengajuan.',
                                                        confirmButtonColor: '#d33'
                                                    });
                                                }
                                            },
                                            error: function(xhr) {
                                                console.log('Error Response:', xhr);
                                                let errorMessage = 'Terjadi kesalahan.';
                                                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                                    errorMessage = Object.values(xhr.responseJSON.errors).flat()
                                                        .join(' ');
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
                        </script>
                        <x-footer></x-footer>
    </main>
</body>

</html>
