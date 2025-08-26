<!DOCTYPE html>
<html lang="en">

<x-head></x-head>

<body class="g-sidenav-show bg-gray-100">
    <x-sidebar></x-sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
        <x-navbar :notifications="$notifications">Workcenter Report</x-navbar>
        <div class="container-fluid py-4">
            <div class="row">
                <div class="mb-4 w-100">
                    <div class="card h-100">
                        <div class="card-header bg-danger d-flex justify-content-between align-items-center">
                            <h4 style="font-weight: bold;" class="text-white">
                                <i class="fa-solid fa-table fs-4 text-white me-3"></i>
                                Workcenter Submission Totals
                                @if ($acc_id && $account)
                                    - {{ $account->account }}
                                @endif
                            </h4>
                            <form method="GET"
                                action="{{ route('reports.workcenterReport', ['wct_id' => $wct_id ?? 'all', 'year' => $year]) }}"
                                class="d-flex">
                                <input type="hidden" name="acc_id" value="{{ $acc_id }}">
                                <select name="submission_type" onchange="this.form.submit()" class="form-select me-2"
                                    style="width: 180px;">
                                    <option value="">-- All Submissions --</option>
                                    <option value="asset" {{ $submission_type == 'asset' ? 'selected' : '' }}>ASSET
                                    </option>
                                    <option value="expenditure"
                                        {{ $submission_type == 'expenditure' ? 'selected' : '' }}>EXPENDITURE</option>
                                </select>
                                <select name="year" onchange="this.form.submit()" class="form-select me-2"
                                    style="width: 80px;">
                                    @foreach ($years as $y)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                        <div class="card-body p-3">
                            <div class="table-responsive"
                                style="max-height: 500px; overflow-y: auto; overflow-x: auto; width: 100%;">
                                <table class="table table-striped" style="min-width: 800px;">
                                    <thead class="thead-dark"
                                        style="position: sticky; top: 0; z-index: 10; background-color: white">
                                        <tr>
                                            <th style="min-width: 200px;">Workcenters</th>
                                            <th style="min-width: 150px;" class="text-center">{{ $year - 1 }}</th>
                                            <th style="min-width: 150px;" class="text-center">{{ $year }}</th>
                                            <th style="min-width: 150px;" class="text-center">Variance</th>
                                            <th style="min-width: 150px;" class="text-center">Percentage (%)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($workcenterData as $data)
                                            <tr>
                                                <td>{{ $data->workcenter }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('reports.detailReport', ['acc_id' => $acc_id, 'wct_id' => $data->wct_id ?? 'all', 'year' => $year - 1, 'submission_type' => $submission_type]) }}"
                                                        class="text-decoration-none">
                                                        {{ number_format($data->total_previous_year, 2, ',', '.') }}
                                                    </a>
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('reports.detailReport', ['acc_id' => $acc_id, 'wct_id' => $data->wct_id ?? 'all', 'year' => $year, 'current_year' => true, 'submission_type' => $submission_type]) }}"
                                                        class="text-decoration-none">
                                                        {{ number_format($data->total_current_year, 2, ',', '.') }}
                                                    </a>
                                                </td>
                                                <td class="text-center">
                                                    {{ number_format($data->variance, 2, ',', '.') }}
                                                </td>
                                                <td class="text-center">
                                                    {{ number_format($data->percentage_change, 2, ',', '.') }}%
                                                </td>
                                            </tr>
                                        @endforeach
                                        <!-- Total Row -->
                                        <tr style="font-weight: bold; position: sticky; bottom: 0; z-index: 10;"
                                            class="bg-danger">
                                            <td class="text-center text-white">{{ $workcenterTotal->workcenter }}</td>
                                            <td class="text-white text-center">
                                                <a href="{{ route('reports.workcenterReport', ['wct_id' => 'all', 'year' => $year - 1, 'submission_type' => $submission_type, 'acc_id' => $acc_id]) }}"
                                                    class="text-white text-decoration-none">
                                                    {{ number_format($workcenterTotal->total_previous_year, 2, ',', '.') }}
                                                </a>
                                            </td>
                                            <td class="text-white text-center">
                                                <a href="{{ route('reports.workcenterReport', ['wct_id' => 'all', 'year' => $year, 'submission_type' => $submission_type, 'acc_id' => $acc_id]) }}"
                                                    class="text-white text-decoration-none">
                                                    {{ number_format($workcenterTotal->total_current_year, 2, ',', '.') }}
                                                </a>
                                            </td>
                                            <td class="text-white text-center">
                                                {{ number_format($workcenterTotal->variance, 2, ',', '.') }}
                                            </td>
                                            <td class="text-white text-center">
                                                {{ number_format($workcenterTotal->percentage_change, 2, ',', '.') }}%
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
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
    </script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="{{ asset('js/soft-ui-dashboard.min.js?v=1.0.3') }}"></script>
</body>

</html>
