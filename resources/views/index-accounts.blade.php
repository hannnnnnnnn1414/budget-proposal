<!DOCTYPE html>
<html lang="en">

<x-head></x-head>

<body class="g-sidenav-show bg-gray-100">
    <x-sidebar></x-sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
        <x-navbar :notifications="$notifications">Account Submission Totals - {{ $department->department }}</x-navbar>
        <div class="container-fluid">
            <div class="row">
                <!-- Account Submission Totals Table -->
                <div class="mb-4 w-100">
                    <div class="card h-100">
                        <div class="card-header bg-danger d-flex justify-content-between align-items-center">
                            <h4 style="font-weight: bold;" class="text-white">
                                <i class="fa-solid fa-table fs-4 text-white me-3"></i>Account Submission Totals -
                                {{ $department->department }}
                            </h4>
                            <div class="d-flex align-items-center">
                                <!-- Tombol Kembali ke Department Submission Totals -->
                                <a href="{{ route('index') }}" class="btn btn-light me-2">
                                    <i class="fa-solid fa-arrow-left me-2"></i>Kembali
                                </a>
                                <!-- Tombol Upload Data hanya untuk dept 6121 -->
                                @if (Auth::user()->dept === '6121')
                                    <button type="button" class="btn btn-light me-2" data-bs-toggle="modal"
                                        data-bs-target="#uploadModal">
                                        <i class="fa-solid fa-upload me-2"></i>Upload Data
                                    </button>
                                @endif
                                <form method="GET" action="{{ route('index.accounts') }}" class="d-flex">
                                    <input type="hidden" name="dpt_id" value="{{ $dpt_id }}">
                                    <select name="submission_type" onchange="this.form.submit()"
                                        class="form-select me-2" style="width: 180px;">
                                        <option value="">-- All Submissions --</option>
                                        <option value="asset" {{ $submission_type == 'asset' ? 'selected' : '' }}>ASSET
                                        </option>
                                        <option value="expenditure"
                                            {{ $submission_type == 'expenditure' ? 'selected' : '' }}>EXPENDITURE
                                        </option>
                                    </select>
                                    <select name="year" onchange="this.form.submit()" class="form-select me-2"
                                        style="width: 80px;">
                                        @foreach ($years as $y)
                                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                                {{ $y }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div class="table-responsive"
                                style="max-height: 500px; overflow-y: auto; overflow-x: auto; width: 100%;">
                                <table class="table table-striped" style="min-width: 800px;">
                                    <thead style="position: sticky; top: 0; z-index: 15; background-color: white;">
                                        <tr>
                                            <th style="min-width: 200px;">Account Budget</th>
                                            <th style="min-width: 150px;" class="text-center">{{ $year }} (Last
                                                Year)</th>
                                            <th style="min-width: 150px;" class="text-center">{{ $year + 1 }}
                                                (Figure Outlook)</th>
                                            <th style="min-width: 150px;" class="text-center">{{ $year + 1 }}
                                                (Budget Proposal)</th>
                                            <th style="min-width: 150px;" class="text-center">Variance Last Year</th>
                                            <th style="min-width: 100px;" class="text-center">%</th>
                                            <th style="min-width: 150px;" class="text-center">Variance Budget Propose
                                            </th>
                                            <th style="min-width: 100px;" class="text-center">%</th>
                                            <th style="min-width: 100px;" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($accountData as $data)
                                            <tr>
                                                <td>{{ $data->account }}</td>
                                                <td class="text-center">
                                                    {{ number_format($data->total_previous_year, 2, ',', '.') }}</td>
                                                <td class="text-center">
                                                    {{ number_format($data->total_current_year_given, 2, ',', '.') }}
                                                </td>
                                                <td class="text-center">
                                                    {{ number_format($data->total_current_year_requested, 2, ',', '.') }}
                                                </td>
                                                <td class="text-center">
                                                    {{ number_format($data->variance_last_year, 2, ',', '.') }}</td>
                                                <td class="text-center">
                                                    {{ number_format($data->percentage_change_last_year, 2, ',', '.') }}%
                                                </td>
                                                <td class="text-center">
                                                    {{ number_format($data->variance_budget_given, 2, ',', '.') }}</td>
                                                <td class="text-center">
                                                    {{ number_format($data->percentage_change_outlook, 2, ',', '.') }}%
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('approvals.pending', ['acc_id' => $data->acc_id, 'dpt_id' => $dpt_id, 'year' => $year, 'submission_type' => $submission_type]) }}"
                                                        class="btn btn-primary btn-sm">
                                                        <i class="fa-solid fa-eye me-1"></i>Lihat
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        <!-- Total Row -->
                                        <tr style="font-weight: bold; position: sticky; bottom: 0; z-index: 10;"
                                            class="bg-danger">
                                            <td class="text-white">{{ $accountTotal->account }}</td>
                                            <td class="text-white text-center">
                                                <a href="{{ route('reports.workcenterReport', ['wct_id' => 'all', 'year' => $year - 1, 'submission_type' => $submission_type, 'dept_id' => $dpt_id]) }}"
                                                    class="text-white text-decoration-none">
                                                    {{ number_format($accountTotal->total_previous_year ?? 0, 2, ',', '.') }}
                                                </a>
                                            </td>
                                            <td class="text-white text-center">
                                                <a href="{{ route('reports.workcenterReport', ['wct_id' => 'all', 'year' => $year, 'submission_type' => $submission_type, 'dept_id' => $dpt_id]) }}"
                                                    class="text-white text-decoration-none">
                                                    {{ number_format($accountTotal->total_current_year_given ?? 0, 2, ',', '.') }}
                                                </a>
                                            </td>
                                            <td class="text-white text-center">
                                                <a href="{{ route('reports.workcenterReport', ['wct_id' => 'all', 'year' => $year, 'submission_type' => $submission_type, 'dept_id' => $dpt_id]) }}"
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
                                            <td class="text-center"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Upload -->
                @if (Auth::user()->dept === '6121')
                    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="uploadModalLabel">Upload Data</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('budget.upload-fy-lo') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="dept_id" value="{{ $dpt_id }}">
                                        <div class="mb-3">
                                            <label for="type" class="form-label">Data Type</label>
                                            <select name="type" class="form-select" required>
                                                <option value="last_year">Last Year</option>
                                                <option value="outlook">Figure Outlook</option>
                                                <option value="proposal">Proposal</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="file" class="form-label">Upload File (Excel)</label>
                                            <input type="file" name="file" class="form-control"
                                                accept=".xlsx,.xls" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Upload</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <x-footer></x-footer>
            </div>
    </main>

    <!-- Scripts tetap sama seperti di index.blade.php -->
    <script src="{{ asset('js/core/popper.min.js') }}"></script>
    <script src="{{ asset('js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('js/plugins/chartjs.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <!-- Scripts Chart tetap sama, tidak perlu ditampilkan ulang -->
</body>

</html>
