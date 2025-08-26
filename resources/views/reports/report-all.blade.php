<!DOCTYPE html>
<html lang="en">

<x-head></x-head>

<body class="g-sidenav-show bg-gray-100">
    <x-sidebar></x-sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
        <x-navbar :notifications="$notifications">
            Submissions</x-navbar>
        <div class="container-fluid">
            <div class="row">
                <div class="card-header bg-danger">
                    <h4 style="font-weight: bold;" class="text-white">
                        <i class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>
                        SUMMARY PLAN MASTER BUDGET
                        {{-- {{ $submission_type == 'asset' ? 'ASSET' : ($submission_type == 'expenditure' ? 'EXPENDITURE' : 'All') }} --}}
                    </h4>
                </div>
                <div class="card rounded-0">
                    <div class="mt-4">
                        <form id="filterForm" method="GET"
                            action="{{ route('reports.report-all') }}">
                            {{-- <input type="hidden" name="submission_type" value="{{ $submission_type }}"> --}}

                            <div class="row">
                                {{-- <div class="mb-2">
                                    <label class="form-label">Account name or ID search</label>
                                    <input name="cari" type="text" id="cari" class="form-control" placeholder="Pencarian" onkeyup="searchTable()" />
                                </div> --}}
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Select Year</label>
                                    <select name="year" id="yearFilter" class="form-select"
                                        onchange="this.form.submit()">
                                        @foreach ($years as $year)
                                            <option value="{{ $year }}"
                                                {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Select Workcenter</label>
                                    <select name="workcenter" id="workcenterFilter" class="form-select"
                                        onchange="this.form.submit()">
                                        <option value="">-- Filter by Workcenter --</option>
                                        @foreach ($workcenters as $workcenter)
                                            <option value="{{ $workcenter->wct_id }}"
                                                {{ $selectedWorkcenter == $workcenter->wct_id ? 'selected' : '' }}>
                                                {{ $workcenter->workcenter }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Select Account</label>
                                    <select name="account" id="accountFilter" class="form-select"
                                        onchange="this.form.submit()">
                                        <option value="">-- Filter by Account --</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->acc_id }}"
                                                {{ $selectedAccount == $account->acc_id ? 'selected' : '' }}>
                                                {{ $account->account }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Select Budget Code</label>
                                    <select name="budget_name" id="budgetFilter" class="form-select"
                                        onchange="this.form.submit()">
                                        <option value="">-- Filter by Budget Code --</option>
                                        @foreach ($budgets as $budget)
                                            <option value="{{ $budget->bdc_id }}"
                                                {{ $selectedBudget == $budget->bdc_id ? 'selected' : '' }}>
                                                {{ $budget->budget_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div id="submissions">
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            <!-- Debug: Check if reports data is available -->
                            @if (empty($reports))
                                <div class="alert alert-warning">No data found for the selected filters.</div>
                            @endif

                            <div class="table-responsive" style="max-height: 500px; overflow-y: auto; overflow-x: auto; width: 100%;">
                                <table id="myTable" class="table table-striped table-bordered" style="min-width: 800px;">
<thead class="thead-dark"
                                        style="position: sticky; top: 0; z-index: 10; background-color: white">                                        <tr class="text-center">
                                            <th>CODE</th>
                                            <th>ACCOUNT/BUDGET</th>
                                            <th>JAN</th>
                                            <th>FEB</th>
                                            <th>MAR</th>
                                            <th>APR</th>
                                            <th>MAY</th>
                                            <th>JUN</th>
                                            <th>JUL</th>
                                            <th>AUG</th>
                                            <th>SEP</th>
                                            <th>OCT</th>
                                            <th>NOV</th>
                                            <th>DEC</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $grandMonthlyTotals = [
                                                'JAN' => 0,
                                                'FEB' => 0,
                                                'MAR' => 0,
                                                'APR' => 0,
                                                'MAY' => 0,
                                                'JUN' => 0,
                                                'JUL' => 0,
                                                'AUG' => 0,
                                                'SEP' => 0,
                                                'OCT' => 0,
                                                'NOV' => 0,
                                                'DEC' => 0,
                                            ];
                                            $grandTotal = 0;
                                        @endphp
                                        @forelse ($reports as $report)
                                            @php
                                                foreach ($report->monthly_totals as $month => $amount) {
                                                    $grandMonthlyTotals[$month] += $amount;
                                                }
                                                $grandTotal += $report->total;
                                            @endphp
                                            <tr>
                                                <td>{{ $report->acc_id }}</td>
                                                <td>{{ $report->account }}</td>
                                                @foreach (['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'] as $month)
                                                    <td class="text-right">
                                                        @if ($report->monthly_totals[$month] > 0)
                                                            <a
                                                                href="{{ route('sumarries.report-acc', [
                                                                    'acc_id' => $report->acc_id,
                                                                    'dpt_id' => $department->dpt_id,
                                                                    'year' => $selectedYear,
                                                                    'account' => $selectedAccount,
                                                                    'month' => $month,
                                                                    // 'submission_type' => $submission_type,
                                                                ]) }}">
                                                                {{ number_format($report->monthly_totals[$month], 0, ',', '.') }}
                                                            </a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                @endforeach
                                                <td class="text-right">
                                                    <a
                                                        href="{{ route('sumarries.report-acc', [
                                                            'acc_id' => $report->acc_id,
                                                            'dpt_id' => $department->dpt_id,
                                                            'year' => $selectedYear,
                                                            'account' => $selectedAccount,
                                                            // 'submission_type' => $submission_type,
                                                        ]) }}">
                                                        {{ number_format($report->total, 0, ',', '.') }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="15">No Account found!</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr style="font-weight: bold;  position: sticky; bottom: 0; z-index: 10; " class="bg-danger">
                                            <td colspan="2" class="text-center text-white">GRAND TOTAL</td>
                                            @foreach (['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'] as $month)
                                                <td class="text-right text-white">
                                                    {{ number_format($grandMonthlyTotals[$month], 0, ',', '.') }}
                                                </td>
                                            @endforeach
                                            <td class="text-right text-white">
                                                {{ number_format($grandTotal, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <br>
                            <div class="d-flex justify-content-between gap-2">
                                <button onclick="history.back()" type="button" class="btn btn-secondary">Back</button>
                                <a class="btn text-white" style="background-color: #0080ff"
                                    href="{{ route('reports.downloadAll', [
                                        'dpt_id' => $department->dpt_id,
                                        'workcenter' => $selectedWorkcenter,
                                        'year' => $selectedYear,
                                        'account' => $selectedAccount,
                                        // 'submission_type' => $submission_type,
                                    ]) }}">
                                    <i class="fa fa-print me-1"></i> Print Summary
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <x-footer></x-footer>
        </div>
    </main>
    <script>
        function searchTable() {
            var input, filter, table, tr, td, i, j, txtValue;
            input = document.getElementById("cari");
            filter = input.value.toUpperCase();
            table = document.getElementById("myTable");
            tr = table.getElementsByTagName("tr");

            for (i = 1; i < tr.length - 1; i++) { // Skip header and footer
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
            }
        }
    </script>
    <x-sidebar-plugin></x-sidebar-plugin>
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
