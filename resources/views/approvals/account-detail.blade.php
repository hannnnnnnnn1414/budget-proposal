<!DOCTYPE html>
<html lang="en">
<x-head></x-head>

<body class="g-sidenav-show bg-gray-100">
    <x-sidebar></x-sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
        <x-navbar :notifications="$notifications">Account Detail: {{ $acc_id }}</x-navbar>
        <div class="container-fluid">
            <div class="row">
                <div class="card-header bg-danger">
                    <h4 style="font-weight: bold;" class="text-white">
                        <i class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>DETAIL SUBMISSIONS FOR ACCOUNT
                        {{ $acc_id }}
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
                                                @php
                                                    $sect = session('sect');
                                                    $statusMap = [
                                                        1 => ['text' => 'Created', 'class' => 'bg-info'],
                                                        2 => ['text' => 'Requested', 'class' => 'bg-warning'],
                                                        3 => ['text' => 'Approved by KADEP', 'class' => 'bg-success'],
                                                        4 => ['text' => 'Approved by KADIV', 'class' => 'bg-success'],
                                                        5 => ['text' => 'Approved by DIC', 'class' => 'bg-success'],
                                                        6 => [
                                                            'text' => 'Approved by PIC Budgeting',
                                                            'class' => 'bg-success',
                                                        ],
                                                        7 => [
                                                            'text' => 'Approved by KADEPT Budgeting',
                                                            'class' => 'bg-success',
                                                        ],
                                                        8 => ['text' => 'Disapproved by KADEP', 'class' => 'bg-danger'],
                                                        9 => ['text' => 'Disapproved by KADIV', 'class' => 'bg-danger'],
                                                        10 => ['text' => 'Disapproved by DIC', 'class' => 'bg-danger'],
                                                        11 => [
                                                            'text' => 'Disapproved by PIC Budgeting',
                                                            'class' => 'bg-danger',
                                                        ],
                                                        12 => [
                                                            'text' => 'Disapproved by KADEPT Budgeting',
                                                            'class' => 'bg-danger',
                                                        ],
                                                    ];
                                                    $statusInfo = $statusMap[$approval->status] ?? [
                                                        'text' => 'Unknown',
                                                        'class' => 'bg-secondary',
                                                    ];
                                                    if ($sect == 'Kadiv' && $approval->status == 3) {
                                                        $statusInfo = [
                                                            'text' => 'REQUIRES APPROVAL',
                                                            'class' => 'bg-warning',
                                                        ];
                                                    }
                                                @endphp
                                                <span
                                                    class="badge {{ $statusInfo['class'] }}">{{ $statusInfo['text'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <!-- Approve Button -->
                                                @if ($sect == 'Kadiv' && $approval->status == 3)
                                                    <form action="{{ route('approvals.approve', $approval->sub_id) }}"
                                                        method="POST" class="approve-form" style="display:inline;">
                                                        @csrf
                                                        {{-- <button class="btn btn-success d-inline-flex align-items-center justify-content-center"
                            style="width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                            title="Approve">
                            <i class="fa-solid fa-check fs-6"></i>
                        </button> --}}
                                                    </form>
                                                    <!-- Reject Button with Modal -->
                                                    {{-- <button class="btn btn-danger d-inline-flex align-items-center justify-content-center"
                        style="width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                        title="Reject" data-bs-toggle="modal"
                        data-bs-target="#rejectModal-{{ $approval->sub_id }}">
                        <i class="fa-solid fa-times fs-6"></i>
                    </button> --}}
                                                @endif
                                                <!-- Delete Button -->
                                                <!-- Delete Button -->
                                                <form action="{{ route('submissions.destroy', $approval->sub_id) }}"
                                                    method="POST" style="display:inline;"
                                                    onsubmit="return confirm('Are you sure you want to delete this Submission?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger text-white"
                                                        style="border-radius: 10px; margin: 4px; height: 30px; text-align: center; padding: 0 12px;">
                                                        <span
                                                            style="display: inline-block; width: 100%; text-align: center;">Delete</span>
                                                    </button>
                                                </form>

                                                <!-- Approval Button -->
                                                <a
                                                    href="{{ route('submissions.report', ['sub_id' => $approval->sub_id]) }}">
                                                    <button type="button" class="btn text-white"
                                                        style="background-color: #0d6efd; border-radius: 10px; margin: 4px; height: 30px; text-align: center; padding: 0 12px;">
                                                        <span
                                                            style="display: inline-block; width: 100%; text-align: center;">Approval</span>
                                                    </button>
                                                </a>

                                            </td>
                                        </tr>
                                        <!-- Reject Modal -->
                                        @if ($sect == 'Kadiv' && $approval->status == 3)
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
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="4">No Submissions found for this account!</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div id="no-records-message" class="text-center mt-3 text-secondary" style="display: none;">
                                No matching records found
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <x-footer></x-footer>
        </div>
    </main>
    <script>
        // Handle Approve form submission
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

        // Handle Disapprove form submission
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
            var input, filter, table, tr, td, i, j, txtValue;
            var visibleRows = 0;
            input = document.getElementById("cari");
            filter = input.value.toUpperCase();
            table = document.getElementById("myTable");
            tr = table.getElementsByTagName("tr");
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
                if (display) visibleRows++;
            }
            var noRecordsMessage = document.getElementById("no-records-message");
            noRecordsMessage.style.display = visibleRows === 0 ? "block" : "none";
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
