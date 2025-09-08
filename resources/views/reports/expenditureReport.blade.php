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
                                                                        APPROVED BY KADIV
                                                                    @endif
                                                                </span>
                                                            @elseif ($submission->status == 5)
                                                                <span class="badge"
                                                                    style="background-color: #0080ff">APPROVED BY
                                                                    DIC</span>
                                                            @elseif ($submission->status == 6)
                                                                <span class="badge"
                                                                    style="background-color: #0080ff">APPROVED BY PIC
                                                                    BUDGETING</span>
                                                            @elseif ($submission->status == 7)
                                                                <span class="badge"
                                                                    style="background-color: #0080ff">APPROVED BY KADEP
                                                                    BUDGETING</span>
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
                                        // Group submissions by item unique (itm_id, asset_class, prioritas, alasan, keterangan)
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
                                            ->map(function ($group) {
                                                $first = $group->first();
                                                $months = [];
                                                foreach ($group as $submission) {
                                                    $months[$submission->month] = $submission->price; // Ganti quantity menjadi price
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
                                                    'amount' => $first->amount,
                                                    'workcenter' =>
                                                        $first->workcenter != null
                                                            ? $first->workcenter->workcenter
                                                            : '',
                                                    'department' =>
                                                        $first->dept != null ? $first->dept->department : '',
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
                                    @endphp
                                    @if ($hasAction)
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
                                                    <th class="text-left border p-2">Asset Class</th>
                                                    <th class="text-left border p-2">Prioritas</th>
                                                    <th class="text-left border p-2">Alasan</th>
                                                    <th class="text-left border p-2">Keterangan</th>
                                                    <th class="text-left border p-2">Price</th>
                                                    <th class="text-left border p-2">Amount</th>
                                                    <th class="text-left border p-2">Workcenter</th>
                                                    <th class="text-left border p-2">Department</th>
                                                    @foreach ($months as $month)
                                                        <th class="text-left border p-2">{{ $month }}</th>
                                                    @endforeach
                                                    @if ($hasAction)
                                                        <th class="text-left border p-2">Action</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($groupedItems as $item)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="border p-2">{{ $item['item'] }}</td>
                                                        <td class="border p-2">{{ $item['asset_class'] }}</td>
                                                        <td class="border p-2">{{ $item['prioritas'] }}</td>
                                                        <td class="border p-2">{{ $item['alasan'] }}</td>
                                                        <td class="border p-2">{{ $item['keterangan'] }}</td>
                                                        <td class="border p-2">Rp
                                                            {{ number_format($item['price'], 0, ',', '.') }}</td>
                                                        <td class="border p-2">Rp
                                                            {{ number_format($item['amount'], 0, ',', '.') }}</td>
                                                        <td class="border p-2">{{ $item['workcenter'] }}</td>
                                                        <td class="border p-2">{{ $item['department'] }}</td>
                                                        @foreach ($months as $month)
                                                            <td class="border p-2 text-center">
                                                                {{ isset($item['months'][$month]) ? 'Rp ' . number_format($item['months'][$month], 0, ',', '.') : '-' }}
                                                            </td>
                                                        @endforeach
                                                        @if ($hasAction)
                                                            <td class="border p-2">
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
                                                        <td colspan="{{ $hasAction ? 22 : 21 }}"
                                                            class="border p-2 text-center">
                                                            No Submissions found!
                                                        </td>
                                                    </tr>
                                                @endforelse
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
                                        @if (in_array($submission->status, [2, 6, 9]))
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
                                                    @endphp
                                                    <p>Status: <span class="font-bold">
                                                            @if ($submission->status == 3)
                                                                <span class="badge bg-warning">REQUIRES APPROVAL</span>
                                                            @elseif ($submission->status == 4)
                                                                <span class="badge"
                                                                    style="background-color: #0080ff">APPROVED BY
                                                                    KADIV</span>
                                                            @elseif ($submission->status == 5)
                                                                <span class="badge"
                                                                    style="background-color: #0080ff">APPROVED BY
                                                                    DIC</span>
                                                            @elseif ($submission->status == 6)
                                                                <span class="badge"
                                                                    style="background-color: #0080ff">APPROVED BY PIC
                                                                    BUDGETING</span>
                                                            @elseif ($submission->status == 7)
                                                                <span class="badge"
                                                                    style="background-color: #0080ff">APPROVED BY KADEP
                                                                    BUDGETING</span>
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
                                            return in_array($submission->status, [2, 9]);
                                        });
                                        // Group submissions by item unique (itm_id, asset_class, prioritas, alasan, keterangan)
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
                                            ->map(function ($group) {
                                                $first = $group->first();
                                                $months = [];
                                                foreach ($group as $submission) {
                                                    $months[$submission->month] = $submission->price; // Ganti quantity menjadi price
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
                                                    'amount' => $first->amount,
                                                    'workcenter' =>
                                                        $first->workcenter != null
                                                            ? $first->workcenter->workcenter
                                                            : '',
                                                    'department' =>
                                                        $first->dept != null ? $first->dept->department : '',
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
                                    @endphp
                                    @if (in_array($submission->status, [3, 10]))
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
                                                    <th class="text-left border p-2">Asset Class</th>
                                                    <th class="text-left border p-2">Prioritas</th>
                                                    <th class="text-left border p-2">Alasan</th>
                                                    <th class="text-left border p-2">Keterangan</th>
                                                    <th class="text-left border p-2">Price</th>
                                                    <th class="text-left border p-2">Amount</th>
                                                    <th class="text-left border p-2">Workcenter</th>
                                                    <th class="text-left border p-2">Department</th>
                                                    @foreach ($months as $month)
                                                        <th class="text-left border p-2">{{ $month }}</th>
                                                    @endforeach
                                                    @if ($hasAction)
                                                        <th class="text-left border p-2">Action</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($groupedItems as $item)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="border p-2">{{ $item['item'] }}</td>
                                                        <td class="border p-2">{{ $item['asset_class'] }}</td>
                                                        <td class="border p-2">{{ $item['prioritas'] }}</td>
                                                        <td class="border p-2">{{ $item['alasan'] }}</td>
                                                        <td class="border p-2">{{ $item['keterangan'] }}</td>
                                                        <td class="border p-2">Rp
                                                            {{ number_format($item['price'], 0, ',', '.') }}</td>
                                                        <td class="border p-2">Rp
                                                            {{ number_format($item['amount'], 0, ',', '.') }}</td>
                                                        <td class="border p-2">{{ $item['workcenter'] }}</td>
                                                        <td class="border p-2">{{ $item['department'] }}</td>
                                                        @foreach ($months as $month)
                                                            <td class="border p-2 text-center">
                                                                {{ isset($item['months'][$month]) ? 'Rp ' . number_format($item['months'][$month], 0, ',', '.') : '-' }}
                                                            </td>
                                                        @endforeach
                                                        @if ($hasAction)
                                                            <td class="border p-2">
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
                                                        <td colspan="{{ $hasAction ? 22 : 21 }}"
                                                            class="border p-2 text-center">
                                                            No Submissions found!
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <br>
                                </div>
                                <div class="d-flex justify-content-between mt-4">
                                    <button onclick="history.back()" type="button"
                                        class="btn btn-secondary me-2">Back</button>
                                    <div class="d-flex gap-3">
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
                                    </div>
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
                                                            @if ($submission->status == 4)
                                                                <span class="badge bg-warning">REQUIRES APPROVAL</span>
                                                            @elseif ($submission->status == 5)
                                                                <span class="badge"
                                                                    style="background-color: #0080ff">APPROVED BY
                                                                    DIC</span>
                                                            @elseif ($submission->status == 6)
                                                                <span class="badge"
                                                                    style="background-color: #0080ff">APPROVED BY PIC
                                                                    BUDGETING</span>
                                                            @elseif ($submission->status == 7)
                                                                <span class="badge"
                                                                    style="background-color: #0080ff">APPROVED BY KADEP
                                                                    BUDGETING</span>
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
                                            return in_array($submission->status, [2, 9]);
                                        });
                                        // Group submissions by item unique (itm_id, asset_class, prioritas, alasan, keterangan)
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
                                            ->map(function ($group) {
                                                $first = $group->first();
                                                $months = [];
                                                foreach ($group as $submission) {
                                                    $months[$submission->month] = $submission->price; // Ganti quantity menjadi price
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
                                                    'amount' => $first->amount,
                                                    'workcenter' =>
                                                        $first->workcenter != null
                                                            ? $first->workcenter->workcenter
                                                            : '',
                                                    'department' =>
                                                        $first->dept != null ? $first->dept->department : '',
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
                                    @endphp
                                    @if (in_array($submission->status, [4, 11]))
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
                                                    <th class="text-left border p-2">Asset Class</th>
                                                    <th class="text-left border p-2">Prioritas</th>
                                                    <th class="text-left border p-2">Alasan</th>
                                                    <th class="text-left border p-2">Keterangan</th>
                                                    <th class="text-left border p-2">Price</th>
                                                    <th class="text-left border p-2">Amount</th>
                                                    <th class="text-left border p-2">Workcenter</th>
                                                    <th class="text-left border p-2">Department</th>
                                                    @foreach ($months as $month)
                                                        <th class="text-left border p-2">{{ $month }}</th>
                                                    @endforeach
                                                    @if ($hasAction)
                                                        <th class="text-left border p-2">Action</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($groupedItems as $item)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="border p-2">{{ $item['item'] }}</td>
                                                        <td class="border p-2">{{ $item['asset_class'] }}</td>
                                                        <td class="border p-2">{{ $item['prioritas'] }}</td>
                                                        <td class="border p-2">{{ $item['alasan'] }}</td>
                                                        <td class="border p-2">{{ $item['keterangan'] }}</td>
                                                        <td class="border p-2">Rp
                                                            {{ number_format($item['price'], 0, ',', '.') }}</td>
                                                        <td class="border p-2">Rp
                                                            {{ number_format($item['amount'], 0, ',', '.') }}</td>
                                                        <td class="border p-2">{{ $item['workcenter'] }}</td>
                                                        <td class="border p-2">{{ $item['department'] }}</td>
                                                        @foreach ($months as $month)
                                                            <td class="border p-2 text-center">
                                                                {{ isset($item['months'][$month]) ? 'Rp ' . number_format($item['months'][$month], 0, ',', '.') : '-' }}
                                                            </td>
                                                        @endforeach
                                                        @if ($hasAction)
                                                            <td class="border p-2">
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
                                                        <td colspan="{{ $hasAction ? 22 : 21 }}"
                                                            class="border p-2 text-center">
                                                            No Submissions found!
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <br>
                                </div>
                                <div class="d-flex justify-content-between mt-4">
                                    <button onclick="history.back()" type="button"
                                        class="btn btn-secondary me-2">Back</button>
                                    <div class="d-flex gap-3">
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
                                    </div>
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
                                                    @endphp
                                                    <p>Status: <span class="font-bold">
                                                            @if ($submission->status == 5)
                                                                <span class="badge bg-warning">REQUIRES APPROVAL</span>
                                                            @elseif ($submission->status == 6)
                                                                <span class="badge"
                                                                    style="background-color: #0080ff">APPROVED BY PIC
                                                                    BUDGETING</span>
                                                            @elseif ($submission->status == 7)
                                                                <span class="badge"
                                                                    style="background-color: #0080ff">APPROVED BY KADEP
                                                                    BUDGETING</span>
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
                                            return in_array($submission->status, [5, 12]);
                                        });
                                        // Group submissions by item unique (itm_id, asset_class, prioritas, alasan, keterangan)
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
                                            ->map(function ($group) {
                                                $first = $group->first();
                                                $months = [];
                                                foreach ($group as $submission) {
                                                    $months[$submission->month] = $submission->price; // Ganti quantity menjadi price
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
                                                    'amount' => $first->amount,
                                                    'workcenter' =>
                                                        $first->workcenter != null
                                                            ? $first->workcenter->workcenter
                                                            : '',
                                                    'department' =>
                                                        $first->dept != null ? $first->dept->department : '',
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
                                    @endphp
                                    @if (in_array($submission->status, [5, 12]))
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
                                                    <th class="text-left border p-2">Asset Class</th>
                                                    <th class="text-left border p-2">Prioritas</th>
                                                    <th class="text-left border p-2">Alasan</th>
                                                    <th class="text-left border p-2">Keterangan</th>
                                                    <th class="text-left border p-2">Price</th>
                                                    <th class="text-left border p-2">Amount</th>
                                                    <th class="text-left border p-2">Workcenter</th>
                                                    <th class="text-left border p-2">Department</th>
                                                    @foreach ($months as $month)
                                                        <th class="text-left border p-2">{{ $month }}</th>
                                                    @endforeach
                                                    @if ($hasAction)
                                                        <th class="text-left border p-2">Action</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($groupedItems as $item)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="border p-2">{{ $item['item'] }}</td>
                                                        <td class="border p-2">{{ $item['asset_class'] }}</td>
                                                        <td class="border p-2">{{ $item['prioritas'] }}</td>
                                                        <td class="border p-2">{{ $item['alasan'] }}</td>
                                                        <td class="border p-2">{{ $item['keterangan'] }}</td>
                                                        <td class="border p-2">Rp
                                                            {{ number_format($item['price'], 0, ',', '.') }}</td>
                                                        <td class="border p-2">Rp
                                                            {{ number_format($item['amount'], 0, ',', '.') }}</td>
                                                        <td class="border p-2">{{ $item['workcenter'] }}</td>
                                                        <td class="border p-2">{{ $item['department'] }}</td>
                                                        @foreach ($months as $month)
                                                            <td class="border p-2 text-center">
                                                                {{ isset($item['months'][$month]) ? 'Rp ' . number_format($item['months'][$month], 0, ',', '.') : '-' }}
                                                            </td>
                                                        @endforeach
                                                        @if ($hasAction)
                                                            <td class="border p-2">
                                                                @if (in_array($item['status'], [5, 12]))
                                                                    <a href="#" data-id="{{ $item['sub_id'] }}"
                                                                        data-itm-id="{{ $item['id'] }}"
                                                                        class="inline-flex items-center justify-center p-2 text-red-600 hover:text-blue-800 open-edit-modal"
                                                                        title="Update">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    <form
                                                                        action="{{ route('submissions.delete', ['sub_id' => $item['sub_id'], 'id' => $item['id']]) }}"
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
                                                        <td colspan="{{ $hasAction ? 22 : 21 }}"
                                                            class="border p-2 text-center">
                                                            No Submissions found!
                                                        </td>
                                                    </tr>
                                                @endforelse
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
                                        <div class="bg-green-100 p-4 rounded-0 shadow mb-4">
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
                                                                style="background-color: #0080ff">APPROVED BY PIC
                                                                BUDGETING</span>
                                                        @elseif ($submission->status == 7)
                                                            <span class="badge"
                                                                style="background-color: #0080ff">APPROVED BY KADEP
                                                                BUDGETING</span>
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
                                        // Group submissions by item unique (itm_id, asset_class, prioritas, alasan, keterangan)
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
                                            ->map(function ($group) {
                                                $first = $group->first();
                                                $months = [];
                                                foreach ($group as $submission) {
                                                    $months[$submission->month] = $submission->price; // Changed from quantity to price
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
                                                    'amount' => $first->amount,
                                                    'workcenter' =>
                                                        $first->workcenter != null
                                                            ? $first->workcenter->workcenter
                                                            : '',
                                                    'department' =>
                                                        $first->dept != null ? $first->dept->department : '',
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
                                    @endphp
                                    @if ($hasAction)
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
                                                    <th class="text-left border p-2">Asset Class</th>
                                                    <th class="text-left border p-2">Prioritas</th>
                                                    <th class="text-left border p-2">Alasan</th>
                                                    <th class="text-left border p-2">Keterangan</th>
                                                    <th class="text-left border p-2">Price</th>
                                                    <th class="text-left border p-2">Amount</th>
                                                    <th class="text-left border p-2">Workcenter</th>
                                                    <th class="text-left border p-2">Department</th>
                                                    @foreach ($months as $month)
                                                        <th class="text-left border p-2">{{ $month }}</th>
                                                    @endforeach
                                                    @if ($hasAction)
                                                        <th class="text-left border p-2">Action</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($groupedItems as $item)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="border p-2">{{ $item['item'] }}</td>
                                                        <td class="border p-2">{{ $item['asset_class'] }}</td>
                                                        <td class="border p-2">{{ $item['prioritas'] }}</td>
                                                        <td class="border p-2">{{ $item['alasan'] }}</td>
                                                        <td class="border p-2">{{ $item['keterangan'] }}</td>
                                                        <td class="border p-2">Rp
                                                            {{ number_format($item['price'], 0, ',', '.') }}</td>
                                                        <td class="border p-2">Rp
                                                            {{ number_format($item['amount'], 0, ',', '.') }}</td>
                                                        <td class="border p-2">{{ $item['workcenter'] }}</td>
                                                        <td class="border p-2">{{ $item['department'] }}</td>
                                                        @foreach ($months as $month)
                                                            <td class="border p-2 text-center">
                                                                {{ isset($item['months'][$month]) ? 'Rp ' . number_format($item['months'][$month], 0, ',', '.') : '-' }}
                                                            </td>
                                                        @endforeach
                                                        @if ($hasAction)
                                                            <td class="border p-2">
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
                                                        <td colspan="{{ $hasAction ? 22 : 21 }}"
                                                            class="border p-2 text-center">
                                                            No Submissions found!
                                                        </td>
                                                    </tr>
                                                @endforelse
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
                                    {{-- <div class="mb-3" id="select_item_container">
                                        <label class="form-label">Item GID <span class="text-danger">*</span></label>
                                        <input type="text" name="itm_id" id="itm_id" class="form-control"
                                            placeholder="Enter Item GID" required>
                                    </div> --}}
                                    <div class="mb-3" id="select_item_container">
                                        <label class="form-label">Item GID <span class="text-danger">*</span></label>
                                        <select name="itm_id" id="itm_id" class="form-control select2" required>
                                            <option value="">-- Select Item --</option>
                                            @foreach ($items as $itm_id => $item_name)
                                                <option value="{{ $itm_id }}">{{ $itm_id }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3" id="manual_item_container" style="display: none;">
                                        <label class="form-label">Item Non-GID <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="manual_item" id="manual_item"
                                            class="form-control" placeholder="Enter item name">
                                    </div> <!-- Description -->
                                    <!-- Description -->
                                    <div class="mb-3">
                                        <label for="keterangan" class="form-label">Keterangan</label>
                                        <input type="text" name="keterangan" id="keterangan" class="form-control"
                                            required>
                                    </div>
                                    <!-- Quantity -->
                                    <div class="mb-3">
                                        <label for="wct_id" class="form-label">Workcenter</label>
                                        <select name="wct_id" id="wct_id" class="form-control select" required>
                                            <option value="">-- Select Workcenter --</option>
                                            @foreach (\App\Models\Workcenter::orderBy('workcenter', 'asc')->get() as $workcenter)
                                                <option value="{{ $workcenter->wct_id }}">
                                                    {{ $workcenter->workcenter }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!-- Right Column -->
                                <div class="col-md-6">
                                    <!-- Price -->
                                    <div class="row mb-3">
                                        <!-- Currency -->
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

                                        <!-- Price -->
                                        <div class="col-md-6">
                                            <label for="price" class="form-label">Price <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" name="price" id="price" class="form-control"
                                                required min="0" step="0.01">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="quantity" class="form-label">Quantity</label>
                                        <input type="number" name="quantity" id="quantity" class="form-control"
                                            required min="1" step="1">
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
                                <!-- Single-Column Layout for Remaining Fields -->
                                <!-- Department -->
                                <div class="col-md-4">

                                    <div class="mb-3">
                                        <label class="form-label">Department <span
                                                class="text-danger">*</span></label>
                                        <input type="hidden" name="dpt_id" value="{{ Auth::user()->dept }}">
                                        <input class="form-control"
                                            value="{{ Auth::user()->department->department ?? '-' }}" readonly>
                                    </div>
                                </div>

                                <!-- Month -->
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

                                    <!-- Budget (R/NR) -->
                                    <div class="mb-3">
                                        <label for="bdc_id" class="form-label">Budget (R/NR)</label>
                                        <select name="bdc_id" id="bdc_id" class="form-control select" required>
                                            <option value="">-- Select Budget Code --</option>
                                            @foreach (\App\Models\BudgetCode::orderBy('budget_name', 'asc')->get() as $budget)
                                                <option value="{{ $budget->bdc_id }}">
                                                    {{ $budget->budget_name }}
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
                // Inisialisasi Select2
                $('.select').select({
                    width: '100%',
                    dropdownParent: $('#addItemModal, #editModal')
                });

                $('#addItemModal').on('shown.bs.modal', function() {
                    $('#itm_id').select2({
                        dropdownParent: $('#addItemModal'),
                        allowClear: true,
                        placeholder: '-- Select Item --',
                        width: '100%',
                        theme: 'bootstrap-5'
                    });

                    // Adjust Select2 height to match other inputs
                    $('.select2-selection--single').css({
                        'height': $('#addItemModal #price').outerHeight() + 'px',
                        'display': 'flex',
                        'align-items': 'center'
                    });
                    $('.select2-selection__rendered').css({
                        'line-height': $('#addItemModal #price').outerHeight() + 'px'
                    });

                    $('#addItemForm')[0].reset();
                    $('#amountDisplay').val('');
                    $('#input_type').val('select').trigger('change');
                    $('#cur_id').val('').trigger('change');
                    $('#itm_id').val('').trigger('change');
                    $('#addItemModal #currencyNote').text('').hide();

                });

                $('#addItemModal #input_type').on('change', function() {
                    if ($(this).val() === 'select') {
                        $('#addItemModal #select_item_container').show();
                        $('#addItemModal #manual_item_container').hide();
                        $('#addItemModal #itm_id').prop('required', true);
                        $('#addItemModal #manual_item').prop('required', false).val('');
                        $('#addItemModal #itm_id').val('').trigger('change'); // Reset Select2
                    } else {
                        $('#addItemModal #select_item_container').hide();
                        $('#addItemModal #manual_item_container').show();
                        $('#addItemModal #itm_id').prop('required', false).val('');
                        $('#addItemModal #manual_item').prop('required', true);
                        $('#addItemModal #itm_id').val('').trigger('change'); // Reset Select2

                    }
                });

                // Calculate amount dynamically for Add Item Modal
                $('#addItemModal').on('input change', '#quantity, #price, #cur_id', function() {
                    const $quantityInput = $('#addItemModal #quantity');
                    const $priceInput = $('#addItemModal #price');
                    const $currencySelect = $('#addItemModal #cur_id');
                    const $amountDisplay = $('#addItemModal #amountDisplay');
                    const $amountHidden = $('#addItemModal #amount');
                    const $currencyNote = $('#addItemModal #currencyNote');

                    const quantity = parseFloat($quantityInput.val()) || 0;
                    const price = parseFloat($priceInput.val()) || 0;
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
                    const amount = quantity * price * currencyNominal;

                    $amountDisplay.val('IDR ' + amount.toLocaleString('id-ID', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                    $amountHidden.val(amount.toFixed(2));
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

                // Calculate amount dynamically
                // $('#addItemModal').on('input', '#quantity, #price', function() {
                //     const quantity = parseFloat($('#quantity').val()) || 0;
                //     const price = parseFloat($('#price').val()) || 0;
                //     const amount = quantity * price;

                //     $('#amountDisplay').val('IDR ' + amount.toLocaleString('id-ID', {
                //         minimumFractionDigits: 2,
                //         maximumFractionDigits: 2
                //     }));
                //     $('#amount').val(amount);
                // });

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
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: xhr.responseJSON.message || 'Failed to add item.',
                                confirmButtonColor: '#d33'
                            });
                        }
                    });
                });

                // $('#quantity, #price').on('input', function() {
                //     const quantity = parseFloat($('#quantity').val()) || 0;
                //     const price = parseFloat($('#price').val()) || 0;
                //     const amount = quantity * price;

                //     // Format amount with IDR currency
                //     $('#amountDisplay').val('IDR ' + amount.toLocaleString('id-ID', {
                //         minimumFractionDigits: 2,
                //         maximumFractionDigits: 2
                //     }));
                // });

                // Handle form submission
                // $('#itemForm').on('submit', function(e) {
                //     const quantity = parseFloat($('#quantity').val()) || 0;
                //     const price = parseFloat($('#price').val()) || 0;
                //     const amount = quantity * price;

                //     // Create a hidden input for amount just before submission
                //     $(this).append(`<input type="hidden" name="amount" value="${amount}">`);
                // });

                // // Add Item button click handler
                // $('#addItemBtn').click(function() {
                //     if ($('#purpose').val().trim() === '') {
                //         alert('Please enter the purpose first');
                //         $('#purpose').focus();
                //         return;
                //     }

                //     $('#modal_purpose').val($('#purpose').val());
                //     var modal = new bootstrap.Modal(document.getElementById('itemModal'));
                //     modal.show();
                // });

                // // Update purpose in modal when changed in main form
                // $('#purpose').on('input change', function() {
                //     $('#modal_purpose').val($(this).val());
                // });

                // Tangani klik tombol edit
                $(document).on('click', '.open-edit-modal', function(e) {
                    e.preventDefault();
                    var subId = $(this).data('id');
                    var itmId = $(this).data('itm-id');
                    var modal = $('#editModal');

                    // Load konten modal via AJAX
                    $.get('/submissions/' + subId + '/id/' + itmId + '/edit', function(data) {
                        modal.find('.modal-dialog').html(data);
                        modal.modal('show');
                    });
                });

                // Tangani submit form di dalam modal
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
                                location.reload(); // Muat ulang halaman setelah berhasil update
                            }
                        },
                        error: function(xhr) {
                            // Tampilkan error jika ada
                            $('#editModal .modal-body').prepend(
                                '<div class="alert alert-danger">' +
                                xhr.responseJSON.message +
                                '</div>'
                            );
                        }
                    });
                });

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
                                '<div class="alert alert-danger">Failed to load approval history</div>');
                        });
                });

                // Handle opening the Add Remark modal
                $(document).on('click', '.open-add-remark-modal', function(e) {
                    e.preventDefault();
                    var subId = $(this).data('id');
                    var modal = $('#addRemarkModal');

                    // Set the sub_id in the form
                    modal.find('#sub_id').val(subId);
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
                                location.reload(); // Reload the page to update the remarks section
                            }
                        },
                        error: function(xhr) {
                            // Display error message in the modal
                            form.find('.modal-body').prepend(
                                '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                (xhr.responseJSON.message || 'Failed to add remark') +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'
                            );
                        }
                    });
                });

                $(document).on('click', '.open-historyremark-modal', function(e) {
                    e.preventDefault();
                    var subId = $(this).data('id');
                    var modal = $('#historyremarkModal');

                    // Show loading state
                    modal.find('.modal-body').html(
                        '<div class="text-center py-4"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>'
                    );
                    modal.modal('show');

                    // Load history content
                    $.get('/remarks/remark/' + subId)
                        .done(function(data) {
                            modal.find('.modal-body').html(data);
                        })
                        .fail(function() {
                            modal.find('.modal-body').html(
                                '<div class="alert alert-danger">Failed to load approval history</div>');
                        });


                });

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

                $(document).on('submit', '.send-form', function(e) {
                    e.preventDefault(); // Prevent default form submission
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
                            // Proceed with AJAX submission
                            $.ajax({
                                url: form.attr('action'),
                                method: form.attr('method'),
                                data: form.serialize(),
                                success: function(response, status, xhr) {
                                    console.log('Success Response:',
                                        response); // Log response for debugging
                                    console.log('Status Code:', xhr
                                        .status); // Log status code
                                    // Since controller returns a redirect (302), check status code
                                    if (xhr.status === 200 || xhr.status === 302) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success!',
                                            text: 'Submission sent successfully.',
                                            confirmButtonColor: '#3085d6'
                                        }).then(() => {
                                            location
                                                .reload(); // Reload page to show flash message
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
                                    console.log('Error Response:',
                                        xhr); // Log error for debugging
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
                $(document).on('submit', '.approve-form', function(e) {
                    e.preventDefault(); // Prevent default form submission
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
                            // Proceed with AJAX submission
                            $.ajax({
                                url: form.attr('action'),
                                method: form.attr('method'),
                                data: form.serialize(),
                                success: function(response, status, xhr) {
                                    console.log('Approve Success Response:',
                                        response); // Log response for debugging
                                    console.log('Approve Status Code:', xhr
                                        .status); // Log status code
                                    // Assume success for 200 or 302 status (redirect)
                                    if (xhr.status === 200 || xhr.status === 302) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success!',
                                            text: 'Submission approved successfully.',
                                            confirmButtonColor: '#3085d6'
                                        }).then(() => {
                                            location
                                                .reload(); // Reload page to show flash message
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
                                    console.log('Approve Error Response:',
                                        xhr); // Log error for debugging
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

                // Handle Disapprove form submission with confirmation
                $(document).on('submit', '.disapprove-form', function(e) {
                    e.preventDefault(); // Prevent default form submission
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
                            // Proceed with AJAX submission
                            $.ajax({
                                url: form.attr('action'),
                                method: form.attr('method'),
                                data: form.serialize(),
                                success: function(response, status, xhr) {
                                    console.log('Disapprove Success Response:',
                                        response); // Log response for debugging
                                    console.log('Disapprove Status Code:', xhr
                                        .status); // Log status code
                                    // Assume success for 200 or 302 status (redirect)
                                    if (xhr.status === 200 || xhr.status === 302) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success!',
                                            text: 'Submission disapproved successfully.',
                                            confirmButtonColor: '#3085d6'
                                        }).then(() => {
                                            location
                                                .reload(); // Reload page to show flash message
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
                                    console.log('Disapprove Error Response:',
                                        xhr); // Log error for debugging
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

                $(document).on('click', '.download-pdf-btn', function() {
                    const subId = $(this).data('sub-id');

                    Swal.fire({
                        title: 'Download Document',
                        text: 'Do you want to download the PDF document?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, download',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading indicator
                            Swal.fire({
                                title: 'Preparing document',
                                html: 'Please wait while we prepare your download...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            // Make AJAX request to download endpoint
                            $.ajax({
                                url: '/submissions/' + subId + '/download-documents',
                                method: 'GET',
                                xhrFields: {
                                    responseType: 'blob' // Important for file download
                                },
                                success: function(data, status, xhr) {
                                    // Create a download link
                                    const blob = new Blob([data], {
                                        type: 'application/pdf'
                                    });
                                    const link = document.createElement('a');
                                    link.href = window.URL.createObjectURL(blob);
                                    // Use a default filename or extract from Content-Disposition header if available
                                    const filename = xhr.getResponseHeader(
                                            'Content-Disposition')?.match(/filename="(.+)"/)
                                        ?.[1] || 'document_' + subId + '.pdf';
                                    link.download = filename;
                                    document.body.appendChild(link);
                                    link.click();
                                    document.body.removeChild(link);

                                    // Close the loading indicator
                                    Swal.close();
                                },
                                error: function(xhr) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Failed to download document: ' + (xhr
                                            .responseJSON?.message ||
                                            'Unknown error'),
                                        confirmButtonColor: '#3085d6'
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
