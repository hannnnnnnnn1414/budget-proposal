<!DOCTYPE html>
<html lang="en">

<x-head></x-head>

<body class="g-sidenav-show bg-gray-100">
    <x-sidebar></x-sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
        <x-navbar :notifications="$notifications">
            Department List</x-navbar>
        <div class="container-fluid">
            <div class="row">
                <div class="card-header bg-danger">
                    <h4 style="font-weight: bold;" class="text-white">
                        <i class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>
                        {{ $account->account ?? 'Unknown' }}
                        @if ($selectedMonth)
                            {{ $selectedMonth }} {{ $selectedYear }}
                        @else
                            {{ $selectedYear }}
                        @endif
                    </h4>
                </div>
                <div class="card rounded-0">
                    <div class="mt-4">
                        <form id="filterForm" method="GET" action="{{ route('reports.report-dept') }}">
                            <input type="hidden" name="acc_id" value="{{ $account->acc_id ?? '' }}">
                            <input type="hidden" name="month" id="monthFilter" value="{{ $selectedMonth ?? '' }}">
                            <input type="hidden" name="workcenter" value="{{ $selectedWorkcenter ?? '' }}">
                            <input type="hidden" name="account" value="{{ $selectedAccount ?? '' }}">
                            <input type="hidden" name="budget_name" value="{{ $selectedBudget ?? '' }}">
                            <div class="row">
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
                                    <label class="form-label">Select Department</label>
                                    <select name="department" id="departmentFilter" class="form-select"
                                        onchange="this.form.submit()">
                                        <option value="">-- Filter by Department --</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->dpt_id }}"
                                                {{ $selectedDept == $department->dpt_id ? 'selected' : '' }}>
                                                {{ $department->department }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="table-responsive">
                            <table id="departmentTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th>Department ID</th>
                                        <th>Department Name</th>
                                        @foreach (['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'] as $month)
                                            <th>
                                                <a href="#" onclick="filterByMonth('{{ $month }}')"
                                                    style="color: {{ $selectedMonth == $month ? '#ff0000' : 'inherit' }}; text-decoration: none;">
                                                    {{ $month }}
                                                </a>
                                            </th>
                                        @endforeach
                                        <th>TOTAL</th> <!-- Kolom total -->
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $monthlyTotals = [
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

                                    @forelse ($departmentData as $dept)
                                        @php
                                            $deptTotal = 0;
                                            foreach ($dept['monthly_totals'] as $month => $amount) {
                                                $monthlyTotals[$month] += $amount;
                                                $deptTotal += $amount;
                                            }
                                            $grandTotal += $deptTotal;
                                        @endphp
                                        <tr>
                                            <td>{{ $dept['dpt_id'] }}</td>
                                            <td>{{ $dept['department'] }}</td>
                                            @foreach (['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'] as $month)
                                                @if (!$selectedMonth || $selectedMonth == $month)
                                                    <td class="text-right">
                                                        @if ($dept['monthly_totals'][$month] > 0)
                                                            <a
                                                                href="{{ route('sumarries.report-acc', [
                                                                    'acc_id' => $account->acc_id,
                                                                    'month' => $month,
                                                                    'year' => $selectedYear,
                                                                    'dpt_id' => $dept['dpt_id'],
                                                                ]) }}">
                                                                {{ number_format($dept['monthly_totals'][$month], 0, ',', '.') }}
                                                            </a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                @else
                                                    <td class="text-right">-</td>
                                                @endif
                                            @endforeach
                                            <td class="text-right fw-bold">
                                                @php $totalValue = $selectedMonth ? $dept['monthly_totals'][$selectedMonth] : $deptTotal; @endphp
                                                @if ($totalValue > 0)
                                                    <a
                                                        href="{{ route('sumarries.report-acc', [
                                                            'acc_id' => $account->acc_id,
                                                            // 'month' => $selectedMonth ?? 'ALL',
                                                            'year' => $selectedYear,
                                                            'dpt_id' => $dept['dpt_id'],
                                                        ]) }}">
                                                        {{ number_format($totalValue, 0, ',', '.') }}
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="15" class="text-center">No Departments found!</td>
                                        </tr>
                                    @endforelse
                                </tbody>

                                <tfoot>
                                    <tr style="font-weight: bold;" class="bg-danger">
                                        <td colspan="2" class="text-center text-white">TOTAL</td>
                                        @foreach (['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'] as $month)
                                            @if ($selectedMonth)
                                                <td class="text-right text-white">
                                                    {{ $selectedMonth == $month ? number_format($monthlyTotals[$month], 0, ',', '.') : '-' }}
                                                </td>
                                            @else
                                                <td class="text-right text-white">
                                                    {{ number_format($monthlyTotals[$month], 0, ',', '.') }}</td>
                                            @endif
                                        @endforeach
                                        <td class="text-right text-white">
                                            {{ $selectedMonth ? number_format($monthlyTotals[$selectedMonth], 0, ',', '.') : number_format($grandTotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <br>
                        <div class="d-flex justify-content-between gap-2">
                            <button onclick="history.back()" type="button" class="btn btn-secondary">Back</button>
                            {{-- <a href="{{ route($selectedMonth ? 'reports.printMonthlyDepartment' : 'reports.printDepartment', [
                                'acc_id' => $account->acc_id,
                                'month' => $selectedMonth,
                                'year' => $selectedYear,
                                'workcenter' => $selectedWorkcenter,
                                'budget_name' => $selectedBudget,
                                'department' => $selectedDept
                            ]) }}"
                                class="btn text-white" style="background-color: #0080ff">
                                <i class="fa fa-print me-1"></i> Print Summary
                            </a> --}}
                        </div>
                    </div>
                </div>
            </div>
            <x-footer></x-footer>
        </div>
    </main>
    <x-sidebar-plugin></x-sidebar-plugin>
    <script>
        function filterByMonth(month) {
            document.getElementById('monthFilter').value = month;
            document.getElementById('filterForm').submit();
        }
    </script>
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
