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

<body class="g-sidenav-show  bg-gray-100">
    <x-sidebar></x-sidebar>
    @if (session('sect') === 'PIC' && session('dept') === '6121')
        <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
            <x-navbar :notifications="$notifications">
                Departments</x-navbar>
            <div class="container-fluid">
                <div class="row">
                    <div class="card-header bg-danger">
                        <h4 style="font-weight: bold;" class="text-white"><i
                                class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>PT. KAYABA INDONESIA
                            DEPARTMENTS
                        </h4>
                    </div>
                    <div class="card rounded-0">
                        <div class="mt-4">
                            <label class="form-label">Search by department, purpose, or status</label>
                            <div class="input-group">
                                <input name="cari" type="text" id="cari" class="form-control"
                                    placeholder="Search..." onkeyup="searchTable()" />
                            </div>
                        </div>
                        <div id="departments">
                            <div class="card-body">
                                @if (Session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                @if (Session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif

                                <table id="myTable" class="table table-striped table-bordered">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Department</th>
                                            <th>Submission ID</th>
                                            <th>Purpose</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>


                                    <tbody>
                                        @forelse ($budgetPlans as $plan)
                                            @php
                                                // Cari department berdasarkan dpt_id
                                                $department = $departments->where('dpt_id', $plan->dpt_id)->first();
                                                $departmentName = $department
                                                    ? $department->department
                                                    : 'Unknown Department';

                                                // Format status berdasarkan mapping
                                                $statusText = $statusMap[$plan->status] ?? 'Unknown Status';
                                                $statusClass = '';

                                                // Tentukan warna badge berdasarkan status
                                                if (in_array($plan->status, [1, 2])) {
                                                    $statusClass = 'bg-secondary'; // Created, Requested
                                                } elseif (in_array($plan->status, [3, 4, 5, 6, 7])) {
                                                    $statusClass = 'bg-success'; // Approved states
                                                } elseif (in_array($plan->status, [8, 9, 11, 12])) {
                                                    $statusClass = 'bg-danger'; // Disapproved states
                                                } elseif ($plan->status == 10) {
                                                    $statusClass = 'bg-warning'; // Request Explanation
                                                } else {
                                                    $statusClass = 'bg-secondary'; // Default
                                                }

                                                // Format amount
                                                $amount = $plan->total_amount
                                                    ? 'Rp ' . number_format($plan->total_amount, 2, ',', '.')
                                                    : '-';

                                                // Format date
                                                $createdDate = $plan->created_at
                                                    ? $plan->created_at->format('d M Y')
                                                    : '-';
                                            @endphp
                                            <tr>
                                                <td>{{ $departmentName }}</td>
                                                <td>{{ $plan->sub_id }}</td>
                                                <td>{{ $plan->purpose ?: '-' }}</td>
                                                <td class="text-center">
                                                    @if ($plan->status == 1)
                                                        <span class="badge bg-warning">DRAFT</span>
                                                    @elseif ($plan->status == 2)
                                                        <span class="badge bg-secondary">UNDER REVIEW KADEP</span>
                                                    @elseif ($plan->status == 3)
                                                        <span class="badge" style="background-color: #0080ff">APPROVED
                                                            BY KADEPT</span>
                                                    @elseif ($plan->status == 4)
                                                        <span class="badge" style="background-color: #0080ff">APPROVED
                                                            BY KADIV</span>
                                                    @elseif ($plan->status == 5)
                                                        <span class="badge"
                                                            style="background-color: #0080ff">ACKNOWLEDGED BY DIC</span>
                                                    @elseif ($plan->status == 6)
                                                        <span class="badge" style="background-color: #0080ff">APPROVED
                                                            BY PIC BUDGETING</span>
                                                    @elseif ($plan->status == 7)
                                                        <span class="badge" style="background-color: #0080ff">APPROVED
                                                            BY KADEP BUDGETING</span>
                                                    @elseif ($plan->status == 8)
                                                        <span class="badge bg-danger">DISAPPROVED BY KADEP</span>
                                                    @elseif ($plan->status == 9)
                                                        <span class="badge bg-danger">DISAPPROVED BY KADIV</span>
                                                    @elseif ($plan->status == 10)
                                                        <span class="badge bg-danger">REQUEST EXPLANATION</span>
                                                    @elseif ($plan->status == 11)
                                                        <span class="badge bg-danger">DISAPPROVED BY PIC
                                                            BUDGETING</span>
                                                    @elseif ($plan->status == 12)
                                                        <span class="badge bg-danger">DISAPPROVED BY KADEP
                                                            BUDGETING</span>
                                                    @else
                                                        <span class="badge bg-danger">REJECTED</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('departments.detail', ['dpt_id' => $plan->dpt_id, 'sub_id' => $plan->sub_id]) }}"
                                                        class="btn d-inline-flex align-items-center justify-content-center text-white"
                                                        style="background-color: #0d6efd; width: 30px; height: 30px; border-radius: 3px; margin: 2px;"
                                                        title="View Details">
                                                        <i class="fa-solid fa-eye fs-6"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">No budget plans found!</td>
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
    @elseif (session('sect') === 'Kadept' && session('dept') === '6121')
        <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
            <x-navbar :notifications="$notifications">
                Departments</x-navbar>
            <div class="container-fluid">
                <div class="row">
                    <div class="card-header bg-danger">
                        <h4 style="font-weight: bold;" class="text-white"><i
                                class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>PT. KAYABA INDONESIA
                            DEPARTMENTS
                        </h4>
                    </div>
                    <div class="card rounded-0">
                        <div class="mt-4">
                            <label class="form-label">Search by department, purpose, or status</label>
                            <div class="input-group">
                                <input name="cari" type="text" id="cari" class="form-control"
                                    placeholder="Search..." onkeyup="searchTable()" />
                            </div>
                        </div>
                        <div id="departments">
                            <div class="card-body">
                                @if (Session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                @if (Session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif

                                <table id="myTable" class="table table-striped table-bordered">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Department</th>
                                            <th>Submission ID</th>
                                            <th>Purpose</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>


                                    <tbody>
                                        @forelse ($budgetPlans as $plan)
                                            @php
                                                // Cari department berdasarkan dpt_id
                                                $department = $departments->where('dpt_id', $plan->dpt_id)->first();
                                                $departmentName = $department
                                                    ? $department->department
                                                    : 'Unknown Department';

                                                // Format status berdasarkan mapping
                                                $statusText = $statusMap[$plan->status] ?? 'Unknown Status';
                                                $statusClass = '';

                                                // Tentukan warna badge berdasarkan status
                                                if (in_array($plan->status, [1, 2])) {
                                                    $statusClass = 'bg-secondary'; // Created, Requested
                                                } elseif (in_array($plan->status, [3, 4, 5, 6, 7])) {
                                                    $statusClass = 'bg-success'; // Approved states
                                                } elseif (in_array($plan->status, [8, 9, 11, 12])) {
                                                    $statusClass = 'bg-danger'; // Disapproved states
                                                } elseif ($plan->status == 10) {
                                                    $statusClass = 'bg-warning'; // Request Explanation
                                                } else {
                                                    $statusClass = 'bg-secondary'; // Default
                                                }

                                                // Format amount
                                                $amount = $plan->total_amount
                                                    ? 'Rp ' . number_format($plan->total_amount, 2, ',', '.')
                                                    : '-';

                                                // Format date
                                                $createdDate = $plan->created_at
                                                    ? $plan->created_at->format('d M Y')
                                                    : '-';
                                            @endphp
                                            <tr>
                                                <td>{{ $departmentName }}</td>
                                                <td>{{ $plan->sub_id }}</td>
                                                <td>{{ $plan->purpose ?: '-' }}</td>
                                                <td class="text-center">
                                                    @if ($plan->status == 1)
                                                        <span class="badge bg-warning">DRAFT</span>
                                                    @elseif ($plan->status == 2)
                                                        <span class="badge bg-secondary">UNDER REVIEW KADEP</span>
                                                    @elseif ($plan->status == 3)
                                                        <span class="badge" style="background-color: #0080ff">APPROVED
                                                            BY KADEPT</span>
                                                    @elseif ($plan->status == 4)
                                                        <span class="badge" style="background-color: #0080ff">APPROVED
                                                            BY KADIV</span>
                                                    @elseif ($plan->status == 5)
                                                        <span class="badge"
                                                            style="background-color: #0080ff">ACKNOWLEDGED BY DIC</span>
                                                    @elseif ($plan->status == 6)
                                                        <span class="badge" style="background-color: #0080ff">APPROVED
                                                            BY PIC BUDGETING</span>
                                                    @elseif ($plan->status == 7)
                                                        <span class="badge" style="background-color: #0080ff">APPROVED
                                                            BY KADEP BUDGETING</span>
                                                    @elseif ($plan->status == 8)
                                                        <span class="badge bg-danger">DISAPPROVED BY KADEP</span>
                                                    @elseif ($plan->status == 9)
                                                        <span class="badge bg-danger">DISAPPROVED BY KADIV</span>
                                                    @elseif ($plan->status == 10)
                                                        <span class="badge bg-danger">REQUEST EXPLANATION</span>
                                                    @elseif ($plan->status == 11)
                                                        <span class="badge bg-danger">DISAPPROVED BY PIC
                                                            BUDGETING</span>
                                                    @elseif ($plan->status == 12)
                                                        <span class="badge bg-danger">DISAPPROVED BY KADEP
                                                            BUDGETING</span>
                                                    @else
                                                        <span class="badge bg-danger">REJECTED</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('departments.detail', ['dpt_id' => $plan->dpt_id, 'sub_id' => $plan->sub_id]) }}"
                                                        class="btn d-inline-flex align-items-center justify-content-center text-white"
                                                        style="background-color: #0d6efd; width: 30px; height: 30px; border-radius: 3px; margin: 2px;"
                                                        title="View Details">
                                                        <i class="fa-solid fa-eye fs-6"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">No budget plans found!</td>
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
        // var win = navigator.platform.indexOf('Win') > -1;
        // if (win && document.querySelector('#sidenav-scrollbar')) {
        //     var options = {
        //         damping: '0.5'
        //     }
        //     Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        // }
    </script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="{{ asset('js/soft-ui-dashboard.min.js?v=1.0.3') }}"></script>
</body>

</html>
