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
                                    @endphp
                                    @if (in_array($submission->status, [2, 6, 9]))
                                        <div class="d-flex justify-content-end mb-3">
                                            <button type="button" class="btn btn-danger open-add-item-modal"
                                                data-sub-id="{{ $submission->sub_id }}">
                                                <i class="fa-solid fa-plus me-2"></i>Add Item
                                            </button>
                                        </div>
                                    @endif
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="bg-gray-200">
                                                <tr>
                                                    <th class="text-left border p-2">Item</th>
                                                    <th class="text-left border p-2">Description</th>
                                                    <th class="text-left border p-2">Days</th>
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
                                                        <td class="border p-2">{{ $submission->description }}</td>
                                                        <td class="border p-2">{{ $submission->days }}</td>
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
                                                                @if (in_array($submission->status, [2, 6, 9]))
                                                                    <a href="#"
                                                                        data-id="{{ $submission->sub_id }}"
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
                                            <thead class="bg-gray-200">
                                                <tr>
                                                    <th class="text-left border p-2">Item</th>
                                                    <th class="text-left border p-2">Description</th>
                                                    <th class="text-left border p-2">Days</th>
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
                                                        <td class="border p-2">{{ $submission->description }}</td>
                                                        <td class="border p-2">{{ $submission->days }}</td>
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
                                                                @if (in_array($submission->status, [3, 10]))
                                                                    <a href="#"
                                                                        data-id="{{ $submission->sub_id }}"
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
                                            <thead class="bg-gray-200">
                                                <tr>
                                                    <th class="text-left border p-2">Item</th>
                                                    <th class="text-left border p-2">Description</th>
                                                    <th class="text-left border p-2">Days</th>
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
                                                        <td class="border p-2">{{ $submission->description }}</td>
                                                        <td class="border p-2">{{ $submission->days }}</td>
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
                                                                @if (in_array($submission->status, [4, 11]))
                                                                    <a href="#"
                                                                        data-id="{{ $submission->sub_id }}"
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
                                            return in_array($submission->status, [5, 12]);
                                        });
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
                                            <thead class="bg-gray-200">
                                                <tr>
                                                    <th class="text-left border p-2">Item</th>
                                                    <th class="text-left border p-2">Description</th>
                                                    <th class="text-left border p-2">Days</th>
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
                                                        <td class="border p-2">{{ $submission->description }}</td>
                                                        <td class="border p-2">{{ $submission->days }}</td>
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
                                                                @if (in_array($submission->status, [5, 12]))
                                                                    <a href="#"
                                                                        data-id="{{ $submission->sub_id }}"
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
                                        <p>Date: {{ $approval ? $approval->created_at->format('d-m-Y H:i') : '-' }}</p>

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
                                    @endphp
                                    @if (in_array($submission->status, [1, 8]))
                                        <div class="d-flex justify-content-end mb-3">
                                            <button type="button" class="btn btn-danger open-add-item-modal"
                                                data-sub-id="{{ $submission->sub_id }}">
                                                <i class="fa-solid fa-plus me-2"></i>Add Item
                                            </button>
                                        </div>
                                    @endif
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="bg-gray-200">
                                                <tr>
                                                    <th class="text-left border p-2" width="5%">No.</th>
                                                    <!-- [MODIFIKASI] Tambah kolom No. -->
                                                    <th class="text-left border p-2">R/NR</th>
                                                    <!-- [MODIFIKASI] Tambah kolom R/NR -->
                                                    <th class="text-left border p-2">Business Partner</th>
                                                    <!-- [MODIFIKASI] Tambah kolom Business Partner -->
                                                    <th class="text-left border p-2">Line Of Business</th>
                                                    <!-- [MODIFIKASI] Tambah kolom Line Of Business -->
                                                    <th class="text-left border p-2">Item</th>
                                                    <!-- [MODIFIKASI] Ganti Trip Propose dengan Item -->
                                                    <th class="text-left border p-2">Description</th>
                                                    <!-- [MODIFIKASI] Ganti Destination dengan Description -->
                                                    <th class="text-left border p-2">Price</th>
                                                    <th class="text-left border p-2">Amount</th>
                                                    <th class="text-left border p-2">Workcenter</th>
                                                    <th class="text-left border p-2">Department</th>
                                                    <th class="text-left border p-2">Month</th>
                                                    @if ($hasAction)
                                                        <th class="text-left border p-2">Action</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($submissions as $index => $submission)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="border p-2">{{ $index + 1 }}</td>
                                                        <!-- [MODIFIKASI] Tampilkan nomor urut -->
                                                        <td class="border p-2">
                                                            {{ isset($budget_codes[$submission->bdc_id]) ? $budget_codes[$submission->bdc_id] : ($submission->bdc_id === 'R' ? 'Routine' : ($submission->bdc_id === 'NR' ? 'Non-Routine' : '-')) }}
                                                        </td> <!-- [MODIFIKASI] Tampilkan R/NR -->
                                                        <td class="border p-2">
                                                            {{ $submission->business_partner ?? '-' }}</td>
                                                        <!-- [MODIFIKASI] Tampilkan Business Partner -->
                                                        <td class="border p-2">
                                                            {{ $line_businesses[$submission->lob_id] ?? ($submission->lob_id ?? '-') }}
                                                        </td> <!-- [MODIFIKASI] Tampilkan Line Of Business -->
                                                        <td class="border p-2">
                                                            {{ $submission->item != null ? $submission->item->itm_id : $submission->itm_id ?? '-' }}
                                                        </td> <!-- [MODIFIKASI] Ganti trip_propose dengan itm_id -->
                                                        <td class="border p-2">{{ $submission->description ?? '-' }}
                                                        </td> <!-- [MODIFIKASI] Ganti destination dengan description -->
                                                        <td class="border p-2">IDR
                                                            {{ number_format($submission->price, 2, ',', '.') }}</td>
                                                        <!-- [MODIFIKASI] Ubah format harga ke IDR dengan 2 desimal -->
                                                        <td class="border p-2">IDR
                                                            {{ number_format($submission->amount, 2, ',', '.') }}</td>
                                                        <!-- [MODIFIKASI] Ubah format amount ke IDR dengan 2 desimal -->
                                                        <td class="border p-2">
                                                            {{ $submission->workcenter != null ? $submission->workcenter->workcenter : '-' }}
                                                        </td>
                                                        <td class="border p-2">
                                                            {{ $submission->dept != null ? $submission->dept->department : '-' }}
                                                        </td>
                                                        <td class="border p-2">{{ $submission->month ?? '-' }}</td>
                                                        @if ($hasAction)
                                                            <td class="border p-2">
                                                                @if (in_array($submission->status, [1, 8]))
                                                                    <a href="#"
                                                                        data-id="{{ $submission->sub_id }}"
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
                                                        <td colspan="12" class="border p-2 text-center">No
                                                            Submissions found!</td>
                                                        <!-- [MODIFIKASI] Ubah colspan menjadi 12 karena ada tambahan kolom -->
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
                                                            <option value="">-- Pilih Budget Code --</option>
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
                                                            <option value="">-- Pilih Line Of Business --
                                                            </option>
                                                            @foreach ($line_businesses as $lob_id => $line)
                                                                <option value="{{ $lob_id }}"
                                                                    @selected(old('lob_id') == $lob_id)>{{ $line }}
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
                                                            <option value="">-- Pilih Workcenter --</option>
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
                                                            <option value="">-- Pilih Bulan --</option>
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
                                // Inisialisasi Select2 untuk semua elemen dengan class 'select'
                                $('.select').select2({
                                    width: '100%',
                                    dropdownParent: $('#addItemModal, #editModal'),
                                    theme: 'bootstrap-5',
                                    allowClear: true,
                                    placeholder: '-- Pilih --'
                                });

                                // Event saat modal tambah item ditampilkan
                                $('#addItemModal').on('shown.bs.modal', function() {
                                    // Inisialisasi Select2 untuk field di modal tambah
                                    $('#bdc_id, #lob_id, #cur_id, #wct_id, #month')
                                        .select2({ // [MODIFIKASI] Ganti #rnr dengan #bdc_id
                                            dropdownParent: $('#addItemModal'),
                                            allowClear: true,
                                            placeholder: '-- Pilih --',
                                            width: '100%',
                                            theme: 'bootstrap-5'
                                        });

                                    // Sesuaikan tinggi Select2 agar sejajar dengan input lain
                                    $('.select2-selection--single').css({
                                        'height': $('#addItemModal #price').outerHeight() + 'px',
                                        'display': 'flex',
                                        'align-items': 'center'
                                    });
                                    $('.select2-selection__rendered').css({
                                        'line-height': $('#addItemModal #price').outerHeight() + 'px'
                                    });

                                    // Reset form saat modal dibuka
                                    $('#addItemForm')[0].reset();
                                    $('#amountDisplay').val('');
                                    $('#bdc_id, #lob_id, #cur_id, #wct_id, #month').val('').trigger(
                                        'change'); // [MODIFIKASI] Reset bdc_id dan lob_id
                                });

                                // Event saat modal edit item ditampilkan
                                $('#editModal').on('shown.bs.modal', function() {
                                    // Inisialisasi Select2 untuk field di modal edit
                                    $('#edit_bdc_id, #edit_lob_id, #edit_cur_id, #edit_wct_id, #edit_month')
                                        .select2({ // [MODIFIKASI] Ganti edit_rnr dengan edit_bdc_id
                                            dropdownParent: $('#editModal'),
                                            allowClear: true,
                                            placeholder: '-- Pilih --',
                                            width: '100%',
                                            theme: 'bootstrap-5'
                                        });

                                    // Sesuaikan tinggi Select2 agar sejajar dengan input lain
                                    $('.select2-selection--single').css({
                                        'height': $('#editModal #edit_price').outerHeight() + 'px',
                                        'display': 'flex',
                                        'align-items': 'center'
                                    });
                                    $('.select2-selection__rendered').css({
                                        'line-height': $('#editModal #edit_price').outerHeight() + 'px'
                                    });
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
                                        url: `/submissions/${subId}/id/${itmId}/edit`,
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
                                                <option value="">-- Pilih Budget Code --</option>
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
                                                <option value="">-- Pilih Line Of Business --</option>
                                                @foreach ($line_businesses as $lob_id => $line)
                                                    <option value="{{ $lob_id }}" ${data.lob_id === '{{ $lob_id }}' ? 'selected' : ''}>{{ $line }}</option>
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
                                                <option value="">-- Pilih Workcenter --</option>
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
                                                <option value="">-- Pilih Bulan --</option>
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
                            });
                        </script>
                        <x-footer></x-footer>
    </main>
</body>

</html>
