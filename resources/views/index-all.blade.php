<!DOCTYPE html>
<html lang="en">

<x-head></x-head>

<body class="g-sidenav-show bg-gray-100">
    <x-sidebar></x-sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
        <x-navbar :notifications="$notifications">Dashboard</x-navbar>
        {{-- <form action="{{ route('submissions.clear-session') }}" method="POST">
            @csrf
        </form> --}}
        <div class="container-fluid">
            <div class="row mt-4">
                <div class="col-lg-12 mb-lg-0 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-danger d-flex justify-content-between align-items-center">
                            <h4 style="font-weight: bold;" class="text-white">
                                <i class="fa-solid fa-table fs-4 text-white me-3"></i>
                                @if ($sect === 'Kadiv' && !$dept_id)
                                    Department Submission Totals
                                @elseif ($sect === 'DIC' && $div_id && !$dept_id)
                                    Department Submission Totals
                                @else
                                    Account Submission Totals
                                @endif
                            </h4>
                            <div class="d-flex">
                                @if (Auth::user()->dept === '6121')
                                    <button class="btn btn-light me-2" data-bs-toggle="modal"
                                        data-bs-target="#uploadModal">
                                        <i class="fa-solid fa-upload me-2"></i>Upload Data
                                    </button>
                                @endif
                                <form method="GET" action="{{ route('index-all') }}" class="d-flex">
                                    <select name="submission_type" onchange="this.form.submit()"
                                        class="form-select me-2" style="width: 180px;">
                                        <option value="">-- All Submissions --</option>
                                        <option value="asset" {{ $submission_type == 'asset' ? 'selected' : '' }}>
                                            ASSET
                                        </option>
                                        <option value="expenditure"
                                            {{ $submission_type == 'expenditure' ? 'selected' : '' }}>EXPENDITURE
                                        </option>
                                    </select>
                                    <select name="year" onchange="this.form.submit()" class="form-select me-2"
                                        style="width: 80px;">
                                        @foreach ($years as $y)
                                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                                {{ $y }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($dept_id)
                                        <input type="hidden" name="dept_id" value="{{ $dept_id }}">
                                    @endif
                                    <!-- [MODIFIKASI BARU] Tambahkan hidden input untuk div_id -->
                                    @if ($div_id)
                                        <input type="hidden" name="div_id" value="{{ $div_id }}">
                                    @endif
                                </form>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div class="table-responsive"
                                style="max-height: 500px; overflow-y: auto; overflow-x: auto; width: 100%; background-color: white;">
                                <table class="table table-striped" style="min-width: 800px;">
                                    <thead style="position: sticky; top: 0; z-index: 15; background-color: white;">
                                        <tr>
                                            <th style="min-width: 200px; position: sticky; top: 0; left: 0; z-index: 20; background-color: white; box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);"
                                                class="sticky-th">
                                                @if (($sect === 'Kadiv' && !$dept_id) || ($sect === 'DIC' && $div_id && !$dept_id))
                                                    Department
                                                @else
                                                    Account ID
                                                @endif
                                            </th>
                                            <th style="min-width: 200px; position: sticky; top: 0; left: 200px; z-index: 20; background-color: white; box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);"
                                                class="sticky-th">
                                                @if (($sect === 'Kadiv' && !$dept_id) || ($sect === 'DIC' && $div_id && !$dept_id))
                                                    Department Name
                                                @else
                                                    Account Name
                                                @endif
                                            </th>
                                            <th style="min-width: 150px; background-color: white; box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);"
                                                class="text-center">{{ $year }} (Last Year)</th>
                                            <th style="min-width: 150px; background-color: white; box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);"
                                                class="text-center">{{ $year + 1 }} (Figure Outlook)</th>
                                            <th style="min-width: 150px; background-color: white; box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);"
                                                class="text-center">{{ $year + 1 }} (Budget Proposal)</th>
                                            <th style="min-width: 150px; background-color: white; box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);"
                                                class="text-center">Variance Last Year</th>
                                            <th style="min-width: 150px; background-color: white; box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);"
                                                class="text-center">%</th>
                                            <th style="min-width: 150px; background-color: white; box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);"
                                                class="text-center">Variance Budget Propose</th>
                                            <th style="min-width: 150px; background-color: white; box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);"
                                                class="text-center">%</th>
                                            <th style="min-width: 200px; background-color: white; box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);"
                                                class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($accountData as $data)
                                            @if ($data->total_previous_year != 0 || $data->total_current_year_given != 0 || $data->total_current_year_requested != 0)
                                                <tr>
                                                    <td
                                                        style="position: sticky; left: 0; z-index: 10; background-color: white; box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);">
                                                        @if (($sect === 'Kadiv' && !$dept_id) || ($sect === 'DIC' && $div_id && !$dept_id))
                                                            {{ $data->dpt_id }}
                                                        @else
                                                            <div class="d-flex align-items-center">
                                                                <span>{{ $data->acc_id }}</span>
                                                                {{-- Tanda ada draft --}}
                                                                @if ($sect != 'Kadept' && $sect != 'Kadiv' && $sect != 'DIC')
                                                                    @php
                                                                        $hasDraft = \App\Models\BudgetPlan::where(
                                                                            'acc_id',
                                                                            $data->acc_id,
                                                                        )
                                                                            ->whereIn('status', [1, 8])
                                                                            ->whereYear('created_at', $year)
                                                                            ->exists();
                                                                    @endphp
                                                                    @if ($hasDraft)
                                                                        <span class="badge bg-danger ms-2"
                                                                            title="Has unsent draft">
                                                                            <i class="fa-solid fa-clock me-1"></i>Draft
                                                                        </span>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td
                                                        style="position: sticky; left: 200px; z-index: 10; background-color: white; box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);">
                                                        @if (($sect === 'Kadiv' && !$dept_id) || ($sect === 'DIC' && $div_id && !$dept_id))
                                                            {{ $data->department }}
                                                        @else
                                                            {{ $data->account }}
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        {{ number_format($data->total_previous_year, 2, ',', '.') }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ number_format($data->total_current_year_given, 2, ',', '.') }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ number_format($data->total_current_year_requested, 2, ',', '.') }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ number_format($data->variance_last_year, 2, ',', '.') }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ number_format($data->percentage_change_last_year, 2, ',', '.') }}%
                                                    </td>
                                                    <td class="text-center">
                                                        {{ number_format($data->variance_budget_given, 2, ',', '.') }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ number_format($data->percentage_change_outlook, 2, ',', '.') }}%
                                                    </td>
                                                    @if (($sect === 'Kadiv' && !$dept_id) || ($sect === 'DIC' && $div_id && !$dept_id))
                                                        <td class="text-center">
                                                            @if ($data->count_submissions > 0)
                                                                @if ($sect === 'Kadiv')
                                                                    @php
                                                                        $hasPendingSubmissions = \App\Models\BudgetPlan::where(
                                                                            'dpt_id',
                                                                            $data->dpt_id,
                                                                        )
                                                                            ->where(function ($query) {
                                                                                $query
                                                                                    ->where('status', 3)
                                                                                    ->orWhere('status', 10);
                                                                            })
                                                                            ->whereYear('created_at', $year)
                                                                            ->exists();
                                                                    @endphp

                                                                    @if ($hasPendingSubmissions)
                                                                        <button
                                                                            onclick="approveDepartment('{{ $data->dpt_id }}', '{{ $data->department }}')"
                                                                            class="btn btn-success btn-sm">
                                                                            <i
                                                                                class="fa-solid fa-check me-1"></i>Approve
                                                                        </button>
                                                                        <button
                                                                            onclick="rejectDepartment('{{ $data->dpt_id }}', '{{ $data->department }}')"
                                                                            class="btn btn-danger btn-sm">
                                                                            <i
                                                                                class="fa-solid fa-times me-1"></i>Disapprove
                                                                        </button>
                                                                    @endif
                                                                @endif
                                                            @endif

                                                            <a href="{{ route('index-all', ['dept_id' => $data->dpt_id, 'year' => $year, 'submission_type' => $submission_type, 'div_id' => $div_id]) }}"
                                                                class="btn btn-primary btn-sm">
                                                                <i class="fa-solid fa-eye me-1"></i>Lihat
                                                            </a>
                                                        </td>
                                                    @else
                                                        <td class="text-center">
                                                            @if (in_array($sect, ['Kadiv', 'DIC']))
                                                                <a href="{{ route('purposes.list', ['acc_id' => $data->acc_id, 'dept_id' => $dept_id, 'year' => $year, 'submission_type' => $submission_type]) }}"
                                                                    class="btn btn-primary btn-sm">
                                                                    <i class="fa-solid fa-eye me-1"></i>Lihat
                                                                </a>
                                                            @elseif (in_array($sect, ['Kadept', 'PIC P&B', 'Kadept P&B']))
                                                                <a href="{{ route('approvals.pending', ['acc_id' => $data->acc_id]) }}"
                                                                    class="btn btn-primary btn-sm">
                                                                    <i class="fa-solid fa-eye me-1"></i>Lihat
                                                                </a>
                                                            @else
                                                                <a href="{{ route('submissions.detail', ['acc_id' => $data->acc_id, 'year' => $year]) }}"
                                                                    class="btn btn-primary btn-sm">
                                                                    <i class="fa-solid fa-eye me-1"></i>Lihat
                                                                </a>
                                                            @endif
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endif
                                        @endforeach
                                        <!-- Total Row -->
                                        <tr style="font-weight: bold; position: sticky; bottom: 0; z-index: 10;"
                                            class="bg-danger">
                                            <td
                                                style="position: sticky; left: 0; bottom: 0; z-index: 30; background-color: #ea0606; color: white; text-align: center; box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);">
                                                {{ $accountTotal->account ?? $accountTotal->department }}
                                            </td>
                                            <td
                                                style="position: sticky; left: 200px; bottom: 0; z-index: 30; background-color: #ea0606; color: white; text-align: center; box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);">
                                            </td>
                                            <td class="text-white text-center">
                                                <a href="{{ route('reports.workcenterReport', ['wct_id' => 'all', 'year' => $year - 1, 'submission_type' => $submission_type]) }}"
                                                    class="text-white text-decoration-none">
                                                    {{ number_format($accountTotal->total_previous_year ?? 0, 2, ',', '.') }}
                                                </a>
                                            </td>
                                            <td class="text-white text-center">
                                                <a href="{{ route('reports.workcenterReport', ['wct_id' => 'all', 'year' => $year, 'submission_type' => $submission_type]) }}"
                                                    class="text-white text-decoration-none">
                                                    {{ number_format($accountTotal->total_current_year_given ?? 0, 2, ',', '.') }}
                                                </a>
                                            </td>
                                            <td class="text-white text-center">
                                                <a href="{{ route('reports.workcenterReport', ['wct_id' => 'all', 'year' => $year, 'submission_type' => $submission_type]) }}"
                                                    class="text-white text-decoration-none">
                                                    {{ number_format($accountTotal->total_current_year_requested ?? 0, 2, ',', '.') }}
                                                </a>
                                            </td>
                                            <td class="text-white text-center">
                                                {{ number_format($accountTotal->variance_last_year ?? 0, 2, ',', '.') }}
                                            </td>
                                            <td class="text-white text-center">
                                                {{ number_format($accountTotal->total_previous_year != 0 ? ($accountTotal->variance_last_year / $accountTotal->total_previous_year) * 100 : 0, 2, ',', '.') }}%
                                            </td>
                                            <td class="text-white text-center">
                                                {{ number_format($accountTotal->variance_budget_given ?? 0, 2, ',', '.') }}
                                            </td>
                                            <td class="text-white text-center">
                                                {{ number_format($accountTotal->total_current_year_given != 0 ? ($accountTotal->variance_budget_given / $accountTotal->total_current_year_given) * 100 : 0, 2, ',', '.') }}%
                                            </td>
                                            <td class="text-center text-white"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload Modal -->
            <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="uploadModalLabel">Upload Budget Data</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('budget.upload-fy-lo') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="uploadType" class="form-label">Data Type</label>
                                    <select class="form-select" id="uploadType" name="type" required>
                                        <option value="">Select Type</option>
                                        <option value="last_year">Last Year Data</option>
                                        <option value="outlook">Figure Outlook</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="uploadFile" class="form-label">Excel File</label>
                                    <input type="file" class="form-control" id="uploadFile" name="file"
                                        accept=".xlsx,.xls" required>
                                    <div class="form-text">Upload Excel file with budget data</div>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">Upload</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <x-footer></x-footer>
        </div>
    </main>

    <!-- Core JS Files -->
    <script src="{{ asset('js/core/popper.min.js') }}"></script>
    <script src="{{ asset('js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('js/plugins/chartjs.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Scrollbar initialization
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }

        // [MODIFIKASI] Fungsi untuk approve departemen via AJAX
        function approveDepartment(dpt_id, departmentName) {
            Swal.fire({
                title: 'Konfirmasi Persetujuan',
                html: `Apakah Anda yakin ingin menyetujui semua pengajuan untuk departemen <strong>${departmentName}</strong>?`,
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
                        text: 'Sedang menyetujui pengajuan departemen',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch('{{ url('approvals/approve-department') }}/' + dpt_id, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.message === 'All submissions for department approved successfully') {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Semua pengajuan untuk departemen berhasil disetujui.',
                                    icon: 'success',
                                    confirmButtonColor: '#28a745',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Gagal menyetujui pengajuan: ' + data.message,
                                    icon: 'error',
                                    confirmButtonColor: '#dc3545',
                                    confirmButtonText: 'OK'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat menyetujui pengajuan.',
                                icon: 'error',
                                confirmButtonColor: '#dc3545',
                                confirmButtonText: 'OK'
                            });
                        });
                }
            });
        }

        function rejectDepartment(dpt_id, departmentName) {
            Swal.fire({
                title: 'Alasan Penolakan',
                html: `Masukkan alasan penolakan untuk departemen <strong>${departmentName}</strong>:`,
                input: 'textarea',
                inputLabel: 'Alasan',
                inputPlaceholder: 'Masukkan alasan penolakan...',
                inputAttributes: {
                    'aria-label': 'Masukkan alasan penolakan'
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
                        text: 'Sedang menolak pengajuan departemen',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Kirim data ke server
                    fetch('{{ url('approvals/reject-department') }}/' + dpt_id, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                remark: result.value
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.message === 'All submissions for department rejected successfully') {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Semua pengajuan untuk departemen berhasil ditolak.',
                                    icon: 'success',
                                    confirmButtonColor: '#28a745',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Gagal menolak pengajuan: ' + data.message,
                                    icon: 'error',
                                    confirmButtonColor: '#dc3545',
                                    confirmButtonText: 'OK'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Error!',
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
