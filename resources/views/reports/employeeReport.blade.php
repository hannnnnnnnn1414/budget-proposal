<!DOCTYPE html>
<html lang="en">

<x-head></x-head>

<body class="g-sidenav-show bg-gray-100">
    <x-sidebar></x-sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
        <x-navbar :notifications="$notifications">
            Submissions</x-navbar>
        <div class="container-fluid">
            <div class="row my-4">
                <div class="col-12">
                    <div class="card">
                        @if (session('sect') === 'Kadept' && session('dept') !== '6121')
                            <div class="card-header bg-danger">
                                <h4 style="font-weight: bold;" class="text-white"><i
                                        class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>PROPOSAL DETAIL
                                    {{ $account_name }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Approval Status -->
                                    <div class="col-md-6">
                                        <div class="card-header bg-secondary text-white py-2 px-2">
                                            <h6 class="mb-0 text-white">Approval Status</h6>
                                            <!-- [MODIFIKASI] Changed header tag from h5 to h6 to match Code 1 -->
                                        </div>
                                        <div class="bg-green-100 p-4 rounded shadow mb-4">
                                            @if ($submissions->isNotEmpty())
                                                @php $submission = $submissions->first(); @endphp
                                                <p>Status: <span class="font-bold">
                                                        @if ($submission->status == 1)
                                                            <span class="badge bg-warning">DRAFT</span>
                                                        @elseif ($submission->status == 2)
                                                            <span class="badge bg-secondary">UNDER REVIEW KADEP</span>
                                                        @elseif ($submission->status == 3)
                                                            <span class="badge"
                                                                style="background-color: #0080ff">APPROVED BY
                                                                KADEPT</span>
                                                        @elseif ($submission->status == 4)
                                                            <span class="badge"
                                                                style="background-color: #0080ff">APPROVED BY
                                                                KADIV</span>
                                                        @elseif ($submission->status == 5)
                                                            <span class="badge"
                                                                style="background-color: #0080ff">APPROVED BY DIC</span>
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
                                                        @elseif ($submission->status == 9)
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
                                                <p>Date: {{ $submission->updated_at->format('d-m-Y') ?? '03-03-2025' }}
                                                </p>
                                                <!-- [MODIFIKASI] Changed date format from 'd-m-Y H:i' to 'd-m-Y' to match Code 1 -->
                                                <div class="mt-4 flex space-x-2">
                                                    <button type="button" class="btn btn-danger open-history-modal"
                                                        data-id="{{ $submission->sub_id }}">History Approval</button>
                                                </div>
                                            @else
                                                <p>No submission data available</p>
                                                <!-- [MODIFIKASI] Changed default message from "<strong>Remark: -</strong><p><strong>Date: -</strong></p>" to match Code 1 -->
                                            @endif
                                        </div>
                                    </div>
                                    <!-- Remark -->
                                    <div class="col-md-6">
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
                                                    ->with('user')
                                                    ->get();
                                            @endphp
                                            @if ($remarks->isNotEmpty())
                                                @php $remark = $remarks->first(); @endphp
                                                @foreach ($remarks as $remark)
                                                    <div class="mb-3">
                                                        <p><strong>Remark:</strong> <span
                                                                class="font-bold">{{ $remark->remark }}</span></p>
                                                        {{-- <p><strong>By:</strong> {{ $remark->user ? $remark->user->name : 'Unknown User' }} (NPK: {{ $remark->remark_by }})</p> --}}
                                                        <!-- [MODIFIKASI] Retained commented-out code from repairReport.blade.php -->
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
                                <!-- Item of Purchase -->
                                <div class="card-header bg-secondary text-white py-2 px-2">
                                    <h6 class="mb-0 text-white">Item of Purchase</h6>
                                    <!-- [MODIFIKASI] Changed header tag from h5 to h6 to match Code 1 -->
                                </div>
                                <div class="bg-white p-4 rounded shadow mb-4">
                                    @php
                                        $hasAction = $submissions->contains(function ($submission) {
                                            return $submission->status == 1;
                                        });
                                    @endphp
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="bg-gray-200">
                                                <tr>
                                                    <th class="text-left border p-2">Ledger Account</th>
                                                    <th class="text-left border p-2">Ledger Account Description</th>
                                                    <th class="text-left border p-2">Price</th>
                                                    <th class="text-left border p-2">Amount</th>
                                                    <th class="text-left border p-2">Workcenter</th>
                                                    <th class="text-left border p-2">Department</th>
                                                    <th class="text-left border p-2">Month</th>
                                                    <th class="text-left border p-2">R/NR</th>
                                                    <th class="text-left border p-2">Line Of Business</th>
                                                    <!-- [MODIFIKASI] Updated table headers to match Code 1 -->
                                                    @if ($hasAction)
                                                        <th class="text-left border p-2">Action</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($submissions as $submission)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="border p-2">{{ $submission->ledger_account ?? '-' }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Changed from item to ledger_account and added default '-' to match Code 1 -->
                                                        <td class="border p-2">
                                                            {{ $submission->ledger_account_description ?? '-' }}</td>
                                                        <!-- [MODIFIKASI] Changed from description to ledger_account_description and added default '-' to match Code 1 -->
                                                        <td class="border p-2">
                                                            {{ $submission->price ? 'IDR ' . number_format($submission->price, 2) : '-' }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Added currency format and default '-' to match Code 1 -->
                                                        <td class="border p-2">
                                                            {{ $submission->amount ? 'IDR ' . number_format($submission->amount, 2) : '-' }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Added amount column to match Code 1 -->
                                                        <td class="border p-2">
                                                            {{ $submission->workcenter != null ? $submission->workcenter->workcenter : '-' }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Added default '-' to match Code 1 -->
                                                        <td class="border p-2">
                                                            {{ $submission->dept != null ? $submission->dept->department : '-' }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Added default '-' to match Code 1 -->
                                                        <td class="border p-2">{{ $submission->month }}</td>
                                                        <td class="border p-2">
                                                            {{ $submission->budget != null ? $submission->budget->budget_name : '-' }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Added default '-' to match Code 1 -->
                                                        <td class="border p-2">
                                                            {{ $line_businesses[$submission->lob_id] ?? ($submission->lob_id ?? '-') }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Added Line Of Business column to match Code 1 -->
                                                        @if ($hasAction)
                                                            <td class="border p-2">
                                                                @if ($submission->status == 1)
                                                                    <!-- [MODIFIKASI] Changed status check from 2 to 1 to match Code 1 -->
                                                                    <a href="#"
                                                                        data-id="{{ $submission->sub_id }}"
                                                                        data-item-id="{{ $submission->id }}"
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
                                                        <td colspan="10" class="border p-2 text-center">No Submissions
                                                            found!</td>
                                                        <!-- [MODIFIKASI] Changed colspan from 7 to 10 to match updated table columns -->
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <br>
                                    <!-- Tombol Add Item -->
                                    @if ($hasAction)
                                        <button type="button" id="addItemBtn" class="btn text-white"
                                            style="background-color: #0080ff">
                                            <i class="fas fa-plus me-2"></i>Add Item
                                        </button>
                                        <!-- [MODIFIKASI] Added Add Item button to match Code 1 -->
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between mt-4">
                                    <button onclick="history.back()" type="button"
                                        class="btn btn-secondary me-2">Back</button>
                                    <!-- [MODIFIKASI] Removed icon from Back button to match Code 1 -->
                                    <div class="d-flex">
                                        <!-- [MODIFIKASI] Changed gap-3 to d-flex to match Code 1 -->
                                        @if ($submission->status == 1)
                                            <!-- [MODIFIKASI] Changed status check from 2 to 1 to match Code 1 -->
                                            <form action="{{ route('submissions.submit', $submission->sub_id) }}"
                                                method="POST">
                                                @csrf
                                                <button type="submit" class="btn text-white"
                                                    style="background-color: #0080ff;">
                                                    <i class="fas fa-paper-plane mr-2"></i> SEND
                                                    <!-- [MODIFIKASI] Changed button text and icon to match Code 1 -->
                                                </button>
                                            </form>
                                        @elseif ($submission->status == 2)
                                            <!-- [MODIFIKASI] Updated approve/disapprove forms to match repairReport.blade.php -->
                                            <form action="{{ route('submissions.submit', $submission->sub_id) }}"
                                                method="POST" class="me-2">
                                                @csrf
                                                <button type="submit" class="btn text-white"
                                                    style="background-color: #0080ff;">
                                                    <i class="fa-solid fa-check me-2"></i> Approved
                                                </button>
                                            </form>
                                            <form action="{{ route('submissions.disapprove', $submission->sub_id) }}"
                                                method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-secondary">
                                                    <i class="fa-solid fa-xmark me-2"></i> DISAPPROVED
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
                                    <!-- Approval Status -->
                                    <div class="col-md-6">
                                        <div class="card-header bg-secondary text-white py-2 px-2">
                                            <h6 class="mb-0 text-white">Approval Status</h6>
                                        </div>
                                        <div class="bg-green-100 p-4 rounded shadow mb-4">
                                            @if ($submissions->isNotEmpty())
                                                @php $submission = $submissions->first(); @endphp
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
                                                            <span class="badge bg-danger">DISAPPROVED BY KADEP</span>
                                                        @elseif ($submission->status == 9)
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
                                                <p>Date: {{ $submission->updated_at->format('d-m-Y') ?? '03-03-2025' }}
                                                </p>
                                                <div class="mt-4 flex space-x-2">
                                                    <button type="button" class="btn btn-danger open-history-modal"
                                                        data-id="{{ $submission->sub_id }}">History Approval</button>
                                                </div>
                                            @else
                                                <p>No submission data available</p>
                                                <!-- [MODIFIKASI] Changed default message from "<strong>Remark: -</strong><p><strong>Date: -</strong></p>" to match Kadept code -->
                                            @endif
                                        </div>
                                    </div>
                                    <!-- Remark -->
                                    <div class="col-md-6">
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
                                                    ->with('user')
                                                    ->get();
                                            @endphp
                                            @if ($remarks->isNotEmpty())
                                                @php $remark = $remarks->first(); @endphp
                                                @foreach ($remarks as $remark)
                                                    <div class="mb-3">
                                                        <p><strong>Remark:</strong> <span
                                                                class="font-bold">{{ $remark->remark }}</span></p>
                                                        {{-- <p><strong>By:</strong> {{ $remark->user ? $remark->user->name : 'Unknown User' }} (NPK: {{ $remark->remark_by }})</p> --}}
                                                        <!-- [MODIFIKASI] Retained commented-out code from repairReport.blade.php -->
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
                                    <!-- [MODIFIKASI] Changed header tag from h5 to h6 to match Kadept code -->
                                </div>
                                <!-- Item Table -->
                                <div class="bg-white p-4 rounded shadow mb-4">
                                    @php
                                        $hasAction = $submissions->contains(function ($submission) {
                                            return $submission->status == 3;
                                        });
                                    @endphp
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="bg-gray-200">
                                                <tr>
                                                    <th class="text-left border p-2">Ledger Account</th>
                                                    <!-- [MODIFIKASI] Ganti Item dengan Ledger Account -->
                                                    <th class="text-left border p-2">Ledger Account Description</th>
                                                    <!-- [MODIFIKASI] Ganti Description dengan Ledger Account Description -->
                                                    <th class="text-left border p-2">Price</th>
                                                    <th class="text-left border p-2">Amount</th>
                                                    <!-- [MODIFIKASI] Tambah kolom Amount -->
                                                    <th class="text-left border p-2">Workcenter</th>
                                                    <th class="text-left border p-2">Department</th>
                                                    <th class="text-left border p-2">Month</th>
                                                    <th class="text-left border p-2">R/NR</th>
                                                    <th class="text-left border p-2">Line Of Business</th>
                                                    <!-- [MODIFIKASI] Tambah kolom Line Of Business -->
                                                    @if ($hasAction)
                                                        <th class="text-left border p-2">Action</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($submissions as $submission)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="border p-2">
                                                            {{ $submission->ledger_account ?? '-' }}</td>
                                                        <!-- [MODIFIKASI] Ganti item dengan ledger_account dan tambah default '-' untuk konsistensi dengan Kadept -->
                                                        <td class="border p-2">
                                                            {{ $submission->ledger_account_description ?? '-' }}</td>
                                                        <!-- [MODIFIKASI] Ganti description dengan ledger_account_description dan tambah default '-' untuk konsistensi dengan Kadept -->
                                                        <td class="border p-2">
                                                            {{ $submission->price ? 'IDR ' . number_format($submission->price, 2) : '-' }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Tambah format mata uang dan default '-' untuk konsistensi dengan Kadept -->
                                                        <td class="border p-2">
                                                            {{ $submission->amount ? 'IDR ' . number_format($submission->amount, 2) : '-' }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Tambah kolom Amount dengan format mata uang dan default '-' untuk konsistensi dengan Kadept -->
                                                        <td class="border p-2">
                                                            {{ $submission->workcenter != null ? $submission->workcenter->workcenter : '-' }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Tambah default '-' untuk konsistensi dengan Kadept -->
                                                        <td class="border p-2">
                                                            {{ $submission->dept != null ? $submission->dept->department : '-' }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Tambah default '-' untuk konsistensi dengan Kadept -->
                                                        <td class="border p-2">{{ $submission->month }}</td>
                                                        <td class="border p-2">
                                                            {{ $submission->budget != null ? $submission->budget->budget_name : '-' }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Tambah default '-' untuk konsistensi dengan Kadept -->
                                                        <td class="border p-2">
                                                            {{ $line_businesses[$submission->lob_id] ?? ($submission->lob_id ?? '-') }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Tambah kolom Line Of Business dengan default '-' untuk konsistensi dengan Kadept -->
                                                        @if ($hasAction)
                                                            <td class="border p-2">
                                                                @if ($submission->status == 3)
                                                                    <a href="#"
                                                                        data-id="{{ $submission->sub_id }}"
                                                                        class="inline-flex items-center justify-center p-2 text-red-600 hover:text-blue-800 open-edit-modal"
                                                                        title="Update">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    <!-- [MODIFIKASI] Perbaikan form penghapusan untuk menggunakan id alih-alih itm_id agar sesuai dengan Kadept -->
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
                                                        <td colspan="10" class="border p-2 text-center">No
                                                            Submissions found!</td>
                                                        <!-- [MODIFIKASI] Sesuaikan colspan karena tambahan kolom Amount dan Line Of Business -->
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <br>
                                    <!-- Tombol Add Item -->
                                    @if ($hasAction)
                                        <button type="button" id="addItemBtn" class="btn text-white"
                                            style="background-color: #0080ff">
                                            <i class="fas fa-plus me-2"></i>Add Item
                                        </button>
                                        <!-- [MODIFIKASI] Tambah tombol Add Item untuk konsistensi dengan Kadept -->
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between mt-4">
                                    <button onclick="history.back()" type="button"
                                        class="btn btn-secondary me-2">Back</button>
                                    <!-- [MODIFIKASI] Removed icon from Back button to match Kadept -->
                                    <div class="d-flex">
                                        <!-- [MODIFIKASI] Changed gap-3 to d-flex to match Kadept -->
                                        @if ($submission->status == 3)
                                            <form action="{{ route('submissions.submit', $submission->sub_id) }}"
                                                method="POST" class="me-2">
                                                @csrf
                                                <button type="submit" class="btn text-white"
                                                    style="background-color: #0080ff;">
                                                    <i class="fa-solid fa-check me-2"></i> Approved
                                                </button>
                                            </form>
                                            <form action="{{ route('submissions.disapprove', $submission->sub_id) }}"
                                                method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-secondary">
                                                    <i class="fa-solid fa-xmark me-2"></i> DISAPPROVED
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
                                    <!-- Approval Status -->
                                    <div class="col-md-6">
                                        <div class="card-header bg-secondary text-white py-2 px-2">
                                            <h6 class="mb-0 text-white">Approval Status</h6>
                                        </div>
                                        <div class="bg-green-100 p-4 rounded shadow mb-4">
                                            @if ($submissions->isNotEmpty())
                                                @php $submission = $submissions->first(); @endphp
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
                                                            <span class="badge bg-danger">DISAPPROVED BY KADEP</span>
                                                        @elseif ($submission->status == 9)
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
                                                <p>Date: {{ $submission->updated_at->format('d-m-Y') ?? '03-03-2025' }}
                                                </p>
                                                <div class="mt-4 flex space-x-2">
                                                    <button type="button" class="btn btn-danger open-history-modal"
                                                        data-id="{{ $submission->sub_id }}">History Approval</button>
                                                </div>
                                            @else
                                                <p>No submission data available</p>
                                                <!-- [MODIFIKASI] Changed default message from "<strong>Remark: -</strong><p><strong>Date: -</strong></p>" to match Kadept code -->
                                            @endif
                                        </div>
                                    </div>
                                    <!-- Remark -->
                                    <div class="col-md-6">
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
                                                    ->with('user')
                                                    ->get();
                                            @endphp
                                            @if ($remarks->isNotEmpty())
                                                @php $remark = $remarks->first(); @endphp
                                                @foreach ($remarks as $remark)
                                                    <div class="mb-3">
                                                        <p><strong>Remark:</strong> <span
                                                                class="font-bold">{{ $remark->remark }}</span></p>
                                                        {{-- <p><strong>By:</strong> {{ $remark->user ? $remark->user->name : 'Unknown User' }} (NPK: {{ $remark->remark_by }})</p> --}}
                                                        <!-- [MODIFIKASI] Retained commented-out code from repairReport.blade.php -->
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
                                    <!-- [MODIFIKASI] Changed header tag from h5 to h6 to match Kadept code -->
                                </div>
                                <!-- Item Table -->
                                <div class="bg-white p-4 rounded shadow mb-4">
                                    @php
                                        $hasAction = $submissions->contains(function ($submission) {
                                            return $submission->status == 4;
                                        });
                                    @endphp
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="bg-gray-200">
                                                <tr>
                                                    <th class="text-left border p-2">Ledger Account</th>
                                                    <!-- [MODIFIKASI] Ganti Item dengan Ledger Account -->
                                                    <th class="text-left border p-2">Ledger Account Description</th>
                                                    <!-- [MODIFIKASI] Ganti Description dengan Ledger Account Description -->
                                                    <th class="text-left border p-2">Price</th>
                                                    <th class="text-left border p-2">Amount</th>
                                                    <!-- [MODIFIKASI] Tambah kolom Amount -->
                                                    <th class="text-left border p-2">Workcenter</th>
                                                    <th class="text-left border p-2">Department</th>
                                                    <th class="text-left border p-2">Month</th>
                                                    <th class="text-left border p-2">R/NR</th>
                                                    <th class="text-left border p-2">Line Of Business</th>
                                                    <!-- [MODIFIKASI] Tambah kolom Line Of Business -->
                                                    @if ($hasAction)
                                                        <th class="text-left border p-2">Action</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($submissions as $submission)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="border p-2">
                                                            {{ $submission->ledger_account ?? '-' }}</td>
                                                        <!-- [MODIFIKASI] Ganti item dengan ledger_account dan tambah default '-' untuk konsistensi dengan Kadept -->
                                                        <td class="border p-2">
                                                            {{ $submission->ledger_account_description ?? '-' }}</td>
                                                        <!-- [MODIFIKASI] Ganti description dengan ledger_account_description dan tambah default '-' untuk konsistensi dengan Kadept -->
                                                        <td class="border p-2">
                                                            {{ $submission->price ? 'IDR ' . number_format($submission->price, 2) : '-' }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Tambah format mata uang dan default '-' untuk konsistensi dengan Kadept -->
                                                        <td class="border p-2">
                                                            {{ $submission->amount ? 'IDR ' . number_format($submission->amount, 2) : '-' }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Tambah kolom Amount dengan format mata uang dan default '-' untuk konsistensi dengan Kadept -->
                                                        <td class="border p-2">
                                                            {{ $submission->workcenter != null ? $submission->workcenter->workcenter : '-' }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Tambah default '-' untuk konsistensi dengan Kadept -->
                                                        <td class="border p-2">
                                                            {{ $submission->dept != null ? $submission->dept->department : '-' }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Tambah default '-' untuk konsistensi dengan Kadept -->
                                                        <td class="border p-2">{{ $submission->month }}</td>
                                                        <td class="border p-2">
                                                            {{ $submission->budget != null ? $submission->budget->budget_name : '-' }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Tambah default '-' untuk konsistensi dengan Kadept -->
                                                        <td class="border p-2">
                                                            {{ $line_businesses[$submission->lob_id] ?? ($submission->lob_id ?? '-') }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Tambah kolom Line Of Business dengan default '-' untuk konsistensi dengan Kadept -->
                                                        @if ($hasAction)
                                                            <td class="border p-2">
                                                                @if ($submission->status == 4)
                                                                    <a href="#"
                                                                        data-id="{{ $submission->sub_id }}"
                                                                        class="inline-flex items-center justify-center p-2 text-red-600 hover:text-blue-800 open-edit-modal"
                                                                        title="Update">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    <!-- [MODIFIKASI] Perbaikan form penghapusan untuk menggunakan id alih-alih itm_id agar sesuai dengan Kadept -->
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
                                                        <td colspan="10" class="border p-2 text-center">No
                                                            Submissions found!</td>
                                                        <!-- [MODIFIKASI] Sesuaikan colspan karena tambahan kolom Amount dan Line Of Business -->
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <br>
                                    <!-- Tombol Add Item -->
                                    @if ($hasAction)
                                        <button type="button" id="addItemBtn" class="btn text-white"
                                            style="background-color: #0080ff">
                                            <i class="fas fa-plus me-2"></i>Add Item
                                        </button>
                                        <!-- [MODIFIKASI] Tambah tombol Add Item untuk konsistensi dengan Kadept -->
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between mt-4">
                                    <button onclick="history.back()" type="button"
                                        class="btn btn-secondary me-2">Back</button>
                                    <!-- [MODIFIKASI] Removed icon from Back button to match Kadept -->
                                    <div class="d-flex">
                                        <!-- [MODIFIKASI] Changed gap-3 to d-flex to match Kadept -->
                                        @if ($submission->status == 4)
                                            <form action="{{ route('submissions.submit', $submission->sub_id) }}"
                                                method="POST" class="me-2">
                                                @csrf
                                                <button type="submit" class="btn text-white"
                                                    style="background-color: #0080ff;">
                                                    <i class="fa-solid fa-check me-2"></i> Approved
                                                </button>
                                            </form>
                                            <form action="{{ route('submissions.disapprove', $submission->sub_id) }}"
                                                method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-secondary">
                                                    <i class="fa-solid fa-xmark me-2"></i> DISAPPROVED
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
                                    <!-- Approval Status -->
                                    <div class="col-md-6">
                                        <div class="card-header bg-secondary text-white py-2 px-2">
                                            <h6 class="mb-0 text-white">Approval Status</h6>
                                        </div>
                                        <div class="bg-green-100 p-4 rounded shadow mb-4">
                                            @if ($submissions->isNotEmpty())
                                                @php $submission = $submissions->first(); @endphp
                                                <p>Status: <span class="font-bold">
                                                        @if ($submission->status == 5)
                                                            <span class="badge"
                                                                style="background-color: #0080ff">REQUIRES
                                                                APPROVAL</span>
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
                                                        @elseif ($submission->status == 9)
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
                                                <p>Date: {{ $submission->updated_at->format('d-m-Y') ?? '03-03-2025' }}
                                                </p>
                                                <!-- [MODIFIKASI] Changed date format from 'd-m-Y H:i' to 'd-m-Y' to match Kadept -->
                                                <div class="mt-4 flex space-x-2">
                                                    <button type="button" class="btn btn-danger open-history-modal"
                                                        data-id="{{ $submission->sub_id }}">History Approval</button>
                                                </div>
                                            @else
                                                <p>No submission data available</p>
                                                <!-- [MODIFIKASI] Changed default message from "<strong>Remark: -</strong><p><strong>Date: -</strong></p>" to match Kadept code -->
                                            @endif
                                        </div>
                                    </div>
                                    <!-- Remark -->
                                    <div class="col-md-6">
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
                                                    ->with('user')
                                                    ->get();
                                            @endphp
                                            @if ($remarks->isNotEmpty())
                                                @php $remark = $remarks->first(); @endphp
                                                @foreach ($remarks as $remark)
                                                    <div class="mb-3">
                                                        <p><strong>Remark:</strong> <span
                                                                class="font-bold">{{ $remark->remark }}</span></p>
                                                        {{-- <p><strong>By:</strong> {{ $remark->user ? $remark->user->name : 'Unknown User' }} (NPK: {{ $remark->remark_by }})</p> --}}
                                                        <!-- [MODIFIKASI] Retained commented-out code from repairReport.blade.php -->
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
                                    <!-- [MODIFIKASI] Changed header tag from h5 to h6 to match Kadept code -->
                                </div>
                                <!-- Item Table -->
                                <div class="bg-white p-4 rounded shadow mb-4">
                                    @php
                                        $hasAction = $submissions->contains(function ($submission) {
                                            return $submission->status == 5;
                                        });
                                    @endphp
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="bg-gray-200">
                                                <tr>
                                                    <th class="text-left border p-2">Ledger Account</th>
                                                    <!-- [MODIFIKASI] Ganti Item dengan Ledger Account -->
                                                    <th class="text-left border p-2">Ledger Account Description</th>
                                                    <!-- [MODIFIKASI] Ganti Description dengan Ledger Account Description -->
                                                    <th class="text-left border p-2">Price</th>
                                                    <th class="text-left border p-2">Amount</th>
                                                    <!-- [MODIFIKASI] Tambah kolom Amount -->
                                                    <th class="text-left border p-2">Workcenter</th>
                                                    <th class="text-left border p-2">Department</th>
                                                    <th class="text-left border p-2">Month</th>
                                                    <th class="text-left border p-2">R/NR</th>
                                                    <th class="text-left border p-2">Line Of Business</th>
                                                    <!-- [MODIFIKASI] Tambah kolom Line Of Business -->
                                                    @if ($hasAction)
                                                        <th class="text-left border p-2">Action</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($submissions as $submission)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="border p-2">
                                                            {{ $submission->ledger_account ?? '-' }}</td>
                                                        <!-- [MODIFIKASI] Ganti item dengan ledger_account dan tambah default '-' untuk konsistensi dengan Kadept -->
                                                        <td class="border p-2">
                                                            {{ $submission->ledger_account_description ?? '-' }}</td>
                                                        <!-- [MODIFIKASI] Ganti description dengan ledger_account_description dan tambah default '-' untuk konsistensi dengan Kadept -->
                                                        <td class="border p-2">
                                                            {{ $submission->price ? 'IDR ' . number_format($submission->price, 2) : '-' }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Tambah format mata uang dan default '-' untuk konsistensi dengan Kadept -->
                                                        <td class="border p-2">
                                                            {{ $submission->amount ? 'IDR ' . number_format($submission->amount, 2) : '-' }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Tambah kolom Amount dengan format mata uang dan default '-' untuk konsistensi dengan Kadept -->
                                                        <td class="border p-2">
                                                            {{ $submission->workcenter != null ? $submission->workcenter->workcenter : '-' }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Tambah default '-' untuk konsistensi dengan Kadept -->
                                                        <td class="border p-2">
                                                            {{ $submission->dept != null ? $submission->dept->department : '-' }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Tambah default '-' untuk konsistensi dengan Kadept -->
                                                        <td class="border p-2">{{ $submission->month }}</td>
                                                        <td class="border p-2">
                                                            {{ $submission->budget != null ? $submission->budget->budget_name : '-' }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Tambah default '-' untuk konsistensi dengan Kadept -->
                                                        <td class="border p-2">
                                                            {{ $line_businesses[$submission->lob_id] ?? ($submission->lob_id ?? '-') }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Tambah kolom Line Of Business dengan default '-' untuk konsistensi dengan Kadept -->
                                                        @if ($hasAction)
                                                            <td class="border p-2">
                                                                @if ($submission->status == 5)
                                                                    <a href="#"
                                                                        data-id="{{ $submission->sub_id }}"
                                                                        class="inline-flex items-center justify-center p-2 text-red-600 hover:text-blue-800 open-edit-modal"
                                                                        title="Update">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    <!-- [MODIFIKASI] Perbaikan form penghapusan untuk menggunakan id alih-alih itm_id agar sesuai dengan Kadept -->
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
                                                        <td colspan="10" class="border p-2 text-center">No
                                                            Submissions found!</td>
                                                        <!-- [MODIFIKASI] Sesuaikan colspan karena tambahan kolom Amount dan Line Of Business -->
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <br>
                                    <!-- Tombol Add Item -->
                                    @if ($hasAction)
                                        <button type="button" id="addItemBtn" class="btn text-white"
                                            style="background-color: #0080ff">
                                            <i class="fas fa-plus me-2"></i>Add Item
                                        </button>
                                        <!-- [MODIFIKASI] Tambah tombol Add Item untuk konsistensi dengan Kadept -->
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between mt-4">
                                    <button onclick="history.back()" type="button"
                                        class="btn btn-secondary me-2">Back</button>
                                    <!-- [MODIFIKASI] Removed icon from Back button to match Kadept -->
                                    <div class="d-flex">
                                        <!-- [MODIFIKASI] Changed gap-3 to d-flex to match Kadept -->
                                        @if ($submission->status == 5)
                                            <form action="{{ route('submissions.submit', $submission->sub_id) }}"
                                                method="POST" class="me-2">
                                                @csrf
                                                <button type="submit" class="btn text-white"
                                                    style="background-color: #0080ff;">
                                                    <i class="fa-solid fa-check me-2"></i> Approved
                                                </button>
                                            </form>
                                            <form action="{{ route('submissions.disapprove', $submission->sub_id) }}"
                                                method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-secondary">
                                                    <i class="fa-solid fa-xmark me-2"></i> DISAPPROVED
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @elseif (session('sect') === 'Kadept' && session('dept') === '6121')
                            <div class="card-header bg-danger">
                                <h4 style="font-weight: bold;" class="text-white"><i
                                        class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>PROPOSAL DETAIL
                                    {{ $account_name }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Approval Status -->
                                    <div class="col-md-6">
                                        <div class="card-header bg-secondary text-white py-2 px-2">
                                            <h6 class="mb-0 text-white">Approval Status</h6>
                                        </div>
                                        <div class="bg-green-100 p-4 rounded shadow mb-4">
                                            @if ($submissions->isNotEmpty())
                                                @php $submission = $submissions->first(); @endphp
                                                <p>Status: <span class="font-bold">
                                                        @if ($submission->status == 6)
                                                            <span class="badge"
                                                                style="background-color: #0080ff">APPROVED BY PIC
                                                                BUDGETING</span>
                                                        @elseif ($submission->status == 7)
                                                            <span class="badge"
                                                                style="background-color: #0080ff">APPROVED BY KADEP
                                                                BUDGETING</span>
                                                        @elseif ($submission->status == 8)
                                                            <span class="badge bg-danger">DISAPPROVED BY KADEP</span>
                                                        @elseif ($submission->status == 9)
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
                                                <p>Date: {{ $submission->updated_at->format('d-m-Y') ?? '03-03-2025' }}
                                                </p>
                                                <!-- [MODIFIKASI] Changed date format from 'd-m-Y H:i' to 'd-m-Y' to match Kadept -->
                                                <div class="mt-4 flex space-x-2">
                                                    <button type="button" class="btn btn-danger open-history-modal"
                                                        data-id="{{ $submission->sub_id }}">History Approval</button>
                                                </div>
                                            @else
                                                <p>No submission data available</p>
                                                <!-- [MODIFIKASI] Changed default message from "<strong>Remark: -</strong><p><strong>Date: -</strong></p>" to match Kadept code -->
                                            @endif
                                        </div>
                                    </div>
                                    <!-- Remark -->
                                    <div class="col-md-6">
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
                                                    ->with('user')
                                                    ->get();
                                            @endphp
                                            @if ($remarks->isNotEmpty())
                                                @php $remark = $remarks->first(); @endphp
                                                @foreach ($remarks as $remark)
                                                    <div class="mb-3">
                                                        <p><strong>Remark:</strong> <span
                                                                class="font-bold">{{ $remark->remark }}</span></p>
                                                        {{-- <p><strong>By:</strong> {{ $remark->user ? $remark->user->name : 'Unknown User' }} (NPK: {{ $remark->remark_by }})</p> --}}
                                                        <!-- [MODIFIKASI] Retained commented-out code from repairReport.blade.php -->
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
                                    <!-- [MODIFIKASI] Changed header tag from h5 to h6 to match Kadept code -->
                                </div>
                                <!-- Item Table -->
                                <div class="bg-white p-4 rounded shadow mb-4">
                                    @php
                                        $hasAction = $submissions->contains(function ($submission) {
                                            return $submission->status == 6;
                                        });
                                    @endphp
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="bg-gray-200">
                                                <tr>
                                                    <th class="text-left border p-2">Ledger Account</th>
                                                    <!-- [MODIFIKASI] Ganti Item dengan Ledger Account -->
                                                    <th class="text-left border p-2">Ledger Account Description</th>
                                                    <!-- [MODIFIKASI] Ganti Description dengan Ledger Account Description -->
                                                    <th class="text-left border p-2">Price</th>
                                                    <th class="text-left border p-2">Amount</th>
                                                    <!-- [MODIFIKASI] Tambah kolom Amount -->
                                                    <th class="text-left border p-2">Workcenter</th>
                                                    <th class="text-left border p-2">Department</th>
                                                    <th class="text-left border p-2">Month</th>
                                                    <th class="text-left border p-2">R/NR</th>
                                                    <th class="text-left border p-2">Line Of Business</th>
                                                    <!-- [MODIFIKASI] Tambah kolom Line Of Business -->
                                                    @if ($hasAction)
                                                        <th class="text-left border p-2">Action</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($submissions as $submission)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="border p-2">
                                                            {{ $submission->ledger_account ?? '-' }}</td>
                                                        <!-- [MODIFIKASI] Ganti item dengan ledger_account dan tambah default '-' untuk konsistensi dengan Kadept -->
                                                        <td class="border p-2">
                                                            {{ $submission->ledger_account_description ?? '-' }}</td>
                                                        <!-- [MODIFIKASI] Ganti description dengan ledger_account_description dan tambah default '-' untuk konsistensi dengan Kadept -->
                                                        <td class="border p-2">
                                                            {{ $submission->price ? 'IDR ' . number_format($submission->price, 2) : '-' }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Tambah format mata uang dan default '-' untuk konsistensi dengan Kadept -->
                                                        <td class="border p-2">
                                                            {{ $submission->amount ? 'IDR ' . number_format($submission->amount, 2) : '-' }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Tambah kolom Amount dengan format mata uang dan default '-' untuk konsistensi dengan Kadept -->
                                                        <td class="border p-2">
                                                            {{ $submission->workcenter != null ? $submission->workcenter->workcenter : '-' }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Tambah default '-' untuk konsistensi dengan Kadept -->
                                                        <td class="border p-2">
                                                            {{ $submission->dept != null ? $submission->dept->department : '-' }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Tambah default '-' untuk konsistensi dengan Kadept -->
                                                        <td class="border p-2">{{ $submission->month }}</td>
                                                        <td class="border p-2">
                                                            {{ $submission->budget != null ? $submission->budget->budget_name : '-' }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Tambah default '-' untuk konsistensi dengan Kadept -->
                                                        <td class="border p-2">
                                                            {{ $line_businesses[$submission->lob_id] ?? ($submission->lob_id ?? '-') }}
                                                        </td>
                                                        <!-- [MODIFIKASI] Tambah kolom Line Of Business dengan default '-' untuk konsistensi dengan Kadept -->
                                                        @if ($hasAction)
                                                            <td class="border p-2">
                                                                @if ($submission->status == 6)
                                                                    <a href="#"
                                                                        data-id="{{ $submission->sub_id }}"
                                                                        class="inline-flex items-center justify-center p-2 text-red-600 hover:text-blue-800 open-edit-modal"
                                                                        title="Update">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    <!-- [MODIFIKASI] Perbaikan form penghapusan untuk menggunakan id alih-alih itm_id agar sesuai dengan Kadept -->
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
                                                        <td colspan="10" class="border p-2 text-center">No
                                                            Submissions found!</td>
                                                        <!-- [MODIFIKASI] Sesuaikan colspan karena tambahan kolom Amount dan Line Of Business -->
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <br>
                                    <!-- Tombol Add Item -->
                                    @if ($hasAction)
                                        <button type="button" id="addItemBtn" class="btn text-white"
                                            style="background-color: #0080ff">
                                            <i class="fas fa-plus me-2"></i>Add Item
                                        </button>
                                        <!-- [MODIFIKASI] Tambah tombol Add Item untuk konsistensi dengan Kadept -->
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between mt-4">
                                    <button onclick="history.back()" type="button"
                                        class="btn btn-secondary me-2">Back</button>
                                    <!-- [MODIFIKASI] Removed icon from Back button to match Kadept -->
                                    <div class="d-flex">
                                        <!-- [MODIFIKASI] Changed gap-3 to d-flex to match Kadept -->
                                        @if ($submission->status == 6)
                                            <form action="{{ route('submissions.submit', $submission->sub_id) }}"
                                                method="POST" class="me-2">
                                                @csrf
                                                <button type="submit" class="btn text-white"
                                                    style="background-color: #0080ff;">
                                                    <i class="fa-solid fa-check me-2"></i> Approved
                                                </button>
                                            </form>
                                            <form action="{{ route('submissions.disapprove', $submission->sub_id) }}"
                                                method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-secondary">
                                                    <i class="fa-solid fa-xmark me-2"></i> DISAPPROVED
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
                                <div class="card-header bg-secondary text-white py-2 px-2">
                                    <h6 class="mb-0 text-white">Approval Status</h6>
                                </div>
                                <!-- Approval Status -->
                                <div class="bg-green-100 p-4 rounded shadow mb-4">
                                    @if ($submissions->isNotEmpty())
                                        @php $submission = $submissions->first(); @endphp
                                        <p>Status: <span class="font-bold">
                                                @if ($submission->status == 1)
                                                    <span class="badge bg-warning">DRAFT</span>
                                                @elseif ($submission->status == 2)
                                                    <span class="badge bg-secondary">UNDER REVIEW KADEP</span>
                                                @elseif ($submission->status == 3)
                                                    <span class="badge" style="background-color: #0080ff">APPROVED BY
                                                        KADEPT</span>
                                                @elseif ($submission->status == 4)
                                                    <span class="badge" style="background-color: #0080ff">APPROVED BY
                                                        KADIV</span>
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
                                                @elseif ($submission->status == 9)
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
                                        <p>Date: {{ $submission->updated_at->format('d-m-Y') ?? '03-03-2025' }}</p>
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
                                </div>
                                <!-- Item Table -->
                                <div class="bg-white p-4 rounded shadow mb-4">
                                    @php
                                        $hasAction = $submissions->contains(function ($submission) {
                                            return $submission->status == 1;
                                        });
                                    @endphp
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="bg-gray-200">
                                                <tr>
                                                    <th class="text-left border p-2">Ledger Account</th>
                                                    <th class="text-left border p-2">Ledger Account Description</th>
                                                    <th class="text-left border p-2">Price</th>
                                                    <th class="text-left border p-2">Amount</th>
                                                    <th class="text-left border p-2">Workcenter</th>
                                                    <th class="text-left border p-2">Department</th>
                                                    <th class="text-left border p-2">Month</th>
                                                    <th class="text-left border p-2">R/NR</th>
                                                    <th class="text-left border p-2">Line Of Business</th>
                                                    @if ($hasAction)
                                                        <th class="text-left border p-2">Action</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($submissions as $submission)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="border p-2">
                                                            {{ $submission->ledger_account ?? '-' }}</td>
                                                        <td class="border p-2">
                                                            {{ $submission->ledger_account_description ?? '-' }}</td>
                                                        <td class="border p-2">
                                                            {{ $submission->price ? 'IDR ' . number_format($submission->price, 2) : '-' }}
                                                        </td>
                                                        <td class="border p-2">
                                                            {{ $submission->amount ? 'IDR ' . number_format($submission->amount, 2) : '-' }}
                                                        </td>
                                                        <td class="border p-2">
                                                            {{ $submission->workcenter != null ? $submission->workcenter->workcenter : '-' }}
                                                        </td>
                                                        <td class="border p-2">
                                                            {{ $submission->dept != null ? $submission->dept->department : '-' }}
                                                        </td>
                                                        <td class="border p-2">{{ $submission->month }}</td>
                                                        <td class="border p-2">
                                                            {{ $submission->budget != null ? $submission->budget->budget_name : '-' }}
                                                        </td>
                                                        <td class="border p-2">
                                                            {{ $line_businesses[$submission->lob_id] ?? ($submission->lob_id ?? '-') }}
                                                        </td>
                                                        @if ($hasAction)
                                                            <td class="border p-2">
                                                                @if ($submission->status == 1)
                                                                    <a href="#"
                                                                        data-id="{{ $submission->sub_id }}"
                                                                        data-item-id="{{ $submission->id }}"
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
                                                        <td colspan="10" class="border p-2 text-center">No
                                                            Submissions found!</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <br>
                                    <!-- Tombol Add Item -->
                                    @if ($hasAction)
                                        <button type="button" id="addItemBtn" class="btn text-white"
                                            style="background-color: #0080ff">
                                            <i class="fas fa-plus me-2"></i>Add Item
                                        </button>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between mt-4">
                                    <button onclick="history.back()" type="button"
                                        class="btn btn-secondary me-2">Back</button>
                                    <div class="d-flex">
                                        @if ($submission->status == 1)
                                            <form action="{{ route('submissions.submit', $submission->sub_id) }}"
                                                method="POST">
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
        <!-- Modal untuk Add Item -->
        <div class="modal fade" id="itemModal" tabindex="-1" aria-labelledby="itemModalLabel" aria-hidden="true">
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
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Ledger Account</label><span
                                            class="text-danger">*</span>
                                        <input type="text" name="ledger_account" id="ledger_account"
                                            class="form-control" placeholder="Enter ledger account" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Ledger Account Description</label><span
                                            class="text-danger">*</span>
                                        <textarea class="form-control" name="ledger_account_description" id="ledger_account_description"
                                            placeholder="Ledger Account Description" required>{{ old('ledger_account_description') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="cur_id" class="form-label">Currency <span
                                                    class="text-danger">*</span></label>
                                            <select name="cur_id" id="cur_id" class="form-select select"
                                                required>
                                                <option value="" data-nominal="1" selected>Rp</option>
                                                @foreach ($currencies as $cur_id => $currency)
                                                    <option value="{{ $cur_id }}"
                                                        data-nominal="{{ $currency['nominal'] }}">
                                                        {{ $currency['currency'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="price" class="form-label">Price <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" class="form-control" required id="price"
                                                name="price" value="{{ old('price', $submission->price ?? 0) }}"
                                                placeholder="Price" step="0.01" min="0">
                                        </div>
                                    </div>
                                    <div id="currencyInfo" class="form-text text-muted" style="display: none;"></div>
                                    <div class="mb-3">
                                        <label class="form-label">Amount</label><span class="text-danger">*</span>
                                        <input type="text" class="form-control" id="amountDisplay" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Workcenter</label>
                                        <select name="wct_id" class="form-control select" required id="wct_id">
                                            <option value="">-- Workcenter --</option>
                                            @foreach ($workcenters as $wctID => $workcenter)
                                                <option value="{{ $wctID }}" @selected(old('wct_id') == $wctID)>
                                                    {{ $workcenter }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Department</label><span class="text-danger">*</span>
                                        <input type="hidden" name="dpt_id" value="{{ Auth::user()->dept }}">
                                        <input class="form-control"
                                            value="{{ Auth::user()->department->department ?? '-' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Month</label><span class="text-danger">*</span>
                                        <select class="form-control select" name="month" id="month" required>
                                            <option value="">-- Select Month --</option>
                                            @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                                <option value="{{ $month }}" @selected(old('month') === $month)>
                                                    {{ $month }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">R/NR</label><span class="text-danger">*</span>
                                        <select name="bdc_id" class="form-control select" required id="bdc_id">
                                            <option value="">-- R/NR --</option>
                                            @foreach ($budgets as $bdcID => $budgets)
                                                <option value="{{ $bdcID }}" @selected(old('bdc_id') == $bdcID)>
                                                    {{ $budgets }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Line Of Business</label><span
                                            class="text-danger">*</span>
                                        <select name="lob_id" class="form-control select" required id="lob_id">
                                            <option value="">-- Line Of Business --</option>
                                            @foreach ($line_businesses as $lobID => $line_businesses)
                                                <option value="{{ $lobID }}" @selected(old('lob_id') == $lobID)>
                                                    {{ $line_businesses }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger">Add Item</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            $(document).ready(function() {
                // Inisialisasi Select2
                $('select').select2();

                // Tangani klik tombol edit
                $(document).on('click', '.open-edit-modal', function(e) {
                    e.preventDefault();
                    var subId = $(this).data('id');
                    var itemId = $(this).data('item-id');
                    var modal = $('#editModal');

                    // Load konten modal via AJAX
                    $.get('/submissions/' + subId + '/' + itemId + '/edit', function(data) {
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
                                location.reload();
                            }
                        },
                        error: function(xhr) {
                            $('#editModal .modal-body').prepend(
                                '<div class="alert alert-danger">' + xhr.responseJSON.message +
                                '</div>'
                            );
                        }
                    });
                });

                // Tangani klik tombol history approval
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
                                '<div class="alert alert-danger">Failed to load approval history</div>'
                            );
                        });
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
                                location.reload();
                            }
                        },
                        error: function(xhr) {
                            form.find('.modal-body').prepend(
                                '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                (xhr.responseJSON.message || 'Failed to add remark') +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'
                            );
                        }
                    });
                });

                // Tangani klik tombol history remark
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
                                '<div class="alert alert-danger">Failed to load remarks history</div>'
                            );
                        });
                });

                // Tangani klik tombol delete
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
                                            'The item has been deleted.',
                                            'success'
                                        ).then(() => {
                                            location.reload();
                                        });
                                    }
                                },
                                error: function(xhr) {
                                    Swal.fire(
                                        'Error!',
                                        xhr.responseJSON.message ||
                                        'Failed to delete the item.',
                                        'error'
                                    );
                                }
                            });
                        }
                    });
                });

                // Logika untuk tombol Add Item
                let currencies = [];

                $('#itemModal').on('shown.bs.modal', function() {
                    $('.select').select2({
                        dropdownParent: $('#itemModal'),
                        allowClear: true,
                        placeholder: function() {
                            return $(this).attr('id') === 'cur_id' ? '-- Select Currency --' :
                                $(this).attr('id') === 'wct_id' ? '-- Workcenter --' :
                                $(this).attr('id') === 'month' ? '-- Select Month --' :
                                $(this).attr('id') === 'bdc_id' ? '-- R/NR --' :
                                $(this).attr('id') === 'lob_id' ? '-- Line Of Business --' :
                                '-- Select --';
                        },
                        width: '100%',
                        theme: 'bootstrap-5'
                    });

                    $('.select2-selection--single').css({
                        'height': $('#price').outerHeight() + 'px',
                        'display': 'flex',
                        'align-items': 'center'
                    });
                    $('.select2-selection__rendered').css({
                        'line-height': $('#price').outerHeight() + 'px'
                    });

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

                $('#ledger_account').on('input', function() {
                    $(this).val($(this).val().toUpperCase());
                });

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

                $('#itemForm').on('submit', function(e) {
                    e.preventDefault();
                    const bdc_id = $('#bdc_id').val();
                    if (!bdc_id) {
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

                    $.ajax({
                        url: $(this).attr('action'),
                        method: 'POST',
                        data: $(this).serialize(),
                        success: function(response) {
                            if (response.success) {
                                $('#itemModal').modal('hide');
                                location.reload();
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON.message || 'Failed to add item.',
                            });
                        }
                    });
                });

                $('#addItemBtn').click(function() {
                    console.log("Add Item button clicked");
                    var modal = new bootstrap.Modal(document.getElementById('itemModal'));
                    if (!modal) {
                        console.error("Modal not found or Bootstrap not loaded");
                    }
                    modal.show();
                });

                $('#itemModal').on('hidden.bs.modal', function() {
                    $('.select').each(function() {
                        if ($(this).data('select2')) {
                            $(this).select2('destroy');
                        }
                    });
                });
            });
        </script>
        <x-footer></x-footer>
    </main>
</body>

</html>
