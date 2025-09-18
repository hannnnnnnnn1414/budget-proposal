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
                                                    <th class="text-left border p-2">Description</th>
                                                    <th class="text-left border p-2">Insurance Company</th>
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
                                                        <td class="border p-2">{{ $submission->description }}</td>
                                                        <td class="border p-2">
                                                            {{ $submission->insurance != null ? $submission->insurance->company : '' }}
                                                        </td>
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
                                                                    style="background-color: #0080ff">APPROVED
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
                                                    <th class="text-left border p-2">Description</th>
                                                    <th class="text-left border p-2">Insurance Company</th>
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
                                                        <td class="border p-2">{{ $submission->description }}</td>
                                                        <td class="border p-2">
                                                            {{ $submission->insurance != null ? $submission->insurance->company : '' }}
                                                        </td>
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
                                                    <th class="text-left border p-2">Description</th>
                                                    <th class="text-left border p-2">Insurance Company</th>
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
                                                        <td class="border p-2">{{ $submission->description }}</td>
                                                        <td class="border p-2">
                                                            {{ $submission->insurance != null ? $submission->insurance->company : '' }}
                                                        </td>
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
                                                    <th class="text-left border p-2">Description</th>
                                                    <th class="text-left border p-2">Insurance Company</th>
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
                                                        <td class="border p-2">{{ $submission->description }}</td>
                                                        <td class="border p-2">
                                                            {{ $submission->insurance != null ? $submission->insurance->company : '' }}
                                                        </td>
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
                                    <h6 class="mb-0 text-white">Approval Status</h5>
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
                                                    <span class="badge bg-secondary">UNDER REVIEW
                                                        KADEP</span>
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
                                        <p>No submission data available</p>
                                    @endif
                                </div>
                                <div class="card-header bg-secondary text-white py-2 px-2">
                                    <h6 class="mb-0 text-white">Item of Purchase</h5>
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
                                                    <th class="text-left border p-2">Description</th>
                                                    <th class="text-left border p-2">Insurance Company</th>
                                                    <!-- [MODIFIKASI] Kolom Qty dihapus -->
                                                    <th class="text-left border p-2">Price</th>
                                                    <th class="text-left border p-2">Amount</th>
                                                    <th class="text-left border p-2">Workcenter</th>
                                                    <th class="text-left border p-2">Department</th>
                                                    <th class="text-left border p-2">Month</th>
                                                    <!-- [MODIFIKASI] Kolom R/NR dihapus -->
                                                    @if ($hasAction)
                                                        <th class="text-left border p-2">Action</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($submissions as $submission)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="border p-2">{{ $submission->description }}</td>
                                                        <td class="border p-2">
                                                            {{ $submission->insurance != null ? $submission->insurance->company : '' }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Kolom Qty dihapus -->
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
                                                        <!-- [MODIFIKASI] Kolom R/NR dihapus -->
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
                                                        <!-- [MODIFIKASI] Ubah colspan karena dua kolom dihapus -->
                                                        <td colspan="{{ $hasAction ? 8 : 7 }}"
                                                            class="border p-2 text-center">
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

                            <!-- Two-Column Layout for Fields -->
                            <div class="row">
                                <!-- Left Column -->
                                <div class="col-md-6">
                                    <!-- Item -->
                                    {{-- <div class="mb-3">
                                        <label for="itm_id" class="form-label">Item</label>
                                        <select name="itm_id" id="itm_id" class="form-control select" required>
                                            <option value="">-- Select Item --</option>
                                            @foreach (\App\Models\Item::orderBy('item', 'asc')->get() as $item)
                                                <option value="{{ $item->itm_id }}">{{ $item->item }}</option>
                                            @endforeach
                                        </select>
                                    </div> --}}
                                    <!-- Description -->
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea name="description" id="description" class="form-control" rows="3" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="ins_id" class="form-label">Insurance Company</label>
                                        <select name="ins_id" id="ins_id" class="form-control select" required>
                                            <option value="">-- Select Company --</option>
                                            @foreach (\App\Models\InsuranceCompany::orderBy('company', 'asc')->get() as $company)
                                                <option value="{{ $company->ins_id }}">{{ $company->company }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- Workcenter -->
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
                                    <!-- [MODIFIKASI] Hapus input Quantity -->
                                    <!-- Amount -->
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
                                <!-- [MODIFIKASI] Hapus input Budget (R/NR) -->
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
                <div class="modal-content">
                    <!-- Konten modal akan dimuat di sini -->
                </div>
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

        <script>
            $(document).ready(function() {
                // [MODIFIKASI] Fungsi untuk inisialisasi Select2
                function initializeSelect2($modal, modalId) {
                    // $modal.find('.select').each(function() {
                    //     $(this).select2({
                    //         width: '100%',
                    //         dropdownParent: $modal,
                    //         theme: 'bootstrap-5',
                    //         placeholder: $(this).attr('id') === 'cur_id' ? '-- Select Currency --' : $(
                    //                 this).attr('id') === 'wct_id' ? '-- Select Workcenter --' : $(this)
                    //             .attr('id') === 'ins_id' ? '-- Select Company --' :
                    //             '-- Select Month --',
                    //         allowClear: true
                    //     });
                    // });

                    // [MODIFIKASI] Sesuaikan tinggi Select2 agar sejajar dengan input lain
                    $modal.find('.select2-selection--single').css({
                        'height': $modal.find('#price').outerHeight() + 'px',
                        'display': 'flex',
                        'align-items': 'center'
                    });
                    $modal.find('.select2-selection__rendered').css({
                        'line-height': $modal.find('#price').outerHeight() + 'px'
                    });

                    // [MODIFIKASI] Hancurkan Select2 saat modal ditutup untuk mencegah duplikasi
                    $modal.on('hidden.bs.modal', function() {
                        $modal.find('.select').each(function() {
                            if ($(this).data('select2')) {
                                $(this).select2('destroy');
                            }
                        });
                    });
                }

                // [MODIFIKASI] Inisialisasi Select2 untuk Add Item Modal
                $('#addItemModal').on('shown.bs.modal', function() {
                    $('#addItemForm')[0].reset();
                    $('#amountDisplay').val('');
                    $('#cur_id').val('').trigger('change');
                    $('#currencyNote').text('').hide();
                    initializeSelect2($(this), '#addItemModal');
                });

                // [MODIFIKASI] Handle opening the Add Item modal
                $(document).on('click', '.open-add-item-modal', function(e) {
                    e.preventDefault();
                    var subId = $(this).data('sub-id');
                    var modal = $('#addItemModal');

                    // Set the sub_id in the form
                    modal.find('#sub_id').val(subId);
                    modal.modal('show');

                    // Reset form fields
                    modal.find('#addItemForm')[0].reset();
                    modal.find('#amountDisplay').val('');
                    modal.find('#cur_id').val('').trigger('change');
                    modal.find('#currencyNote').text('').hide();
                });

                // [MODIFIKASI] Calculate amount dynamically for Add Item Modal
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
                    const amount = price * currencyNominal;
                    $amountDisplay.val('IDR ' + amount.toLocaleString('id-ID', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                    $amountHidden.val(amount.toFixed(2));
                });

                // [MODIFIKASI] Handle Add Item form submission
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
                                    title: 'Sukses!',
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

                // [MODIFIKASI] Tangani klik tombol edit
                $(document).on('click', '.open-edit-modal', function(e) {
                    e.preventDefault();
                    var subId = $(this).data('id');
                    var itmId = $(this).data('itm-id');
                    var modal = $('#editModal');

                    // Load konten modal via AJAX
                    $.get('/submissions/' + subId + '/id/' + itmId + '/edit', function(data) {
                        // [MODIFIKASI] Sanitasi data untuk mencegah undefined, gunakan ins_id
                        const itemData = {
                            ins_id: data.ins_id ||
                                '', // [MODIFIKASI] Gunakan ins_id untuk laporan asuransi
                            description: data.description || '',
                            cur_id: data.cur_id || '',
                            price: data.price || 0,
                            amount: parseFloat(data.amount) || 0,
                            dpt_id: data.dpt_id || '',
                            wct_id: data.wct_id || '',
                            month: data.month || '',
                            acc_id: data.acc_id || '',
                            purpose: data.purpose || '',
                            department: data.department || '-'
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
                                        <label for="description" class="form-label">Description</label>
                                        <textarea name="description" id="description" class="form-control" rows="3" required>${itemData.description}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="ins_id" class="form-label">Insurance Company</label>
                                        <select name="ins_id" id="ins_id" class="form-control select" required>
                                            <option value="">-- Select Company --</option>
                                            @foreach (\App\Models\InsuranceCompany::orderBy('company', 'asc')->get() as $company)
                                                <option value="{{ $company->ins_id }}" ${itemData.ins_id === '{{ $company->ins_id }}' ? 'selected' : ''}>
                                                    {{ $company->company }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="wct_id" class="form-label">Workcenter</label>
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
                                            <select name="cur_id" id="cur_id" class="form-select select" required>
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
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn text-white" style="background-color: #0080ff;">Update Item</button>
                            </div>
                        </form>
                    </div>
                `);

                        // [MODIFIKASI] Inisialisasi Select2 untuk Edit Modal
                        initializeSelect2(modal, '#editModal');

                        // [MODIFIKASI] Hitung amount secara dinamis untuk Edit Modal
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

                        modal.modal('show');
                    }).fail(function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Gagal memuat form edit: ' + (xhr.responseJSON?.message ||
                                'Kesalahan tidak diketahui'),
                            confirmButtonColor: '#d33'
                        });
                    });
                });

                // [MODIFIKASI] Tangani submit form di dalam modal
                $(document).on('submit', '#editItemForm', function(e) {
                    e.preventDefault();
                    var form = $(this);

                    // [MODIFIKASI] Validasi field wajib sebelum submit
                    const $insId = form.find('#ins_id');
                    const $curId = form.find('#cur_id');
                    const $month = form.find('#month');
                    const $description = form.find('#description');
                    const $price = form.find('#price');

                    if (!$insId.val() || !$curId.val() || !$month.val() || !$description.val() || !$price
                        .val()) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Harap isi semua field yang wajib.',
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
                                    title: 'Sukses!',
                                    text: 'Item berhasil diperbarui.',
                                    confirmButtonColor: '#3085d6'
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = xhr.responseJSON?.message ||
                                'Gagal memperbarui item.';
                            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                errorMessage = Object.values(xhr.responseJSON.errors).flat().join(
                                    ' ');
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

                // [MODIFIKASI] Tangani klik tombol history approval
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
                                '<div class="alert alert-danger">Gagal memuat riwayat persetujuan</div>'
                            );
                        });
                });

                // [MODIFIKASI] Handle opening the Add Remark modal
                $(document).on('click', '.open-add-remark-modal', function(e) {
                    e.preventDefault();
                    var subId = $(this).data('id');
                    var modal = $('#addRemarkModal');

                    // Set the sub_id in the form
                    modal.find('#sub_id').val(subId);
                    modal.modal('show');
                });

                // [MODIFIKASI] Handle Add Remark form submission
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
                                    title: 'Sukses!',
                                    text: 'Komentar berhasil ditambahkan.',
                                    confirmButtonColor: '#3085d6'
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function(xhr) {
                            form.find('.modal-body').prepend(
                                '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                (xhr.responseJSON.message || 'Gagal menambahkan komentar') +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'
                            );
                        }
                    });
                });

                // [MODIFIKASI] Tangani klik tombol history remark
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
                                '<div class="alert alert-danger">Gagal memuat riwayat komentar</div>');
                        });
                });

                // [MODIFIKASI] Tangani klik tombol delete
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

                // [MODIFIKASI] Tangani submit form send
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
                                            title: 'Sukses!',
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

                // [MODIFIKASI] Tangani submit form approve
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
                                            title: 'Sukses!',
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

                // [MODIFIKASI] Tangani submit form disapprove
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
                                            title: 'Sukses!',
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
            });
        </script>
        <x-footer></x-footer>
    </main>
</body>

</html>
