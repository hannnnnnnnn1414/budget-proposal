<!DOCTYPE html>
<html lang="en">
<x-head></x-head>

<body class="g-sidenav-show bg-gray-100">
    <x-sidebar></x-sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
        <x-navbar>Budget Revision Upload</x-navbar>

        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <!-- Header -->
                        <div class="card-header bg-danger">
                            <h4 style="font-weight: bold;" class="text-white">
                                <i class="fa-solid fa-file-pen fs-4 me-2 text-white me-3"></i>
                                UPLOAD BUDGET REVISION - RE-BUDGET PLAN
                            </h4>
                        </div>

                        <div class="card-body">
                            <!-- Upload Form -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header bg-secondary text-white py-2">
                                            <h6 class="mb-0 text-white"><i class="fas fa-upload me-2"></i>Upload Budget
                                                Revision</h6>
                                        </div>
                                        <div class="card-body">
                                            <form id="uploadForm" enctype="multipart/form-data">
                                                @csrf
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label for="year" class="form-label">Tahun Budget *</label>
                                                        <select name="year" id="year" class="form-control"
                                                            required>
                                                            @foreach ($availableYears as $year)
                                                                <option value="{{ $year }}"
                                                                    {{ $year == date('Y') + 1 ? 'selected' : '' }}>
                                                                    {{ $year }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="description" class="form-label">Keterangan
                                                            Revisi</label>
                                                        <input type="text" class="form-control" name="description"
                                                            id="description" placeholder="Contoh: Revisi Q3 2025"
                                                            maxlength="500">
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="file" class="form-label">File Excel *</label>
                                                    <input type="file" class="form-control" name="file"
                                                        id="file" accept=".xlsx,.xls" required>
                                                    <small class="text-muted">
                                                        Format harus sama dengan template budget plan. Max: 10MB
                                                    </small>
                                                </div>

                                                <button type="submit" class="btn btn-danger w-100" id="uploadButton">
                                                    <i class="fas fa-upload me-2"></i>Upload Revision Data
                                                    <span class="spinner-border spinner-border-sm d-none ms-2"
                                                        id="uploadSpinner"></span>
                                                </button>
                                            </form>

                                            <div class="mt-3">
                                                <a href="{{ route('budget-revision.download-template') }}"
                                                    class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-download me-2"></i>Download Template
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Summary Data -->
                            <div class="card mt-4">
                                <div class="card-header bg-secondary text-white py-2">
                                    <h6 class="mb-0 text-white"><i class="fas fa-list me-2"></i>Data Revisions per
                                        Departemen</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <select id="filter_periode" class="form-control"
                                                    onchange="loadSummary()">
                                                    <option value="">Semua Tahun</option>
                                                    @foreach ($availableYears as $year)
                                                        <option value="{{ $year }}">{{ $year }}
                                                        </option>
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
                                                    <th>Total Items</th>
                                                    <th>Total Amount</th>
                                                    <th>Last Update</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="summaryBody">
                                                <tr>
                                                    <td colspan="7" class="text-center">
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
                                    <h6 class="mb-0 text-white"><i class="fas fa-history me-2"></i>Riwayat Upload
                                        Revisions</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Revision Code</th>
                                                    <th>Tahun</th>
                                                    <th>Keterangan</th>
                                                    <th>Jumlah Items</th>
                                                    <th>Total Amount</th>
                                                    <th>Upload By</th>
                                                    <th>Upload Date</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($uploads as $index => $upload)
                                                    @php
                                                        $data = json_decode($upload->data, true);
                                                        $isRevision =
                                                            isset($data['type']) && $data['type'] === 'revision';
                                                    @endphp
                                                    @if ($isRevision)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td><code>{{ $data['revision_code'] ?? '-' }}</code></td>
                                                            <td>{{ $upload->year }}</td>
                                                            <td>{{ $data['description'] ?? '-' }}</td>
                                                            <td>{{ $upload->total_rows ?? 0 }}</td>
                                                            <td>Rp
                                                                {{ number_format($data['total_amount'] ?? 0, 0, ',', '.') }}
                                                            </td>
                                                            <td>{{ $upload->uploader->name ?? '-' }}</td>
                                                            <td>{{ $upload->created_at->format('d/m/Y H:i') }}</td>
                                                            <td>
                                                                <button
                                                                    onclick="viewDetail('{{ $data['revision_code'] ?? '' }}')"
                                                                    class="btn btn-sm btn-info">
                                                                    <i class="fas fa-eye"></i> Detail
                                                                </button>
                                                                <button
                                                                    onclick="deleteRevision('{{ $data['revision_code'] ?? '' }}')"
                                                                    class="btn btn-sm btn-danger ms-1">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @empty
                                                    <tr>
                                                        <td colspan="9" class="text-center text-muted">
                                                            Belum ada data upload revision
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

    <!-- Modal Detail -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white">
                        <i class="fas fa-list me-2"></i>Detail Revision
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

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

                if (!confirm('Upload budget revision ini?')) {
                    return;
                }

                var formData = new FormData(this);

                $('#uploadButton').prop('disabled', true);
                $('#uploadSpinner').removeClass('d-none');

                $.ajax({
                    url: "{{ route('budget-revision.upload') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            alert('✓ ' + response.message + '\nRevision Code: ' + response
                                .revision_code + '\nTotal Amount: Rp ' + response
                                .total_amount);
                            location.reload();
                        } else {
                            alert('✗ Error: ' + response.message);
                        }
                    },
                    error: function(xhr) {
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
                url: "{{ route('budget-revision.summary') }}",
                type: 'GET',
                data: {
                    periode: periode
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
                                    <td>${item.item_count || 0}</td>
                                    <td>Rp ${item.total_amount || '0'}</td>
                                    <td>${item.last_upload || '-'}</td>
                                    <td>
                                        <button onclick="viewDetailByDept('${item.dept_code}')" 
                                                class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="deleteDeptRevision('${item.dept_code}', '${periode || 'all'}')" 
                                                class="btn btn-sm btn-danger ms-1">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });

                        $('#summaryBody').html(html);
                    } else {
                        $('#summaryBody').html(
                            '<tr><td colspan="7" class="text-center text-muted">Tidak ada data untuk periode ini</td></tr>'
                        );
                    }
                },
                error: function(xhr) {
                    console.error('Error loading summary:', xhr);
                    $('#summaryBody').html(
                        '<tr><td colspan="7" class="text-center text-danger">Error loading data</td></tr>');
                }
            });
        }

        function viewDetail(revisionCode) {
            $('#detailModal').modal('show');

            $.ajax({
                url: "{{ url('budget-revision/detail') }}/" + revisionCode,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        var html = `
                            <div class="alert alert-danger">
                                <strong>Revision Code:</strong> ${response.summary.revision_code}<br>
                                <strong>Departemen:</strong> ${response.summary.department}<br>
                                <strong>Total Items:</strong> ${response.summary.total_items}<br>
                                <strong>Total Amount:</strong> Rp ${response.summary.total_amount}
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Account</th>
                                            <th>Account Name</th>
                                            <th>Description</th>
                                            <th>Qty</th>
                                            <th>Price</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;

                        response.data.forEach(function(item) {
                            html += `
                                <tr>
                                    <td><code>${item.acc_id}</code></td>
                                    <td>${item.acc_name}</td>
                                    <td>${item.description || '-'}</td>
                                    <td class="text-end">${item.quantity}</td>
                                    <td class="text-end">${item.price}</td>
                                    <td class="text-end">${item.amount}</td>
                                </tr>
                            `;
                        });

                        html += '</tbody></table></div>';
                        $('#detailContent').html(html);
                    }
                },
                error: function() {
                    $('#detailContent').html('<div class="alert alert-danger">Error loading detail</div>');
                }
            });
        }

        function viewDetailByDept(deptCode) {
            alert('View detail untuk departemen: ' + deptCode);
        }

        function deleteRevision(revisionCode) {
            if (!confirm('Hapus revision ini? Data akan dihapus permanen.')) return;

            $.ajax({
                url: "{{ route('budget-revision.delete') }}",
                type: 'DELETE',
                data: {
                    revision_code: revisionCode,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert(response.message);
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error: ' + (xhr.responseJSON?.message || 'Gagal menghapus data'));
                }
            });
        }

        function deleteDeptRevision(deptCode, periode) {
            if (!confirm('Hapus semua revision untuk departemen ini?')) return;

            $.ajax({
                url: "{{ route('budget-revision.delete') }}",
                type: 'DELETE',
                data: {
                    dept: deptCode,
                    periode: periode,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert(response.message);
                    loadSummary();
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
