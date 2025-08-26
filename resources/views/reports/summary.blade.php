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
                        <i class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>SUMMARY PLAN MASTER BUDGET
                    </h4>
                </div>
                <div class="card rounded-0">
                    <div class="mt-4">
                        <form id="filterForm" method="GET"
                            action="{{ route('reports.report', ['dpt_id' => $department->dpt_id]) }}">
                            <div class="row">
                                {{-- <div class="mb-2">
                                    <label class="form-label">Account name or ID search</label>
                                    <input name="cari" type="text" id="cari" class="form-control" placeholder="Pencarian" onkeyup="searchTable()" />
                                </div> --}}
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Select Year</label>
                                    <select name="year" id="yearFilter" class="form-select"
                                        onchange="this.form.submit()">
                                        <option value="">-- Filter by Year --</option>
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
                            <div class="table-responsive">
                                <table id="myTable" class="table table-striped table-bordered">
                                    <thead>
                                        <tr class="text-center">
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
                                                <td class="text-right">
                                                    {{ $report->monthly_totals['JAN'] > 0 ? number_format($report->monthly_totals['JAN'], 0, ',', '.') : '-' }}
                                                </td>
                                                <td class="text-right">
                                                    {{ $report->monthly_totals['FEB'] > 0 ? number_format($report->monthly_totals['FEB'], 0, ',', '.') : '-' }}
                                                </td>
                                                <td class="text-right">
                                                    {{ $report->monthly_totals['MAR'] > 0 ? number_format($report->monthly_totals['MAR'], 0, ',', '.') : '-' }}
                                                </td>
                                                <td class="text-right">
                                                    {{ $report->monthly_totals['APR'] > 0 ? number_format($report->monthly_totals['APR'], 0, ',', '.') : '-' }}
                                                </td>
                                                <td class="text-right">
                                                    {{ $report->monthly_totals['MAY'] > 0 ? number_format($report->monthly_totals['MAY'], 0, ',', '.') : '-' }}
                                                </td>
                                                <td class="text-right">
                                                    {{ $report->monthly_totals['JUN'] > 0 ? number_format($report->monthly_totals['JUN'], 0, ',', '.') : '-' }}
                                                </td>
                                                <td class="text-right">
                                                    {{ $report->monthly_totals['JUL'] > 0 ? number_format($report->monthly_totals['JUL'], 0, ',', '.') : '-' }}
                                                </td>
                                                <td class="text-right">
                                                    {{ $report->monthly_totals['AUG'] > 0 ? number_format($report->monthly_totals['AUG'], 0, ',', '.') : '-' }}
                                                </td>
                                                <td class="text-right">
                                                    {{ $report->monthly_totals['SEP'] > 0 ? number_format($report->monthly_totals['SEP'], 0, ',', '.') : '-' }}
                                                </td>
                                                <td class="text-right">
                                                    {{ $report->monthly_totals['OCT'] > 0 ? number_format($report->monthly_totals['OCT'], 0, ',', '.') : '-' }}
                                                </td>
                                                <td class="text-right">
                                                    {{ $report->monthly_totals['NOV'] > 0 ? number_format($report->monthly_totals['NOV'], 0, ',', '.') : '-' }}
                                                </td>
                                                <td class="text-right">
                                                    {{ $report->monthly_totals['DEC'] > 0 ? number_format($report->monthly_totals['DEC'], 0, ',', '.') : '-' }}
                                                </td>
                                                <td class="text-right">{{ number_format($report->total, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="15">No Account found!</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr style="font-weight: bold; background-color: #ff0000;">
                                            <td colspan="2" class="text-center text-white">GRAND TOTAL</td>
                                            <td class="text-right text-white">
                                                {{ number_format($grandMonthlyTotals['JAN'], 0, ',', '.') }}</td>
                                            <td class="text-right text-white">
                                                {{ number_format($grandMonthlyTotals['FEB'], 0, ',', '.') }}</td>
                                            <td class="text-right text-white">
                                                {{ number_format($grandMonthlyTotals['MAR'], 0, ',', '.') }}</td>
                                            <td class="text-right text-white">
                                                {{ number_format($grandMonthlyTotals['APR'], 0, ',', '.') }}</td>
                                            <td class="text-right text-white">
                                                {{ number_format($grandMonthlyTotals['MAY'], 0, ',', '.') }}</td>
                                            <td class="text-right text-white">
                                                {{ number_format($grandMonthlyTotals['JUN'], 0, ',', '.') }}</td>
                                            <td class="text-right text-white">
                                                {{ number_format($grandMonthlyTotals['JUL'], 0, ',', '.') }}</td>
                                            <td class="text-right text-white">
                                                {{ number_format($grandMonthlyTotals['AUG'], 0, ',', '.') }}</td>
                                            <td class="text-right text-white">
                                                {{ number_format($grandMonthlyTotals['SEP'], 0, ',', '.') }}</td>
                                            <td class="text-right text-white">
                                                {{ number_format($grandMonthlyTotals['OCT'], 0, ',', '.') }}</td>
                                            <td class="text-right text-white">
                                                {{ number_format($grandMonthlyTotals['NOV'], 0, ',', '.') }}</td>
                                            <td class="text-right text-white">
                                                {{ number_format($grandMonthlyTotals['DEC'], 0, ',', '.') }}</td>
                                            <td class="text-right text-white">
                                                {{ number_format($grandTotal, 0, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <br>
                            <div class="d-flex justify-content-between gap-2">
                                <button onclick="history.back()" type="button" class="btn btn-secondary">Back</button>
                                <a class="btn text-white" style="background-color: #0080ff"
                                    href="{{ route('reports.downloadAllReport', ['dpt_id' => $department->dpt_id, 'workcenter' => $selectedWorkcenter, 'year' => $selectedYear, 'account' => $selectedAccount]) }}">
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
