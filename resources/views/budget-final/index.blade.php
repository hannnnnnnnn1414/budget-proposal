<!DOCTYPE html>
<html lang="en">
<x-head></x-head>

<body class="g-sidenav-show bg-gray-100">
    <x-sidebar></x-sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
        <x-navbar>Budget Final Upload</x-navbar>

        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <!-- Header -->
                        <div class="card-header bg-danger">
                            <h4 style="font-weight: bold;" class="text-white">
                                <i class="fa-solid fa-file-invoice-dollar fs-4 me-2 text-white me-3"></i>
                                UPLOAD BUDGET FINAL - PLAN & BUDGET (6121)
                            </h4>
                        </div>

                        <div class="card-body">
                            <!-- Upload Form -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header bg-secondary text-white py-2">
                                            <h6 class="mb-0 text-white"><i class="fas fa-upload me-2"></i>Upload Budget
                                                Final</h6>
                                        </div>
                                        <div class="card-body">
                                            <form id="uploadForm" enctype="multipart/form-data">
                                                @csrf
                                                <div class="row mb-3">
                                                    <div class="col-md-12">
                                                        <label for="periode" class="form-label">Periode *</label>
                                                        <select name="periode" id="periode" class="form-control"
                                                            required>
                                                            @for ($year = date('Y'); $year <= date('Y') + 2; $year++)
                                                                <option value="{{ $year }}"
                                                                    {{ $year == date('Y') + 1 ? 'selected' : '' }}>
                                                                    {{ $year }}
                                                                </option>
                                                            @endfor
                                                        </select>
                                                        <small class="text-muted">Data lama untuk periode ini akan
                                                            otomatis di-overwrite</small>
                                                    </div>
                                                </div>

                                                <input type="hidden" name="tipe" value="final">

                                                <div class="mb-3">
                                                    <label for="file" class="form-label">File Excel *</label>
                                                    <input type="file" class="form-control" name="file"
                                                        id="file" accept=".xlsx,.xls" required>
                                                    <small class="text-muted">
                                                        Format harus sama dengan template FY/LO. Max: 10MB
                                                    </small>
                                                </div>

                                                <button type="submit" class="btn btn-danger w-100" id="uploadButton">
                                                    <i class="fas fa-upload me-2"></i>Upload Budget Final
                                                    <span class="spinner-border spinner-border-sm d-none ms-2"
                                                        id="uploadSpinner"></span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Summary Data -->
                            <div class="card mt-4">
                                <div class="card-header bg-secondary text-white py-2">
                                    <h6 class="mb-0 text-white"><i class="fas fa-list me-2"></i>Data Final Budget</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <select id="filter_periode" class="form-control"
                                                    onchange="loadSummary()">
                                                    <option value="">Semua Periode</option>
                                                    @foreach ($availableYears as $year)
                                                        <option value="{{ $year }}">{{ $year }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Departemen</th>
                                                    <th>Dept Code</th>
                                                    <th>Jumlah Akun</th>
                                                    <th>Total Budget</th>
                                                    <th>Tanggal Upload</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="summaryBody">
                                                <tr>
                                                    <td colspan="6" class="text-center">
                                                        <div class="spinner-border spinner-border-sm" role="status">
                                                            <span class="visually-hidden">Loading...</span>
                                                        </div>
                                                        Loading data...
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Upload History -->
                            <div class="card mt-4">
                                <div class="card-header bg-secondary text-white py-2">
                                    <h6 class="mb-0 text-white"><i class="fas fa-history me-2"></i>Riwayat Upload</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Periode</th>
                                                    <th>Departemen</th>
                                                    <th>Jumlah Akun</th>
                                                    <th>Total Budget</th>
                                                    <th>Upload By</th>
                                                    <th>Upload Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($uploads as $index => $upload)
                                                    @php
                                                        $summary = \App\Models\BudgetFinal::where(
                                                            'periode',
                                                            $upload->year,
                                                        )
                                                            ->selectRaw(
                                                                'COUNT(*) as account_count, SUM(total) as total_amount',
                                                            )
                                                            ->first();
                                                        $dept = \App\Models\BudgetFinal::where(
                                                            'periode',
                                                            $upload->year,
                                                        )->first();
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $upload->year }}</td>
                                                        <td>{{ $dept->dept ?? 'Multiple Departments' }}</td>
                                                        <td>{{ $summary->account_count ?? 0 }}</td>
                                                        <td>Rp
                                                            {{ number_format($summary->total_amount ?? 0, 0, ',', '.') }}
                                                        </td>
                                                        <td>{{ $upload->uploader->name ?? '-' }}</td>
                                                        <td>{{ $upload->created_at->format('d/m/Y H:i') }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center text-muted">
                                                            Belum ada data upload
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                        {{ $uploads->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <x-footer></x-footer>
        </div>
    </main>

    <script>
        $(document).ready(function() {
            loadSummary();

            $('#uploadForm').submit(function(e) {
                e.preventDefault();

                const fileInput = $('#file')[0];
                if (!fileInput.files.length) {
                    alert('Pilih file Excel terlebih dahulu!');
                    return;
                }

                const file = fileInput.files[0];
                const fileSize = file.size / 1024 / 1024;

                if (fileSize > 10) {
                    alert('Ukuran file maksimal 10MB!');
                    return;
                }

                if (!confirm('Data lama untuk periode ini akan dihapus. Lanjutkan?')) {
                    return;
                }

                var formData = new FormData(this);

                console.log('Form data being sent:');
                for (let pair of formData.entries()) {
                    console.log(pair[0] + ': ' + pair[1]);
                }

                $('#uploadButton').prop('disabled', true);
                $('#uploadSpinner').removeClass('d-none');

                $.ajax({
                    url: "{{ route('budget-final.upload') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log('Success response:', response);

                        if (response.success) {
                            alert('✓ ' + response.message);
                            location.reload();
                        } else {
                            alert('✗ Error: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error response:', xhr);

                        let errorMsg = 'Terjadi kesalahan saat upload';

                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }

                            if (xhr.responseJSON.errors) {
                                errorMsg += '\n\nDetail:\n';
                                $.each(xhr.responseJSON.errors, function(key, value) {
                                    errorMsg += '- ' + key + ': ' + value[0] + '\n';
                                });
                            }

                            if (xhr.responseJSON.debug) {
                                console.error('Debug info:', xhr.responseJSON.debug);
                            }
                        }

                        alert(errorMsg);
                    },
                    complete: function() {
                        $('#uploadButton').prop('disabled', false);
                        $('#uploadSpinner').addClass('d-none');
                    }
                });
            });
        });

        function loadSummary() {
            var periode = $('#filter_periode').val();

            $.ajax({
                url: "{{ route('budget-final.summary') }}",
                type: 'GET',
                data: {
                    periode: periode,
                    tipe: 'final'
                },
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        var html = '';
                        response.data.forEach(function(item) {
                            html += `
                                <tr>
                                    <td>${item.department || '-'}</td>
                                    <td><code>${item.dept_code || '-'}</code></td>
                                    <td>${item.account_count || 0}</td>
                                    <td>Rp ${item.total_amount || '0'}</td>
                                    <td>${item.last_upload || '-'}</td>
                                    <td>
                                        <button onclick="deleteFinal('${item.dept_code}', '${periode || 'all'}')" 
                                                class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });

                        $('#summaryBody').html(html);
                    } else {
                        $('#summaryBody').html(
                            '<tr><td colspan="6" class="text-center text-muted">Tidak ada data untuk periode ini</td></tr>'
                        );
                    }
                },
                error: function(xhr) {
                    console.error('Error loading summary:', xhr);
                    $('#summaryBody').html(
                        '<tr><td colspan="6" class="text-center text-danger">Error loading data</td></tr>');
                }
            });
        }

        function deleteFinal(dept, periode) {
            if (!confirm('Hapus data final untuk departemen ini?')) return;

            $.ajax({
                url: "{{ route('budget-final.delete') }}",
                type: 'DELETE',
                data: {
                    dept: dept,
                    periode: periode,
                    tipe: 'final',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert(response.message);
                    loadSummary();
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error: ' + (xhr.responseJSON?.message || 'Gagal menghapus data'));
                }
            });
        }
    </script>

    <x-sidebar-plugin></x-sidebar-plugin>
    <script src="{{ asset('js/core/popper.min.js') }}"></script>
    <script src="{{ asset('js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    <script src="{{ asset('js/soft-ui-dashboard.min.js?v=1.0.3') }}"></script>
</body>

</html>
