<!-- File: C:\laragon\www\budget-proposal-hanif\budget-proposal\resources\views\index-all-divisions.blade.php -->
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
        <div class="container-fluid py-4">

            <div class="row">
                <!-- Cards for all users -->
                @if (Auth::user()->sect === 'Kadept')
                    <!-- Cards specific for Kadept -->
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="numbers">
                                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Budget Needs
                                                Approval</p>
                                            <h5 class="font-weight-bolder mb-0">
                                                {{ number_format($needsApprovalCount, 0, ',', '.') }}
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div
                                            class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                            <i class="ni ni-time-alarm text-lg opacity-10" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- [BARU] Division Submission Totals -->
                <div class="row mt-4">
                    <div class="col-lg-12 mb-lg-0 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-danger d-flex justify-content-between align-items-center">
                                <h4 style="font-weight: bold;" class="text-white">
                                    <i class="fa-solid fa-table fs-4 text-white me-3"></i>
                                    Division Submission Totals
                                </h4>
                                <div class="d-flex">
                                    <!-- Form filter -->
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
                                    </form>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <div class="table-responsive"
                                    style="max-height: 500px; overflow-y: auto; overflow-x: auto; width: 100%; background-color: white;">
                                    <table class="table table-striped" style="min-width: 800px;">
                                        <thead style="position: sticky; top: 0; z-index: 15; background-color: white;">
                                            <tr>
                                                <th
                                                    style="min-width: 200px; position: sticky; top: 0; left: 0; z-index: 20; background-color: white; box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);">
                                                    Division
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
                                                <th style="min-width: 200px; background-color: white; box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);"
                                                    class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($divisionData as $division)
                                                <tr>
                                                    <td
                                                        style="position: sticky; left: 0; z-index: 10; background-color: white; box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);">
                                                        {{ $division->name }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ number_format($division->total_previous_year, 2, ',', '.') }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ number_Format($division->total_current_year_given, 2, ',', '.') }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ number_format($division->total_current_year_requested, 2, ',', '.') }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ number_format($division->variance_last_year, 2, ',', '.') }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ number_format($division->percentage_change_last_year, 2, '.', ',') }}%
                                                    </td>
                                                    <td class="text-center">
                                                        {{ number_format($division->variance_budget_given, 2, ',', '.') }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ number_format($division->percentage_change_outlook, 2, ',', '.') }}%
                                                    </td>
                                                    <td class="text-center">
                                                        <!-- Tombol Approve dan Disapprove untuk DIC -->
                                                        @if ($division->count_submissions > 0)
                                                            <button
                                                                onclick="approveDivision('{{ $division->div_id }}', '{{ $division->name }}')"
                                                                class="btn btn-success btn-sm">
                                                                <i class="fa-solid fa-check me-1"></i>ACKNOWLEDGE
                                                            </button>
                                                            <button
                                                                onclick="rejectDivision('{{ $division->div_id }}', '{{ $division->name }}')"
                                                                class="btn btn-danger btn-sm">
                                                                <i class="fa-solid fa-times me-1"></i>REQUEST
                                                                EXPLANATION
                                                            </button>
                                                        @endif
                                                        <a href="{{ route('index-all', ['div_id' => $division->div_id, 'year' => $year, 'submission_type' => $submission_type]) }}"
                                                            class="btn btn-primary btn-sm">
                                                            <i class="fa-solid fa-eye me-1"></i>Lihat
                                                        </a>
                                                    </td>

                                                </tr>
                                            @endforeach
                                            <!-- Total Row -->
                                            <tr style="font-weight: bold; position: sticky; bottom: 0; z-index: 10;"
                                                class="bg-danger">
                                                <td
                                                    style="position: sticky; left: 0; bottom: 0; z-index: 30; background-color: #ea0606; color: white; text-align: center; box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);">
                                                    {{ $divisionTotal->name }}
                                                </td>
                                                <td class="text-white text-center">
                                                    <a href="{{ route('reports.workcenterReport', ['wct_id' => 'all', 'year' => $year - 1, 'submission_type' => $submission_type]) }}"
                                                        class="text-white text-decoration-none">
                                                        {{ number_format($divisionTotal->total_previous_year ?? 0, 2, ',', '.') }}
                                                    </a>
                                                </td>
                                                <td class="text-white text-center">
                                                    <a href="{{ route('reports.workcenterReport', ['wct_id' => 'all', 'year' => $year, 'submission_type' => $submission_type]) }}"
                                                        class="text-white text-decoration-none">
                                                        {{ number_format($divisionTotal->total_current_year_given ?? 0, 2, ',', '.') }}
                                                    </a>
                                                </td>
                                                <td class="text-white text-center">
                                                    <a href="{{ route('reports.workcenterReport', ['wct_id' => 'all', 'year' => $year, 'submission_type' => $submission_type]) }}"
                                                        class="text-white text-decoration-none">
                                                        {{ number_format($divisionTotal->total_current_year_requested ?? 0, 2, ',', '.') }}
                                                    </a>
                                                </td>
                                                <td class="text-white text-center">
                                                    {{ number_format($divisionTotal->variance_last_year ?? 0, 2, ',', '.') }}
                                                </td>
                                                <td class="text-white text-center">
                                                    {{ number_format($divisionTotal->percentage_change_last_year ?? 0, 2, ',', '.') }}%
                                                </td>
                                                <td class="text-white text-center">
                                                    {{ number_format($divisionTotal->variance_budget_given ?? 0, 2, ',', '.') }}
                                                </td>
                                                <td class="text-white text-center">
                                                    {{ number_format($divisionTotal->percentage_change_outlook ?? 0, 2, ',', '.') }}%
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

                <x-footer></x-footer>
            </div>
    </main>

    <!-- Core JS Files -->
    <script src="{{ asset('js/core/popper.min.js') }}"></script>
    <script src="{{ asset('js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('js/plugins/chartjs.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Fungsi untuk approve divisi dengan SweetAlert
        function approveDivision(div_id, divisionName) {
            Swal.fire({
                title: 'Konfirmasi Persetujuan',
                html: `Apakah Anda yakin ingin menyetujui semua pengajuan untuk divisi <strong>${divisionName}</strong>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Setujui!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang menyetujui pengajuan divisi',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch('{{ url('approvals/approve-division') }}/' + div_id, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => {
                                    throw err;
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Semua pengajuan untuk divisi berhasil disetujui.',
                                icon: 'success',
                                confirmButtonColor: '#28a745',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            let errorMessage = 'Terjadi kesalahan saat menyetujui pengajuan.';
                            if (error.message) {
                                errorMessage = error.message;
                            }
                            Swal.fire({
                                title: 'Gagal!',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonColor: '#dc3545',
                                confirmButtonText: 'OK'
                            });
                        });
                }
            });
        }

        // Fungsi untuk reject divisi dengan SweetAlert
        function rejectDivision(div_id, divisionName) {
            Swal.fire({
                title: 'Alasan Penolakan',
                html: `Masukkan alasan penolakan untuk divisi <strong>${divisionName}</strong>:`,
                input: 'textarea',
                inputLabel: 'Alasan',
                inputPlaceholder: 'Masukkan alasan penolakan...',
                inputAttributes: {
                    'aria-label': 'Masukkan alasan penolakan',
                    'maxlength': 500
                },
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Tolak Pengajuan',
                cancelButtonText: 'Batal',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Alasan penolakan harus diisi!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang menolak pengajuan divisi',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Kirim data ke server
                    fetch('{{ url('approvals/reject-division') }}/' + div_id, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                remark: result.value
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => {
                                    throw err;
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Semua pengajuan untuk divisi berhasil ditolak.',
                                icon: 'success',
                                confirmButtonColor: '#28a745',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            let errorMessage = 'Terjadi kesalahan saat menolak pengajuan.';
                            if (error.message) {
                                errorMessage = error.message;
                            }
                            Swal.fire({
                                title: 'Gagal!',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonColor: '#dc3545',
                                confirmButtonText: 'OK'
                            });
                        });
                }
            });
        }

        // Handler untuk form submit
        document.addEventListener('DOMContentLoaded', function() {
            // Tangani submit form reject
            var rejectForms = document.querySelectorAll('form[id^="rejectDivisionForm-"]');
            rejectForms.forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    var formData = new FormData(this);
                    var url = this.action;

                    fetch(url, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.message ===
                                'All submissions for division rejected successfully') {
                                alert('Semua pengajuan untuk divisi berhasil ditolak.');
                                location.reload();
                            } else {
                                alert('Gagal menolak pengajuan: ' + data.message);
                            }
                            // Tutup modal
                            var modalId = this.id.replace('rejectDivisionForm-',
                                'rejectDivisionModal-');
                            var modal = bootstrap.Modal.getInstance(document.getElementById(
                                modalId));
                            modal.hide();
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Terjadi kesalahan saat menolak pengajuan.');
                        });
                });
            });
        });

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
                            باشد: function(context) {
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
