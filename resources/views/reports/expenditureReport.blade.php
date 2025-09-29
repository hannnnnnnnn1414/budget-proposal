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
                                                <h6 class="mb-0 text-white">Approval Status</h6>
                                            </div>
                                            <!-- Approval Status -->
                                            <div class="bg-green-100 col-md-6 p-4 rounded-0 shadow mb-4">
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
                                                                        APPROVED BY KADIV
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
                                                        <button type="button"
                                                            class="btn open-historyremark-modal text-white"
                                                            style="background-color: #0080ff;"
                                                            data-id="{{ $submission->sub_id ?? '' }}">View
                                                            Remarks</button>
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
                                                    @foreach ($remarks as $remark)
                                                        <div class="mb-3">
                                                            <p><strong>Remark:</strong> <span
                                                                    class="font-bold">{{ $remark->remark }}</span></p>
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
                                    <h6 class="mb-0 text-white">Item of Purchase</h6>
                                </div>
                                <!-- Item Table -->
                                <div class="bg-white p-4 rounded shadow mb-4">
                                    @php
                                        $hasAction = $submissions->contains(function ($submission) {
                                            return in_array($submission->status, [2, 9]);
                                        });

                                        // Definisikan pemetaan bulan untuk menangani format yang berbeda
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

                                        // Kelompokkan submissions berdasarkan item unik
                                        $groupedItems = $submissions
                                            ->groupBy(function ($submission) {
                                                return ($submission->item != null
                                                    ? $submission->item->item
                                                    : $submission->itm_id ?? '') .
                                                    '-' .
                                                    $submission->asset_class .
                                                    '-' .
                                                    $submission->prioritas .
                                                    '-' .
                                                    $submission->alasan .
                                                    '-' .
                                                    $submission->keterangan;
                                            })
                                            ->map(function ($group) use ($monthMap, $monthLabels) {
                                                $first = $group->first();
                                                $months = [];
                                                $totalPrice = 0;

                                                foreach ($group as $submission) {
                                                    // Normalisasi nama bulan
                                                    $month = isset($monthMap[$submission->month])
                                                        ? $monthMap[$submission->month]
                                                        : null;
                                                    if ($month && array_key_exists($month, $monthLabels)) {
                                                        $months[$month] = [
                                                            'month_value' => $submission->month_value,
                                                            'sub_id' => $submission->sub_id,
                                                            'id' => $submission->id,
                                                            'status' => $submission->status,
                                                        ];
                                                        $totalPrice += $submission->month_value;
                                                    }
                                                }

                                                return [
                                                    'item' =>
                                                        $first->item != null
                                                            ? $first->item->item
                                                            : $first->itm_id ?? '',
                                                    'asset_class' => $first->asset_class,
                                                    'prioritas' => $first->prioritas,
                                                    'alasan' => $first->alasan,
                                                    'keterangan' => $first->keterangan,
                                                    'price' => $first->price,
                                                    'amount' => $first->month_value,
                                                    'workcenter' =>
                                                        $first->workcenter != null
                                                            ? $first->workcenter->workcenter
                                                            : '',
                                                    'department' =>
                                                        $first->dept != null ? $first->dept->department : '',
                                                    'months' => $months,
                                                    'total' => $totalPrice,
                                                    'sub_id' => $first->sub_id,
                                                    'id' => $first->id,
                                                    'status' => $first->status,
                                                    'quantity' => $first->quantity,
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
                                    @endphp

                                    @if ($hasAction)
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
                                                    <!-- Fixed Columns (Kiri) -->
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 0; z-index: 110; background-color: #e9ecef; min-width: 80px;">
                                                        Item
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 80px; z-index: 110; background-color: #e9ecef; min-width: 100px;">
                                                        Asset Class
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 180px; z-index: 110; background-color: #e9ecef; min-width: 80px;">
                                                        Prioritas
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 260px; z-index: 110; background-color: #e9ecef; min-width: 120px;">
                                                        Alasan
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 380px; z-index: 110; background-color: #e9ecef; min-width: 120px;">
                                                        Keterangan
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 500px; z-index: 110; background-color: #e9ecef; min-width: 120px;">
                                                        Workcenter
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 620px; z-index: 110; background-color: #e9ecef; min-width: 120px;">
                                                        Department
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 740px; z-index: 110; background-color: #e9ecef; min-width: 80px;">
                                                        Quantity
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 820px; z-index: 110; background-color: #e9ecef; min-width: 120px;">
                                                        Price
                                                    </th>

                                                    <!-- Bulan Columns (Scrollable) -->
                                                    @foreach ($months as $month)
                                                        <th class="text-left border p-2" style="min-width: 100px;">
                                                            {{ $monthLabels[$month] }}
                                                        </th>
                                                    @endforeach

                                                    <!-- Total & Action -->
                                                    <th class="text-left border p-2" style="min-width: 120px;">Total
                                                    </th>

                                                    @if ($hasAction)
                                                        <th class="text-left border p-2" style="min-width: 100px;">
                                                            Action</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($groupedItems as $item)
                                                    <tr class="hover:bg-gray-50">
                                                        <!-- Fixed Columns Data -->
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 0; z-index: 10; background-color: white;">
                                                            {{ $item['item'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 80px; z-index: 10; background-color: white;">
                                                            {{ $item['asset_class'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 180px; z-index: 10; background-color: white;">
                                                            {{ $item['prioritas'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 260px; z-index: 10; background-color: white;">
                                                            {{ $item['alasan'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 380px; z-index: 10; background-color: white;">
                                                            {{ $item['keterangan'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 500px; z-index: 10; background-color: white;">
                                                            {{ $item['workcenter'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 620px; z-index: 10; background-color: white;">
                                                            {{ $item['department'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 740px; z-index: 10; background-color: white;">
                                                            {{ $item['quantity'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 820px; z-index: 10; background-color: white;">
                                                            Rp {{ number_format($item['price'], 0, ',', '.') }}
                                                        </td>

                                                        <!-- Bulan Data -->
                                                        @foreach ($months as $month)
                                                            <td class="border p-2" style="min-width: 100px;">
                                                                @if (isset($item['months'][$month]) && $item['months'][$month]['month_value'] > 0)
                                                                    @if (in_array($item['months'][$month]['status'], [2, 9]))
                                                                        <a href="#" class="editable-month"
                                                                            data-id="{{ $item['months'][$month]['id'] }}"
                                                                            data-sub-id="{{ $item['months'][$month]['sub_id'] }}"
                                                                            data-month="{{ $month }}"
                                                                            data-price="{{ $item['months'][$month]['month_value'] }}"
                                                                            data-price-real="{{ $item['price'] }}">
                                                                            Rp
                                                                            {{ number_format($item['months'][$month]['month_value'], 0, ',', '') }}
                                                                        </a>
                                                                    @else
                                                                        Rp
                                                                        {{ number_format($item['months'][$month]['month_value'], 0, ',', '') }}
                                                                    @endif
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                        @endforeach

                                                        <!-- Total -->
                                                        <td class="border p-2" style="min-width: 120px;">
                                                            Rp {{ number_format($item['total'], 0, ',', '') }}
                                                        </td>

                                                        <!-- Action -->
                                                        @if ($hasAction)
                                                            <td class="border p-2" style="min-width: 100px;">
                                                                @if (in_array($item['status'], [2, 9]))
                                                                    <a href="#" data-id="{{ $item['sub_id'] }}"
                                                                        data-itm-id="{{ $item['id'] }}"
                                                                        class="inline-flex items-center justify-center p-2 text-red-600 hover:text-blue-800 open-edit-modal"
                                                                        title="Update">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    <form
                                                                        action="{{ route('submissions.delete', ['sub_id' => $item['sub_id'], 'id' => $item['id']]) }}"
                                                                        method="POST" class="delete-form"
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
                                                        <td colspan="{{ $hasAction ? 9 + count($months) + 2 : 9 + count($months) + 1 }}"
                                                            class="border p-2 text-center">
                                                            No Submissions found!
                                                        </td>
                                                    </tr>
                                                @endforelse

                                                <!-- Grand Total Row -->
                                                @php
                                                    $grandTotal = $groupedItems->sum('total');
                                                @endphp
                                                <tr class="bg-gray-100 font-bold">
                                                    <td colspan="9" class="border p-2 text-right"
                                                        style="position: sticky; left: 0; z-index: 10; background-color: #f8f9fa;">
                                                        Total
                                                    </td>
                                                    @foreach ($months as $month)
                                                        <td class="border p-2"></td>
                                                    @endforeach
                                                    <td class="border p-2">Rp
                                                        {{ number_format($grandTotal, 0, ',', '.') }}</td>
                                                    @if ($hasAction)
                                                        <td class="border p-2"></td>
                                                    @endif
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <br>
                                </div>
                                <div class="d-flex justify-content-between mt-4">
                                    <button onclick="history.back()" type="button" class="btn btn-secondary me-2">
                                        <i class="fa-solid fa-arrow-left me-2"></i>Back
                                    </button>
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
                        @elseif (session('sect') === 'Kadiv')
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
                                                <h6 class="mb-0 text-white">Approval Status</h6>
                                            </div>
                                            <!-- Approval Status -->
                                            <div class="bg-green-100 col-md-6 p-4 rounded-0 shadow mb-4">
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
                                                        <button type="button"
                                                            class="btn open-historyremark-modal text-white"
                                                            style="background-color: #0080ff;"
                                                            data-id="{{ $submission->sub_id ?? '' }}">View
                                                            Remarks</button>
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
                                    <h6 class="mb-0 text-white">Item of Purchase</h6>
                                </div>
                                <!-- Item Table -->
                                <div class="bg-white p-4 rounded shadow mb-4">
                                    @php
                                        $hasAction = $submissions->contains(function ($submission) {
                                            return in_array($submission->status, [3, 10]);
                                        });

                                        // Definisikan pemetaan bulan untuk menangani format yang berbeda
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

                                        // Kelompokkan submissions berdasarkan item unik
                                        $groupedItems = $submissions
                                            ->groupBy(function ($submission) {
                                                return ($submission->item != null
                                                    ? $submission->item->item
                                                    : $submission->itm_id ?? '') .
                                                    '-' .
                                                    $submission->asset_class .
                                                    '-' .
                                                    $submission->prioritas .
                                                    '-' .
                                                    $submission->alasan .
                                                    '-' .
                                                    $submission->keterangan;
                                            })
                                            ->map(function ($group) use ($monthMap, $monthLabels) {
                                                $first = $group->first();
                                                $months = [];
                                                $totalPrice = 0;

                                                foreach ($group as $submission) {
                                                    // Normalisasi nama bulan
                                                    $month = isset($monthMap[$submission->month])
                                                        ? $monthMap[$submission->month]
                                                        : null;
                                                    if ($month && array_key_exists($month, $monthLabels)) {
                                                        $months[$month] = [
                                                            'month_value' => $submission->month_value,
                                                            'sub_id' => $submission->sub_id,
                                                            'id' => $submission->id,
                                                            'status' => $submission->status,
                                                        ];
                                                        $totalPrice += $submission->month_value;
                                                    }
                                                }

                                                return [
                                                    'item' =>
                                                        $first->item != null
                                                            ? $first->item->item
                                                            : $first->itm_id ?? '',
                                                    'asset_class' => $first->asset_class,
                                                    'prioritas' => $first->prioritas,
                                                    'alasan' => $first->alasan,
                                                    'keterangan' => $first->keterangan,
                                                    'price' => $first->price,
                                                    'amount' => $first->month_value,
                                                    'workcenter' =>
                                                        $first->workcenter != null
                                                            ? $first->workcenter->workcenter
                                                            : '',
                                                    'department' =>
                                                        $first->dept != null ? $first->dept->department : '',
                                                    'months' => $months,
                                                    'total' => $totalPrice,
                                                    'sub_id' => $first->sub_id,
                                                    'id' => $first->id,
                                                    'status' => $first->status,
                                                    'quantity' => $first->quantity,
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
                                    @endphp

                                    @if ($hasAction)
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
                                                    <!-- Fixed Columns (Kiri) -->
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 0; z-index: 110; background-color: #e9ecef; min-width: 80px;">
                                                        Item
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 80px; z-index: 110; background-color: #e9ecef; min-width: 100px;">
                                                        Asset Class
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 180px; z-index: 110; background-color: #e9ecef; min-width: 80px;">
                                                        Prioritas
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 260px; z-index: 110; background-color: #e9ecef; min-width: 120px;">
                                                        Alasan
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 380px; z-index: 110; background-color: #e9ecef; min-width: 120px;">
                                                        Keterangan
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 500px; z-index: 110; background-color: #e9ecef; min-width: 120px;">
                                                        Workcenter
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 620px; z-index: 110; background-color: #e9ecef; min-width: 120px;">
                                                        Department
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 740px; z-index: 110; background-color: #e9ecef; min-width: 80px;">
                                                        Quantity
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 820px; z-index: 110; background-color: #e9ecef; min-width: 120px;">
                                                        Price
                                                    </th>

                                                    <!-- Bulan Columns (Scrollable) -->
                                                    @foreach ($months as $month)
                                                        <th class="text-left border p-2" style="min-width: 100px;">
                                                            {{ $monthLabels[$month] }}
                                                        </th>
                                                    @endforeach

                                                    <!-- Total & Action -->
                                                    <th class="text-left border p-2" style="min-width: 120px;">Total
                                                    </th>

                                                    @if ($hasAction)
                                                        <th class="text-left border p-2" style="min-width: 100px;">
                                                            Action</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($groupedItems as $item)
                                                    <tr class="hover:bg-gray-50">
                                                        <!-- Fixed Columns Data -->
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 0; z-index: 10; background-color: white;">
                                                            {{ $item['item'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 80px; z-index: 10; background-color: white;">
                                                            {{ $item['asset_class'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 180px; z-index: 10; background-color: white;">
                                                            {{ $item['prioritas'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 260px; z-index: 10; background-color: white;">
                                                            {{ $item['alasan'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 380px; z-index: 10; background-color: white;">
                                                            {{ $item['keterangan'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 500px; z-index: 10; background-color: white;">
                                                            {{ $item['workcenter'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 620px; z-index: 10; background-color: white;">
                                                            {{ $item['department'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 740px; z-index: 10; background-color: white;">
                                                            {{ $item['quantity'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 820px; z-index: 10; background-color: white;">
                                                            Rp {{ number_format($item['price'], 0, ',', '.') }}
                                                        </td>

                                                        <!-- Bulan Data -->
                                                        @foreach ($months as $month)
                                                            <td class="border p-2" style="min-width: 100px;">
                                                                @if (isset($item['months'][$month]) && $item['months'][$month]['month_value'] > 0)
                                                                    @if (in_array($item['months'][$month]['status'], [3, 10]))
                                                                        <a href="#" class="editable-month"
                                                                            data-id="{{ $item['months'][$month]['id'] }}"
                                                                            data-sub-id="{{ $item['months'][$month]['sub_id'] }}"
                                                                            data-month="{{ $month }}"
                                                                            data-price="{{ $item['months'][$month]['month_value'] }}"
                                                                            data-price-real="{{ $item['price'] }}">
                                                                            Rp
                                                                            {{ number_format($item['months'][$month]['month_value'], 0, ',', '') }}
                                                                        </a>
                                                                    @else
                                                                        Rp
                                                                        {{ number_format($item['months'][$month]['month_value'], 0, ',', '') }}
                                                                    @endif
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                        @endforeach

                                                        <!-- Total -->
                                                        <td class="border p-2" style="min-width: 120px;">
                                                            Rp {{ number_format($item['total'], 0, ',', '') }}
                                                        </td>

                                                        <!-- Action -->
                                                        @if ($hasAction)
                                                            <td class="border p-2" style="min-width: 100px;">
                                                                @if (in_array($item['status'], [3, 10]))
                                                                    <a href="#" data-id="{{ $item['sub_id'] }}"
                                                                        data-itm-id="{{ $item['id'] }}"
                                                                        class="inline-flex items-center justify-center p-2 text-red-600 hover:text-blue-800 open-edit-modal"
                                                                        title="Update">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    <form
                                                                        action="{{ route('submissions.delete', ['sub_id' => $item['sub_id'], 'id' => $item['id']]) }}"
                                                                        method="POST" class="delete-form"
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
                                                        <td colspan="{{ $hasAction ? 9 + count($months) + 2 : 9 + count($months) + 1 }}"
                                                            class="border p-2 text-center">
                                                            No Submissions found!
                                                        </td>
                                                    </tr>
                                                @endforelse

                                                <!-- Grand Total Row -->
                                                @php
                                                    $grandTotal = $groupedItems->sum('total');
                                                @endphp
                                                <tr class="bg-gray-100 font-bold">
                                                    <td colspan="9" class="border p-2 text-right"
                                                        style="position: sticky; left: 0; z-index: 10; background-color: #f8f9fa;">
                                                        Total
                                                    </td>
                                                    @foreach ($months as $month)
                                                        <td class="border p-2"></td>
                                                    @endforeach
                                                    <td class="border p-2">Rp
                                                        {{ number_format($grandTotal, 0, ',', '.') }}</td>
                                                    @if ($hasAction)
                                                        <td class="border p-2"></td>
                                                    @endif
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <br>
                                </div>
                                <div class="d-flex justify-content-between mt-4">
                                    <button onclick="history.back()" type="button"
                                        class="btn btn-secondary me-2">Back</button>
                                    {{-- <div class="d-flex gap-3">
                                        @if (in_array($submission->status, [3, 10]))
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
                                    </div> --}}
                                </div>
                            </div>
                        @elseif (session('sect') === 'DIC')
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
                                                <h6 class="mb-0 text-white">Approval Status</h6>
                                            </div>
                                            <!-- Approval Status -->
                                            <div class="bg-green-100 col-md-6 p-4 rounded-0 shadow mb-4">
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
                                                        <button type="button"
                                                            class="btn open-historyremark-modal text-white"
                                                            style="background-color: #0080ff;"
                                                            data-id="{{ $submission->sub_id ?? '' }}">View
                                                            Remarks</button>
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
                                    <h6 class="mb-0 text-white">Item of Purchase</h6>
                                </div>
                                <!-- Item Table -->
                                <div class="bg-white p-4 rounded shadow mb-4">
                                    @php
                                        $hasAction = $submissions->contains(function ($submission) {
                                            return in_array($submission->status, [4, 11]);
                                        });

                                        // Definisikan pemetaan bulan untuk menangani format yang berbeda
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

                                        // Kelompokkan submissions berdasarkan item unik
                                        $groupedItems = $submissions
                                            ->groupBy(function ($submission) {
                                                return ($submission->item != null
                                                    ? $submission->item->item
                                                    : $submission->itm_id ?? '') .
                                                    '-' .
                                                    $submission->asset_class .
                                                    '-' .
                                                    $submission->prioritas .
                                                    '-' .
                                                    $submission->alasan .
                                                    '-' .
                                                    $submission->keterangan;
                                            })
                                            ->map(function ($group) use ($monthMap, $monthLabels) {
                                                $first = $group->first();
                                                $months = [];
                                                $totalPrice = 0;

                                                foreach ($group as $submission) {
                                                    // Normalisasi nama bulan
                                                    $month = isset($monthMap[$submission->month])
                                                        ? $monthMap[$submission->month]
                                                        : null;
                                                    if ($month && array_key_exists($month, $monthLabels)) {
                                                        $months[$month] = [
                                                            'month_value' => $submission->month_value,
                                                            'sub_id' => $submission->sub_id,
                                                            'id' => $submission->id,
                                                            'status' => $submission->status,
                                                        ];
                                                        $totalPrice += $submission->month_value;
                                                    }
                                                }

                                                return [
                                                    'item' =>
                                                        $first->item != null
                                                            ? $first->item->item
                                                            : $first->itm_id ?? '',
                                                    'asset_class' => $first->asset_class,
                                                    'prioritas' => $first->prioritas,
                                                    'alasan' => $first->alasan,
                                                    'keterangan' => $first->keterangan,
                                                    'price' => $first->price,
                                                    'amount' => $first->month_value,
                                                    'workcenter' =>
                                                        $first->workcenter != null
                                                            ? $first->workcenter->workcenter
                                                            : '',
                                                    'department' =>
                                                        $first->dept != null ? $first->dept->department : '',
                                                    'months' => $months,
                                                    'total' => $totalPrice,
                                                    'sub_id' => $first->sub_id,
                                                    'id' => $first->id,
                                                    'status' => $first->status,
                                                    'quantity' => $first->quantity,
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
                                    @endphp

                                    @if ($hasAction)
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
                                                    <!-- Fixed Columns (Kiri) -->
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 0; z-index: 110; background-color: #e9ecef; min-width: 80px;">
                                                        Item
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 80px; z-index: 110; background-color: #e9ecef; min-width: 100px;">
                                                        Asset Class
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 180px; z-index: 110; background-color: #e9ecef; min-width: 80px;">
                                                        Prioritas
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 260px; z-index: 110; background-color: #e9ecef; min-width: 120px;">
                                                        Alasan
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 380px; z-index: 110; background-color: #e9ecef; min-width: 120px;">
                                                        Keterangan
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 500px; z-index: 110; background-color: #e9ecef; min-width: 120px;">
                                                        Workcenter
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 620px; z-index: 110; background-color: #e9ecef; min-width: 120px;">
                                                        Department
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 740px; z-index: 110; background-color: #e9ecef; min-width: 80px;">
                                                        Quantity
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 820px; z-index: 110; background-color: #e9ecef; min-width: 120px;">
                                                        Price
                                                    </th>

                                                    <!-- Bulan Columns (Scrollable) -->
                                                    @foreach ($months as $month)
                                                        <th class="text-left border p-2" style="min-width: 100px;">
                                                            {{ $monthLabels[$month] }}
                                                        </th>
                                                    @endforeach

                                                    <!-- Total & Action -->
                                                    <th class="text-left border p-2" style="min-width: 120px;">Total
                                                    </th>

                                                    @if ($hasAction)
                                                        <th class="text-left border p-2" style="min-width: 100px;">
                                                            Action</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($groupedItems as $item)
                                                    <tr class="hover:bg-gray-50">
                                                        <!-- Fixed Columns Data -->
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 0; z-index: 10; background-color: white;">
                                                            {{ $item['item'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 80px; z-index: 10; background-color: white;">
                                                            {{ $item['asset_class'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 180px; z-index: 10; background-color: white;">
                                                            {{ $item['prioritas'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 260px; z-index: 10; background-color: white;">
                                                            {{ $item['alasan'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 380px; z-index: 10; background-color: white;">
                                                            {{ $item['keterangan'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 500px; z-index: 10; background-color: white;">
                                                            {{ $item['workcenter'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 620px; z-index: 10; background-color: white;">
                                                            {{ $item['department'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 740px; z-index: 10; background-color: white;">
                                                            {{ $item['quantity'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 820px; z-index: 10; background-color: white;">
                                                            Rp {{ number_format($item['price'], 0, ',', '.') }}
                                                        </td>

                                                        <!-- Bulan Data -->
                                                        @foreach ($months as $month)
                                                            <td class="border p-2" style="min-width: 100px;">
                                                                @if (isset($item['months'][$month]) && $item['months'][$month]['month_value'] > 0)
                                                                    @if (in_array($item['months'][$month]['status'], [4, 11]))
                                                                        <a href="#" class="editable-month"
                                                                            data-id="{{ $item['months'][$month]['id'] }}"
                                                                            data-sub-id="{{ $item['months'][$month]['sub_id'] }}"
                                                                            data-month="{{ $month }}"
                                                                            data-price="{{ $item['months'][$month]['month_value'] }}"
                                                                            data-price-real="{{ $item['price'] }}">
                                                                            Rp
                                                                            {{ number_format($item['months'][$month]['month_value'], 0, ',', '') }}
                                                                        </a>
                                                                    @else
                                                                        Rp
                                                                        {{ number_format($item['months'][$month]['month_value'], 0, ',', '') }}
                                                                    @endif
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                        @endforeach

                                                        <!-- Total -->
                                                        <td class="border p-2" style="min-width: 120px;">
                                                            Rp {{ number_format($item['total'], 0, ',', '') }}
                                                        </td>

                                                        <!-- Action -->
                                                        @if ($hasAction)
                                                            <td class="border p-2" style="min-width: 100px;">
                                                                @if (in_array($item['status'], [4, 11]))
                                                                    <a href="#" data-id="{{ $item['sub_id'] }}"
                                                                        data-itm-id="{{ $item['id'] }}"
                                                                        class="inline-flex items-center justify-center p-2 text-red-600 hover:text-blue-800 open-edit-modal"
                                                                        title="Update">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    <form
                                                                        action="{{ route('submissions.delete', ['sub_id' => $item['sub_id'], 'id' => $item['id']]) }}"
                                                                        method="POST" class="delete-form"
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
                                                        <td colspan="{{ $hasAction ? 9 + count($months) + 2 : 9 + count($months) + 1 }}"
                                                            class="border p-2 text-center">
                                                            No Submissions found!
                                                        </td>
                                                    </tr>
                                                @endforelse

                                                <!-- Grand Total Row -->
                                                @php
                                                    $grandTotal = $groupedItems->sum('total');
                                                @endphp
                                                <tr class="bg-gray-100 font-bold">
                                                    <td colspan="9" class="border p-2 text-right"
                                                        style="position: sticky; left: 0; z-index: 10; background-color: #f8f9fa;">
                                                        Total
                                                    </td>
                                                    @foreach ($months as $month)
                                                        <td class="border p-2"></td>
                                                    @endforeach
                                                    <td class="border p-2">Rp
                                                        {{ number_format($grandTotal, 0, ',', '.') }}</td>
                                                    @if ($hasAction)
                                                        <td class="border p-2"></td>
                                                    @endif
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <br>
                                </div>
                                <div class="d-flex justify-content-between mt-4">
                                    <button onclick="history.back()" type="button"
                                        class="btn btn-secondary me-2">Back</button>
                                    {{-- <div class="d-flex gap-3">
                                        @if (in_array($submission->status, [4, 11]))
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
                                    </div> --}}
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
                                                <h6 class="mb-0 text-white">Approval Status</h6>
                                            </div>
                                            <!-- Approval Status -->
                                            <div class="bg-green-100 col-md-6 p-4 rounded-0 shadow mb-4">
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
                                                        <button type="button"
                                                            class="btn open-historyremark-modal text-white"
                                                            style="background-color: #0080ff;"
                                                            data-id="{{ $submission->sub_id ?? '' }}">View
                                                            Remarks</button>
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
                                                    @foreach ($remarks as $remark)
                                                        <div class="mb-3">
                                                            <p><strong>Remark:</strong> <span
                                                                    class="font-bold">{{ $remark->remark }}</span>
                                                            </p>
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
                                    <h6 class="mb-0 text-white">Item of Purchase</h6>
                                </div>
                                <!-- Item Table -->
                                <div class="bg-white p-4 rounded shadow mb-4">
                                    @php
                                        $hasAction = $submissions->contains(function ($submission) {
                                            return in_array($submission->status, [5, 12]);
                                        });

                                        // Definisikan pemetaan bulan untuk menangani format yang berbeda
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

                                        // Kelompokkan submissions berdasarkan item unik
                                        $groupedItems = $submissions
                                            ->groupBy(function ($submission) {
                                                return ($submission->item != null
                                                    ? $submission->item->item
                                                    : $submission->itm_id ?? '') .
                                                    '-' .
                                                    $submission->asset_class .
                                                    '-' .
                                                    $submission->prioritas .
                                                    '-' .
                                                    $submission->alasan .
                                                    '-' .
                                                    $submission->keterangan;
                                            })
                                            ->map(function ($group) use ($monthMap, $monthLabels) {
                                                $first = $group->first();
                                                $months = [];
                                                $totalPrice = 0;

                                                foreach ($group as $submission) {
                                                    // Normalisasi nama bulan
                                                    $month = isset($monthMap[$submission->month])
                                                        ? $monthMap[$submission->month]
                                                        : null;
                                                    if ($month && array_key_exists($month, $monthLabels)) {
                                                        $months[$month] = [
                                                            'month_value' => $submission->month_value,
                                                            'sub_id' => $submission->sub_id,
                                                            'id' => $submission->id,
                                                            'status' => $submission->status,
                                                        ];
                                                        $totalPrice += $submission->month_value;
                                                    }
                                                }

                                                return [
                                                    'item' =>
                                                        $first->item != null
                                                            ? $first->item->item
                                                            : $first->itm_id ?? '',
                                                    'asset_class' => $first->asset_class,
                                                    'prioritas' => $first->prioritas,
                                                    'alasan' => $first->alasan,
                                                    'keterangan' => $first->keterangan,
                                                    'price' => $first->price,
                                                    'amount' => $first->month_value,
                                                    'workcenter' =>
                                                        $first->workcenter != null
                                                            ? $first->workcenter->workcenter
                                                            : '',
                                                    'department' =>
                                                        $first->dept != null ? $first->dept->department : '',
                                                    'months' => $months,
                                                    'total' => $totalPrice,
                                                    'sub_id' => $first->sub_id,
                                                    'id' => $first->id,
                                                    'status' => $first->status,
                                                    'quantity' => $first->quantity,
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
                                    @endphp

                                    @if ($hasAction)
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
                                                    <!-- Fixed Columns (Kiri) -->
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 0; z-index: 110; background-color: #e9ecef; min-width: 80px;">
                                                        Item
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 80px; z-index: 110; background-color: #e9ecef; min-width: 100px;">
                                                        Asset Class
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 180px; z-index: 110; background-color: #e9ecef; min-width: 80px;">
                                                        Prioritas
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 260px; z-index: 110; background-color: #e9ecef; min-width: 120px;">
                                                        Alasan
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 380px; z-index: 110; background-color: #e9ecef; min-width: 120px;">
                                                        Keterangan
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 500px; z-index: 110; background-color: #e9ecef; min-width: 120px;">
                                                        Workcenter
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 620px; z-index: 110; background-color: #e9ecef; min-width: 120px;">
                                                        Department
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 740px; z-index: 110; background-color: #e9ecef; min-width: 80px;">
                                                        Quantity
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 820px; z-index: 110; background-color: #e9ecef; min-width: 120px;">
                                                        Price
                                                    </th>

                                                    <!-- Bulan Columns (Scrollable) -->
                                                    @foreach ($months as $month)
                                                        <th class="text-left border p-2" style="min-width: 100px;">
                                                            {{ $monthLabels[$month] }}
                                                        </th>
                                                    @endforeach

                                                    <!-- Total & Action -->
                                                    <th class="text-left border p-2" style="min-width: 120px;">Total
                                                    </th>

                                                    @if ($hasAction)
                                                        <th class="text-left border p-2" style="min-width: 100px;">
                                                            Action</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($groupedItems as $item)
                                                    <tr class="hover:bg-gray-50">
                                                        <!-- Fixed Columns Data -->
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 0; z-index: 10; background-color: white;">
                                                            {{ $item['item'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 80px; z-index: 10; background-color: white;">
                                                            {{ $item['asset_class'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 180px; z-index: 10; background-color: white;">
                                                            {{ $item['prioritas'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 260px; z-index: 10; background-color: white;">
                                                            {{ $item['alasan'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 380px; z-index: 10; background-color: white;">
                                                            {{ $item['keterangan'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 500px; z-index: 10; background-color: white;">
                                                            {{ $item['workcenter'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 620px; z-index: 10; background-color: white;">
                                                            {{ $item['department'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 740px; z-index: 10; background-color: white;">
                                                            {{ $item['quantity'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 820px; z-index: 10; background-color: white;">
                                                            Rp {{ number_format($item['price'], 0, ',', '.') }}
                                                        </td>

                                                        <!-- Bulan Data -->
                                                        @foreach ($months as $month)
                                                            <td class="border p-2" style="min-width: 100px;">
                                                                @if (isset($item['months'][$month]) && $item['months'][$month]['month_value'] > 0)
                                                                    @if (in_array($item['months'][$month]['status'], [5, 12]))
                                                                        <a href="#" class="editable-month"
                                                                            data-id="{{ $item['months'][$month]['id'] }}"
                                                                            data-sub-id="{{ $item['months'][$month]['sub_id'] }}"
                                                                            data-month="{{ $month }}"
                                                                            data-price="{{ $item['months'][$month]['month_value'] }}"
                                                                            data-price-real="{{ $item['price'] }}">
                                                                            Rp
                                                                            {{ number_format($item['months'][$month]['month_value'], 0, ',', '') }}
                                                                        </a>
                                                                    @else
                                                                        Rp
                                                                        {{ number_format($item['months'][$month]['month_value'], 0, ',', '') }}
                                                                    @endif
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                        @endforeach

                                                        <!-- Total -->
                                                        <td class="border p-2" style="min-width: 120px;">
                                                            Rp {{ number_format($item['total'], 0, ',', '') }}
                                                        </td>

                                                        <!-- Action -->
                                                        @if ($hasAction)
                                                            <td class="border p-2" style="min-width: 100px;">
                                                                @if (in_array($item['status'], [5, 12]))
                                                                    <a href="#"
                                                                        data-id="{{ $item['sub_id'] }}"
                                                                        data-itm-id="{{ $item['id'] }}"
                                                                        class="inline-flex items-center justify-center p-2 text-red-600 hover:text-blue-800 open-edit-modal"
                                                                        title="Update">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    <form
                                                                        action="{{ route('submissions.delete', ['sub_id' => $item['sub_id'], 'id' => $item['id']]) }}"
                                                                        method="POST" class="delete-form"
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
                                                        <td colspan="{{ $hasAction ? 9 + count($months) + 2 : 9 + count($months) + 1 }}"
                                                            class="border p-2 text-center">
                                                            No Submissions found!
                                                        </td>
                                                    </tr>
                                                @endforelse

                                                <!-- Grand Total Row -->
                                                @php
                                                    $grandTotal = $groupedItems->sum('total');
                                                @endphp
                                                <tr class="bg-gray-100 font-bold">
                                                    <td colspan="9" class="border p-2 text-right"
                                                        style="position: sticky; left: 0; z-index: 10; background-color: #f8f9fa;">
                                                        Total
                                                    </td>
                                                    @foreach ($months as $month)
                                                        <td class="border p-2"></td>
                                                    @endforeach
                                                    <td class="border p-2">Rp
                                                        {{ number_format($grandTotal, 0, ',', '.') }}</td>
                                                    @if ($hasAction)
                                                        <td class="border p-2"></td>
                                                    @endif
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <br>
                                </div>
                                <div class="d-flex justify-content-between mt-4">
                                    <button onclick="history.back()" type="button" class="btn btn-secondary me-2">
                                        <i class="fa-solid fa-arrow-left me-2"></i>Back
                                    </button>
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
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card-header rounded-0 bg-secondary text-white py-2 px-2">
                                            <h6 class="mb-0 text-white">Approval Status</h6>
                                        </div>
                                        <!-- Approval Status -->
                                        <div class="bg-green-100 col-md-6 p-4 rounded-0 shadow mb-4">
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
                                                            <span class="badge bg-secondary">UNDER REVIEW KADEP</span>
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
                                                            <span class="badge bg-danger">DISAPPROVED BY KADEP</span>
                                                        @elseif ($submission->status == 9 && !$directDIC)
                                                            <span class="badge bg-danger">DISAPPROVED BY KADIV</span>
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
                                                        data-id="{{ $submission->sub_id }}">History Approval</button>
                                                    <button type="button"
                                                        class="btn open-historyremark-modal text-white"
                                                        style="background-color: #0080ff;"
                                                        data-id="{{ $submission->sub_id ?? '' }}">View
                                                        Remarks</button>
                                                </div>
                                            @else
                                                <p>No submission data available</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card-header rounded-0 bg-secondary text-white py-2 px-2">
                                            <h6 class="mb-0 text-white">Remark</h6>
                                        </div>
                                        <div class="bg-white p-4 rounded-0 shadow mb-4">
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
                                                @foreach ($remarks as $remark)
                                                    <div class="mb-3">
                                                        <p><strong>Remark:</strong> <span
                                                                class="font-bold">{{ $remark->remark }}</span></p>
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
                                                    data-id="{{ $submission->sub_id ?? '' }}">View Remarks</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-header bg-secondary text-white py-2 px-2">
                                    <h6 class="mb-0 text-white">Item of Purchase</h6>
                                </div>
                                <!-- Item Table -->
                                <div class="bg-white p-4 rounded shadow mb-4">
                                    @php
                                        $hasAction = $submissions->contains(function ($submission) {
                                            return in_array($submission->status, [1, 8]);
                                        });

                                        // Definisikan pemetaan bulan untuk menangani format yang berbeda
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

                                        // Kelompokkan submissions berdasarkan item unik
                                        $groupedItems = $submissions
                                            ->groupBy(function ($submission) {
                                                return ($submission->item != null
                                                    ? $submission->item->item
                                                    : $submission->itm_id ?? '') .
                                                    '-' .
                                                    $submission->asset_class .
                                                    '-' .
                                                    $submission->prioritas .
                                                    '-' .
                                                    $submission->alasan .
                                                    '-' .
                                                    $submission->keterangan;
                                            })
                                            ->map(function ($group) use ($monthMap, $monthLabels) {
                                                $first = $group->first();
                                                $months = [];
                                                $totalPrice = 0;

                                                foreach ($group as $submission) {
                                                    // Normalisasi nama bulan
                                                    $month = isset($monthMap[$submission->month])
                                                        ? $monthMap[$submission->month]
                                                        : null;
                                                    if ($month && array_key_exists($month, $monthLabels)) {
                                                        $months[$month] = [
                                                            'month_value' => $submission->month_value,
                                                            'sub_id' => $submission->sub_id,
                                                            'id' => $submission->id,
                                                            'status' => $submission->status,
                                                        ];
                                                        $totalPrice += $submission->month_value;
                                                    }
                                                }

                                                return [
                                                    'item' =>
                                                        $first->item != null
                                                            ? $first->item->item
                                                            : $first->itm_id ?? '',
                                                    'asset_class' => $first->asset_class,
                                                    'prioritas' => $first->prioritas,
                                                    'alasan' => $first->alasan,
                                                    'keterangan' => $first->keterangan,
                                                    'price' => $first->price,
                                                    'amount' => $first->month_value,
                                                    'workcenter' =>
                                                        $first->workcenter != null
                                                            ? $first->workcenter->workcenter
                                                            : '',
                                                    'department' =>
                                                        $first->dept != null ? $first->dept->department : '',
                                                    'months' => $months,
                                                    'total' => $totalPrice,
                                                    'sub_id' => $first->sub_id,
                                                    'id' => $first->id,
                                                    'status' => $first->status,
                                                    'quantity' => $first->quantity,
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
                                    @endphp

                                    @if ($hasAction)
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
                                                    <!-- Fixed Columns (Kiri) -->
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 0; z-index: 110; background-color: #e9ecef; min-width: 80px;">
                                                        Item
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 80px; z-index: 110; background-color: #e9ecef; min-width: 100px;">
                                                        Asset Class
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 180px; z-index: 110; background-color: #e9ecef; min-width: 80px;">
                                                        Prioritas
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 260px; z-index: 110; background-color: #e9ecef; min-width: 120px;">
                                                        Alasan
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 380px; z-index: 110; background-color: #e9ecef; min-width: 120px;">
                                                        Keterangan
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 500px; z-index: 110; background-color: #e9ecef; min-width: 120px;">
                                                        Workcenter
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 620px; z-index: 110; background-color: #e9ecef; min-width: 120px;">
                                                        Department
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 740px; z-index: 110; background-color: #e9ecef; min-width: 80px;">
                                                        Quantity
                                                    </th>
                                                    <th class="text-left border p-2"
                                                        style="position: sticky; left: 820px; z-index: 110; background-color: #e9ecef; min-width: 120px;">
                                                        Price
                                                    </th>

                                                    <!-- Bulan Columns (Scrollable) -->
                                                    @foreach ($months as $month)
                                                        <th class="text-left border p-2" style="min-width: 100px;">
                                                            {{ $monthLabels[$month] }}
                                                        </th>
                                                    @endforeach

                                                    <!-- Total & Action -->
                                                    <th class="text-left border p-2" style="min-width: 120px;">Total
                                                    </th>

                                                    @if ($hasAction)
                                                        <th class="text-left border p-2" style="min-width: 100px;">
                                                            Action</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($groupedItems as $item)
                                                    <tr class="hover:bg-gray-50">
                                                        <!-- Fixed Columns Data -->
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 0; z-index: 10; background-color: white;">
                                                            {{ $item['item'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 80px; z-index: 10; background-color: white;">
                                                            {{ $item['asset_class'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 180px; z-index: 10; background-color: white;">
                                                            {{ $item['prioritas'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 260px; z-index: 10; background-color: white;">
                                                            {{ $item['alasan'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 380px; z-index: 10; background-color: white;">
                                                            {{ $item['keterangan'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 500px; z-index: 10; background-color: white;">
                                                            {{ $item['workcenter'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 620px; z-index: 10; background-color: white;">
                                                            {{ $item['department'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 740px; z-index: 10; background-color: white;">
                                                            {{ $item['quantity'] }}
                                                        </td>
                                                        <td class="border p-2"
                                                            style="position: sticky; left: 820px; z-index: 10; background-color: white;">
                                                            Rp {{ number_format($item['price'], 0, ',', '.') }}
                                                        </td>

                                                        <!-- Bulan Data -->
                                                        @foreach ($months as $month)
                                                            <td class="border p-2" style="min-width: 100px;">
                                                                @if (isset($item['months'][$month]) && $item['months'][$month]['month_value'] > 0)
                                                                    @if (in_array($item['months'][$month]['status'], [1, 8]))
                                                                        <a href="#" class="editable-month"
                                                                            data-id="{{ $item['months'][$month]['id'] }}"
                                                                            data-sub-id="{{ $item['months'][$month]['sub_id'] }}"
                                                                            data-month="{{ $month }}"
                                                                            data-price="{{ $item['months'][$month]['month_value'] }}"
                                                                            data-price-real="{{ $item['price'] }}">
                                                                            Rp
                                                                            {{ number_format($item['months'][$month]['month_value'], 0, ',', '') }}
                                                                        </a>
                                                                    @else
                                                                        Rp
                                                                        {{ number_format($item['months'][$month]['month_value'], 0, ',', '') }}
                                                                    @endif
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                        @endforeach

                                                        <!-- Total -->
                                                        <td class="border p-2" style="min-width: 120px;">
                                                            Rp {{ number_format($item['total'], 0, ',', '') }}
                                                        </td>

                                                        <!-- Action -->
                                                        @if ($hasAction)
                                                            <td class="border p-2" style="min-width: 100px;">
                                                                @if (in_array($item['status'], [1, 8]))
                                                                    <a href="#"
                                                                        data-id="{{ $item['sub_id'] }}"
                                                                        data-itm-id="{{ $item['id'] }}"
                                                                        class="inline-flex items-center justify-center p-2 text-red-600 hover:text-blue-800 open-edit-modal"
                                                                        title="Update">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    <form
                                                                        action="{{ route('submissions.delete', ['sub_id' => $item['sub_id'], 'id' => $item['id']]) }}"
                                                                        method="POST" class="delete-form"
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
                                                        <td colspan="{{ $hasAction ? 9 + count($months) + 2 : 9 + count($months) + 1 }}"
                                                            class="border p-2 text-center">
                                                            No Submissions found!
                                                        </td>
                                                    </tr>
                                                @endforelse

                                                <!-- Grand Total Row -->
                                                @php
                                                    $grandTotal = $groupedItems->sum('total');
                                                @endphp
                                                <tr class="bg-gray-100 font-bold">
                                                    <td colspan="9" class="border p-2 text-right"
                                                        style="position: sticky; left: 0; z-index: 10; background-color: #f8f9fa;">
                                                        Total
                                                    </td>
                                                    @foreach ($months as $month)
                                                        <td class="border p-2"></td>
                                                    @endforeach
                                                    <td class="border p-2">Rp
                                                        {{ number_format($grandTotal, 0, ',', '.') }}</td>
                                                    @if ($hasAction)
                                                        <td class="border p-2"></td>
                                                    @endif
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <br>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <button onclick="history.back()" type="button"
                                    class="btn btn-secondary me-2">Back</button>
                                <div class="d-flex">
                                    @if (in_array($submission->status, [1, 8]))
                                        {{-- <button type="button" class="btn btn-danger open-add-item-modal"
                                                data-sub-id="{{ $submission->sub_id }}">
                                                <i class="fa-solid fa-plus me-2"></i>Add Item
                                            </button> --}}
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
                                <div class="col-md-4">
                                    <label class="form-label">Asset Class</label>
                                    <input type="text" id="edit_month_asset_class" class="form-control" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Prioritas</label>
                                    <input type="text" id="edit_month_prioritas" class="form-control" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Alasan</label>
                                    <input type="text" id="edit_month_alasan" class="form-control" readonly>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Keterangan</label>
                                    <textarea id="edit_month_keterangan" class="form-control" readonly></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_month_price" class="form-label">Price</label>
                                    <input type="text" name="price" id="edit_month_price" class="form-control"
                                        required min="0" step="0.01">
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_month_month_value" class="form-label">Month Value</label>
                                    <input type="text" name="month_value" id="edit_month_month_value"
                                        class="form-control" required min="0" step="0.01">
                                </div>

                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="edit_month_cur_id" class="form-label">Currency</label>
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
                                                {{ $workcenter->workcenter }}
                                            </option>
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
                                <!-- Left Column -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Item <span class="text-danger">*</span></label>
                                        <input type="text" name="itm_id" id="itm_id" class="form-control"
                                            placeholder="Enter item ID" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Asset Class <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="asset_class" id="asset_class"
                                            class="form-control" placeholder="Enter asset class" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Prioritas <span class="text-danger">*</span></label>
                                        <input type="text" name="prioritas" id="prioritas" class="form-control"
                                            placeholder="Enter priority" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Alasan <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="alasan" id="alasan" placeholder="Enter reason" required></textarea>
                                    </div>
                                </div>
                                <!-- Right Column -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Keterangan <span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control" name="keterangan" id="keterangan" placeholder="Enter description" required></textarea>
                                    </div>

                                    <!-- TAMBAHKAN FIELD QUANTITY -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="quantity" class="form-label">Quantity <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="quantity" id="quantity"
                                                class="form-control" required min="1" value="1">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="cur_id" class="form-label">Currency <span
                                                    class="text-danger">*</span></label>
                                            <select name="cur_id" id="cur_id" class="form-control select"
                                                required>
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
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="price" class="form-label">Price <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="price" id="price"
                                                class="form-control" required min="0" step="0.01">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="month_value" class="form-label">Month Value <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="month_value" id="month_value"
                                                class="form-control" required min="0" step="0.01">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="amountDisplay" class="form-label">Total Amount (IDR)</label>
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
                                        <input type="hidden" name="dpt_id" value="{{ $submission->dpt_id }}">
                                        <input class="form-control"
                                            value="{{ $submission->dept->department ?? '-' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="wct_id" class="form-label">Workcenter</label>
                                        <select name="wct_id" id="wct_id" class="form-control select">
                                            <option value="">-- Select Workcenter --</option>
                                            @foreach (\App\Models\Workcenter::orderBy('workcenter', 'asc')->get() as $workcenter)
                                                <option value="{{ $workcenter->wct_id }}">
                                                    {{ $workcenter->workcenter }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="month" class="form-label">Month <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control select" name="month" id="month"
                                            required>
                                            <option value="">-- Select Month --</option>
                                            @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                                <option value="{{ $month }}" @selected(old('month') === $month)>
                                                    {{ $month }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn text-white"
                                    style="background-color: #0080ff;">Add
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
                        <h5 class="modal-title">Add Remark</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addRemarkForm" method="POST" action="{{ route('remarks.store') }}">
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link
            href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
            rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

        <script>
            $(document).ready(function() {
                // Handle Edit Month Modal untuk expenditureReport
                $(document).on('click', '.editable-month', function(e) {
                    e.preventDefault();
                    const subId = $(this).data('sub-id');
                    const id = $(this).data('id');
                    const month = $(this).data('month');
                    const monthValue = $(this).data('price');
                    const priceReal = $(this).data('price-real');
                    const itmId = $(this).data('itm-id');
                    const assetClass = $(this).data('asset-class');
                    const prioritas = $(this).data('prioritas');
                    const alasan = $(this).data('alasan');
                    const keterangan = $(this).data('keterangan');
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

                    $('#edit_month_sub_id').val(subId);
                    $('#edit_month_id').val(id);
                    $('#edit_month_name').val(month);
                    $('#display_month').val(month);
                    $('#edit_month_itm_id').val(itmId);
                    $('#edit_month_asset_class').val(assetClass);
                    $('#edit_month_prioritas').val(prioritas);
                    $('#edit_month_alasan').val(alasan);
                    $('#edit_month_keterangan').val(keterangan);

                    // Set nilai yang benar
                    $('#edit_month_month_value').val(monthValue || '');
                    $('#edit_month_price').val(priceReal || '');
                    $('#edit_month_wct_id').val(workcenterId || '');
                    $('#edit_month_cur_id').val(currencyId || '');

                    updateMonthAmountDisplay();
                    $('#editMonthForm').attr('action', '/submissions/' + subId + '/id/' + id + '/month/' +
                        encodeURIComponent(month));
                    $('#editMonthModal').modal('show');
                    initializeSelect2($('#editMonthModal'));
                });

                // Calculate amount for Edit Month Modal - PERBAIKAN DI SINI
                $('#editMonthModal').on('input change',
                    '#edit_month_price, #edit_month_cur_id, #edit_month_month_value',
                    function() {
                        updateMonthAmountDisplay();
                    });

                function updateMonthAmountDisplay() {
                    const price = parseFloat($('#edit_month_price').val()) || 0;
                    const monthValue = parseFloat($('#edit_month_month_value').val()) || 0;
                    const selectedCurrency = $('#edit_month_cur_id').find('option:selected');
                    const currencyNominal = parseFloat(selectedCurrency.data('nominal')) || 1;

                    // Hitung amount berdasarkan price (bukan month_value)
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

                    // Validasi: pastikan month_value tidak kosong
                    const monthValue = parseFloat($('#edit_month_month_value').val()) || 0;
                    if (monthValue <= 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Month Value harus lebih dari 0.',
                            confirmButtonColor: '#d33'
                        });
                        return;
                    }

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

                // Calculate amount for Add Item Modal dengan quantity - PERBAIKAN DI SINI
                $('#addItemModal').on('input change', '#price, #cur_id, #quantity', function() {
                    calculateTotalAmount();
                });

                function calculateTotalAmount() {
                    const $priceInput = $('#addItemModal #price');
                    const $currencySelect = $('#addItemModal #cur_id');
                    const $quantityInput = $('#addItemModal #quantity');
                    const $amountDisplay = $('#addItemModal #amountDisplay');
                    const $amountHidden = $('#addItemModal #amount');
                    const $monthValueInput = $('#addItemModal #month_value');
                    const $currencyNote = $('#addItemModal #currencyNote');

                    const price = parseFloat($priceInput.val()) || 0;
                    const quantity = parseInt($quantityInput.val()) || 1;
                    const selectedCurrency = $currencySelect.find('option:selected');
                    const currencyNominal = parseFloat(selectedCurrency.data('nominal')) || 1;
                    const currencyCode = selectedCurrency.text().trim();

                    // Tampilkan informasi kurs
                    if (currencyNominal !== 1 && currencyCode) {
                        const formattedNominal = currencyNominal.toLocaleString('id-ID', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                        $currencyNote.text(`1 ${currencyCode} = IDR ${formattedNominal}`).show();
                    } else {
                        $currencyNote.text('').hide();
                    }

                    // Hitung total amount (untuk field amount)
                    const totalAmount = price * quantity * currencyNominal;

                    // Set nilai untuk display
                    $amountDisplay.val('IDR ' + totalAmount.toLocaleString('id-ID', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));

                    // Set nilai untuk hidden field amount
                    $amountHidden.val(totalAmount.toFixed(2));
                }

                // Handle Add Item Modal
                $(document).on('click', '.open-add-item-modal', function(e) {
                    e.preventDefault();
                    var subId = $(this).data('sub-id');
                    var modal = $('#addItemModal');
                    modal.find('#sub_id').val(subId);
                    modal.find('#addItemForm')[0].reset();
                    modal.find('#amountDisplay').val('');
                    modal.find('#month_value').val(''); // Biarkan kosong untuk diisi manual
                    modal.find('#quantity').val(1); // Set default quantity
                    modal.find('#cur_id').val('').trigger('change');
                    modal.find('#currencyNote').text('').hide();
                    modal.modal('show');
                    initializeSelect2(modal);
                });

                // Handle Add Item Form Submission
                $(document).on('submit', '#addItemForm', function(e) {
                    e.preventDefault();
                    var form = $(this);

                    // Validasi tambahan
                    const monthValue = parseFloat($('#month_value').val()) || 0;
                    if (monthValue <= 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Month Value harus lebih dari 0.',
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
                                    title: 'Success!',
                                    text: 'Item added successfully.',
                                    confirmButtonColor: '#3085d6'
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = xhr.responseJSON?.message || 'Failed to add item.';
                            if (xhr.status === 422 && xhr.responseJSON?.errors) {
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

            });
        </script>
        <x-footer></x-footer>
    </main>
</body>

</html>
