<!DOCTYPE html>
<html lang="en">

<x-head></x-head>

<body class="g-sidenav-show bg-gray-100">
    <x-sidebar></x-sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
        <x-navbar :notifications="$notifications">Dashboard</x-navbar>
        <div class="container-fluid">
            <div class="row">
                <!-- Department Table -->
                <div class="mb-4 w-100">
                    <div class="card h-100">
                        <div class="card-header bg-danger d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <!-- Tombol Upload Data hanya untuk dept 6121 -->
                                @if (Auth::user()->dept === '6121')
                                    <button type="button" class="btn btn-light me-2" data-bs-toggle="modal"
                                        data-bs-target="#uploadModal">
                                        <i class="fa-solid fa-upload me-2"></i>Upload Data
                                    </button>
                                @endif
                                <form method="GET" action="{{ route('index') }}" class="d-flex">
                                    <!-- ... (form filter tetap sama) -->
                                </form>
                            </div>
                            <h4 style="font-weight: bold;" class="text-white">
                                <i class="fa-solid fa-table fs-4 text-white me-3"></i>Department Submission Totals
                            </h4>
                            <form method="GET" action="{{ route('index') }}" class="d-flex">
                                <select name="submission_type" onchange="this.form.submit()"
                                    class="form-select me-2 "style="width: 180px;">
                                    <option value="">-- All Submissions --</option>
                                    <option value="asset" {{ $submission_type == 'asset' ? 'selected' : '' }}>ASSET
                                    </option>
                                    <option value="expenditure"
                                        {{ $submission_type == 'expenditure' ? 'selected' : '' }}>EXPENDITURE</option>
                                </select>

                                <select name="dpt_id" onchange="this.form.submit()" class="form-select me-2 w-auto">
                                    <option value="">-- All Departments --</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->dpt_id }}"
                                            {{ $dpt_id == $dept->dpt_id ? 'selected' : '' }}>
                                            {{ $dept->department }}
                                        </option>
                                    @endforeach
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
                                            <th style="min-width: 200px;">Department</th>
                                            <th style="min-width: 150px;" class="text-center">{{ $year - 1 }}</th>
                                            <th style="min-width: 150px;" class="text-center">{{ $year }}</th>
                                            <th style="min-width: 150px;" class="text-center">Variance</th>
                                            <th style="min-width: 150px;" class="text-center">Percentage (%)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($departmentData as $data)
                                            <tr>
                                                <td>{{ $data->department }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('sumarries.byDepartmentAndYear', ['dpt_id' => $data->dpt_id, 'year' => $year - 1, 'submission_type' => $submission_type]) }}"
                                                        class="text-decoration-none">
                                                        {{ number_format($data->total_previous_year, 2, ',', '.') }}
                                                    </a>
                                                    {{-- <a href="{{ route('sumarries.byDepartmentAndYear', ['dpt_id' => $data->dpt_id, 'year' => $year - 1]) }}"
                                                        class="text-decoration-none">
                                                        {{ number_format($data->total_previous_year, 2, ',', '.') }}
                                                    </a> --}}
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('sumarries.byDepartmentAndYear', ['dpt_id' => $data->dpt_id, 'year' => $year, 'submission_type' => $submission_type]) }}"
                                                        class="text-decoration-none">
                                                        {{ number_format($data->total_current_year, 2, ',', '.') }}
                                                    </a>
                                                    {{-- <a href="{{ route('sumarries.byDepartmentAndYear', ['dpt_id' => $data->dpt_id, 'year' => $year]) }}"
                                                        class="text-decoration-none">
                                                        {{ number_format($data->total_current_year, 2, ',', '.') }}
                                                    </a> --}}
                                                </td>
                                                <td class="text-center">
                                                    {{ number_format($data->variance, 2, ',', '.') }}</td>
                                                <td class="text-center">
                                                    {{ number_format($data->percentage_change, 2, ',', '.') }}%
                                                </td>
                                            </tr>
                                        @endforeach
                                        <!-- Total Row -->
                                        <tr style="font-weight: bold; position: sticky; bottom: 0; z-index: 10; "
                                            class="bg-danger">
                                            <td class="text-center text-white">{{ $departmentTotal->department }}</td>
                                            <td class="text-white text-center">
                                                <a href="{{ route('sumarries.byDepartmentAndYear', ['dpt_id' => 'all', 'year' => $year - 1, 'submission_type' => $submission_type]) }}"
                                                    class="text-white text-decoration-none">
                                                    {{ number_format($departmentTotal->total_previous_year, 2, ',', '.') }}
                                                </a>
                                                {{-- <a href="{{ route('sumarries.byDepartmentAndYear', ['dpt_id' => 'all', 'year' => $year - 1]) }}"
                                                    class="text-white text-decoration-none">
                                                    {{ number_format($departmentTotal->total_previous_year, 2, ',', '.') }}
                                                </a> --}}
                                            </td>
                                            <td class="text-white text-center">
                                                <a href="{{ route('sumarries.byDepartmentAndYear', ['dpt_id' => 'all', 'year' => $year, 'submission_type' => $submission_type]) }}"
                                                    class="text-white text-decoration-none">
                                                    {{ number_format($departmentTotal->total_current_year, 2, ',', '.') }}
                                                </a>
                                                {{-- <a href="{{ route('sumarries.byDepartmentAndYear', ['dpt_id' => 'all', 'year' => $year]) }}"
                                                    class="text-white text-decoration-none">
                                                    {{ number_format($departmentTotal->total_current_year, 2, ',', '.') }}
                                                </a> --}}
                                            </td>
                                            <td class="text-white text-center">
                                                {{ number_format($departmentTotal->variance, 2, ',', '.') }}
                                            </td>
                                            <td class="text-white text-center">
                                                {{ number_format($departmentTotal->total_previous_year > 0 ? (($departmentTotal->total_current_year - $departmentTotal->total_previous_year) / $departmentTotal->total_previous_year) * 100 : 0, 2, ',', '.') }}%
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Department Chart -->
                {{-- <div class="mb-4 w-100">
                    <div class="card h-100">
                        <div class="card-header bg-dark d-flex justify-content-between align-items-center">
                            <h4 style="font-weight: bold;" class="text-white">
                                <i class="fa-solid fa-file-invoice fs-4 text-white me-3"></i>GRAFIK BUDGET DEPARTMENT
                            </h4>
                            <form method="GET" action="{{ route('index') }}">
                                <select name="dpt_id" onchange="this.form.submit()" class="form-select w-auto">
                                    <option value="">-- All Departments --</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->dpt_id }}"
                                            {{ $dpt_id == $dept->dpt_id ? 'selected' : '' }}>
                                            {{ $dept->department }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="year" value="{{ $year }}">
                                <input type="hidden" name="month" value="{{ $month }}">
                            </form>
                        </div>
                        <div class="card-body p-3">
                            <canvas id="departmentChart" height="250"></canvas>
                        </div>
                    </div>
                </div> --}}

                <!-- Modal Upload -->
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

                {{-- <div class="row"> --}}
                <!-- Monthly Chart -->
                <div class="mb-4 ">
                    <div class="card h-100">
                        <div class="card-header bg-danger d-flex justify-content-between align-items-center">
                            <h4 style="font-weight: bold;" class="text-white">
                                <i class="fa-solid fa-chart-bar fs-4 text-white me-3"></i>Monthly Submission Totals
                            </h4>
                            <form method="GET" action="{{ route('index') }}" class="d-flex">
                                {{-- <select name="year" onchange="this.form.submit()" class="form-select me-2 w-auto">
                                    @foreach ($years as $y)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endforeach
                                </select> --}}
                                {{-- <select name="month" onchange="this.form.submit()" class="form-select me-2 w-auto">
                                    @foreach ($months as $key => $value)
                                        <option value="{{ $key }}" {{ $month == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select> --}}
                                {{-- <select name="dpt_id" onchange="this.form.submit()" class="form-select w-auto">
                                    <option value="">-- All Departments --</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->dpt_id }}"
                                            {{ $dpt_id == $dept->dpt_id ? 'selected' : '' }}>
                                            {{ $dept->department }}
                                        </option>
                                    @endforeach
                                </select> --}}
                            </form>
                        </div>
                        <div class="card-body p-3">
                            <canvas id="monthlyChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-danger d-flex justify-content-between align-items-center">
                            <h4 style="font-weight: bold;" class="text-white">
                                <i class="fa-solid fa-chart-pie fs-4 text-white me-3"></i>Department Submission
                                Percentage
                            </h4>
                            {{-- <form method="GET" action="{{ route('index') }}" class="d-flex">
                                <select name="year" onchange="this.form.submit()" class="form-select me-2 w-auto">
                                    @foreach ($years as $y)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endforeach
                                </select>
                                <select name="dpt_id" onchange="this.form.submit()" class="form-select w-auto">
                                    <option value="">-- All Departments --</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->dpt_id }}"
                                            {{ $dpt_id == $dept->dpt_id ? 'selected' : '' }}>
                                            {{ $dept->department }}
                                        </option>
                                    @endforeach
                                </select>
                            </form> --}}
                        </div>
                        <div class="card-body p-3" style="max-height: 700px;width:100%;overflow-x:auto">
                            <canvas id="departmentPieChart" width="300px"
                                style="max-height: 600px; max-width: 1150px"></canvas>
                        </div>
                    </div>
                </div>
                {{-- </div> --}}

                <x-footer></x-footer>
            </div>
    </main>

    <!-- Scripts -->
    <script src="{{ asset('js/core/popper.min.js') }}"></script>
    <script src="{{ asset('js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('js/plugins/chartjs.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script>
        // Department Chart
        // new Chart(document.getElementById('departmentChart'), {
        //     type: 'bar',
        //     data: {
        //         labels: [
        //             @foreach ($departmentData as $data)
        //                 '{{ $data->department }}',
        //             @endforeach
        //         ],
        //         datasets: [
        //             {
        //                 label: 'Total Cost ({{ $year }})',
        //                 data: [
        //                     @foreach ($departmentData as $data)
        //                         {{ $data->total_current_year }},
        //                     @endforeach
        //                 ],
        //                 backgroundColor: 'rgba(75, 192, 192, 0.6)',
        //                 borderColor: 'rgba(75, 192, 192, 1)',
        //                 borderWidth: 1
        //             },
        //             {
        //                 label: 'Total Cost ({{ $year - 1 }})',
        //                 data: [
        //                     @foreach ($departmentData as $data)
        //                         {{ $data->total_previous_year }},
        //                     @endforeach
        //                 ],
        //                 backgroundColor: 'rgba(153, 102, 255, 0.6)',
        //                 borderColor: 'rgba(153, 102, 255, 1)',
        //                 borderWidth: 1
        //             }
        //         ]
        //     },
        //     options: {
        //         indexAxis: 'y',
        //         responsive: true,
        //         scales: {
        //             x: {
        //                 beginAtZero: true,
        //                 title: {
        //                     display: true,
        //                     text: 'Total (IDR)'
        //                 }
        //             },
        //             y: {
        //                 title: {
        //                     display: true,
        //                     text: 'Department'
        //                 }
        //             }
        //         },
        //         plugins: {
        //             legend: {
        //                 display: true,
        //                 position: 'top'
        //             }
        //         }
        //     }
        // });

        // Monthly Chart
        new Chart(document.getElementById('monthlyChart'), {
            type: 'line',
            data: {
                labels: [
                    @foreach ($monthlyDataFormatted as $data)
                        '{{ $months[$data->month] }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Total Submissions ({{ $year }})',
                    data: [
                        @foreach ($monthlyDataFormatted as $data)
                            {{ $data->total }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total (IDR)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });

        // Scrollbar init
        // Scrollbar init (keep this as is)
        function generateColors(count) {
            const colors = [];
            for (let i = 0; i < count; i++) {
                const hue = (i * 360 / count) % 360;
                colors.push(`hsl(${hue}, 70%, 60%)`);
            }
            return colors;
        }

        // Prepare data for sorting
        const departmentLabels = [
            @foreach ($departmentDataWithPercentage as $data)
                '{{ $data->department }}',
            @endforeach
        ];

        const departmentData = [
            @foreach ($departmentDataWithPercentage as $data)
                {{ $data->percentage }},
            @endforeach
        ];

        // Combine labels and data into an array of objects
        const combinedData = departmentLabels.map((label, index) => ({
            label: label,
            percentage: departmentData[index]
        }));

        // Sort by percentage in descending order
        combinedData.sort((a, b) => b.percentage - a.percentage);

        // Extract sorted labels and data
        const sortedLabels = combinedData.map(item => item.label);
        const sortedData = combinedData.map(item => item.percentage);

        // Generate colors based on sorted data
        const pieColors = generateColors(sortedLabels.length);

        // Create the pie chart with sorted data
        new Chart(document.getElementById('departmentPieChart'), {
            type: 'pie',
            data: {
                labels: sortedLabels.slice(0, 10),
                datasets: [{
                    data: sortedData.slice(0, 10),
                    backgroundColor: pieColors.slice(0, 10),
                    borderColor: '#fff',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'right',
                        labels: {
                            boxWidth: 19,
                            padding: 10,
                            font: {
                                size: 10
                            },
                            generateLabels: function(chart) {
                                // Gunakan original sortedLabels dan sortedData, BUKAN yang dipotong
                                return sortedLabels.map((label, index) => {
                                    const value = sortedData[index];
                                    const bgColor = pieColors[index] ||
                                        '#ccc'; // fallback warna abu jika tidak ada
                                    return {
                                        text: `${label}: ${value.toFixed(2)}%`,
                                        fillStyle: bgColor,
                                        strokeStyle: '#fff',
                                        index: index
                                    };
                                });
                            }
                            // generateLabels: function(chart) {
                            //     const data = chart.data;
                            //     return data.labels.map((label, index) => {
                            //         const value = data.datasets[0].data[index];
                            //         return {
                            //             text: `${label}: ${value.toFixed(2)}%`,
                            //             fillStyle: data.datasets[0].backgroundColor[index],
                            //             index: index
                            //         };
                            //     });
                            // }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                return `${label}: ${value.toFixed(2)}%`;
                            }
                        }
                    }
                }
            }
        });

        // Scrollbar init
        if (navigator.platform.indexOf('Win') > -1 && document.querySelector('#sidenav-scrollbar')) {
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), {
                damping: '0.5'
            });
        }
    </script>

    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="{{ asset('js/soft-ui-dashboard.min.js?v=1.0.3') }}"></script>
</body>

</html>
