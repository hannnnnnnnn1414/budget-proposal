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
                                                    <th class="text-left border p-2">Beneficiary</th>
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
                                                        <td class="border p-2">{{ $submission->beneficiary }}</td>
                                                        <td class="border p-2">{{ $submission->quantity }}</td>
                                                        <td class="border p-2">{{ $submission->price }}</td>
                                                        <td class="border p-2">{{ $submission->amount }}</td>
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
                                                    <th class="text-left border p-2">Item</th>
                                                    <th class="text-left border p-2">Description</th>
                                                    <th class="text-left border p-2">Beneficiary</th>
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
                                                        <td class="border p-2">{{ $submission->beneficiary }}</td>
                                                        <td class="border p-2">{{ $submission->quantity }}</td>
                                                        <td class="border p-2">{{ $submission->price }}</td>
                                                        <td class="border p-2">{{ $submission->amount }}</td>
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
                                                    <th class="text-left border p-2">Beneficiary</th>
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
                                                        <td class="border p-2">{{ $submission->beneficiary }}</td>
                                                        <td class="border p-2">{{ $submission->quantity }}</td>
                                                        <td class="border p-2">{{ $submission->price }}</td>
                                                        <td class="border p-2">{{ $submission->amount }}</td>
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
                                                            @if ($submission->status == 5)
                                                                <span class="badge bg-warning">REQUIRES APPROVAL
                                                                </span>
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
                                                    <th class="text-left border p-2">Beneficiary</th>
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
                                                        <td class="border p-2">{{ $submission->beneficiary }}</td>
                                                        <td class="border p-2">{{ $submission->quantity }}</td>
                                                        <td class="border p-2">{{ $submission->price }}</td>
                                                        <td class="border p-2">{{ $submission->amount }}</td>
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
                                        @endphp <p>Status: <span class="font-bold">
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
                                                    <th class="text-left border p-2">Item</th>
                                                    <th class="text-left border p-2">Description</th>
                                                    <th class="text-left border p-2">Beneficiary</th>
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
                                                        <td class="border p-2">{{ $submission->beneficiary }}</td>
                                                        <td class="border p-2">{{ $submission->quantity }}</td>
                                                        <td class="border p-2">{{ $submission->price }}</td>
                                                        <td class="border p-2">{{ $submission->amount }}</td>
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
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal untuk Tambah Item -->
        <div id="addItemModal" class="modal fade" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white">Tambah Item Baru</h5>
                        <!-- [MODIFIKASI] Ubah teks ke bahasa Indonesia -->
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
                            <input type="hidden" name="amount" id="amount">
                            <!-- [MODIFIKASI] Tambah hidden input untuk amount -->

                            <!-- Komentar asli: Two-Column Layout for Six Fields -->
                            <div class="row">
                                <!-- Left Column -->
                                <div class="col-md-6">
                                    <!-- Komentar asli: Field input_type dan select_item_container/manual_item_container -->
                                    {{-- <div class="mb-3">
                                <label class="form-label">Input Type <span class="text-danger">*</span></label>
                                <select name="input_type" id="input_type" class="form-control select" required>
                                    <option value="select">Item GID</option>
                                    <option value="manual">Item Non-GID</option>
                                </select>
                            </div>
                            <div class="mb-3" id="select_item_container">
                                <label class="form-label">Item GID <span class="text-danger">*</span></label>
                                <select name="itm_id" id="itm_id" class="form-control select2" required>
                                    <option value="">-- Select Item --</option>
                                    @foreach ($items as $itm_id => $item_name)
                                        <option value="{{ $itm_id }}">{{ $itm_id }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3" id="manual_item_container" style="display: none;">
                                <label class="form-label">Item Non-GID <span class="text-danger">*</span></label>
                                <input type="text" name="manual_item" id="manual_item" class="form-control" placeholder="Enter item name">
                            </div> --}}
                                    <!-- [MODIFIKASI] Nonaktifkan field input_type dan select_item_container/manual_item_container -->

                                    <div class="mb-3">
                                        <label class="form-label">Item <span class="text-danger">*</span></label>
                                        <!-- [MODIFIKASI] Ubah label ke bahasa Indonesia -->
                                        <input type="text" name="itm_id" id="itm_id" class="form-control"
                                            placeholder="Masukkan nama item" required>
                                        <!-- [MODIFIKASI] Ganti dengan input text -->
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                                        <!-- [MODIFIKASI] Ubah label ke bahasa Indonesia -->
                                        <textarea class="form-control" name="description" id="description" placeholder="Deskripsi" required></textarea>
                                        <!-- [MODIFIKASI] Ubah placeholder ke bahasa Indonesia -->
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Penerima <span class="text-danger">*</span></label>
                                        <!-- [MODIFIKASI] Ubah label ke bahasa Indonesia -->
                                        <textarea class="form-control" name="beneficiary" id="beneficiary" placeholder="Penerima" required></textarea>
                                        <!-- [MODIFIKASI] Ubah placeholder ke bahasa Indonesia -->
                                    </div>
                                </div>
                                <!-- Right Column -->
                                <div class="col-md-6">
                                    <!-- Komentar asli: Quantity -->
                                    {{-- <div class="mb-3">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" name="quantity" id="quantity" class="form-control" required min="1" step="1">
                            </div> --}} <!-- [MODIFIKASI] Nonaktifkan field quantity -->

                                    <!-- Komentar asli: Price -->
                                    <div class="row mb-3">
                                        <!-- Currency -->
                                        <div class="col-md-6">
                                            <label for="cur_id" class="form-label">Mata Uang <span
                                                    class="text-danger">*</span></label>
                                            <!-- [MODIFIKASI] Ubah label ke bahasa Indonesia -->
                                            <select name="cur_id" id="cur_id" class="form-select" required>
                                                <option value="" data-nominal="1" selected>Rp</option>
                                                <!-- [MODIFIKASI] Tambah opsi default Rp -->
                                                @foreach (\App\Models\Currency::orderBy('currency', 'asc')->get() as $currency)
                                                    <option value="{{ $currency->cur_id }}"
                                                        data-nominal="{{ $currency->nominal }}">
                                                        {{ $currency->currency }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small id="currencyNote" class="form-text text-muted"
                                                style="display: none;"></small> <!-- Komentar asli: Currency note -->
                                        </div>
                                        <!-- Price -->
                                        <div class="col-md-6">
                                            <label for="price" class="form-label">Harga <span
                                                    class="text-danger">*</span></label>
                                            <!-- [MODIFIKASI] Ubah label ke bahasa Indonesia -->
                                            <input type="number" name="price" id="price" class="form-control"
                                                required min="0" step="0.01" placeholder="Harga">
                                            <!-- [MODIFIKASI] Tambah placeholder dalam bahasa Indonesia -->
                                        </div>
                                    </div>
                                    <!-- Komentar asli: Workcenter -->
                                    <div class="mb-3">
                                        <label for="amountDisplay" class="form-label">Jumlah</label>
                                        <!-- [MODIFIKASI] Ubah label ke bahasa Indonesia -->
                                        <input type="text" id="amountDisplay" class="form-control" readonly>
                                        <input type="hidden" name="amount" id="amount">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Workcenter</label>
                                        <select name="wct_id" id="wct_id" class="form-control select" required>
                                            <option value="">-- Pilih Workcenter --</option>
                                            <!-- [MODIFIKASI] Ubah placeholder ke bahasa Indonesia -->
                                            @foreach (\App\Models\Workcenter::orderBy('workcenter', 'asc')->get() as $workcenter)
                                                <option value="{{ $workcenter->wct_id }}">
                                                    {{ $workcenter->workcenter }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!-- Komentar asli: Department -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Departemen <span
                                                class="text-danger">*</span></label>
                                        <!-- [MODIFIKASI] Ubah label ke bahasa Indonesia -->
                                        <input type="hidden" name="dpt_id" value="{{ $submission->dpt_id }}">
                                        <input class="form-control"
                                            value="{{ $submission->dept->department ?? '-' }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- Komentar asli: Month -->
                                    <div class="mb-3">
                                        <label for="month" class="form-label">Bulan <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control select" name="month" id="month" required>
                                            <option value="">-- Pilih Bulan --</option>
                                            @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                                <option value="{{ $month }}" @selected(old('month') === $month)>
                                                    {{ $month }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- Komentar asli: Budget (R/NR) -->
                            {{-- <div class="mb-3">
                        <label for="bdc_id" class="form-label">Budget (R/NR)</label>
                        <select name="bdc_id" id="bdc_id" class="form-control select" required>
                            <option value="">-- Select Budget Code --</option>
                            @foreach (\App\Models\BudgetCode::orderBy('budget_name', 'asc')->get() as $budget)
                                <option value="{{ $budget->bdc_id }}">{{ $budget->budget_name }}</option>
                            @endforeach
                        </select>
                    </div> --}} <!-- [MODIFIKASI] Nonaktifkan field budget (R/NR) -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Tutup</button>
                                <!-- [MODIFIKASI] Ubah teks ke bahasa Indonesia -->
                                <button type="submit" class="btn text-white"
                                    style="background-color: #0080ff;">Tambah Item</button>
                                <!-- [MODIFIKASI] Ubah teks ke bahasa Indonesia -->
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal untuk Edit Item -->
        <div id="editRepresentModal" class="modal fade" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white">Edit Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editRepresentForm" method="POST" action="">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="sub_id" id="edit_sub_id">
                            <input type="hidden" name="acc_id" id="edit_acc_id">
                            <input type="hidden" name="amount" id="edit_amount">

                            <div class="row">
                                <!-- Left Column -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Item <span class="text-danger">*</span></label>
                                        <input type="text" name="itm_id" id="edit_itm_id" class="form-control"
                                            placeholder="Masukkan nama item" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="description" id="edit_description" placeholder="Deskripsi" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Penerima <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="beneficiary" id="edit_beneficiary" placeholder="Penerima" required></textarea>
                                    </div>
                                </div>
                                <!-- Right Column -->
                                <div class="col-md-6">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="edit_cur_id" class="form-label">Mata Uang <span
                                                    class="text-danger">*</span></label>
                                            <select name="cur_id" id="edit_cur_id" class="form-select select"
                                                required>
                                                <option value="" data-nominal="1" selected>Rp</option>
                                                @foreach (\App\Models\Currency::orderBy('currency', 'asc')->get() as $currency)
                                                    <option value="{{ $currency->cur_id }}"
                                                        data-nominal="{{ $currency->nominal }}">
                                                        {{ $currency->currency }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small id="edit_currencyNote" class="form-text text-muted"
                                                style="display: none;"></small>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="edit_price" class="form-label">Harga <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" name="price" id="edit_price"
                                                class="form-control" required min="0" step="0.01"
                                                placeholder="Harga">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_amountDisplay" class="form-label">Jumlah</label>
                                        <input type="text" id="edit_amountDisplay" class="form-control" readonly>
                                        <input type="hidden" name="amount" id="edit_amount">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Workcenter</label>
                                        <select name="wct_id" id="edit_wct_id" class="form-control select" required>
                                            <option value="">-- Pilih Workcenter --</option>
                                            @foreach (\App\Models\Workcenter::orderBy('workcenter', 'asc')->get() as $workcenter)
                                                <option value="{{ $workcenter->wct_id }}">
                                                    {{ $workcenter->workcenter }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Departemen <span
                                                class="text-danger">*</span></label>
                                        <input type="hidden" name="dpt_id" id="edit_dpt_id">
                                        <input class="form-control" id="edit_department" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_month" class="form-label">Bulan <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control select" name="month" id="edit_month" required>
                                            <option value="">-- Pilih Bulan --</option>
                                            @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                                <option value="{{ $month }}">{{ $month }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn text-white"
                                    style="background-color: #0080ff;">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
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
                // Inisialisasi Select2 untuk field select (wct_id, month, cur_id)
                $('.select').select2({
                    width: '100%',
                    dropdownParent: $('#addItemModal, #editRepresentModal')
                });

                // Event saat modal Add Item dibuka
                $('#addItemModal').on('shown.bs.modal', function() {
                    // Reset form
                    $('#addItemForm')[0].reset();
                    $('#amountDisplay').val('');
                    $('#cur_id').val('').trigger('change');
                    $('#addItemModal #currencyNote').text('').hide();

                    // Inisialisasi ulang Select2 di dalam modal
                    $('#addItemModal .select').select2({
                        width: '100%',
                        dropdownParent: $('#addItemModal')
                    });
                });

                // Event untuk mengubah itm_id ke uppercase di Add Item Modal
                $('#addItemModal #itm_id').on('input', function() {
                    $(this).val($(this).val().toUpperCase());
                });

                // Perhitungan amount dinamis untuk Add Item Modal
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

                    // Hitung amount
                    const amount = price * currencyNominal;

                    $amountDisplay.val('IDR ' + amount.toLocaleString('id-ID', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                    $amountHidden.val(amount.toFixed(2));
                });

                // Handle pembukaan modal Add Item
                $(document).on('click', '.open-add-item-modal', function(e) {
                    e.preventDefault();
                    const subId = $(this).data('sub-id');
                    const modal = $('#addItemModal');

                    // Set sub_id di form
                    modal.find('#sub_id').val(subId);
                    modal.modal('show');
                });

                // Handle pengiriman form Add Item
                $(document).on('submit', '#addItemForm', function(e) {
                    e.preventDefault();
                    const form = $(this);

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
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Kesalahan!',
                                    text: response.message || 'Gagal menambahkan item.',
                                    confirmButtonColor: '#d33'
                                });
                            }
                        },
                        error: function(xhr) {
                            console.error('AJAX Error:', xhr);
                            let errorMessage = 'Gagal menambahkan item.';
                            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                errorMessage = Object.values(xhr.responseJSON.errors).flat().join(
                                    ' ');
                            } else if (xhr.responseJSON?.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan!',
                                text: errorMessage,
                                confirmButtonColor: '#d33'
                            });
                        }
                    });
                });

                // Handle klik tombol edit
                $(document).on('click', '.open-edit-modal', function(e) {
                    e.preventDefault();
                    const subId = $(this).data('id');
                    const itmId = $(this).data('itm-id');
                    const modal = $('#editRepresentModal');

                    // Reset form dan inisialisasi Select2
                    modal.find('#editRepresentForm')[0].reset();
                    modal.find('#edit_amountDisplay').val('');
                    modal.find('#edit_currencyNote').text('').hide();
                    modal.find('.select').select2({
                        width: '100%',
                        dropdownParent: modal
                    });

                    // Load data via AJAX
                    $.get(`/submissions/${subId}/id/${itmId}/edit`, function(data) {
                        // Isi field dengan data
                        modal.find('#edit_sub_id').val(data.sub_id);
                        modal.find('#edit_acc_id').val(data.acc_id);
                        modal.find('#edit_itm_id').val(data.itm_id).trigger('input');
                        modal.find('#edit_description').val(data.description);
                        modal.find('#edit_beneficiary').val(data.beneficiary);
                        modal.find('#edit_price').val(data.price);
                        modal.find('#edit_cur_id').val(data.cur_id).trigger('change');
                        modal.find('#edit_wct_id').val(data.wct_id).trigger('change');
                        modal.find('#edit_dpt_id').val(data.dpt_id);
                        modal.find('#edit_department').val(data.department || '-');
                        modal.find('#edit_month').val(data.month).trigger('change');

                        // Update action form
                        modal.find('#editRepresentForm').attr('action',
                            `/submissions/${subId}/id/${itmId}`);
                        modal.modal('show');

                        // Trigger perhitungan amount
                        modal.find('#edit_price').trigger('input');
                    }).fail(function(xhr) {
                        console.error('Edit Modal Error:', xhr);
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan!',
                            text: 'Gagal memuat data edit.',
                            confirmButtonColor: '#d33'
                        });
                    });
                });

                // Perhitungan amount dinamis untuk Edit Modal
                $('#editRepresentModal').on('input change', '#edit_price, #edit_cur_id', function() {
                    const $priceInput = $('#editRepresentModal #edit_price');
                    const $currencySelect = $('#editRepresentModal #edit_cur_id');
                    const $amountDisplay = $('#editRepresentModal #edit_amountDisplay');
                    const $amountHidden = $('#editRepresentModal #edit_amount');
                    const $currencyNote = $('#editRepresentModal #edit_currencyNote');

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

                    // Hitung amount
                    const amount = price * currencyNominal;

                    $amountDisplay.val('IDR ' + amount.toLocaleString('id-ID', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                    $amountHidden.val(amount.toFixed(2));
                });

                // Event untuk mengubah edit_itm_id ke uppercase
                $('#editRepresentModal #edit_itm_id').on('input', function() {
                    $(this).val($(this).val().toUpperCase());
                });

                // Handle submit form edit
                $(document).on('submit', '#editRepresentForm', function(e) {
                    e.preventDefault();
                    const form = $(this);

                    $.ajax({
                        url: form.attr('action'),
                        method: form.attr('method'),
                        data: form.serialize(),
                        success: function(response) {
                            if (response.success) {
                                $('#editRepresentModal').modal('hide');
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
                            console.error('Edit Form Error:', xhr);
                            let errorMessage = 'Gagal memperbarui item.';
                            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                errorMessage = Object.values(xhr.responseJSON.errors).flat().join(
                                    ' ');
                            } else if (xhr.responseJSON?.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan!',
                                text: errorMessage,
                                confirmButtonColor: '#d33'
                            });
                        }
                    });
                });

                // Handle modal history approval
                $(document).on('click', '.open-history-modal', function(e) {
                    e.preventDefault();
                    const subId = $(this).data('id');
                    const modal = $('#historyModal');

                    modal.find('.modal-body').html(
                        '<div class="text-center py-4"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>'
                    );
                    modal.modal('show');

                    $.get(`/approvals/history/${subId}`)
                        .done(function(data) {
                            modal.find('.modal-body').html(data);
                        })
                        .fail(function(xhr) {
                            console.error('History Modal Error:', xhr);
                            modal.find('.modal-body').html(
                                '<div class="alert alert-danger">Gagal memuat riwayat persetujuan.</div>'
                            );
                        });
                });

                // Handle pembukaan modal Add Remark
                $(document).on('click', '.open-add-remark-modal', function(e) {
                    e.preventDefault();
                    const subId = $(this).data('id');
                    const modal = $('#addRemarkModal');

                    modal.find('#sub_id').val(subId);
                    modal.modal('show');
                });

                // Handle pengiriman form Add Remark
                $(document).on('submit', '#addRemarkForm', function(e) {
                    e.preventDefault();
                    const form = $(this);

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
                                    text: 'Catatan berhasil ditambahkan.',
                                    confirmButtonColor: '#3085d6'
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function(xhr) {
                            console.error('Add Remark Error:', xhr);
                            let errorMessage = 'Gagal menambahkan catatan.';
                            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                errorMessage = Object.values(xhr.responseJSON.errors).flat().join(
                                    ' ');
                            } else if (xhr.responseJSON?.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            form.find('.modal-body').prepend(
                                `<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            ${errorMessage}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>`
                            );
                        }
                    });
                });

                // Handle modal riwayat catatan
                $(document).on('click', '.open-historyremark-modal', function(e) {
                    e.preventDefault();
                    const subId = $(this).data('id');
                    const modal = $('#historyremarkModal');

                    modal.find('.modal-body').html(
                        '<div class="text-center py-4"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>'
                    );
                    modal.modal('show');

                    $.get(`/remarks/remark/${subId}`)
                        .done(function(data) {
                            modal.find('.modal-body').html(data);
                        })
                        .fail(function(xhr) {
                            console.error('History Remark Error:', xhr);
                            modal.find('.modal-body').html(
                                '<div class="alert alert-danger">Gagal memuat riwayat catatan.</div>'
                            );
                        });
                });

                // Handle tombol hapus
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
                        text: 'Anda tidak akan dapat mengembalikan ini!',
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
                                        Swal.fire({
                                            title: 'Terhapus!',
                                            text: response.message,
                                            icon: 'success',
                                            confirmButtonColor: '#3085d6'
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            title: 'Kesalahan!',
                                            text: response.message,
                                            icon: 'error',
                                            confirmButtonColor: '#d33'
                                        });
                                    }
                                },
                                error: function(xhr) {
                                    console.error('Delete Error:', xhr);
                                    let errorMessage = 'Terjadi kesalahan.';
                                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                        errorMessage = Object.values(xhr.responseJSON
                                            .errors).flat().join(' ');
                                    } else if (xhr.responseJSON?.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }
                                    Swal.fire({
                                        title: 'Kesalahan!',
                                        text: errorMessage,
                                        icon: 'error',
                                        confirmButtonColor: '#d33'
                                    });
                                }
                            });
                        }
                    });
                });

                // Handle pengiriman form
                $(document).on('submit', '.send-form', function(e) {
                    e.preventDefault();
                    const form = $(this);

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
                                            title: 'Kesalahan!',
                                            text: 'Gagal mengirim pengajuan.',
                                            confirmButtonColor: '#d33'
                                        });
                                    }
                                },
                                error: function(xhr) {
                                    console.error('Send Error:', xhr);
                                    let errorMessage = 'Terjadi kesalahan.';
                                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                        errorMessage = Object.values(xhr.responseJSON
                                            .errors).flat().join(' ');
                                    } else if (xhr.responseJSON?.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Kesalahan!',
                                        text: errorMessage,
                                        confirmButtonColor: '#d33'
                                    });
                                }
                            });
                        }
                    });
                });

                // Handle approve form
                $(document).on('submit', '.approve-form', function(e) {
                    e.preventDefault();
                    const form = $(this);

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
                                            title: 'Kesalahan!',
                                            text: 'Gagal menyetujui pengajuan.',
                                            confirmButtonColor: '#d33'
                                        });
                                    }
                                },
                                error: function(xhr) {
                                    console.error('Approve Error:', xhr);
                                    let errorMessage = 'Terjadi kesalahan.';
                                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                        errorMessage = Object.values(xhr.responseJSON
                                            .errors).flat().join(' ');
                                    } else if (xhr.responseJSON?.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Kesalahan!',
                                        text: errorMessage,
                                        confirmButtonColor: '#d33'
                                    });
                                }
                            });
                        }
                    });
                });

                // Handle disapprove form
                $(document).on('submit', '.disapprove-form', function(e) {
                    e.preventDefault();
                    const form = $(this);

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
                                            title: 'Kesalahan!',
                                            text: 'Gagal menolak pengajuan.',
                                            confirmButtonColor: '#d33'
                                        });
                                    }
                                },
                                error: function(xhr) {
                                    console.error('Disapprove Error:', xhr);
                                    let errorMessage = 'Terjadi kesalahan.';
                                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                        errorMessage = Object.values(xhr.responseJSON
                                            .errors).flat().join(' ');
                                    } else if (xhr.responseJSON?.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Kesalahan!',
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
