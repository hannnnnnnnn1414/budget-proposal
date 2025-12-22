<!DOCTYPE html>
<html lang="en">
<x-head></x-head>

<body class="g-sidenav-show bg-gray-100">
    <x-sidebar></x-sidebar>

    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
        <x-navbar>Budget Revision Upload</x-navbar>

        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-danger">
                            <h4 style="font-weight: bold;" class="text-white">
                                <i class="fa-solid fa-file-pen fs-4 me-2 text-white me-3"></i>
                                UPLOAD BUDGET REVISION - RE-BUDGET PLAN
                            </h4>
                        </div>

                        <div class="card-body">
                            <!-- TAMPILAN ERROR SUMMARY -->
                            @if (session('error_summary'))
                                <div class="card border-danger mb-4">
                                    <div class="card-header bg-danger text-white py-2">
                                        <h6 class="mb-0 text-white">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            Upload Selesai dengan {{ session('error_summary.total_failed') }} Masalah
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- Budget Mismatch -->
                                        @if (!empty(session('error_summary.budget_errors')))
                                            <div class="mb-4">
                                                <h6 class="text-danger mb-3">
                                                    <i class="fas fa-balance-scale me-1"></i> Ketidaksesuaian Budget
                                                </h6>
                                                @foreach (session('error_summary.budget_errors') as $budgetError)
                                                    <div class="alert alert-light border border-danger mb-2">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <span
                                                                    class="fw-bold">{{ $budgetError['account'] }}</span>
                                                                <span class="text-muted ms-2">•
                                                                    {{ $budgetError['month'] }}</span>
                                                            </div>
                                                            <span
                                                                class="badge bg-{{ strpos($budgetError['difference'], '-') === 0 ? 'success' : 'danger' }}">
                                                                {{ $budgetError['difference'] }}
                                                            </span>
                                                        </div>
                                                        <div class="mt-1">
                                                            <small class="text-muted">
                                                                Budget: <strong>Rp
                                                                    {{ $budgetError['budget_final'] }}</strong> •
                                                                Upload: <strong>Rp
                                                                    {{ $budgetError['total_upload'] }}</strong>
                                                            </small>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        <!-- Error Categories -->
                                        @if (!empty(session('error_summary.by_type')))
                                            <div class="mb-4">
                                                <h6 class="text-danger mb-3">
                                                    <i class="fas fa-tags me-1"></i> Jenis Error
                                                </h6>
                                                <div class="row">
                                                    @foreach (session('error_summary.by_type') as $type => $data)
                                                        <div class="col-md-6 mb-3">
                                                            <div class="card border border-light">
                                                                <div class="card-body p-3">
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center mb-2">
                                                                        <span
                                                                            class="fw-bold">{{ $data['description'] }}</span>
                                                                        <span
                                                                            class="badge bg-warning text-dark">{{ $data['count'] }}</span>
                                                                    </div>
                                                                    <p class="small text-muted mb-1">
                                                                        {{ implode(', ', array_slice($data['accounts'], 0, 2)) }}
                                                                        @if (count($data['accounts']) > 2)
                                                                            dan {{ count($data['accounts']) - 2 }}
                                                                            lainnya
                                                                        @endif
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Failed Accounts List -->
                                        @if (session('failed_accounts'))
                                            <div>
                                                <h6 class="text-danger mb-3">
                                                    <i class="fas fa-file-excel me-1"></i> Sheet Bermasalah
                                                </h6>
                                                <div class="bg-light p-3 rounded">
                                                    <div class="d-flex flex-wrap gap-2" id="failedContainer">
                                                        @foreach (array_slice(session('failed_accounts') ?? [], 0, 10) as $account)
                                                            <span class="badge bg-light text-dark border"
                                                                title="Sheet: {{ $account }}">
                                                                {{ Str::limit($account, 20, '...') }}
                                                            </span>
                                                        @endforeach
                                                        @foreach (array_slice(session('failed_accounts') ?? [], 10) as $account)
                                                            <span
                                                                class="badge bg-light text-dark border d-none extra-failed"
                                                                title="Sheet: {{ $account }}">
                                                                {{ Str::limit($account, 20, '...') }}
                                                            </span>
                                                        @endforeach
                                                        @if (count(session('failed_accounts') ?? []) > 10)
                                                            <button
                                                                class="btn btn-sm btn-link p-0 text-danger toggle-btn clickable"
                                                                style="text-decoration: none; cursor: pointer;"
                                                                data-toggle="show">
                                                                +{{ count(session('failed_accounts') ?? []) - 10 }}
                                                                lainnya
                                                            </button>
                                                        @endif
                                                    </div>
                                                    <div class="mt-2 text-end">
                                                        <small class="text-muted">Total:
                                                            {{ count(session('failed_accounts') ?? []) }} sheet</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- TAMPILAN SUCCESS -->
                            @if (session('success'))
                                <div class="card border-success mb-4">
                                    <div class="card-header bg-success text-white py-2">
                                        <h6 class="mb-0 text-white">
                                            <i class="fas fa-check-circle me-2"></i>
                                            {{ session('success') }}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @if (!empty(session('success_by_account')))
                                            <div class="mb-4">
                                                <h6 class="text-success mb-3">
                                                    <i class="fas fa-check-circle me-1"></i> Successfully Uploaded
                                                    Accounts
                                                </h6>
                                                <div class="row">
                                                    @foreach (session('success_by_account') as $accountData)
                                                        <div class="col-md-6 mb-3">
                                                            <div class="card border border-light">
                                                                <div class="card-body p-3">
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center mb-2">
                                                                        <span
                                                                            class="fw-bold text-success">{{ $accountData['account'] }}</span>
                                                                        <span
                                                                            class="badge bg-success">{{ $accountData['count'] }}
                                                                            items</span>
                                                                    </div>
                                                                    <p class="small text-muted mb-1">
                                                                        Total: Rp
                                                                        {{ number_format($accountData['total'], 0, ',', '.') }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        @if (session('processed_rows') || session('total_amount'))
                                            <div class="row">
                                                @if (session('total_amount'))
                                                    <div class="col-md-12">
                                                        <div class="card bg-light border-0">
                                                            <div class="card-body p-3">
                                                                <h6 class="text-muted mb-1">Total Amount</h6>
                                                                <h4 class="mb-0">Rp
                                                                    {{ number_format(session('total_amount'), 0, ',', '.') }}
                                                                </h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Form Upload -->
                            <form id="uploadForm" enctype="multipart/form-data" method="POST"
                                action="{{ route('budget-revision.upload') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="file" class="form-label">File Excel *</label>
                                    <input type="file" class="form-control" name="file" id="file"
                                        accept=".xlsx,.xls" required>
                                    <small class="text-muted">Format harus sama dengan template budget plan. Max:
                                        10MB</small>
                                </div>
                                <button type="submit" class="btn btn-danger w-100" id="uploadButton">
                                    <i class="fas fa-upload me-2"></i>Upload Revision Data
                                    <span class="spinner-border spinner-border-sm d-none ms-2"
                                        id="uploadSpinner"></span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Data Revisions per Departemen -->
            <div class="card mt-4">
                <div class="card-header bg-secondary text-white py-2">
                    <h6 class="mb-0 text-white"><i class="fas fa-list me-2"></i>Data Revisions per Departemen</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <form method="GET" action="{{ route('budget-revision.index') }}">
                                    <select name="periode" class="form-control" onchange="this.form.submit()">
                                        <option value="">Semua Tahun</option>
                                        <option value="{{ date('Y') }}"
                                            {{ $periode == date('Y') ? 'selected' : '' }}>{{ date('Y') }}</option>
                                        <option value="{{ date('Y') + 1 }}"
                                            {{ $periode == date('Y') + 1 ? 'selected' : '' }}>{{ date('Y') + 1 }}
                                        </option>
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Jumlah Akun</th>
                                    <th>Total Items</th>
                                    <th>Total Amount</th>
                                    <th>Last Update</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($summaryData as $item)
                                    <tr>
                                        <td>{{ $item->account_count ?? 0 }}</td>
                                        <td>{{ $item->item_count ?? 0 }}</td>
                                        <td>Rp {{ $item->total_amount ?? '0' }}</td>
                                        <td>{{ $item->last_upload ?? '-' }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('budget-revision.delete') }}"
                                                class="delete-dept-form d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="dept" value="{{ $item->dept_code }}">
                                                <input type="hidden" name="periode" value="{{ $periode }}">
                                                <button type="submit" class="btn btn-sm btn-danger ms-1">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Tidak ada data untuk periode
                                            ini</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Card untuk Expense & CAPEX Revision -->
            <div class="card mt-4">
                <div class="card-body">
                    <!-- Expense Revision -->
                    <div id="revision-expense" class="revision-table" style="display: block;">
                        <div class="mt-4">
                            <label class="form-label">Account name or ID search</label>
                            <div class="input-group">
                                <input type="text" id="searchExpense" class="form-control"
                                    placeholder="Pencarian" onkeyup="searchRevisionTable('expenseTable')" />
                            </div>
                        </div>
                        <div class="table-responsive mt-3">
                            <table id="expenseTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th>Kode</th>
                                        <th>Account Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($expenseAccounts as $account)
                                        <tr>
                                            <td>{{ $account->acc_id }}</td>
                                            <td>{{ $account->account }}</td>
                                            <td class="text-center">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No Expense Accounts found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div id="no-records-expense" style="display:none;"
                                class="text-center mt-3 text-secondary">
                                No matching records found
                            </div>
                        </div>
                    </div>

                    <!-- CAPEX/ASSET Revision -->
                    <div id="revision-capex" class="revision-table" style="display: none;">
                        <div class="mt-4">
                            <label class="form-label">Account name or ID search</label>
                            <div class="input-group">
                                <input type="text" id="searchCapex" class="form-control" placeholder="Pencarian"
                                    onkeyup="searchRevisionTable('capexTable')" />
                            </div>
                        </div>
                        <div class="table-responsive mt-3">
                            <table id="capexTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th>Kode</th>
                                        <th>Account Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($capexAccounts as $account)
                                        <tr>
                                            <td>{{ $account->acc_id }}</td>
                                            <td>{{ $account->account }}</td>
                                            <td class="text-center">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No CAPEX Accounts found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div id="no-records-capex" style="display:none;" class="text-center mt-3 text-secondary">
                                No matching records found
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-footer></x-footer>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/core/popper.min.js') }}"></script>
    <script src="{{ asset('js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('js/soft-ui-dashboard.min.js?v=1.0.3') }}"></script>

    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            };
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>

    <!-- SweetAlert & AJAX Upload Script -->
    <script>
        $(document).ready(function() {
            $('#uploadForm').submit(function(e) {
                e.preventDefault();
                const fileInput = $('#file')[0];
                if (!fileInput.files.length) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan!',
                        text: 'Pilih file Excel terlebih dahulu!',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
                const file = fileInput.files[0];
                if (file.size / 1024 / 1024 > 10) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan!',
                        text: 'Ukuran file maksimal 10MB!',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi Upload',
                    text: 'Upload budget revision ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Upload!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#dc3545'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const formData = new FormData(this);
                        $('#uploadButton').prop('disabled', true);
                        $('#uploadSpinner').removeClass('d-none');

                        $.ajax({
                            url: $(this).attr('action'),
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                $('#uploadButton').prop('disabled', false);
                                $('#uploadSpinner').addClass('d-none');
                                if (response.success) {
                                    Swal.fire({
                                            icon: 'success',
                                            title: 'Upload Berhasil!',
                                            text: response.message,
                                            confirmButtonText: 'OK'
                                        })
                                        .then(() => location.reload());
                                } else {
                                    Swal.fire({
                                            icon: 'error',
                                            title: 'Upload Gagal!',
                                            text: response.message,
                                            confirmButtonText: 'OK'
                                        })
                                        .then(() => location.reload());
                                }
                            },
                            error: function(xhr) {
                                $('#uploadButton').prop('disabled', false);
                                $('#uploadSpinner').addClass('d-none');
                                const errorMsg = xhr.responseJSON ? xhr.responseJSON
                                    .message : 'Terjadi kesalahan saat upload!';
                                Swal.fire({
                                        icon: 'error',
                                        title: 'Upload Gagal!',
                                        text: errorMsg,
                                        confirmButtonText: 'OK'
                                    })
                                    .then(() => location.reload());
                            }
                        });
                    }
                });
            });

            // Delete confirmation
            $('.delete-dept-form, .delete-revision-form').on('submit', function(e) {
                e.preventDefault();
                const form = this;
                const submitBtn = $(form).find('button[type="submit"]');
                const originalText = submitBtn.html();
                const isDept = $(form).hasClass('delete-dept-form');
                const confirmText = isDept ? 'Hapus semua revision untuk departemen ini?' :
                    'Hapus revision ini? Data akan dihapus permanen.';

                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: confirmText,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#dc3545'
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitBtn.prop('disabled', true).html(
                            '<i class="fas fa-spinner fa-spin"></i>');
                        form.submit();
                    } else {
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Toggle failed accounts
            $(document).on('click', '.toggle-btn.clickable', function(e) {
                e.preventDefault();
                $('.extra-failed').toggleClass('d-none');
                const isHidden = $('.extra-failed').hasClass('d-none');
                const n = {{ count(session('failed_accounts') ?? []) - 10 }};
                $(this).text(isHidden ? `+${n} lainnya` : `-${n} lainnya`);
            });
        });
    </script>

    <!-- JavaScript untuk dropdown revision type (jika ada) -->
    <script>
        function showRevisionTable(type) {
            var expenseTable = document.getElementById('revision-expense');
            var capexTable = document.getElementById('revision-capex');
            if (type === 'expense') {
                expenseTable.style.display = 'block';
                capexTable.style.display = 'none';
            } else {
                expenseTable.style.display = 'none';
                capexTable.style.display = 'block';
            }
        }

        function searchRevisionTable(tableId) {
            var input = tableId === 'expenseTable' ? document.getElementById("searchExpense") : document.getElementById(
                "searchCapex");
            var filter = input.value.toUpperCase();
            var table = document.getElementById(tableId);
            var tr = table.getElementsByTagName("tr");
            var visibleRows = 0;

            for (var i = 1; i < tr.length; i++) {
                var display = false;
                for (var j = 0; j < tr[i].cells.length; j++) {
                    var td = tr[i].cells[j];
                    if (td) {
                        var txtValue = td.textContent || td.innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            display = true;
                            break;
                        }
                    }
                }
                tr[i].style.display = display ? "" : "none";
                if (display) visibleRows++;
            }

            var noRecordsId = tableId === 'expenseTable' ? "no-records-expense" : "no-records-capex";
            document.getElementById(noRecordsId).style.display = visibleRows === 0 ? "block" : "none";
        }
    </script>
</body>

</html>
