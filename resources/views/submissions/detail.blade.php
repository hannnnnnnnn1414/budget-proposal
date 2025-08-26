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
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
        <x-navbar :notifications="$notifications">
            Account Budgeting</x-navbar>
        <div class="container-fluid">
            <div class="row">
                <div class="card-header bg-danger">
                    <h4 style="font-weight: bold;" class="text-white"><i
                            class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>DETAIL {{ $account_name }}
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
                        <div class="card-body">
                            <div class="table-responsive">
                                @if (Session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                @if (Session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif
                                <table id="myTable" class="table table-striped table-bordered">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Kode</th>
                                            <th>Purpose</th>
                                            <th>Submission Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($submissions as $submission)
                                            <tr class="text-center">
                                                <td>{{ $submission->sub_id }}</td>
                                                <td>{{ $submission->purpose }}</td>
                                                <td>{{ $submission->created_at->format('d-m-Y') }}</td>
                                                <td class="text-center">
                                                    @if ($submission->status == 1)
                                                        <span class="badge bg-warning">DRAFT</span>
                                                    @elseif ($submission->status == 2)
                                                        <span class="badge bg-secondary">UNDER REVIEW KADEP</span>
                                                    @elseif ($submission->status == 3)
                                                        <span class="badge" style="background-color: #0080ff">APPROVED
                                                            BY KADEPT</span>
                                                    @elseif ($submission->status == 4)
                                                        <span class="badge" style="background-color: #0080ff">APPROVED
                                                            BY KADIV</span>
                                                    @elseif ($submission->status == 5)
                                                        <span class="badge" style="background-color: #0080ff">APPROVED
                                                            BY DIC</span>
                                                    @elseif ($submission->status == 6)
                                                        <span class="badge" style="background-color: #0080ff">APPROVED
                                                            BY PIC BUDGETING</span>
                                                    @elseif ($submission->status == 7)
                                                        <span class="badge" style="background-color: #0080ff">APPROVED
                                                            BY KADEP BUDGETING</span>
                                                    @elseif ($submission->status == 8)
                                                        <span class="badge bg-danger">DISAPPROVE BY KADEP</span>
                                                    @elseif ($submission->status == 9)
                                                        <span class="badge bg-danger">DISAPPROVE BY KADIV</span>
                                                    @elseif ($submission->status == 10)
                                                        <span class="badge bg-danger">DISAPPROVE BY DIC</span>
                                                    @elseif ($submission->status == 11)
                                                        <span class="badge bg-danger">DISAPPROVE BY PIC BUDGETING</span>
                                                    @elseif ($submission->status == 12)
                                                        <span class="badge bg-danger">DISAPPROVE BY KADEP
                                                            BUDGETING</span>
                                                    @else
                                                        <span class="badge bg-danger">REJECTED</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if (in_array($submission->status, [1, 8]))
                                                        <form
                                                            action="{{ route('submissions.destroy', $submission->sub_id) }}"
                                                            method="POST" style="display:inline;" class="delete-form"
                                                            data-sub-id="{{ $submission->sub_id }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button
                                                                class="btn btn-danger d-inline-flex align-items-center justify-content-center"
                                                                style="width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                                                                title="Delete">
                                                                <i class="fa-solid fa-trash fs-6"></i>
                                                            </button>
                                                        </form>
                                                        <a href="{{ route('submissions.report', ['sub_id' => $submission->sub_id]) }}"
                                                            class="btn d-inline-flex align-items-center justify-content-center text-white"
                                                            style="background-color: #0d6efd; width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                                                            title="Detail">
                                                            <i class="fa-solid fa-circle-info fs-6"></i>
                                                        </a>
                                                    @else
                                                        <a href="{{ route('submissions.report', ['sub_id' => $submission->sub_id]) }}"
                                                            class="btn d-inline-flex align-items-center justify-content-center text-white"
                                                            style="background-color: #0d6efd; width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                                                            title="Detail">
                                                            <i class="fa-solid fa-circle-info fs-6"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8">No Submissions found!</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div id="no-records-message" class="text-center mt-3 text-secondary"
                                    style="display: none;">
                                    No matching records found
                                </div>
                                <br>
                                <button onclick="history.back()" type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Back</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <x-footer></x-footer>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const subId = this.getAttribute('data-sub-id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: `Do you want to delete submission ${subId}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit(); // Submit the form if confirmed
                    }
                });
            });
        });
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
    </script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="{{ asset('js/soft-ui-dashboard.min.js?v=1.0.3') }}"></script>
</body>

</html>
