<!--
=========================================================
* Soft UI Dashboard - v1.0.3
=========================================================

* Product Page: https://www.creative-tim.com/product/soft-ui-dashboard
* Copyright 2021 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)

* Coded by Creative Tim

=========================================================
    
* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>
<html lang="en">

<x-head></x-head>

<body class="g-sidenav-show  bg-gray-100">
    <x-sidebar></x-sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
        <x-navbar :notifications="$notifications">
            Submissions</x-navbar>
        <div class="container-fluid">
            @if (isset($acc_id))
                @if ($acc_id == 'FOHTAXPUB')
                    <div class="row">
                        <div class="card-header bg-danger">
                            <h4 style="font-weight: bold;" class="text-white">
                                <i class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>SUMMARY
                                {{ $account_name }}
                            </h4>
                        </div>
                        <div class="card rounded-0">
                            <div class="mt-4">
                                <form method="GET" action="{{ route('reports.index', $acc_id) }}">
                                    <div class="row">
                                        {{-- <div class="col-md-6 mb-2">
                                            <label class="form-label">Account name or ID search</label>
                                            <input name="cari" type="text" id="cari" class="form-control"
                                                placeholder="Pencarian" onkeyup="searchTable()" />
                                        </div> --}}
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Select Year</label>
                                            <select name="year" id="yearFilter" class="form-select"
                                                onchange="this.form.submit()">
                                                <option value="">-- All Years --</option>
                                                @foreach ($years as $year)
                                                    <option value="{{ $year }}"
                                                        {{ $selectedYear == $year ? 'selected' : '' }}>
                                                        {{ $year }}</option>
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
                                                    <th>Item</th>
                                                    <th>Description</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>
                                                    <th>Amount</th>
                                                    <th>Workcenter</th>
                                                    <th>Department</th>
                                                    <th>R/NR</th>
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
                                                        // Langsung ambil nilai amount tanpa perlu perhitungan
                                                        $total = $report->amount;
                                                        $grandTotal += $total;

                                                        $monthValues = array_fill_keys(
                                                            array_keys($monthlyTotals),
                                                            null,
                                                        );
                                                        if (!empty($report->month)) {
                                                            $month = strtoupper(substr($report->month, 0, 3));
                                                            if (array_key_exists($month, $monthValues)) {
                                                                $monthValues[$month] = $total;
                                                                $monthlyTotals[$month] += $total;
                                                            }
                                                        }
                                                    @endphp <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $report->item != null ? $report->item->item : $report->itm_id ?? '' }}
                                                        </td>
                                                        <td>{{ $report->description }}</td>
                                                        <td>{{ $report->quantity }}</td>
                                                        <td>{{ number_format($report->price, 0, ',', '.') }}</td>
                                                        <td>{{ number_format($report->amount, 0, ',', '.') }}</td>
                                                        <td>{{ $report->workcenter->workcenter ?? '' }}</td>
                                                        <td>{{ $report->dept->department ?? '' }}</td>
                                                        <td>{{ $report->budget->budget_name ?? '' }}</td>
                                                        @foreach (['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'] as $month)
                                                            <td class="text-right">
                                                                {{ $monthValues[$month] ? number_format($monthValues[$month], 0, ',', '.') : '' }}
                                                            </td>
                                                        @endforeach
                                                        <td class="text-right">
                                                            {{ number_format($total, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="21" class="text-center">No Report found!</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot>
                                                <tr style="font-weight: bold;" class="bg-danger">
                                                    <td colspan="9" class="text-center text-white">TOTAL</td>
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
                                        <button onclick="history.back()" type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Back</button>
                                        <a href="{{ route('reports.printAccount', [
                                            'acc_id' => $acc_id,
                                            'dpt_id' => $dpt_id,
                                            'year' => $selectedYear,
                                            'workcenter' => $selectedWorkcenter,
                                            'budget_name' => $selectedBudget,
                                        ]) }}"
                                            class="btn text-white" style="background-color: #0080ff">
                                            <i class="fa fa-print me-1"></i> Print Summary
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif ($acc_id == 'SGATAXPUB')
                    <div class="row">
                        <div class="card-header bg-danger">
                            <h4 style="font-weight: bold;" class="text-white">
                                <i class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>SUMMARY
                                {{ $account_name }}
                            </h4>
                        </div>
                        <div class="card rounded-0">
                            <div class="mt-4">
                                <form method="GET" action="{{ route('reports.index', $acc_id) }}">
                                    <div class="row">
                                        {{-- <div class="col-md-6 mb-2">
                                            <label class="form-label">Account name or ID search</label>
                                            <input name="cari" type="text" id="cari" class="form-control"
                                                placeholder="Pencarian" onkeyup="searchTable()" />
                                        </div> --}}
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Select Year</label>
                                            <select name="year" id="yearFilter" class="form-select"
                                                onchange="this.form.submit()">
                                                <option value="">-- All Years --</option>
                                                @foreach ($years as $year)
                                                    <option value="{{ $year }}"
                                                        {{ $selectedYear == $year ? 'selected' : '' }}>
                                                        {{ $year }}</option>
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
                                                    <th>Item</th>
                                                    <th>Description</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>
                                                    <th>Amount</th>
                                                    <th>Workcenter</th>
                                                    <th>Department</th>
                                                    <th>R/NR</th>
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
                                                        // Langsung ambil nilai amount tanpa perlu perhitungan
                                                        $total = $report->amount;
                                                        $grandTotal += $total;

                                                        $monthValues = array_fill_keys(
                                                            array_keys($monthlyTotals),
                                                            null,
                                                        );
                                                        if (!empty($report->month)) {
                                                            $month = strtoupper(substr($report->month, 0, 3));
                                                            if (array_key_exists($month, $monthValues)) {
                                                                $monthValues[$month] = $total;
                                                                $monthlyTotals[$month] += $total;
                                                            }
                                                        }
                                                    @endphp <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $report->item != null ? $report->item->item : $report->itm_id ?? '' }}
                                                        </td>
                                                        {{-- <td>{{ $report->item->item ?? '' }}</td> --}}
                                                        <td>{{ $report->description }}</td>
                                                        <td>{{ $report->quantity }}</td>
                                                        <td>{{ number_format($report->price, 0, ',', '.') }}</td>
                                                        <td>{{ number_format($report->amount, 0, ',', '.') }}</td>
                                                        <td>{{ $report->workcenter->workcenter ?? '' }}</td>
                                                        <td>{{ $report->dept->department ?? '' }}</td>
                                                        <td>{{ $report->budget->budget_name ?? '' }}</td>
                                                        @foreach (['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'] as $month)
                                                            <td class="text-right">
                                                                {{ $monthValues[$month] ? number_format($monthValues[$month], 0, ',', '.') : '' }}
                                                            </td>
                                                        @endforeach
                                                        <td class="text-right">
                                                            {{ number_format($total, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="21" class="text-center">No Report found!</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot>
                                                <tr style="font-weight: bold;" class="bg-danger">
                                                    <td colspan="9" class="text-center text-white">TOTAL</td>
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
                                        <button onclick="history.back()" type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Back</button>
                                        <a href="{{ route('reports.printAccount', [
                                            'acc_id' => $acc_id,
                                            'dpt_id' => $dpt_id,
                                            'year' => $selectedYear,
                                            'workcenter' => $selectedWorkcenter,
                                            'budget_name' => $selectedBudget,
                                        ]) }}"
                                            class="btn text-white" style="background-color: #0080ff">
                                            <i class="fa fa-print me-1"></i> Print Summary
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif ($acc_id == 'FOHPROF')
                    <div class="row">
                        <div class="card-header bg-danger">
                            <h4 style="font-weight: bold;" class="text-white">
                                <i class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>SUMMARY
                                {{ $account_name }}
                            </h4>
                        </div>
                        <div class="card rounded-0">
                            <div class="mt-4">
                                <form method="GET" action="{{ route('reports.index', $acc_id) }}">
                                    <div class="row">
                                        {{-- <div class="col-md-6 mb-2">
                                            <label class="form-label">Account name or ID search</label>
                                            <input name="cari" type="text" id="cari" class="form-control"
                                                placeholder="Pencarian" onkeyup="searchTable()" />
                                        </div> --}}
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Select Year</label>
                                            <select name="year" id="yearFilter" class="form-select"
                                                onchange="this.form.submit()">
                                                <option value="">-- All Years --</option>
                                                @foreach ($years as $year)
                                                    <option value="{{ $year }}"
                                                        {{ $selectedYear == $year ? 'selected' : '' }}>
                                                        {{ $year }}</option>
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
                                                    <th>Item</th>
                                                    <th>Description</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>
                                                    <th>Amount</th>
                                                    <th>Workcenter</th>
                                                    <th>Department</th>
                                                    <th>R/NR</th>
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
                                                        // Langsung ambil nilai amount tanpa perlu perhitungan
                                                        $total = $report->amount;
                                                        $grandTotal += $total;

                                                        $monthValues = array_fill_keys(
                                                            array_keys($monthlyTotals),
                                                            null,
                                                        );
                                                        if (!empty($report->month)) {
                                                            $month = strtoupper(substr($report->month, 0, 3));
                                                            if (array_key_exists($month, $monthValues)) {
                                                                $monthValues[$month] = $total;
                                                                $monthlyTotals[$month] += $total;
                                                            }
                                                        }
                                                    @endphp <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $report->item != null ? $report->item->item : $report->itm_id ?? '' }}
                                                        </td>
                                                        <td>{{ $report->description }}</td>
                                                        <td>{{ $report->quantity }}</td>
                                                        <td>{{ number_format($report->price, 0, ',', '.') }}</td>
                                                        <td>{{ number_format($report->amount, 0, ',', '.') }}</td>
                                                        <td>{{ $report->workcenter->workcenter ?? '' }}</td>
                                                        <td>{{ $report->dept->department ?? '' }}</td>
                                                        <td>{{ $report->budget->budget_name ?? '' }}</td>
                                                        @foreach (['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'] as $month)
                                                            <td class="text-right">
                                                                {{ $monthValues[$month] ? number_format($monthValues[$month], 0, ',', '.') : '' }}
                                                            </td>
                                                        @endforeach
                                                        <td class="text-right">
                                                            {{ number_format($total, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="21" class="text-center">No Report found!</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot>
                                                <tr style="font-weight: bold;" class="bg-danger">
                                                    <td colspan="9" class="text-center text-white">TOTAL</td>
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
                                        <button onclick="history.back()" type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Back</button>
                                        <a href="{{ route('reports.printAccount', [
                                            'acc_id' => $acc_id,
                                            'dpt_id' => $dpt_id,
                                            'year' => $selectedYear,
                                            'workcenter' => $selectedWorkcenter,
                                            'budget_name' => $selectedBudget,
                                        ]) }}"
                                            class="btn text-white" style="background-color: #0080ff">
                                            <i class="fa fa-print me-1"></i> Print Summary
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif ($acc_id == 'SGAPROF')
                    <div class="row">
                        <div class="card-header bg-danger">
                            <h4 style="font-weight: bold;" class="text-white">
                                <i class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>SUMMARY
                                {{ $account_name }}
                            </h4>
                        </div>
                        <div class="card rounded-0">
                            <div class="mt-4">
                                <form method="GET" action="{{ route('reports.index', $acc_id) }}">
                                    <div class="row">
                                        {{-- <div class="col-md-6 mb-2">
                                            <label class="form-label">Account name or ID search</label>
                                            <input name="cari" type="text" id="cari" class="form-control"
                                                placeholder="Pencarian" onkeyup="searchTable()" />
                                        </div> --}}
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Select Year</label>
                                            <select name="year" id="yearFilter" class="form-select"
                                                onchange="this.form.submit()">
                                                <option value="">-- All Years --</option>
                                                @foreach ($years as $year)
                                                    <option value="{{ $year }}"
                                                        {{ $selectedYear == $year ? 'selected' : '' }}>
                                                        {{ $year }}</option>
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
                                                    <th>Item</th>
                                                    <th>Description</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>
                                                    <th>Amount</th>
                                                    <th>Workcenter</th>
                                                    <th>Department</th>
                                                    <th>R/NR</th>
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
                                                        // Langsung ambil nilai amount tanpa perlu perhitungan
                                                        $total = $report->amount;
                                                        $grandTotal += $total;

                                                        $monthValues = array_fill_keys(
                                                            array_keys($monthlyTotals),
                                                            null,
                                                        );
                                                        if (!empty($report->month)) {
                                                            $month = strtoupper(substr($report->month, 0, 3));
                                                            if (array_key_exists($month, $monthValues)) {
                                                                $monthValues[$month] = $total;
                                                                $monthlyTotals[$month] += $total;
                                                            }
                                                        }
                                                    @endphp <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $report->item != null ? $report->item->item : $report->itm_id ?? '' }}
                                                        </td>
                                                        <td>{{ $report->description }}</td>
                                                        <td>{{ $report->quantity }}</td>
                                                        <td>{{ number_format($report->price, 0, ',', '.') }}</td>
                                                        <td>{{ number_format($report->amount, 0, ',', '.') }}</td>
                                                        <td>{{ $report->workcenter->workcenter ?? '' }}</td>
                                                        <td>{{ $report->dept->department ?? '' }}</td>
                                                        <td>{{ $report->budget->budget_name ?? '' }}</td>
                                                        @foreach (['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'] as $month)
                                                            <td class="text-right">
                                                                {{ $monthValues[$month] ? number_format($monthValues[$month], 0, ',', '.') : '' }}
                                                            </td>
                                                        @endforeach
                                                        <td class="text-right">
                                                            {{ number_format($total, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="21" class="text-center">No Report found!</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot>
                                                <tr style="font-weight: bold;" class="bg-danger">
                                                    <td colspan="9" class="text-center text-white">TOTAL</td>
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
                                        <button onclick="history.back()" type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Back</button>
                                        <a href="{{ route('reports.printAccount', [
                                            'acc_id' => $acc_id,
                                            'dpt_id' => $dpt_id,
                                            'year' => $selectedYear,
                                            'workcenter' => $selectedWorkcenter,
                                            'budget_name' => $selectedBudget,
                                        ]) }}"
                                            class="btn text-white" style="background-color: #0080ff">
                                            <i class="fa fa-print me-1"></i> Print Summary
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif ($acc_id == 'FOHPACKING')
                    <div class="row">
                        <div class="card-header bg-danger">
                            <h4 style="font-weight: bold;" class="text-white">
                                <i class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>SUMMARY
                                {{ $account_name }}
                            </h4>
                        </div>
                        <div class="card rounded-0">
                            <div class="mt-4">
                                <form method="GET" action="{{ route('reports.index', $acc_id) }}">
                                    <div class="row">
                                        {{-- <div class="col-md-6 mb-2">
                                            <label class="form-label">Account name or ID search</label>
                                            <input name="cari" type="text" id="cari" class="form-control"
                                                placeholder="Pencarian" onkeyup="searchTable()" />
                                        </div> --}}
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Select Year</label>
                                            <select name="year" id="yearFilter" class="form-select"
                                                onchange="this.form.submit()">
                                                <option value="">-- All Years --</option>
                                                @foreach ($years as $year)
                                                    <option value="{{ $year }}"
                                                        {{ $selectedYear == $year ? 'selected' : '' }}>
                                                        {{ $year }}</option>
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
                                                    <th>Item</th>
                                                    <th>Description</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>
                                                    <th>Amount</th>
                                                    <th>Workcenter</th>
                                                    <th>Department</th>
                                                    <th>R/NR</th>
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
                                                        // Langsung ambil nilai amount tanpa perlu perhitungan
                                                        $total = $report->amount;
                                                        $grandTotal += $total;

                                                        $monthValues = array_fill_keys(
                                                            array_keys($monthlyTotals),
                                                            null,
                                                        );
                                                        if (!empty($report->month)) {
                                                            $month = strtoupper(substr($report->month, 0, 3));
                                                            if (array_key_exists($month, $monthValues)) {
                                                                $monthValues[$month] = $total;
                                                                $monthlyTotals[$month] += $total;
                                                            }
                                                        }
                                                    @endphp <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $report->item != null ? $report->item->item : $report->itm_id ?? '' }}
                                                        </td>
                                                        <td>{{ $report->description }}</td>
                                                        <td>{{ $report->quantity }}</td>
                                                        <td>{{ number_format($report->price, 0, ',', '.') }}</td>
                                                        <td>{{ number_format($report->amount, 0, ',', '.') }}</td>
                                                        <td>{{ $report->workcenter->workcenter ?? '' }}</td>
                                                        <td>{{ $report->dept->department ?? '' }}</td>
                                                        <td>{{ $report->budget->budget_name ?? '' }}</td>
                                                        @foreach (['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'] as $month)
                                                            <td class="text-right">
                                                                {{ $monthValues[$month] ? number_format($monthValues[$month], 0, ',', '.') : '' }}
                                                            </td>
                                                        @endforeach
                                                        <td class="text-right">
                                                            {{ number_format($total, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="21" class="text-center">No Report found!</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot>
                                                <tr style="font-weight: bold;" class="bg-danger">
                                                    <td colspan="9" class="text-center text-white">TOTAL</td>
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
                                        <button onclick="history.back()" type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Back</button>
                                        <a href="{{ route('reports.printAccount', [
                                            'acc_id' => $acc_id,
                                            'dpt_id' => $dpt_id,
                                            'year' => $selectedYear,
                                            'workcenter' => $selectedWorkcenter,
                                            'budget_name' => $selectedBudget,
                                        ]) }}"
                                            class="btn text-white" style="background-color: #0080ff">
                                            <i class="fa fa-print me-1"></i> Print Summary
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif ($acc_id == 'FOHRENT')
                    <div class="row">
                        <div class="card-header bg-danger">
                            <h4 style="font-weight: bold;" class="text-white">
                                <i class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>SUMMARY
                                {{ $account_name }}
                            </h4>
                        </div>
                        <div class="card rounded-0">
                            <div class="mt-4">
                                <form method="GET" action="{{ route('reports.index', $acc_id) }}">
                                    <div class="row">
                                        {{-- <div class="col-md-6 mb-2">
                                            <label class="form-label">Account name or ID search</label>
                                            <input name="cari" type="text" id="cari" class="form-control"
                                                placeholder="Pencarian" onkeyup="searchTable()" />
                                        </div> --}}
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Select Year</label>
                                            <select name="year" id="yearFilter" class="form-select"
                                                onchange="this.form.submit()">
                                                <option value="">-- All Years --</option>
                                                @foreach ($years as $year)
                                                    <option value="{{ $year }}"
                                                        {{ $selectedYear == $year ? 'selected' : '' }}>
                                                        {{ $year }}</option>
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
                                                    <th>Item</th>
                                                    <th>Description</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>
                                                    <th>Amount</th>
                                                    <th>Workcenter</th>
                                                    <th>Department</th>
                                                    <th>R/NR</th>
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
                                                        // Langsung ambil nilai amount tanpa perlu perhitungan
                                                        $total = $report->amount;
                                                        $grandTotal += $total;

                                                        $monthValues = array_fill_keys(
                                                            array_keys($monthlyTotals),
                                                            null,
                                                        );
                                                        if (!empty($report->month)) {
                                                            $month = strtoupper(substr($report->month, 0, 3));
                                                            if (array_key_exists($month, $monthValues)) {
                                                                $monthValues[$month] = $total;
                                                                $monthlyTotals[$month] += $total;
                                                            }
                                                        }
                                                    @endphp <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $report->item != null ? $report->item->item : $report->itm_id ?? '' }}
                                                        </td>
                                                        <td>{{ $report->description }}</td>
                                                        <td>{{ $report->quantity }}</td>
                                                        <td>{{ number_format($report->price, 0, ',', '.') }}</td>
                                                        <td>{{ number_format($report->amount, 0, ',', '.') }}</td>
                                                        <td>{{ $report->workcenter->workcenter ?? '' }}</td>
                                                        <td>{{ $report->dept->department ?? '' }}</td>
                                                        <td>{{ $report->budget->budget_name ?? '' }}</td>
                                                        @foreach (['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'] as $month)
                                                            <td class="text-right">
                                                                {{ $monthValues[$month] ? number_format($monthValues[$month], 0, ',', '.') : '' }}
                                                            </td>
                                                        @endforeach
                                                        <td class="text-right">
                                                            {{ number_format($total, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="21" class="text-center">No Report found!</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot>
                                                <tr style="font-weight: bold;" class="bg-danger">
                                                    <td colspan="9" class="text-center text-white">TOTAL</td>
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
                                        <button onclick="history.back()" type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Back</button>
                                        <a href="{{ route('reports.printAccount', [
                                            'acc_id' => $acc_id,
                                            'dpt_id' => $dpt_id,
                                            'year' => $selectedYear,
                                            'workcenter' => $selectedWorkcenter,
                                            'budget_name' => $selectedBudget,
                                        ]) }}"
                                            class="btn text-white" style="background-color: #0080ff">
                                            <i class="fa fa-print me-1"></i> Print Summary
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif ($acc_id == 'FOHAUTOMOBILE')
                    <div class="row">
                        <div class="card-header bg-danger">
                            <h4 style="font-weight: bold;" class="text-white">
                                <i class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>SUMMARY
                                {{ $account_name }}
                            </h4>
                        </div>
                        <div class="card rounded-0">
                            <div class="mt-4">
                                <form method="GET" action="{{ route('reports.index', $acc_id) }}">
                                    <div class="row">
                                        {{-- <div class="col-md-6 mb-2">
                                            <label class="form-label">Account name or ID search</label>
                                            <input name="cari" type="text" id="cari" class="form-control"
                                                placeholder="Pencarian" onkeyup="searchTable()" />
                                        </div> --}}
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Select Year</label>
                                            <select name="year" id="yearFilter" class="form-select"
                                                onchange="this.form.submit()">
                                                <option value="">-- All Years --</option>
                                                @foreach ($years as $year)
                                                    <option value="{{ $year }}"
                                                        {{ $selectedYear == $year ? 'selected' : '' }}>
                                                        {{ $year }}</option>
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
                                                    <th>Item</th>
                                                    <th>Description</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>
                                                    <th>Amount</th>
                                                    <th>Workcenter</th>
                                                    <th>Department</th>
                                                    <th>R/NR</th>
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
                                                        // Langsung ambil nilai amount tanpa perlu perhitungan
                                                        $total = $report->amount;
                                                        $grandTotal += $total;

                                                        $monthValues = array_fill_keys(
                                                            array_keys($monthlyTotals),
                                                            null,
                                                        );
                                                        if (!empty($report->month)) {
                                                            $month = strtoupper(substr($report->month, 0, 3));
                                                            if (array_key_exists($month, $monthValues)) {
                                                                $monthValues[$month] = $total;
                                                                $monthlyTotals[$month] += $total;
                                                            }
                                                        }
                                                    @endphp <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $report->item != null ? $report->item->item : $report->itm_id ?? '' }}
                                                        </td>
                                                        <td>{{ $report->description }}</td>
                                                        <td>{{ $report->quantity }}</td>
                                                        <td>{{ number_format($report->price, 0, ',', '.') }}</td>
                                                        <td>{{ number_format($report->amount, 0, ',', '.') }}</td>
                                                        <td>{{ $report->workcenter->workcenter ?? '' }}</td>
                                                        <td>{{ $report->dept->department ?? '' }}</td>
                                                        <td>{{ $report->budget->budget_name ?? '' }}</td>
                                                        @foreach (['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'] as $month)
                                                            <td class="text-right">
                                                                {{ $monthValues[$month] ? number_format($monthValues[$month], 0, ',', '.') : '' }}
                                                            </td>
                                                        @endforeach
                                                        <td class="text-right">
                                                            {{ number_format($total, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="21" class="text-center">No Report found!</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot>
                                                <tr style="font-weight: bold;" class="bg-danger">
                                                    <td colspan="9" class="text-center text-white">TOTAL</td>
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
                                        <button onclick="history.back()" type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Back</button>
                                        <a href="{{ route('reports.printAccount', [
                                            'acc_id' => $acc_id,
                                            'dpt_id' => $dpt_id,
                                            'year' => $selectedYear,
                                            'workcenter' => $selectedWorkcenter,
                                            'budget_name' => $selectedBudget,
                                        ]) }}"
                                            class="btn text-white" style="background-color: #0080ff">
                                            <i class="fa fa-print me-1"></i> Print Summary
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif ($acc_id == 'SGAAUTOMOBILE')
                    <div class="row">
                        <div class="card-header bg-danger">
                            <h4 style="font-weight: bold;" class="text-white">
                                <i class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>SUMMARY
                                {{ $account_name }}
                            </h4>
                        </div>
                        <div class="card rounded-0">
                            <div class="mt-4">
                                <form method="GET" action="{{ route('reports.index', $acc_id) }}">
                                    <div class="row">
                                        {{-- <div class="col-md-6 mb-2">
                                            <label class="form-label">Account name or ID search</label>
                                            <input name="cari" type="text" id="cari" class="form-control"
                                                placeholder="Pencarian" onkeyup="searchTable()" />
                                        </div> --}}
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Select Year</label>
                                            <select name="year" id="yearFilter" class="form-select"
                                                onchange="this.form.submit()">
                                                <option value="">-- All Years --</option>
                                                @foreach ($years as $year)
                                                    <option value="{{ $year }}"
                                                        {{ $selectedYear == $year ? 'selected' : '' }}>
                                                        {{ $year }}</option>
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
                                                    <th>Item</th>
                                                    <th>Description</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>
                                                    <th>Amount</th>
                                                    <th>Workcenter</th>
                                                    <th>Department</th>
                                                    <th>R/NR</th>
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
                                                        // Langsung ambil nilai amount tanpa perlu perhitungan
                                                        $total = $report->amount;
                                                        $grandTotal += $total;

                                                        $monthValues = array_fill_keys(
                                                            array_keys($monthlyTotals),
                                                            null,
                                                        );
                                                        if (!empty($report->month)) {
                                                            $month = strtoupper(substr($report->month, 0, 3));
                                                            if (array_key_exists($month, $monthValues)) {
                                                                $monthValues[$month] = $total;
                                                                $monthlyTotals[$month] += $total;
                                                            }
                                                        }
                                                    @endphp <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $report->item != null ? $report->item->item : $report->itm_id ?? '' }}
                                                        </td>
                                                        <td>{{ $report->description }}</td>
                                                        <td>{{ $report->quantity }}</td>
                                                        <td>{{ number_format($report->price, 0, ',', '.') }}</td>
                                                        <td>{{ number_format($report->amount, 0, ',', '.') }}</td>
                                                        <td>{{ $report->workcenter->workcenter ?? '' }}</td>
                                                        <td>{{ $report->dept->department ?? '' }}</td>
                                                        <td>{{ $report->budget->budget_name ?? '' }}</td>
                                                        @foreach (['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'] as $month)
                                                            <td class="text-right">
                                                                {{ $monthValues[$month] ? number_format($monthValues[$month], 0, ',', '.') : '' }}
                                                            </td>
                                                        @endforeach
                                                        <td class="text-right">
                                                            {{ number_format($total, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="21" class="text-center">No Report found!</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot>
                                                <tr style="font-weight: bold;" class="bg-danger">
                                                    <td colspan="9" class="text-center text-white">TOTAL</td>
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
                                        <button onclick="history.back()" type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Back</button>
                                        <a href="{{ route('reports.printAccount', [
                                            'acc_id' => $acc_id,
                                            'dpt_id' => $dpt_id,
                                            'year' => $selectedYear,
                                            'workcenter' => $selectedWorkcenter,
                                            'budget_name' => $selectedBudget,
                                        ]) }}"
                                            class="btn text-white" style="background-color: #0080ff">
                                            <i class="fa fa-print me-1"></i> Print Summary
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif ($acc_id == 'SGAADVERT')
                    <div class="row">
                        <div class="card-header bg-danger">
                            <h4 style="font-weight: bold;" class="text-white">
                                <i class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>SUMMARY
                                {{ $account_name }}
                            </h4>
                        </div>
                        <div class="card rounded-0">
                            <div class="mt-4">
                                <form method="GET" action="{{ route('reports.index', $acc_id) }}">
                                    <div class="row">
                                        {{-- <div class="col-md-6 mb-2">
                                            <label class="form-label">Account name or ID search</label>
                                            <input name="cari" type="text" id="cari" class="form-control"
                                                placeholder="Pencarian" onkeyup="searchTable()" />
                                        </div> --}}
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Select Year</label>
                                            <select name="year" id="yearFilter" class="form-select"
                                                onchange="this.form.submit()">
                                                <option value="">-- All Years --</option>
                                                @foreach ($years as $year)
                                                    <option value="{{ $year }}"
                                                        {{ $selectedYear == $year ? 'selected' : '' }}>
                                                        {{ $year }}</option>
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
                                                    <th>Item</th>
                                                    <th>Description</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>
                                                    <th>Amount</th>
                                                    <th>Workcenter</th>
                                                    <th>Department</th>
                                                    <th>R/NR</th>
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
                                                        // Langsung ambil nilai amount tanpa perlu perhitungan
                                                        $total = $report->amount;
                                                        $grandTotal += $total;

                                                        $monthValues = array_fill_keys(
                                                            array_keys($monthlyTotals),
                                                            null,
                                                        );
                                                        if (!empty($report->month)) {
                                                            $month = strtoupper(substr($report->month, 0, 3));
                                                            if (array_key_exists($month, $monthValues)) {
                                                                $monthValues[$month] = $total;
                                                                $monthlyTotals[$month] += $total;
                                                            }
                                                        }
                                                    @endphp <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $report->item != null ? $report->item->item : $report->itm_id ?? '' }}
                                                        </td>
                                                        <td>{{ $report->description }}</td>
                                                        <td>{{ $report->quantity }}</td>
                                                        <td>{{ number_format($report->price, 0, ',', '.') }}</td>
                                                        <td>{{ number_format($report->amount, 0, ',', '.') }}</td>
                                                        <td>{{ $report->workcenter->workcenter ?? '' }}</td>
                                                        <td>{{ $report->dept->department ?? '' }}</td>
                                                        <td>{{ $report->budget->budget_name ?? '' }}</td>
                                                        @foreach (['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'] as $month)
                                                            <td class="text-right">
                                                                {{ $monthValues[$month] ? number_format($monthValues[$month], 0, ',', '.') : '' }}
                                                            </td>
                                                        @endforeach
                                                        <td class="text-right">
                                                            {{ number_format($total, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="21" class="text-center">No Report found!</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot>
                                                <tr style="font-weight: bold;" class="bg-danger">
                                                    <td colspan="9" class="text-center text-white">TOTAL</td>
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
                                        <button onclick="history.back()" type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Back</button>
                                        <a href="{{ route('reports.printAccount', [
                                            'acc_id' => $acc_id,
                                            'dpt_id' => $dpt_id,
                                            'year' => $selectedYear,
                                            'workcenter' => $selectedWorkcenter,
                                            'budget_name' => $selectedBudget,
                                        ]) }}"
                                            class="btn text-white" style="background-color: #0080ff">
                                            <i class="fa fa-print me-1"></i> Print Summary
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif ($acc_id == 'SGABCHARGES')
                    <div class="row">
                        <div class="card-header bg-danger">
                            <h4 style="font-weight: bold;" class="text-white">
                                <i class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>SUMMARY
                                {{ $account_name }}
                            </h4>
                        </div>
                        <div class="card rounded-0">
                            <div class="mt-4">
                                <form method="GET" action="{{ route('reports.index', $acc_id) }}">
                                    <div class="row">
                                        {{-- <div class="col-md-6 mb-2">
                                            <label class="form-label">Account name or ID search</label>
                                            <input name="cari" type="text" id="cari" class="form-control"
                                                placeholder="Pencarian" onkeyup="searchTable()" />
                                        </div> --}}
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Select Year</label>
                                            <select name="year" id="yearFilter" class="form-select"
                                                onchange="this.form.submit()">
                                                <option value="">-- All Years --</option>
                                                @foreach ($years as $year)
                                                    <option value="{{ $year }}"
                                                        {{ $selectedYear == $year ? 'selected' : '' }}>
                                                        {{ $year }}</option>
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
                                                    <th>Item</th>
                                                    <th>Description</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>
                                                    <th>Amount</th>
                                                    <th>Workcenter</th>
                                                    <th>Department</th>
                                                    <th>R/NR</th>
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
                                                        // Langsung ambil nilai amount tanpa perlu perhitungan
                                                        $total = $report->amount;
                                                        $grandTotal += $total;

                                                        $monthValues = array_fill_keys(
                                                            array_keys($monthlyTotals),
                                                            null,
                                                        );
                                                        if (!empty($report->month)) {
                                                            $month = strtoupper(substr($report->month, 0, 3));
                                                            if (array_key_exists($month, $monthValues)) {
                                                                $monthValues[$month] = $total;
                                                                $monthlyTotals[$month] += $total;
                                                            }
                                                        }
                                                    @endphp <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $report->item->item ?? '' }}</td>
                                                        <td>{{ $report->description }}</td>
                                                        <td>{{ $report->quantity }}</td>
                                                        <td>{{ number_format($report->price, 0, ',', '.') }}</td>
                                                        <td>{{ number_format($report->amount, 0, ',', '.') }}</td>
                                                        <td>{{ $report->workcenter->workcenter ?? '' }}</td>
                                                        <td>{{ $report->dept->department ?? '' }}</td>
                                                        <td>{{ $report->budget->budget_name ?? '' }}</td>
                                                        @foreach (['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'] as $month)
                                                            <td class="text-right">
                                                                {{ $monthValues[$month] ? number_format($monthValues[$month], 0, ',', '.') : '' }}
                                                            </td>
                                                        @endforeach
                                                        <td class="text-right">
                                                            {{ number_format($total, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="21" class="text-center">No Report found!</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot>
                                                <tr style="font-weight: bold;" class="bg-danger">
                                                    <td colspan="9" class="text-center text-white">TOTAL</td>
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
                                        <button onclick="history.back()" type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Back</button>
                                        <a href="{{ route('reports.printAccount', [
                                            'acc_id' => $acc_id,
                                            'dpt_id' => $dpt_id,
                                            'year' => $selectedYear,
                                            'workcenter' => $selectedWorkcenter,
                                            'budget_name' => $selectedBudget,
                                        ]) }}"
                                            class="btn text-white" style="background-color: #0080ff">
                                            <i class="fa fa-print me-1"></i> Print Summary
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @elseif ($acc_id == 'SGABOOK')
                    <div class="row">
                        <div class="card-header bg-danger">
                            <h4 style="font-weight: bold;" class="text-white">
                                <i class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>SUMMARY
                                {{ $account_name }}
                            </h4>
                        </div>
                        <div class="card rounded-0">
                            <div class="mt-4">
                                <form method="GET" action="{{ route('reports.index', $acc_id) }}">
                                    <div class="row">
                                        {{-- <div class="col-md-6 mb-2">
                                            <label class="form-label">Account name or ID search</label>
                                            <input name="cari" type="text" id="cari" class="form-control"
                                                placeholder="Pencarian" onkeyup="searchTable()" />
                                        </div> --}}
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Select Year</label>
                                            <select name="year" id="yearFilter" class="form-select"
                                                onchange="this.form.submit()">
                                                <option value="">-- All Years --</option>
                                                @foreach ($years as $year)
                                                    <option value="{{ $year }}"
                                                        {{ $selectedYear == $year ? 'selected' : '' }}>
                                                        {{ $year }}</option>
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
                                                    <th>Item</th>
                                                    <th>Description</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>
                                                    <th>Amount</th>
                                                    <th>Workcenter</th>
                                                    <th>Department</th>
                                                    <th>R/NR</th>
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
                                                        // Langsung ambil nilai amount tanpa perlu perhitungan
                                                        $total = $report->amount;
                                                        $grandTotal += $total;

                                                        $monthValues = array_fill_keys(
                                                            array_keys($monthlyTotals),
                                                            null,
                                                        );
                                                        if (!empty($report->month)) {
                                                            $month = strtoupper(substr($report->month, 0, 3));
                                                            if (array_key_exists($month, $monthValues)) {
                                                                $monthValues[$month] = $total;
                                                                $monthlyTotals[$month] += $total;
                                                            }
                                                        }
                                                    @endphp <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $report->item->item ?? '' }}</td>
                                                        <td>{{ $report->description }}</td>
                                                        <td>{{ $report->quantity }}</td>
                                                        <td>{{ number_format($report->price, 0, ',', '.') }}</td>
                                                        <td>{{ number_format($report->amount, 0, ',', '.') }}</td>
                                                        <td>{{ $report->workcenter->workcenter ?? '' }}</td>
                                                        <td>{{ $report->dept->department ?? '' }}</td>
                                                        <td>{{ $report->budget->budget_name ?? '' }}</td>
                                                        @foreach (['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'] as $month)
                                                            <td class="text-right">
                                                                {{ $monthValues[$month] ? number_format($monthValues[$month], 0, ',', '.') : '' }}
                                                            </td>
                                                        @endforeach
                                                        <td class="text-right">
                                                            {{ number_format($total, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="21" class="text-center">No Report found!</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot>
                                                <tr style="font-weight: bold;" class="bg-danger">
                                                    <td colspan="9" class="text-center text-white">TOTAL</td>
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
                                        <button onclick="history.back()" type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Back</button>
                                        <a href="{{ route('reports.printAccount', [
                                            'acc_id' => $acc_id,
                                            'dpt_id' => $dpt_id,
                                            'year' => $selectedYear,
                                            'workcenter' => $selectedWorkcenter,
                                            'budget_name' => $selectedBudget,
                                        ]) }}"
                                            class="btn text-white" style="background-color: #0080ff">
                                            <i class="fa fa-print me-1"></i> Print Summary
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif ($acc_id == 'SGARYLT')
                    <div class="row">
                        <div class="card-header bg-danger">
                            <h4 style="font-weight: bold;" class="text-white">
                                <i class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>SUMMARY
                                {{ $account_name }}
                            </h4>
                        </div>
                        <div class="card rounded-0">
                            <div class="mt-4">
                                <form method="GET" action="{{ route('reports.index', $acc_id) }}">
                                    <div class="row">
                                        {{-- <div class="col-md-6 mb-2">
                                            <label class="form-label">Account name or ID search</label>
                                            <input name="cari" type="text" id="cari" class="form-control"
                                                placeholder="Pencarian" onkeyup="searchTable()" />
                                        </div> --}}
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Select Year</label>
                                            <select name="year" id="yearFilter" class="form-select"
                                                onchange="this.form.submit()">
                                                <option value="">-- All Years --</option>
                                                @foreach ($years as $year)
                                                    <option value="{{ $year }}"
                                                        {{ $selectedYear == $year ? 'selected' : '' }}>
                                                        {{ $year }}</option>
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
                                                    <th>Item</th>
                                                    <th>Description</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>
                                                    <th>Amount</th>
                                                    <th>Workcenter</th>
                                                    <th>Department</th>
                                                    <th>R/NR</th>
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
                                                        // Langsung ambil nilai amount tanpa perlu perhitungan
                                                        $total = $report->amount;
                                                        $grandTotal += $total;

                                                        $monthValues = array_fill_keys(
                                                            array_keys($monthlyTotals),
                                                            null,
                                                        );
                                                        if (!empty($report->month)) {
                                                            $month = strtoupper(substr($report->month, 0, 3));
                                                            if (array_key_exists($month, $monthValues)) {
                                                                $monthValues[$month] = $total;
                                                                $monthlyTotals[$month] += $total;
                                                            }
                                                        }
                                                    @endphp <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $report->item->item ?? '' }}</td>
                                                        <td>{{ $report->description }}</td>
                                                        <td>{{ $report->quantity }}</td>
                                                        <td>{{ number_format($report->price, 0, ',', '.') }}</td>
                                                        <td>{{ number_format($report->amount, 0, ',', '.') }}</td>
                                                        <td>{{ $report->workcenter->workcenter ?? '' }}</td>
                                                        <td>{{ $report->dept->department ?? '' }}</td>
                                                        <td>{{ $report->budget->budget_name ?? '' }}</td>
                                                        @foreach (['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'] as $month)
                                                            <td class="text-right">
                                                                {{ $monthValues[$month] ? number_format($monthValues[$month], 0, ',', '.') : '' }}
                                                            </td>
                                                        @endforeach
                                                        <td class="text-right">
                                                            {{ number_format($total, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="21" class="text-center">No Report found!</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot>
                                                <tr style="font-weight: bold;" class="bg-danger">
                                                    <td colspan="9" class="text-center text-white">TOTAL</td>
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
                                        <button onclick="history.back()" type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Back</button>
                                        <a href="{{ route('reports.printAccount', [
                                            'acc_id' => $acc_id,
                                            'dpt_id' => $dpt_id,
                                            'year' => $selectedYear,
                                            'workcenter' => $selectedWorkcenter,
                                            'budget_name' => $selectedBudget,
                                        ]) }}"
                                            class="btn text-white" style="background-color: #0080ff">
                                            <i class="fa fa-print me-1"></i> Print Summary
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif ($acc_id == 'SGACONTRIBUTION')
                    <div class="row">
                        <div class="card-header bg-danger">
                            <h4 style="font-weight: bold;" class="text-white">
                                <i class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>SUMMARY
                                {{ $account_name }}
                            </h4>
                        </div>
                        <div class="card rounded-0">
                            <div class="mt-4">
                                <form method="GET" action="{{ route('reports.index', $acc_id) }}">
                                    <div class="row">
                                        {{-- <div class="col-md-6 mb-2">
                                            <label class="form-label">Account name or ID search</label>
                                            <input name="cari" type="text" id="cari" class="form-control"
                                                placeholder="Pencarian" onkeyup="searchTable()" />
                                        </div> --}}
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Select Year</label>
                                            <select name="year" id="yearFilter" class="form-select"
                                                onchange="this.form.submit()">
                                                <option value="">-- All Years --</option>
                                                @foreach ($years as $year)
                                                    <option value="{{ $year }}"
                                                        {{ $selectedYear == $year ? 'selected' : '' }}>
                                                        {{ $year }}</option>
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
                                                    <th>Item</th>
                                                    <th>Description</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>
                                                    <th>Amount</th>
                                                    <th>Workcenter</th>
                                                    <th>Department</th>
                                                    <th>R/NR</th>
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
                                                        // Langsung ambil nilai amount tanpa perlu perhitungan
                                                        $total = $report->amount;
                                                        $grandTotal += $total;

                                                        $monthValues = array_fill_keys(
                                                            array_keys($monthlyTotals),
                                                            null,
                                                        );
                                                        if (!empty($report->month)) {
                                                            $month = strtoupper(substr($report->month, 0, 3));
                                                            if (array_key_exists($month, $monthValues)) {
                                                                $monthValues[$month] = $total;
                                                                $monthlyTotals[$month] += $total;
                                                            }
                                                        }
                                                    @endphp <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $report->item->item ?? '' }}</td>
                                                        <td>{{ $report->description }}</td>
                                                        <td>{{ $report->quantity }}</td>
                                                        <td>{{ number_format($report->price, 0, ',', '.') }}</td>
                                                        <td>{{ number_format($report->amount, 0, ',', '.') }}</td>
                                                        <td>{{ $report->workcenter->workcenter ?? '' }}</td>
                                                        <td>{{ $report->dept->department ?? '' }}</td>
                                                        <td>{{ $report->budget->budget_name ?? '' }}</td>
                                                        @foreach (['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'] as $month)
                                                            <td class="text-right">
                                                                {{ $monthValues[$month] ? number_format($monthValues[$month], 0, ',', '.') : '' }}
                                                            </td>
                                                        @endforeach
                                                        <td class="text-right">
                                                            {{ number_format($total, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="21" class="text-center">No Report found!</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot>
                                                <tr style="font-weight: bold;" class="bg-danger">
                                                    <td colspan="9" class="text-center text-white">TOTAL</td>
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
                                        <button onclick="history.back()" type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Back</button>
                                        <a href="{{ route('reports.printAccount', [
                                            'acc_id' => $acc_id,
                                            'dpt_id' => $dpt_id,
                                            'year' => $selectedYear,
                                            'workcenter' => $selectedWorkcenter,
                                            'budget_name' => $selectedBudget,
                                        ]) }}"
                                            class="btn text-white" style="background-color: #0080ff">
                                            <i class="fa fa-print me-1"></i> Print Summary
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif ($acc_id == 'SGAASSOCIATION')
                    <div class="row">
                        <div class="card-header bg-danger">
                            <h4 style="font-weight: bold;" class="text-white">
                                <i class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>SUMMARY
                                {{ $account_name }}
                            </h4>
                        </div>
                        <div class="card rounded-0">
                            <div class="mt-4">
                                <form method="GET" action="{{ route('reports.index', $acc_id) }}">
                                    <div class="row">
                                        {{-- <div class="col-md-6 mb-2">
                                            <label class="form-label">Account name or ID search</label>
                                            <input name="cari" type="text" id="cari" class="form-control"
                                                placeholder="Pencarian" onkeyup="searchTable()" />
                                        </div> --}}
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Select Year</label>
                                            <select name="year" id="yearFilter" class="form-select"
                                                onchange="this.form.submit()">
                                                <option value="">-- All Years --</option>
                                                @foreach ($years as $year)
                                                    <option value="{{ $year }}"
                                                        {{ $selectedYear == $year ? 'selected' : '' }}>
                                                        {{ $year }}</option>
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
                                                    <th>Item</th>
                                                    <th>Description</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>
                                                    <th>Amount</th>
                                                    <th>Workcenter</th>
                                                    <th>Department</th>
                                                    <th>R/NR</th>
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
                                                        // Langsung ambil nilai amount tanpa perlu perhitungan
                                                        $total = $report->amount;
                                                        $grandTotal += $total;

                                                        $monthValues = array_fill_keys(
                                                            array_keys($monthlyTotals),
                                                            null,
                                                        );
                                                        if (!empty($report->month)) {
                                                            $month = strtoupper(substr($report->month, 0, 3));
                                                            if (array_key_exists($month, $monthValues)) {
                                                                $monthValues[$month] = $total;
                                                                $monthlyTotals[$month] += $total;
                                                            }
                                                        }
                                                    @endphp <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $report->item->item ?? '' }}</td>
                                                        <td>{{ $report->description }}</td>
                                                        <td>{{ $report->quantity }}</td>
                                                        <td>{{ number_format($report->price, 0, ',', '.') }}</td>
                                                        <td>{{ number_format($report->amount, 0, ',', '.') }}</td>
                                                        <td>{{ $report->workcenter->workcenter ?? '' }}</td>
                                                        <td>{{ $report->dept->department ?? '' }}</td>
                                                        <td>{{ $report->budget->budget_name ?? '' }}</td>
                                                        @foreach (['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'] as $month)
                                                            <td class="text-right">
                                                                {{ $monthValues[$month] ? number_format($monthValues[$month], 0, ',', '.') : '' }}
                                                            </td>
                                                        @endforeach
                                                        <td class="text-right">
                                                            {{ number_format($total, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="21" class="text-center">No Report found!</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot>
                                                <tr style="font-weight: bold;" class="bg-danger">
                                                    <td colspan="9" class="text-center text-white">TOTAL</td>
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
                                        <button onclick="history.back()" type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Back</button>
                                        <a href="{{ route('reports.printAccount', [
                                            'acc_id' => $acc_id,
                                            'dpt_id' => $dpt_id,
                                            'year' => $selectedYear,
                                            'workcenter' => $selectedWorkcenter,
                                            'budget_name' => $selectedBudget,
                                        ]) }}"
                                            class="btn text-white mb-3" style="background-color: #0080ff">
                                            <i class="fa fa-download me-1"></i> Download Excel
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning">
                        Akun dengan ID <strong>{{ $acc_id }}</strong> belum memiliki template khusus.
                    </div>
                @endif
            @else
                <div class="alert alert-danger">
                    Tidak ada ID akun yang diberikan.
                </div>
            @endif
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

            for (i = 1; i < tr.length; i++) { // Mulai dari 1 untuk menghindari baris header
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
                if (display) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    </script>
    <x-sidebar-plugin></x-sidebar-plugin>
    <!--   Core JS Files   -->
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
