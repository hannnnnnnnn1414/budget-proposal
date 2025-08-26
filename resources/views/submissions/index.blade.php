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

<body class="g-sidenav-show bg-gray-100">
    <x-sidebar></x-sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
        <x-navbar :notifications="$notifications">
            Submissions</x-navbar>
        <div class="container-fluid">
            <!-- Tombol -->
            @if (!(auth()->user()->sect == 'Kadept' || auth()->user()->sect == 'Kadiv' || auth()->user()->sect == 'DIC'))
                <div class="d-flex justify-content-end align-items-center gap-2 mt-3 mb-2">
                    <div class="btn-group">
                        <button type="button" class="btn bg-danger dropdown-toggle text-white fw-bold"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-download me-1"></i> DOWNLOAD FORM
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('template.download') }}">
                                    <i class="fa fa-file-alt me-1"></i> FORM ASSET
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('template.downloadExpend') }}">
                                    <i class="fa fa-file-archive me-1"></i> FORM EXPEND
                                </a>
                            </li>
                        </ul>
                    </div>

                    <button type="button" class="btn bg-danger text-white fw-bold" data-bs-toggle="modal"
                        data-bs-target="#uploadModal">
                        <i class="fa fa-upload me-1"></i> UPLOAD
                    </button>
                </div>
            @endif

            <!-- Modal Upload -->
            <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <form id="uploadForm" action="{{ route('upload.template') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="uploadModalLabel">Upload Template</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            {{-- <div class="modal-body">
                                <div id="upload-error" class="alert alert-danger d-none"></div>
                                <div class="mb-3">
                                    <label class="form-label">Upload Type</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="upload_type"
                                            id="assetRadio" value="asset" checked onchange="uploadFormAction()">
                                        <label class="form-check-label" for="assetRadio">
                                            Asset
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="upload_type"
                                            id="expenditureRadio" value="expenditure" onchange="uploadFormAction()">
                                        <label class="form-check-label" for="expenditureRadio">
                                            Expenditure
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="purpose" class="form-label">Purpose</label>
                                    <textarea name="purpose" id="purpose" class="form-control" rows="2" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="template" class="form-label">Upload File</label>
                                    <input type="file" name="file" id="template" class="form-control"
                                        accept=".xlsx,.xls" required>
                                </div>
                                <div class="mb-3" id="proposalField" style="display: none;">
                                    <label for="proposal" class="form-label">Upload Proposal</label>
                                    <input type="file" name="proposal" id="proposal" class="form-control"
                                        accept=".pdf">
                                </div>
                            </div> --}}
                            <div class="modal-body">
                                <div id="upload-error" class="alert alert-danger d-none"></div>
                                <div class="mb-3">
                                    <label class="form-label">Upload Type</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="upload_type"
                                            id="assetRadio" value="asset" checked onchange="toggleUploadFields()">
                                        <label class="form-check-label" for="assetRadio">Capital Expenditure</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="upload_type"
                                            id="expenditureRadio" value="expenditure" onchange="toggleUploadFields()">
                                        <label class="form-check-label" for="expenditureRadio">Expense/Cost</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="purpose" class="form-label">Purpose</label>
                                    <textarea name="purpose" id="purpose" class="form-control" rows="2" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="template" class="form-label">Upload File</label>
                                    <input type="file" name="template" id="template" class="form-control"
                                        accept=".xlsx,.xls" required>
                                </div>
                                <div class="mb-3" id="proposalField" style="display: none;">
                                    <label for="proposal" class="form-label">Upload Proposal</label>
                                    <input type="file" name="proposal" id="proposal" class="form-control"
                                        accept=".pdf">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fa fa-paper-plane me-1"></i> Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            <div class="row">
                <!-- Header dengan Dropdown -->
                <div class="card-header bg-danger">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 style="font-weight: bold;" class="text-white">
                            <i class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>PT. KAYABA INDONESIA
                            ACCOUNT BUDGETING
                        </h4>
                        <div class="dropdown">
                            <button class="btn bg-white text-danger dropdown-toggle fw-bold mt-2" type="button"
                                id="submissionDropdown" data-bs-toggle="dropdown"
                                style="width: 200px;"aria-expanded="false">
                                Select Submission Type
                            </button>
                            <ul class="dropdown-menu mt-2" aria-labelledby="submissionDropdown">
                                <li><a class="dropdown-item" href="#" onclick="showTable('asset')">Submissions
                                        Asset</a></li>
                                <li><a class="dropdown-item" href="#"
                                        onclick="showTable('expenditure')">Submissions Expenditure</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Tabel untuk Asset -->
                <div class="card rounded-0">
                    <div class="mt-4">
                        <label class="form-label">Account name or ID search</label>
                        <div class="input-group">
                            <input name="cari" type="text" id="cari" class="form-control"
                                placeholder="Pencarian" onkeyup="searchTable('assetTable')" />
                        </div>
                    </div>
                    <div id="submissions-asset" class="submissions-table" style="display: block;">
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif
                            <div class="table-responsive">
                                <table id="assetTable" class="table table-striped table-bordered">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Kode</th>
                                            <th>Account Name</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($assetSubmissions as $account)
                                            <tr>
                                                <td>{{ $account->acc_id }}</td>
                                                <td>{{ $account->account }}</td>
                                                <td class="text-center">
                                                    @if (
                                                        (auth()->user()->sect == 'Kadept' && auth()->user()->dept != '6121') ||
                                                            auth()->user()->sect == 'Kadiv' ||
                                                            auth()->user()->sect == 'DIC')
                                                        <a href="{{ route('reports.index', ['acc_id' => $account->acc_id]) }}"
                                                            class="btn btn-danger d-inline-flex align-items-center justify-content-center"
                                                            style="width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                                                            title="Report">
                                                            <i class="fa-solid fa-file-export fs-6"></i>
                                                        </a>
                                                    @else
                                                        <a href="{{ route('accounts.create', ['acc_id' => $account->acc_id]) }}"
                                                            class="btn btn-success d-inline-flex align-items-center justify-content-center create-account"
                                                            style="width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                                                            title="Create">
                                                            <i class="fa-solid fa-plus fs-6"></i>
                                                        </a>
                                                        <a href="{{ route('submissions.detail', ['acc_id' => $account->acc_id]) }}"
                                                            class="btn d-inline-flex align-items-center justify-content-center text-white"
                                                            style="background-color: #0d6efd; width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                                                            title="Detail">
                                                            <i class="fa-solid fa-circle-info fs-6"></i>
                                                        </a>
                                                        <a href="{{ route('reports.index', ['acc_id' => $account->acc_id]) }}"
                                                            class="btn btn-warning d-inline-flex align-items-center justify-content-center"
                                                            style="width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                                                            title="Report">
                                                            <i class="fa-solid fa-file-export fs-6"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3">No Account found!</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div id="no-records-message-asset" class="text-center mt-3 text-secondary"
                                    style="display: none;">
                                    No matching records found
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel untuk Expenditure -->
                <div class="card rounded-0" style="display: none;" id="expenditure-table">
                    <div class="mt-4">
                        <label class="form-label">Account name or ID search</label>
                        <div class="input-group">
                            <input name="cari-expenditure" type="text" id="cari-expenditure" class="form-control"
                                placeholder="Pencarian" onkeyup="searchTable('expenditureTable')" />
                        </div>
                    </div>
                    <div id="submissions-expenditure" class="submissions-table">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="expenditureTable" class="table table-striped table-bordered">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Kode</th>
                                            <th>Account Name</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($expenditureSubmissions as $account)
                                            <tr>
                                                <td>{{ $account->acc_id }}</td>
                                                <td>{{ $account->account }}</td>
                                                <td class="text-center">
                                                    @if (
                                                        (auth()->user()->sect == 'Kadept' && auth()->user()->dept != '6121') ||
                                                            auth()->user()->sect == 'Kadiv' ||
                                                            auth()->user()->sect == 'DIC')
                                                        <a href="{{ route('reports.index', ['acc_id' => $account->acc_id]) }}"
                                                            class="btn btn-danger d-inline-flex align-items-center justify-content-center"
                                                            style="width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                                                            title="Report">
                                                            <i class="fa-solid fa-file-export fs-6"></i>
                                                        </a>
                                                    @else
                                                        <a href="{{ route('accounts.create', ['acc_id' => $account->acc_id]) }}"
                                                            class="btn btn-success d-inline-flex align-items-center justify-content-center create-account"
                                                            style="width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                                                            title="Create">
                                                            <i class="fa-solid fa-plus fs-6"></i>
                                                        </a>
                                                        <a href="{{ route('submissions.detail', ['acc_id' => $account->acc_id]) }}"
                                                            class="btn d-inline-flex align-items-center justify-content-center text-white"
                                                            style="background-color: #0d6efd; width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                                                            title="Detail">
                                                            <i class="fa-solid fa-circle-info fs-6"></i>
                                                        </a>
                                                        <a href="{{ route('reports.index', ['acc_id' => $account->acc_id]) }}"
                                                            class="btn btn-warning d-inline-flex align-items-center justify-content-center"
                                                            style="width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                                                            title="Report">
                                                            <i class="fa-solid fa-file-export fs-6"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3">No Account found!</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div id="no-records-message-expenditure" class="text-center mt-3 text-secondary"
                                    style="display: none;">
                                    No matching records found
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
        function toggleUploadFields() {
            const uploadType = document.querySelector('input[name="upload_type"]:checked').value;
            const proposalField = document.getElementById('proposalField');
            const proposalInput = document.getElementById('proposal');
            if (uploadType === 'expenditure') {
                proposalField.style.display = 'block';
                proposalInput.required = true;
            } else {
                proposalField.style.display = 'none';
                proposalInput.required = false;
                proposalInput.value = ''; // Clear proposal input when hidden
            }
            uploadFormAction();
        }

        function uploadFormAction() {
            const form = document.getElementById('uploadForm');
            const uploadType = document.querySelector('input[name="upload_type"]:checked').value;
            if (uploadType === 'asset') {
                form.action = "{{ route('upload.template') }}";
            } else {
                form.action = "{{ route('upload.templateExpend') }}";
            }
        }

        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);
            const errorDiv = document.getElementById('upload-error');
            const uploadType = document.querySelector('input[name="upload_type"]:checked').value;


            // Validate purpose
            if (!formData.get('purpose').trim()) {
                errorDiv.textContent = 'Purpose is required.';
                errorDiv.classList.remove('d-none');
                return;
            }

            // Validate files
            if (!formData.get('template')) {
                errorDiv.textContent = 'Template file is required.';
                errorDiv.classList.remove('d-none');
                return;
            }
            if (uploadType === 'expenditure' && !formData.get('proposal')) {
                errorDiv.textContent = 'Proposal PDF is required for Expenditure.';
                errorDiv.classList.remove('d-none');
                return;
            }

            fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message.includes('No data was processed') || data.message.includes('Failed')) {
                        errorDiv.textContent = data.message;
                        errorDiv.classList.remove('d-none');
                    } else {
                        errorDiv.classList.add('d-none');
                        Swal.fire({
                            icon: 'success',
                            title: 'Upload Success!',
                            html: 'Data has been successfully uploaded. Please check under each respective budget account.',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#dc3545'
                        }).then(() => {
                            form.reset(); // Reset form fields
                            toggleUploadFields(); // Reset field visibility
                            $('#uploadModal').modal('hide'); // Close modal
                            window.location.reload(); // Reload page
                        });
                    }
                })
                .catch(error => {
                    errorDiv.textContent = 'Upload failed: ' + error.message;
                    errorDiv.classList.remove('d-none');
                });
        });

        // Initialize field visibility on page load
        toggleUploadFields();

        function searchTable(tableId) {
            var input, filter, table, tr, td, i, j, txtValue;
            var visibleRows = 0;

            // Pilih input berdasarkan tableId
            input = tableId === 'assetTable' ? document.getElementById("cari") : document.getElementById(
                "cari-expenditure");
            filter = input.value.toUpperCase();
            table = document.getElementById(tableId);
            tr = table.getElementsByTagName("tr");

            for (i = 1; i < tr.length; i++) {
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
                if (display) visibleRows++;
            }

            // Tampilkan pesan jika tidak ditemukan
            var messageId = tableId === 'assetTable' ? "no-records-message-asset" : "no-records-message-expenditure";
            var noRecordsMessage = document.getElementById(messageId);
            if (noRecordsMessage) {
                noRecordsMessage.style.display = visibleRows === 0 ? "block" : "none";
            }
        }

        function showTable(type) {
            var assetTable = document.getElementById('submissions-asset').parentElement;
            var expenditureTable = document.getElementById('expenditure-table');

            if (type === 'asset') {
                assetTable.style.display = 'block';
                expenditureTable.style.display = 'none';
                document.getElementById('submissionDropdown').innerText = 'Submissions Asset';
            } else {
                assetTable.style.display = 'none';
                expenditureTable.style.display = 'block';
                document.getElementById('submissionDropdown').innerText = 'Submissions Expenditure';
            }
        }

        // document.getElementById('uploadForm').addEventListener('submit', function(e) {
        //     e.preventDefault();
        //     const form = this;
        //     const formData = new FormData(form);
        //     const errorDiv = document.getElementById('upload-error');

        //     fetch(form.action, {
        //             method: 'POST',
        //             body: formData,
        //             headers: {
        //                 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        //             }
        //         })
        //         .then(response => response.json())
        //         .then(data => {
        //             if (data.message.includes('No data was processed') || data.message.includes('Failed')) {
        //                 errorDiv.textContent = data.message;
        //                 errorDiv.classList.remove('d-none');
        //             } else {
        //                 errorDiv.classList.add('d-none');
        //                 Swal.fire({
        //                     icon: 'success',
        //                     title: 'Upload Success!',
        //                     html: 'Data has been successfully uploaded. Please check under each respective budget account.',
        //                     confirmButtonText: 'OK',
        //                     confirmButtonColor: '#dc3545'
        //                 }).then(() => {
        //                     window.location.reload();
        //                 });
        //             }
        //         })
        //         .catch(error => {
        //             errorDiv.textContent = 'Upload failed: ' + error.message;
        //             errorDiv.classList.remove('d-none');
        //         });
        // });
    </script>
    <x-sidebar-plugin></x-sidebar-plugin>
    <!-- Core JS Files -->
    <script src="{{ asset('js/core/popper.min.js') }}"></script>
    <script src="{{ asset('js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('js/plugins/chartjs.min.js') }}"></script>
    <script src="{{ asset('js/curve-chart.js') }}"></script>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
