<!DOCTYPE html>
<html lang="en">
<x-head></x-head>

<body class="g-sidenav-show bg-gray-100">
    <x-sidebar></x-sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
        <x-navbar :notifications="$notifications">Submissions</x-navbar>
        <div class="container-fluid">
            <div class="row">
                <div class="card-header bg-danger">
                    <h4 style="font-weight: bold;" class="text-white">
                        <i class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>SUMMARY {{ $account_name }}
                    </h4>
                </div>
                <div class="card rounded-0">
                    <div class="mt-4">
                        <form id="filterForm" method="GET"
                            action="{{ route('sumarries.report-acc', ['acc_id' => $acc_id, 'dpt_id' => $dpt_id, 'year' => $selectedYear ?: date('Y')]) }}">
                            {{-- <input type="hidden" name="submission_type" value="{{ $submission_type }}"> --}}

                            <div class="row">
                                <input type="hidden" name="month" id="monthFilter" value="{{ $selectedMonth }}">
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Select Year</label>
                                    <select name="year" id="yearFilter" class="form-control"
                                        onchange="this.form.submit()" disabled>
                                        <option value="">-- All Years --</option>
                                        @foreach ($years as $year)
                                            <option value="{{ $year }}"
                                                {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Select Workcenter</label>
                                    <select name="workcenter" id="workcenterFilter" class="form-select"
                                        onchange="this.form.submit()">
                                        <option value="">-- All Workcenters --</option>
                                        @foreach ($workcenters as $workcenter)
                                            <option value="{{ $workcenter->wct_id }}"
                                                {{ $selectedWorkcenter == $workcenter->wct_id ? 'selected' : '' }}>
                                                {{ $workcenter->workcenter }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Select Budget Code</label>
                                    <select name="budget_name" id="budgetFilter" class="form-select"
                                        onchange="this.form.submit()">
                                        <option value="">-- All Budget Codes --</option>
                                        @foreach ($budgetCodes as $budget)
                                            <option value="{{ $budget->bdc_id }}"
                                                {{ $selectedBudget == $budget->bdc_id ? 'selected' : '' }}>
                                                {{ $budget->budget_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Hidden input for month filter -->
                                {{-- <input type="hidden" name="month" id="monthFilter" value="{{ $selectedMonth }}"> --}}
                            </div>
                        </form>
                    </div>
                    <div id="reports">
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
                                            <th>No.</th>
                                            @if ($report_type == 'general')
                                                <th>Item</th>
                                                <th>Description</th>
                                            @elseif ($report_type == 'aftersales')
                                                <th>Item</th>
                                                <th>Customer</th>
                                            @elseif ($report_type == 'training')
                                                <th>Participant</th>
                                                <th>Jenis Training</th>
                                            @elseif ($report_type == 'utilities')
                                                <th>Item</th>
                                                <th>KWH</th>
                                            @elseif ($report_type == 'business')
                                                <th>Item</th>
                                                <th>Description</th>
                                                <th>Days</th>
                                            @elseif ($report_type == 'represent')
                                                <th>Item</th>
                                                <th>Description</th>
                                                <th>Beneficiary</th>
                                            @elseif ($report_type == 'insurance')
                                                <th>Description</th>
                                                <th>Insurance Company</th>
                                            @elseif ($report_type == 'support')
                                                <th>Item</th>
                                                <th>Description</th>
                                                <th>Unit</th>
                                            @endif
                                            <th>Qty</th>
                                            <th>Price</th>
                                            <th>Amount</th>
                                            <th>Workcenter</th>
                                            <th>Department</th>
                                            <th>R/NR</th>
                                            @if ($report_type == 'support')
                                                <th>Line Of Business</th>
                                            @endif
                                            @foreach (['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'] as $month)
                                                <th>
                                                    <a href="#" onclick="filterByMonth('{{ $month }}')"
                                                        style="color: {{ $selectedMonth == $month ? '#ff0000' : 'inherit' }}; text-decoration: none;">
                                                        {{ $month }}
                                                    </a>
                                                </th>
                                            @endforeach
                                            <th>Total</th>
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
                                        @forelse ($reports as $index => $report)
                                            @php
                                                $total = $report->amount ?? $report->quantity * $report->price;
                                                $month = strtoupper(substr($report->month, 0, 3));
                                                $monthValues = array_fill_keys(array_keys($monthlyTotals), null);
                                                if (array_key_exists($month, $monthValues)) {
                                                    $monthValues[$month] = $total;
                                                    $monthlyTotals[$month] += $total;
                                                    $grandTotal += $total;
                                                }
                                            @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                @if ($report_type == 'general')
                                                    <td> {{ $report->item != null ? $report->item->item : $report->itm_id ?? '' }}
                                                    </td>
                                                    <td>{{ $report->description ?? '' }}</td>
                                                @elseif ($report_type == 'aftersales')
                                                    <td> {{ $report->item != null ? $report->item->item : $report->itm_id ?? '' }}
                                                    </td>
                                                    <td>{{ $report->customer ?? '' }}</td>
                                                @elseif ($report_type == 'training')
                                                    <td>{{ $report->participant ?? '' }}</td>
                                                    <td>{{ $report->jenis_training ?? '' }}</td>
                                                @elseif ($report_type == 'utilities')
                                                    <td> {{ $report->item != null ? $report->item->item : $report->itm_id ?? '' }}
                                                    </td>
                                                    <td>{{ $report->kwh ?? '' }}</td>
                                                @elseif ($report_type == 'business')
                                                    <td> {{ $report->item != null ? $report->item->item : $report->itm_id ?? '' }}
                                                    </td>
                                                    <td>{{ $report->description ?? '' }}</td>
                                                    <td>{{ $report->days ?? '' }}</td>
                                                @elseif ($report_type == 'represent')
                                                    <td> {{ $report->item != null ? $report->item->item : $report->itm_id ?? '' }}
                                                    </td>
                                                    <td>{{ $report->description ?? '' }}</td>
                                                    <td>{{ $report->beneficiary ?? '' }}</td>
                                                @elseif ($report_type == 'insurance')
                                                    <td>{{ $report->description ?? '' }}</td>
                                                    <td>{{ $report->insurance->company ?? '' }}
                                                    </td>
                                                @elseif ($report_type == 'support')
                                                    <td> {{ $report->item != null ? $report->item->item : $report->itm_id ?? '' }}
                                                    </td>
                                                    <td>{{ $report->description ?? '' }}</td>
                                                    <td>{{ $report->unit ?? '' }}</td>
                                                @endif
                                                <td>{{ $report->quantity ?? '' }}</td>
                                                <td>{{ number_format($report->price ?? 0, 0, ',', '.') }}</td>
                                                <td>{{ number_format($report->amount ?? $total, 0, ',', '.') }}
                                                </td>
                                                <td>{{ $report->workcenter->workcenter ?? '' }}</td>
                                                <td>{{ $report->dept->department ?? '' }}</td>
                                                <td>{{ $report->budget->budget_name ?? '' }}</td>
                                                @if ($report_type == 'support')
                                                    <td>{{ $report->line_business->line_business ?? '' }}
                                                @endif
                                                @foreach (['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'] as $month)
                                                    <td class="text-right">
                                                        {{ $monthValues[$month] ? number_format($monthValues[$month], 0, ',', '.') : '' }}
                                                    </td>
                                                @endforeach
                                                <td class="text-right">{{ number_format($total, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="22" class="text-center">No Report found!</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    @php
                                        $colspanMap = [
                                            'general' => 9,
                                            'aftersales' => 9,
                                            'training' => 9,
                                            'utilities' => 9,
                                            'business' => 10,
                                            'represent' => 10,
                                            'insurance' => 8,
                                            'support' => 11,
                                        ];
                                        $colspan = $colspanMap[$report_type] ?? 9;
                                    @endphp

                                    <tfoot>
                                        <tr style="font-weight: bold;" class="bg-danger">
                                            <td colspan="{{ $colspan }}" class="text-center text-white">TOTAL</td>
                                            @foreach (['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'] as $month)
                                                <td class="text-right text-white">
                                                    {{ number_format($monthlyTotals[$month], 0, ',', '.') }}
                                                </td>
                                            @endforeach
                                            <td class="text-right text-white">
                                                {{ number_format($grandTotal, 0, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <br>
                            <div class="d-flex justify-content-between gap-2">
                                <button onclick="history.back()" type="button" class="btn btn-secondary">Back</button>
                                <a href="{{ route($selectedMonth ? 'reports.printMonthlyAccount' : 'reports.printAccount', [
                                    'dpt_id' => $dpt_id,
                                    'acc_id' => $acc_id,
                                    'month' => $selectedMonth,
                                    'year' => $selectedYear,
                                    'workcenter' => $selectedWorkcenter,
                                    'budget_name' => $selectedBudget,
                                ]) }}"
                                    class="btn text-white" style="background-color: #0080ff">
                                    <i class="fa fa-print me-1"></i> Print Summary
                                </a>
                            </div>
                            {{-- <div class="d-flex justify-content-between gap-2">
                                <button onclick="history.back()" type="button" class="btn btn-secondary">Back</button>
                                <a href="{{ route('reports.printAccount', ['acc_id' => $acc_id, 'year' => $selectedYear, 'workcenter' => $selectedWorkcenter, 'budget_name' => $selectedBudget]) }}"
                                    class="btn text-white" style="background-color: #0080ff">
                                    <i class="fa fa-print me-1"></i> Print Summary
                                </a>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
            <x-footer></x-footer>
        </div>
    </main>
    <script>
        function filterByMonth(month) {
            document.getElementById('monthFilter').value = month;
            document.getElementById('filterForm').submit();
        }

        function searchTable() {
            var input = document.getElementById("cari");
            if (!input) return; // Exit if input not found
            var filter = input.value.toUpperCase();
            var table = document.getElementById("myTable");
            var tr = table.getElementsByTagName("tr");
            var visibleRows = 0;

            for (var i = 1; i < tr.length - 1; i++) { // Skip header and footer
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

            var noRecordsMessage = document.getElementById("no-records-message");
            if (noRecordsMessage) {
                noRecordsMessage.style.display = visibleRows === 0 ? "block" : "none";
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
            };
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="{{ asset('js/soft-ui-dashboard.min.js?v=1.0.3') }}"></script>
</body>

</html>
