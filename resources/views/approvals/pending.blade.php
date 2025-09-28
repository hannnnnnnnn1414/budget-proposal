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
                                                        @if (session('sect') == 'DIC' || session('sect') == 'Kadiv')
                                                            <!-- Approve Button -->
                                                            <form
                                                                action="{{ route('approvals.approveByAccount', [$account['acc_id'], $dpt_id]) }}"
                                                                method="POST" class="approve-form"
                                                                style="display:inline;">
                                                                @csrf
                                                                <button type="button"
                                                                    class="btn btn-success btn-sm approve-btn"
                                                                    data-acc-id="{{ $account['acc_id'] }}"
                                                                    data-dpt-id="{{ $dpt_id }}"
                                                                    data-account-info="{{ $account['acc_id'] }} - {{ $accounts->first()['dept_name'] }}">
                                                                    <i class="fa-solid fa-check me-1"></i>Approve
                                                                </button>
                                                            </form>

                                                            <!-- Disapprove Button -->
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm disapprove-btn"
                                                                data-acc-id="{{ $account['acc_id'] }}"
                                                                data-dpt-id="{{ $dpt_id }}"
                                                                data-account-info="{{ $account['acc_id'] }} - {{ $accounts->first()['dept_name'] }}">
                                                                <i class="fa-solid fa-times me-1"></i>Disapprove
                                                            </button>
                                                        @endif


                                                        <!-- Lihat Button -->
                                                        <a href="{{ route('approvals.account-detail', [$account['acc_id'], $dpt_id]) }}"
                                                            class="btn btn-primary btn-sm">
                                                            <i class="fa-solid fa-eye me-1"></i>Lihat
                                                        </a>
                                                    </td>
                                                </tr>
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
        // SweetAlert untuk Approve
        $(document).on('click', '.approve-btn', function(e) {
            e.preventDefault();

            const accId = $(this).data('acc-id');
            const dptId = $(this).data('dpt-id');
            const accountInfo = $(this).data('account-info');
            const form = $(this).closest('form');

            Swal.fire({
                title: 'Konfirmasi Persetujuan',
                html: `Apakah Anda yakin ingin menyetujui semua pengajuan untuk account <strong>${accountInfo}</strong>?`,
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
                        text: 'Sedang menyetujui pengajuan account',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit form via AJAX
                    $.ajax({
                        url: form.attr('action'),
                        method: form.attr('method'),
                        data: form.serialize(),
                        success: function(response) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Semua pengajuan untuk account berhasil disetujui.',
                                icon: 'success',
                                confirmButtonColor: '#28a745',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            let errorMessage = 'Terjadi kesalahan saat menyetujui pengajuan.';
                            if (xhr.responseJSON?.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                title: 'Gagal!',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonColor: '#dc3545',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });

        // SweetAlert untuk Disapprove (gantikan modal)
        $(document).on('click', '.disapprove-btn', function(e) {
            e.preventDefault();

            const accId = $(this).data('acc-id');
            const dptId = $(this).data('dpt-id');
            const accountInfo = $(this).data('account-info');

            Swal.fire({
                title: 'Alasan Penolakan',
                html: `Masukkan alasan penolakan untuk account <strong>${accountInfo}</strong>:`,
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
                        text: 'Sedang menolak pengajuan account',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Kirim data ke server
                    $.ajax({
                        url: `{{ url('approvals/reject-by-account') }}/${accId}/${dptId}`,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            remark: result.value
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Semua pengajuan untuk account berhasil ditolak.',
                                icon: 'success',
                                confirmButtonColor: '#28a745',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            let errorMessage = 'Terjadi kesalahan saat menolak pengajuan.';
                            if (xhr.responseJSON?.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                title: 'Gagal!',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonColor: '#dc3545',
                                confirmButtonText: 'OK'
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
