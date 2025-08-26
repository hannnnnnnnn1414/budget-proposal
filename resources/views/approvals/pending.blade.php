<!DOCTYPE html>
<html lang="en">
<x-head></x-head>

<body class="g-sidenav-show bg-gray-100">
    <x-sidebar></x-sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
        <x-navbar :notifications="$notifications">Pending Approvals</x-navbar>
        <div class="container-fluid">
            <div class="row">
                <div class="card-header bg-danger">
                    <h4 style="font-weight: bold;" class="text-white">
                        <i class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>PENDING APPROVALS
                    </h4>
                </div>
                <div class="card rounded-0">
                    <div class="mt-4">
                        <label class="form-label">Search by Department or Account</label>
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
                            @if (session('sect') == 'Kadept')
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
                                                <td>
                                                    <span class="badge bg-warning">Requested</span>
                                                </td>
                                                <td>
                                                    <form action="{{ route('approvals.approve', $approval->sub_id) }}"
                                                        method="POST" class="approve-form" style="display:inline;">
                                                        @csrf
                                                        {{-- <button
                                                            class="btn btn-success d-inline-flex align-items-center justify-content-center"
                                                            style="width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                                                            title="Approve">
                                                            <i class="fa-solid fa-check fs-6"></i>
                                                        </button> --}}
                                                    </form>
                                                    {{-- <button
                                                        class="btn btn-danger d-inline-flex align-items-center justify-content-center"
                                                        style="width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                                                        title="Reject" data-bs-toggle="modal"
                                                        data-bs-target="#rejectModal-{{ $approval->sub_id }}">
                                                        <i class="fa-solid fa-times fs-6"></i>
                                                    </button> --}}
                                                    <form
                                                        action="{{ route('submissions.report', ['sub_id' => $approval->sub_id]) }}"
                                                        method="GET" style="display: inline;">
                                                        <button type="submit" class="btn text-white"
                                                            style="background-color: #0d6efd; border-radius: 10px; margin: 4px; height: 30px; text-align: center; padding: 0 12px;">
                                                            <span
                                                                style="display: inline-block; width: 100%; text-align: center;">Approval</span>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                            <div class="modal fade" id="rejectModal-{{ $approval->sub_id }}"
                                                tabindex="-1"
                                                aria-labelledby="rejectModalLabel-{{ $approval->sub_id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="rejectModalLabel-{{ $approval->sub_id }}">Reject
                                                                Submission: {{ $approval->sub_id }}</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form
                                                            action="{{ route('approvals.reject', $approval->sub_id) }}"
                                                            method="POST" class="disapprove-form">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label for="remark-{{ $approval->sub_id }}"
                                                                        class="form-label">Reason for Rejection</label>
                                                                    <textarea class="form-control" id="remark-{{ $approval->sub_id }}" name="remark" rows="4" required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit"
                                                                    class="btn btn-danger">Reject</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <tr>
                                                <td colspan="4">No Pending Approvals found!</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            @else
                                @forelse ($groupedAccounts as $dpt_id => $accounts)
                                    <h5 class="mt-4">Department: {{ $accounts->first()['dept_name'] }}</h5>
                                    <table id="myTable-{{ $dpt_id }}" class="table table-striped table-bordered">
                                        <thead>
                                            <tr class="text-center">
                                                <th>Account ID</th>
                                                <th>Number of Submissions</th>
                                                <th>Total Amount</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($accounts as $account)
                                                <tr class="text-center">
                                                    <td>{{ $account['acc_id'] }}</td>
                                                    <td>{{ $account['count_submissions'] }}</td>
                                                    <td>{{ number_format($account['total_amount'], 2) }}</td>
                                                    <td>
                                                        @if (session('sect') == 'DIC')
                                                            <form
                                                                action="{{ route('approvals.approveByAccount', [$account['acc_id'], $dpt_id]) }}"
                                                                method="POST" class="approve-form"
                                                                style="display:inline;">
                                                                @csrf
                                                                <button
                                                                    class="btn btn-success d-inline-flex align-items-center justify-content-center"
                                                                    style="width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                                                                    title="Approve All">
                                                                    <i class="fa-solid fa-check fs-6"></i>
                                                                </button>
                                                            </form>
                                                            <button
                                                                class="btn btn-danger d-inline-flex align-items-center justify-content-center"
                                                                style="width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                                                                title="Reject All" data-bs-toggle="modal"
                                                                data-bs-target="#rejectModal-{{ $account['acc_id'] }}-{{ $dpt_id }}">
                                                                <i class="fa-solid fa-times fs-6"></i>
                                                            </button>
                                                        @endif
                                                        <a href="{{ route('approvals.account-detail', [$account['acc_id'], $dpt_id]) }}"
                                                            class="btn d-inline-flex align-items-center justify-content-center text-white"
                                                            style="background-color: #0d6efd; width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                                                            title="View Details">
                                                            <i class="fa-solid fa-circle-info fs-6"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                @if (session('sect') == 'DIC')
                                                    <div class="modal fade"
                                                        id="rejectModal-{{ $account['acc_id'] }}-{{ $dpt_id }}"
                                                        tabindex="-1"
                                                        aria-labelledby="rejectModalLabel-{{ $account['acc_id'] }}-{{ $dpt_id }}"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title"
                                                                        id="rejectModalLabel-{{ $account['acc_id'] }}-{{ $dpt_id }}">
                                                                        Reject
                                                                        All Submissions for {{ $account['acc_id'] }}
                                                                        ({{ $dpt_id }})
                                                                    </h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>
                                                                <form
                                                                    action="{{ route('approvals.rejectByAccount', [$account['acc_id'], $dpt_id]) }}"
                                                                    method="POST" class="disapprove-form">
                                                                    @csrf
                                                                    <div class="modal-body">
                                                                        <div class="mb-3">
                                                                            <label
                                                                                for="remark-{{ $account['acc_id'] }}-{{ $dpt_id }}"
                                                                                class="form-label">Reason for
                                                                                Rejection</label>
                                                                            <textarea class="form-control" id="remark-{{ $account['acc_id'] }}-{{ $dpt_id }}" name="remark"
                                                                                rows="4" required></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button"
                                                                            class="btn btn-secondary"
                                                                            data-bs-dismiss="modal">Cancel</button>
                                                                        <button type="submit"
                                                                            class="btn btn-danger">Reject All</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div id="no-records-message-{{ $dpt_id }}"
                                        class="text-center mt-3 text-secondary" style="display: none;">
                                        No matching records found
                                    </div>
                                @empty
                                    <div class="text-center mt-3 text-secondary">
                                        No Pending Approvals found!
                                    </div>
                                @endforelse
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <x-footer></x-footer>
        </div>
    </main>
    <script>
        $(document).on('submit', '.approve-form', function(e) {
            e.preventDefault();
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
                    $.ajax({
                        url: form.attr('action'),
                        method: form.attr('method'),
                        data: form.serialize(),
                        success: function(response, status, xhr) {
                            if (xhr.status === 200 || xhr.status === 302) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Submission approved successfully.',
                                    confirmButtonColor: '#3085d6'
                                }).then(() => {
                                    location.reload();
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
                            let errorMessage = 'Something went wrong.';
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

        $(document).on('submit', '.disapprove-form', function(e) {
            e.preventDefault();
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
                    $.ajax({
                        url: form.attr('action'),
                        method: form.attr('method'),
                        data: form.serialize(),
                        success: function(response, status, xhr) {
                            if (xhr.status === 200 || xhr.status === 302) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Submission disapproved successfully.',
                                    confirmButtonColor: '#3085d6'
                                }).then(() => {
                                    location.reload();
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
                            let errorMessage = 'Something went wrong.';
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

        function searchTable() {
            var input, filter, tables, tr, td, i, j, txtValue;
            var visibleRows = 0;
            input = document.getElementById("cari");
            filter = input.value.toUpperCase();
            tables = document.querySelectorAll("table[id^='myTable-']");
            tables.forEach(function(table) {
                tr = table.getElementsByTagName("tr");
                var tableVisibleRows = 0;
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
                    if (display) tableVisibleRows++;
                }
                var dpt_id = table.id.replace('myTable-', '');
                var noRecordsMessage = document.getElementById("no-records-message-" + dpt_id);
                if (noRecordsMessage) {
                    noRecordsMessage.style.display = tableVisibleRows === 0 ? "block" : "none";
                }
                if (tableVisibleRows > 0) visibleRows++;
            });
            // Hide department headers if no visible rows in their tables
            document.querySelectorAll("h5.mt-4").forEach(function(header) {
                var dpt_id = header.nextElementSibling.id.replace('myTable-', '');
                var noRecordsMessage = document.getElementById("no-records-message-" + dpt_id);
                var table = document.getElementById("myTable-" + dpt_id);
                if (noRecordsMessage.style.display === "block") {
                    header.style.display = "none";
                    table.style.display = "none";
                } else {
                    header.style.display = "";
                    table.style.display = "";
                }
            });
        }
    </script>
    <x-sidebar-plugin></x-sidebar-plugin>
    <script src="{{ asset('js/core/popper.min.js') }}"></script>
    <script src="{{ asset('js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('js/plugins/chartjs.min.js') }}"></script>
    <script src="{{ asset('js/curve-chart.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            };
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="{{ asset('js/soft-ui-dashboard.min.js?v=1.0.3') }}"></script>
</body>

</html>
