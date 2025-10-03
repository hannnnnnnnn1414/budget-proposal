<!--
=========================================================
* Soft UI Dashboard - v1.0.3
=========================================================

* Product Page: https://www.creative-tim.com/product/soft-ui-dashboard
* Copyright 2021 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)

* Coded by Creative Tim

=========================================================
    
* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>
<html lang="en">

<x-head></x-head>

<body class="g-sidenav-show bg-gray-100">
    <x-sidebar></x-sidebar>
    @if (session('sect') === 'Kadept' && session('dept') !== '6121')
        <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
            <x-navbar :notifications="$notifications">
                Approval</x-navbar>
            <div class="container-fluid">
                <div class="row">
                    <div class="card-header bg-danger">
                        <h4 style="font-weight: bold;" class="text-white"><i
                                class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>APPROVAL SUBMISSIONS</h4>
                    </div>
                    <div class="card rounded-0">
                        <div class="mt-4">
                            <label class="form-label">Submission name or ID search</label>
                            <div class="input-group">
                                <input name="cari" type="text" id="cari" class="form-control"
                                    placeholder="Pencarian" onkeyup="searchTable()" />
                            </div>
                        </div>
                        <div id="submissions">
                            <div class="card-body table-responsive">

                                @if (Session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                @if (Session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif
                                <table id="myTable" class="table table-striped table-bordered">
                                    <thead>
                                        <tr class="text-center">
                                            <th>No</th>
                                            <th>Purpose</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($approvals as $approval)
                                            @php
                                                $directDIC = in_array($approval->dpt_id, ['6111', '6121', '4211']);
                                            @endphp
                                            <tr class="text-center">
                                                <td>{{ $approval->sub_id }}</td>
                                                <td>{{ $approval->purpose }}</td>
                                                <td class="text-center">
                                                    @if ($approval->status == 3 && !$directDIC)
                                                        <span class="badge" style="background-color: #0080ff">APPROVED
                                                            BY KADEP</span>
                                                    @elseif ($approval->status == 4)
                                                        <span class="badge" style="background-color: #0080ff">
                                                            @if ($directDIC)
                                                                APPROVED BY KADEPT
                                                            @else
                                                                Approved by KADIV
                                                            @endif
                                                        </span>
                                                    @elseif ($approval->status == 5)
                                                        <span class="badge"
                                                            style="background-color: #0080ff">ACKNOWLEDGED
                                                            BY DIC</span>
                                                    @elseif ($approval->status == 6)
                                                        <span class="badge" style="background-color: #0080ff">APPROVED
                                                            BY PIC BUDGETING</span>
                                                    @elseif ($approval->status == 7)
                                                        <span class="badge" style="background-color: #0080ff">APPROVED
                                                            BY KADEP BUDGETING</span>
                                                    @elseif ($approval->status == 8)
                                                        <span class="badge bg-danger">DISAPPROVED BY KADEP</span>
                                                    @elseif ($approval->status == 9 && !$directDIC)
                                                        <span class="badge bg-danger">DISApproved by KADIV</span>
                                                    @elseif ($approval->status == 10)
                                                        <span class="badge bg-danger">REQUEST EXPLANATION</span>
                                                    @elseif ($approval->status == 11)
                                                        <span class="badge bg-danger">DISAPPROVED BY PIC
                                                            BUDGETING</span>
                                                    @elseif ($approval->status == 12)
                                                        <span class="badge bg-danger">DISAPPROVED BY KADEP
                                                            BUDGETING</span>
                                                    @else
                                                        <span class="badge bg-danger">Rejected</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('submissions.report', ['sub_id' => $approval->sub_id]) }}"
                                                        class="btn d-inline-flex align-items-center justify-content-center text-white"
                                                        style="background-color: #0d6efd; width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                                                        title="Detail">
                                                        <i class="fa-solid fa-circle-info fs-6"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4">No Approved or Disapproved Submissions found!</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div id="no-records-message" class="text-center mt-3 text-secondary"
                                    style="display: none;">
                                    No matching records found
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <x-footer></x-footer>
            </div>
        </main>
    @elseif(session('sect') === 'Kadiv')
        <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
            <x-navbar :notifications="$notifications">
                Account Budgeting</x-navbar>
            <div class="container-fluid">
                <div class="row">
                    <div class="card-header bg-danger">
                        <h4 style="font-weight: bold;" class="text-white"><i
                                class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>APPROVAL SUBMISSIONS</h4>
                    </div>
                    <div class="card rounded-0">
                        <div class="mt-4">
                            <label class="form-label">Submission name or ID search</label>
                            <div class="input-group">
                                <input name="cari" type="text" id="cari" class="form-control"
                                    placeholder="Pencarian" onkeyup="searchTable()" />
                            </div>
                        </div>
                        <div id="submissions">
                            <div class="card-body table-responsive">
                                @if (Session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                @if (Session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif
                                <table id="myTable" class="table table-striped table-bordered">
                                    <thead>
                                        <tr class="text-center">
                                            <th>No</th>
                                            <th>Purpose</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($approvals as $approval)
                                            <tr class="text-center">
                                                <td>{{ $approval->sub_id }}</td>
                                                <td>{{ $approval->purpose }}</td>
                                                <td class="text-center">
                                                    @if ($approval->status == 4)
                                                        <span class="badge" style="background-color: #0080ff">APPROVED
                                                            BY KADIV</span>
                                                    @elseif ($approval->status == 5)
                                                        <span class="badge"
                                                            style="background-color: #0080ff">ACKNOWLEDGED
                                                            BY DIC</span>
                                                    @elseif ($approval->status == 6)
                                                        <span class="badge" style="background-color: #0080ff">APPROVED
                                                            BY PIC BUDGETING</span>
                                                    @elseif ($approval->status == 7)
                                                        <span class="badge" style="background-color: #0080ff">APPROVED
                                                            BY KADEP BUDGETING</span>
                                                    @elseif ($approval->status == 8)
                                                        <span class="badge bg-danger">DISAPPROVED BY KADEP</span>
                                                    @elseif ($approval->status == 9)
                                                        <span class="badge bg-danger">DISApproved by KADIV</span>
                                                    @elseif ($approval->status == 10)
                                                        <span class="badge bg-danger">REQUEST EXPLANATION</span>
                                                    @elseif ($approval->status == 11)
                                                        <span class="badge bg-danger">DISAPPROVED BY PIC
                                                            BUDGETING</span>
                                                    @elseif ($approval->status == 12)
                                                        <span class="badge bg-danger">DISAPPROVED BY KADEP
                                                            BUDGETING</span>
                                                    @else
                                                        <span class="badge bg-danger">Rejected</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('submissions.report', ['sub_id' => $approval->sub_id]) }}"
                                                        class="btn d-inline-flex align-items-center justify-content-center text-white"
                                                        style="background-color: #0d6efd; width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                                                        title="Detail">
                                                        <i class="fa-solid fa-circle-info fs-6"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4">No Approved or Disapproved Submissions found!</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div id="no-records-message" class="text-center mt-3 text-secondary"
                                    style="display: none;">
                                    No matching records found
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <x-footer></x-footer>
            </div>
        </main>
    @elseif(
        (session('sect') === 'PIC' && session('dept') === '6121') ||
            (session('sect') === 'Kadept' && session('dept') === '6121'))
        <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
            <x-navbar :notifications="$notifications">
                Account Budgeting</x-navbar>
            <div class="container-fluid">
                <div class="row">
                    <div class="card-header bg-danger">
                        <h4 style="font-weight: bold;" class="text-white"><i
                                class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>APPROVAL SUBMISSIONS -
                            BUDGETING</h4>
                    </div>
                    <div class="card rounded-0">
                        <div class="mt-4">
                            <label class="form-label">Submission name or ID search</label>
                            <div class="input-group">
                                <input name="cari" type="text" id="cari" class="form-control"
                                    placeholder="Pencarian" onkeyup="searchTable()" />
                            </div>
                        </div>
                        <div id="submissions">
                            <div class="card-body table-responsive">
                                @if (Session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                @if (Session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif
                                <table id="myTable" class="table table-striped table-bordered">
                                    <thead>
                                        <tr class="text-center">
                                            <th>No</th>
                                            <th>Purpose</th>
                                            <th>Department</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($approvals as $approval)
                                            @php
                                                $department = \App\Models\Departments::where(
                                                    'dpt_id',
                                                    $approval->dpt_id,
                                                )->first();
                                                $deptName = $department ? $department->department : $approval->dpt_id;
                                            @endphp
                                            <tr class="text-center">
                                                <td>{{ $approval->sub_id }}</td>
                                                <td>{{ $approval->purpose }}</td>
                                                <td>{{ $deptName }}</td>
                                                <td class="text-center">
                                                    @if ($approval->status == 1)
                                                        <span class="badge bg-warning">DRAFT</span>
                                                    @elseif ($approval->status == 2)
                                                        <span class="badge bg-secondary">UNDER REVIEW
                                                            KADEP</span>
                                                    @elseif ($approval->status == 3 && !$directDIC)
                                                        <span class="badge"
                                                            style="background-color: #0080ff">APPROVED BY
                                                            KADEPT</span>
                                                    @elseif ($approval->status == 4)
                                                        <span class="badge" style="background-color: #0080ff">
                                                            @if ($directDIC)
                                                                APPROVED BY KADEPT
                                                            @else
                                                                Approved by KADIV
                                                            @endif
                                                        </span>
                                                    @elseif ($approval->status == 5)
                                                        {{-- Tampilkan REQUIRES APPROVAL untuk PIC 6121 --}}
                                                        @if (session('sect') === 'PIC' && session('dept') === '6121')
                                                            <span class="badge bg-warning">REQUIRES APPROVAL</span>
                                                        @else
                                                            <span class="badge"
                                                                style="background-color: #0080ff">ACKNOWLEDGED
                                                                BY
                                                                DIC</span>
                                                        @endif
                                                    @elseif ($approval->status == 6)
                                                        {{-- Tampilkan REQUIRES APPROVAL untuk Kadept 6121 --}}
                                                        @if (session('sect') === 'Kadept' && session('dept') === '6121')
                                                            <span class="badge bg-warning">REQUIRES APPROVAL</span>
                                                        @else
                                                            <span class="badge"
                                                                style="background-color: #0080ff">APPROVED
                                                                BY
                                                                PIC BUDGETING</span>
                                                        @endif
                                                    @elseif ($approval->status == 7)
                                                        <span class="badge"
                                                            style="background-color: #0080ff">APPROVED
                                                            BY
                                                            KADEP BUDGETING</span>
                                                    @elseif ($approval->status == 8)
                                                        <span class="badge bg-danger">DISAPPROVED BY
                                                            KADEP</span>
                                                    @elseif ($approval->status == 9 && !$directDIC)
                                                        <span class="badge bg-danger">DISAPPROVED BY
                                                            KADIV</span>
                                                    @elseif ($approval->status == 10)
                                                        <span class="badge bg-danger">REQUEST EXPLANATION</span>
                                                    @elseif ($approval->status == 11)
                                                        <span class="badge bg-danger">DISAPPROVED BY PIC
                                                            BUDGETING</span>
                                                    @elseif ($approval->status == 12)
                                                        <span class="badge bg-danger">DISAPPROVED BY KADEP
                                                            BUDGETING</span>
                                                    @else
                                                        <span class="badge bg-danger">REJECTED</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('submissions.report', ['sub_id' => $approval->sub_id]) }}"
                                                        class="btn d-inline-flex align-items-center justify-content-center text-white"
                                                        style="background-color: #0d6efd; width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                                                        title="Detail">
                                                        <i class="fa-solid fa-circle-info fs-6"></i>
                                                    </a>

                                                    {{-- Tombol Approve untuk PIC 6121  --}}
                                                    @if (in_array($approval->status, [5, 12]) && session('sect') === 'PIC' && session('dept') === '6121')
                                                        <button type="button"
                                                            class="btn d-inline-flex align-items-center justify-content-center text-white"
                                                            style="background-color: #28a745; width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                                                            title="Approve"
                                                            onclick="approveSubmission('{{ $approval->sub_id }}', '{{ $approval->purpose }}', '{{ $approval->dpt_id }}')">
                                                            <i class="fa-solid fa-check fs-6"></i>
                                                        </button>
                                                    @endif

                                                    {{-- Tombol Disapprove untuk PIC 6121  --}}
                                                    @if (in_array($approval->status, [5, 12]) && session('sect') === 'PIC' && session('dept') === '6121')
                                                        <button type="button"
                                                            class="btn d-inline-flex align-items-center justify-content-center text-white"
                                                            style="background-color: #dc3545; width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                                                            title="Disapprove"
                                                            onclick="rejectSubmission('{{ $approval->sub_id }}', '{{ $approval->purpose }}', '{{ $approval->dpt_id }}')">
                                                            <i class="fa-solid fa-times fs-6"></i>
                                                        </button>
                                                    @endif

                                                    {{-- Tombol Approve untuk Kadept 6121 --}}
                                                    @if ($approval->status == 6 && session('sect') === 'Kadept' && session('dept') === '6121')
                                                        <button type="button"
                                                            class="btn d-inline-flex align-items-center justify-content-center text-white"
                                                            style="background-color: #28a745; width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                                                            title="Approve"
                                                            onclick="approveSubmission('{{ $approval->sub_id }}', '{{ $approval->purpose }}', '{{ $approval->dpt_id }}')">
                                                            <i class="fa-solid fa-check fs-6"></i>
                                                        </button>
                                                    @endif

                                                    {{-- Tombol Disapprove untuk Kadept 6121 --}}
                                                    @if ($approval->status == 6 && session('sect') === 'Kadept' && session('dept') === '6121')
                                                        <button type="button"
                                                            class="btn d-inline-flex align-items-center justify-content-center text-white"
                                                            style="background-color: #dc3545; width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                                                            title="Disapprove"
                                                            onclick="rejectSubmission('{{ $approval->sub_id }}', '{{ $approval->purpose }}', '{{ $approval->dpt_id }}')">
                                                            <i class="fa-solid fa-times fs-6"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5">No Approved or Disapproved Submissions found!</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div id="no-records-message" class="text-center mt-3 text-secondary"
                                    style="display: none;">
                                    No matching records found
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <x-footer></x-footer>
            </div>
        </main>
    @elseif(session('sect') === 'DIC')
        <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
            <x-navbar :notifications="$notifications">
                Account Budgeting</x-navbar>
            <div class="container-fluid">
                <div class="row">
                    <div class="card-header bg-danger">
                        <h4 style="font-weight: bold;" class="text-white"><i
                                class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>APPROVAL SUBMISSIONS
                        </h4>
                    </div>
                    <div class="card rounded-0">
                        <div class="mt-4">
                            <label class="form-label">Submission name or ID search</label>
                            <div class="input-group">
                                <input name="cari" type="text" id="cari" class="form-control"
                                    placeholder="Pencarian" onkeyup="searchTable()" />
                            </div>
                        </div>
                        <div id="submissions">
                            <div class="card-body table-responsive">
                                @if (Session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                @if (Session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif
                                <table id="myTable" class="table table-striped table-bordered">
                                    <thead>
                                        <tr class="text-center">
                                            <th>No</th>
                                            <th>Purpose</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($approvals as $approval)
                                            <tr class="text-center">
                                                <td>{{ $approval->sub_id }}</td>
                                                <td>{{ $approval->purpose }}</td>
                                                <td class="text-center">
                                                    @if ($approval->status == 4)
                                                        <span class="badge"
                                                            style="background-color: #0080ff">ACKNOWLEDGED BY
                                                            KADIV</span>
                                                    @elseif ($approval->status == 5)
                                                        <span class="badge"
                                                            style="background-color: #0080ff">APPROVED BY
                                                            DIC</span>
                                                    @elseif ($approval->status == 6)
                                                        <span class="badge"
                                                            style="background-color: #0080ff">APPROVED BY PIC
                                                            BUDGETING</span>
                                                    @elseif ($approval->status == 7)
                                                        <span class="badge"
                                                            style="background-color: #0080ff">APPROVED BY KADEP
                                                            BUDGETING</span>
                                                    @elseif ($approval->status == 8)
                                                        <span class="badge bg-danger">DISAPPROVED BY KADEP</span>
                                                    @elseif ($approval->status == 9)
                                                        <span class="badge bg-danger">DISApproved by KADIV</span>
                                                    @elseif ($approval->status == 10)
                                                        <span class="badge bg-danger">REQUEST EXPLANATION</span>
                                                    @elseif ($approval->status == 11)
                                                        <span class="badge bg-danger">DISAPPROVED BY PIC
                                                            BUDGETING</span>
                                                    @elseif ($approval->status == 12)
                                                        <span class="badge bg-danger">DISAPPROVED BY KADEP
                                                            BUDGETING</span>
                                                    @else
                                                        <span class="badge bg-danger">Rejected</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('submissions.report', ['sub_id' => $approval->sub_id]) }}"
                                                        class="btn d-inline-flex align-items-center justify-content-center text-white"
                                                        style="background-color: #0d6efd; width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                                                        title="Detail">
                                                        <i class="fa-solid fa-circle-info fs-6"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4">No Approved or Disapproved Submissions found!</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div id="no-records-message" class="text-center mt-3 text-secondary"
                                    style="display: none;">
                                    No matching records found
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <x-footer></x-footer>
            </div>
        </main>
    @else
        <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
            <x-navbar :notifications="$notifications">
                Approval</x-navbar>
            <div class="container-fluid">
                <div class="row">
                    <div class="card-header bg-danger">
                        <h4 style="font-weight: bold;" class="text-white"><i
                                class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>APPROVAL SUBMISSIONS
                        </h4>
                    </div>
                    <div class="card rounded-0">
                        <div class="mt-4">
                            <label class="form-label">Submission name or ID search</label>
                            <div class="input-group">
                                <input name="cari" type="text" id="cari" class="form-control"
                                    placeholder="Pencarian" onkeyup="searchTable()" />
                            </div>
                        </div>
                        <div id="submissions">
                            <div class="card-body table-responsive">

                                @if (Session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                @if (Session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif
                                <table id="myTable" class="table table-striped table-bordered">
                                    <thead>
                                        <tr class="text-center">
                                            <th>No</th>
                                            <th>Purpose</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($approvals as $approval)
                                            @php
                                                $directDIC = in_array($approval->dpt_id, ['6111', '6121', '4211']);
                                            @endphp
                                            <tr class="text-center">
                                                <td>{{ $approval->sub_id }}</td>
                                                <td>{{ $approval->purpose }}</td>
                                                <td class="text-center">
                                                    @if ($approval->status == 3 && !$directDIC)
                                                        <span class="badge"
                                                            style="background-color: #0080ff">APPROVED BY KADEP</span>
                                                    @elseif ($approval->status == 4)
                                                        <span class="badge" style="background-color: #0080ff">
                                                            @if ($directDIC)
                                                                APPROVED BY KADEPT
                                                            @else
                                                                Approved by KADIV
                                                            @endif
                                                        </span>
                                                    @elseif ($approval->status == 5)
                                                        <span class="badge"
                                                            style="background-color: #0080ff">Acknowledged by
                                                            DIC</span>
                                                    @elseif ($approval->status == 6)
                                                        <span class="badge"
                                                            style="background-color: #0080ff">APPROVED BY PIC
                                                            BUDGETING</span>
                                                    @elseif ($approval->status == 7)
                                                        <span class="badge"
                                                            style="background-color: #0080ff">APPROVED BY KADEP
                                                            BUDGETING</span>
                                                    @elseif ($approval->status == 8)
                                                        <span class="badge bg-danger">DISAPPROVED BY KADEP</span>
                                                    @elseif ($approval->status == 9 && !$directDIC)
                                                        <span class="badge bg-danger">DISApproved by KADIV</span>
                                                    @elseif ($approval->status == 10)
                                                        <span class="badge bg-danger">REQUEST EXPLANATION</span>
                                                    @elseif ($approval->status == 11)
                                                        <span class="badge bg-danger">DISAPPROVED BY PIC
                                                            BUDGETING</span>
                                                    @elseif ($approval->status == 12)
                                                        <span class="badge bg-danger">DISAPPROVED BY KADEP
                                                            BUDGETING</span>
                                                    @else
                                                        <span class="badge bg-danger">Rejected</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('submissions.report', ['sub_id' => $approval->sub_id]) }}"
                                                        class="btn d-inline-flex align-items-center justify-content-center text-white"
                                                        style="background-color: #0d6efd; width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                                                        title="Detail">
                                                        <i class="fa-solid fa-circle-info fs-6"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4">No Approved or Disapproved Submissions found!</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div id="no-records-message" class="text-center mt-3 text-secondary"
                                    style="display: none;">
                                    No matching records found
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <x-footer></x-footer>
            </div>
        </main>
    @endif
    <script>
        function searchTable() {
            var input, filter, table, tr, td, i, j, txtValue;
            var visibleRows = 0; // Initialize visibleRows
            input = document.getElementById("cari");
            filter = input.value.toUpperCase();
            table = document.getElementById("myTable");
            tr = table.getElementsByTagName("tr");

            // Start from i = 1 to skip the header row
            for (i = 1; i < tr.length; i++) {
                var display = false;
                for (j = 0; j < tr[i].cells.length; j++) {
                    td = tr[i].cells[j];
                    if (td) {
                        txtValue = td.textContent || td.innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            display = true;
                            break;
                        }
                    }
                }
                tr[i].style.display = display ? "" : "none";
                if (display) visibleRows++; // Increment visibleRows if the row is visible
            }

            // Show or hide the "No matching records found" message
            var noRecordsMessage = document.getElementById("no-records-message");
            noRecordsMessage.style.display = visibleRows === 0 ? "block" : "none";
        }
    </script>
    <x-sidebar-plugin></x-sidebar-plugin>
    <!--   Core JS Files   -->
    <script src="{{ asset('js/core/popper.min.js') }}"></script>
    <script src="{{ asset('js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('js/plugins/chartjs.min.js') }}"></script>
    <script src="{{ asset('js/curve-chart.js') }}"></script>
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }

        // Fungsi untuk approve submission dengan SweetAlert
        function approveSubmission(sub_id, purpose, dept_id) { // ✅ TAMBAHKAN PARAMETER dept_id
            Swal.fire({
                title: 'Konfirmasi Persetujuan',
                html: `Apakah Anda yakin ingin menyetujui pengajuan <strong>${purpose}</strong>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Setujui!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang menyetujui pengajuan',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Kirim request approve dengan department
                    fetch('{{ url('submissions') }}/' + sub_id + '/submit', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ // ✅ KIRIM DEPARTMENT SEBAGAI BODY
                                specific_dept: dept_id
                            })
                        })
                        .then(response => {
                            // Cek jika response adalah HTML redirect
                            const contentType = response.headers.get('content-type');
                            if (contentType && contentType.includes('text/html')) {
                                return {
                                    success: true,
                                    html: true
                                };
                            }

                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }

                            // Coba parse sebagai JSON, jika gagal anggap sukses
                            return response.json().catch(() => {
                                return {
                                    success: true
                                };
                            });
                        })
                        .then(data => {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Pengajuan berhasil disetujui.',
                                icon: 'success',
                                confirmButtonColor: '#28a745',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Terjadi kesalahan saat menyetujui pengajuan.',
                                icon: 'error',
                                confirmButtonColor: '#dc3545',
                                confirmButtonText: 'OK'
                            });
                        });
                }
            });
        }

        // Fungsi untuk reject submission dengan SweetAlert
        function rejectSubmission(sub_id, purpose, dept_id) {
            Swal.fire({
                title: 'Alasan Penolakan',
                html: `Masukkan alasan penolakan untuk pengajuan <strong>${purpose}</strong>:`,
                input: 'textarea',
                inputLabel: 'Alasan',
                inputPlaceholder: 'Masukkan alasan penolakan...',
                inputAttributes: {
                    'aria-label': 'Masukkan alasan penolakan',
                    'maxlength': 500
                },
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Tolak Pengajuan',
                cancelButtonText: 'Batal',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Alasan penolakan harus diisi!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang menolak pengajuan',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Kirim data ke server dengan department
                    fetch('{{ url('submissions') }}/' + sub_id + '/disapprove', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                remark: result.value,
                                specific_dept: dept_id //
                            })
                        })
                        .then(response => {
                            // Cek jika response adalah HTML redirect
                            const contentType = response.headers.get('content-type');
                            if (contentType && contentType.includes('text/html')) {
                                return {
                                    success: true,
                                    html: true
                                };
                            }

                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }

                            // Coba parse sebagai JSON, jika gagal anggap sukses
                            return response.json().catch(() => {
                                return {
                                    success: true
                                };
                            });
                        })
                        .then(data => {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Pengajuan berhasil ditolak.',
                                icon: 'success',
                                confirmButtonColor: '#28a745',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Terjadi kesalahan saat menolak pengajuan.',
                                icon: 'error',
                                confirmButtonColor: '#dc3545',
                                confirmButtonText: 'OK'
                            });
                        });
                }
            });
        }
    </script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="{{ asset('js/soft-ui-dashboard.min.js?v=1.0.3') }}"></script>
</body>

</html>
