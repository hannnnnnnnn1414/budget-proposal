<!DOCTYPE html>
<html lang="en">
<x-head></x-head>

<body class="g-sidenav-show bg-gray-100">
    <x-sidebar></x-sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
        <x-navbar :notifications="$notifications">Daftar Purpose - {{ $accountName }}</x-navbar>
        <div class="container-fluid">
            <div class="row mt-4">
                <div class="col-lg-12 mb-lg-0 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-danger d-flex justify-content-between align-items-center">
                            <h4 style="font-weight: bold;" class="text-white">
                                <i class="fa-solid fa-table fs-4 text-white me-3"></i>
                                Daftar Purpose untuk {{ $accountName }} ({{ $department->department }})
                            </h4>
                            <form method="GET"
                                action="{{ route('purposes.list', ['acc_id' => $acc_id, 'dept_id' => $dept_id]) }}"
                                class="d-flex">
                                <select name="year" onchange="this.form.submit()" class="form-select me-2"
                                    style="width: 80px;">
                                    @foreach ($years as $y)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                            {{ $y }}</option>
                                    @endforeach
                                </select>
                                <select name="submission_type" onchange="this.form.submit()" class="form-select me-2"
                                    style="width: 180px;">
                                    <option value="">-- Semua Pengajuan --</option>
                                    <option value="asset" {{ $submission_type == 'asset' ? 'selected' : '' }}>ASSET
                                    </option>
                                    <option value="expenditure"
                                        {{ $submission_type == 'expenditure' ? 'selected' : '' }}>EXPENDITURE</option>
                                </select>
                            </form>
                        </div>
                        <div class="card-body p-3">
                            <div class="table-responsive"
                                style="max-height: 500px; overflow-y: auto; overflow-x: auto; width: 100%;">
                                <table class="table table-striped" style="min-width: 800px;">
                                    <thead style="position: sticky; top: 0; z-index: 15; background-color: white;">
                                        <tr>
                                            <th style="min-width: 300px;" class="sticky-th">Purpose</th>
                                            <th style="min-width: 150px;" class="text-center">Sub ID</th>
                                            <th style="min-width: 150px;" class="text-center">Amount (Rp)</th>
                                            <th style="min-width: 150px;" class="text-center">Created At</th>
                                            <th style="min-width: 150px;" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($purposes as $purpose)
                                            <tr>
                                                <td>{{ $purpose->purpose }}</td>
                                                <td class="text-center">{{ $purpose->sub_id }}</td>
                                                <td class="text-center">
                                                    {{ number_format($purpose->total_price, 2, ',', '.') }}</td>
                                                <td class="text-center">{{ $purpose->created_at->format('d-m-Y') }}
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('submissions.report', ['sub_id' => $purpose->sub_id]) }}"
                                                        class="btn btn-primary btn-sm">
                                                        <i class="fa-solid fa-eye me-1"></i>Lihat
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <button onclick="history.back()" type="button"
                                    class="btn btn-secondary me-2">Back</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <x-footer></x-footer>
        </div>
    </main>
    <script src="{{ asset('js/core/popper.min.js') }}"></script>
    <script src="{{ asset('js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('js/soft-ui-dashboard.min.js?v=1.0.3') }}"></script>
</body>

</html>
