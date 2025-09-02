<!DOCTYPE html>
<html lang="en">

<x-head></x-head>

<body class="g-sidenav-show bg-gray-100">
    <x-sidebar></x-sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
        <x-navbar :notifications="$notifications">Dashboard</x-navbar>
        <form action="{{ route('submissions.clear-session') }}" method="POST">
            @csrf
        </form>
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
                                            @if (($sect === 'Kadiv' && !$dept_id) || ($sect === 'DIC' && $div_id && !$dept_id))
                                                <th style="min-width: 200px; background-color: white; box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);"
                                                    class="text-center">Action</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($accountData as $data)
                                            <tr>
                                                <td
                                                    style="position: sticky; left: 0; z-index: 10; background-color: white; box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);">
                                                    @if (($sect === 'Kadiv' && !$dept_id) || ($sect === 'DIC' && $div_id && !$dept_id))
                                                        {{ $data->dpt_id }}
                                                    @else
                                                        {{ $data->acc_id }}
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
                                                            <button onclick="approveDepartment('{{ $data->dpt_id }}')"
                                                                class="btn btn-success btn-sm">
                                                                <i class="fa-solid fa-check me-1"></i>Approve
                                                            </button>
                                                            <button data-bs-toggle="modal"
                                                                data-bs-target="#rejectDepartmentModal-{{ $data->dpt_id }}"
                                                                class="btn btn-danger btn-sm">
                                                                <i class="fa-solid fa-times me-1"></i>Disapprove
                                                            </button>
                                                        @endif
                                                        <a href="{{ route('index-all', ['dept_id' => $data->dpt_id, 'year' => $year, 'submission_type' => $submission_type, 'div_id' => $div_id]) }}"
                                                            class="btn btn-primary btn-sm">
                                                            <i class="fa-solid fa-eye me-1"></i>Lihat
                                                        </a>
                                                    </td>
                                                @else
                                                    <td class="text-center">
                                                        <a href="{{ route('approvals.pending', ['acc_id' => $data->acc_id]) }}"
                                                            class="btn btn-primary btn-sm">
                                                            <i class="fa-solid fa-eye me-1"></i>Lihat
                                                        </a>
                                                    </td>
                                                @endif
                                            </tr>
                                            @if (($sect === 'Kadiv' && !$dept_id) || ($sect === 'DIC' && $div_id && !$dept_id))
                                                @if ($data->count_submissions > 0)
                                                    <div class="modal fade"
                                                        id="rejectDepartmentModal-{{ $data->dpt_id }}" tabindex="-1"
                                                        aria-labelledby="rejectDepartmentModalLabel-{{ $data->dpt_id }}"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-danger text-white">
                                                                    <h5 class="modal-title text-white"
                                                                        id="rejectDepartmentModalLabel-{{ $data->dpt_id }}">
                                                                        Reject Department {{ $data->department }}
                                                                    </h5>
                                                                    <button type="button"
                                                                        class="btn-close btn-close-white"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form
                                                                        id="rejectDepartmentForm-{{ $data->dpt_id }}"
                                                                        action="{{ route('approvals.reject-department', ['dpt_id' => $data->dpt_id]) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        @method('POST')
                                                                        <div class="mb-3">
                                                                            <label for="remark-{{ $data->dpt_id }}"
                                                                                class="form-label">Reason for
                                                                                Rejection</label>
                                                                            <textarea class="form-control" id="remark-{{ $data->dpt_id }}" name="remark" rows="4" required
                                                                                placeholder="Enter reason for rejection"></textarea>
                                                                        </div>
                                                                        <div class="d-grid gap-2">
                                                                            <button type="submit"
                                                                                class="btn btn-danger">Reject
                                                                                Department</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
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

            <!-- Total Budget by Year Chart -->
            {{-- <div class="row mt-4">
                <div class="col-lg-12 mb-lg-0 mb-4">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6>Total Annual Budget by Year</h6>
                        </div>
                        <div class="card-body p-3">
                            <canvas id="budgetChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div> --}}

            <x-footer></x-footer>
        </div>
    </main>

    <!-- Core JS Files -->
    <script src="{{ asset('js/core/popper.min.js') }}"></script>
    <script src="{{ asset('js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('js/plugins/chartjs.min.js') }}"></script>
    <script>
        // Initialize Total Budget by Year Chart
        var ctx = document.getElementById('budgetChart').getContext('2d');
        var budgetChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($years),
                datasets: [{
                    label: 'Total Budget (Rp)',
                    data: @json($budgetValues),
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Rp' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });

        // Scrollbar initialization
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }

        // [MODIFIKASI] Fungsi untuk approve departemen via AJAX
        function approveDepartment(dpt_id) {
            if (confirm('Apakah Anda yakin ingin menyetujui semua pengajuan untuk departemen ini?')) {
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
                            alert('Semua pengajuan untuk departemen berhasil disetujui.');
                            location.reload();
                        } else {
                            alert('Gagal menyetujui pengajuan: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menyetujui pengajuan.');
                    });
            }
        }
    </script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="{{ asset('js/soft-ui-dashboard.min.js?v=1.0.3') }}"></script>
</body>

</html>
