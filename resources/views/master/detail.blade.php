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
    @if ($dim_id == 1)
        <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
            <x-navbar :notifications="$notifications">Approval</x-navbar>
            <div class="container-fluid">
                <div class="d-flex justify-content-end mb-3">
                    <a href="#" class="btn btn-danger" title="Create New Entry"
                        onclick="showCreateModal('{{ route('dimensions.create', ['dim_id' => $dim_id]) }}')">
                        <i class="fa-solid fa-plus me-2"></i>Add New
                    </a>
                </div>
                <div class="row">
                    <div class="card-header bg-danger">
                        <h4 style="font-weight: bold;" class="text-white"><i
                                class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>LINE OF BUSINESS DATA
                        </h4>
                    </div>
                    <div class="card rounded-0">
                        <div class="mt-4">
                            <label class="form-label">Account name or ID search</label>
                            <div class="input-group">
                                <input name="cari" type="text" id="cari" class="form-control"
                                    placeholder="Pencarian" onkeyup="searchTable()" />
                            </div>
                        </div>
                        <div id="submissions">
                            <div class="card-body table-responsive">
                                @if (session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif
                                @if (session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif
                                <table id="myTable" class="table table-striped table-bordered">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Kode</th>
                                            <th>Line Of Business</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($data as $item)
                                            <tr class="text-center">
                                                <td>{{ $item->lob_id }}</td>
                                                <td>{{ $item->line_business }}</td>
                                                <td class="text-center">
                                                    @if ($item->status == 0)
                                                        <span class="badge bg-danger">DISABLED</span>
                                                    @elseif ($item->status == 1)
                                                        <span class="badge"
                                                            style="background-color: #0080ff">ACTIVE</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <button class="btn-warning"
                                                            onclick="showEditModal('{{ route('dimensions.edit', ['dim_id' => $dim_id, 'id' => $item->id]) }}')"
                                                            title="Update">
                                                            <i class="fa-solid fa-edit"></i>
                                                        </button>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input status-switch"
                                                                title="Active/Disabled" type="checkbox"
                                                                data-id="{{ $item->id }}"
                                                                data-dim="{{ $dim_id }}"
                                                                {{ $item->status == 1 ? 'checked' : '' }}>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4">No Submissions found!</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div id="no-records-message" class="text-center mt-3 text-secondary"
                                    style="display: none;">
                                    No matching records found
                                </div>
                                <br>
                                <div class="d-flex justify-content-between gap-2">
                                    <button onclick="history.back()" type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Back</button>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title text-white" id="createModalLabel">Add New Line of Business</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="modalBody">
                            <!-- Content will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title text-white" id="editModalLabel">Edit Line of Business</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="editModalBody">
                            <!-- Content will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
            <x-footer></x-footer>
        </main>
    @elseif ($dim_id == 2)
        <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
            <x-navbar :notifications="$notifications">Approval</x-navbar>
            <div class="container-fluid">
                <div class="d-flex justify-content-end mb-3">
                    <a href="#" class="btn btn-danger" title="Create New Entry"
                        onclick="showCreateModal('{{ route('dimensions.create', ['dim_id' => $dim_id]) }}')">
                        <i class="fa-solid fa-plus me-2"></i>Add New
                    </a>
                </div>
                <div class="row">
                    <div class="card-header bg-danger">
                        <h4 style="font-weight: bold;" class="text-white"><i
                                class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>DEPARTMENT DATA
                        </h4>
                    </div>
                    <div class="card rounded-0">
                        <div class="mt-4">
                            <label class="form-label">Department name or ID search</label>
                            <div class="input-group">
                                <input name="cari" type="text" id="cari" class="form-control"
                                    placeholder="Pencarian" onkeyup="searchTable()" />
                            </div>
                        </div>
                        <div id="submissions">
                            <div class="card-body table-responsive">
                                @if (session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif
                                @if (session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif
                                <table id="myTable" class="table table-striped table-bordered">
                                    <thead>
                                        <tr class="text-center">
                                            <th>No</th>
                                            <th>Department</th>
                                            <th>Level</th>
                                            <th>Parent</th>
                                            <th>Alloc</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($data as $item)
                                            <tr class="text-center">
                                                <td>{{ $item->dpt_id }}</td>
                                                <td>{{ $item->department }}</td>
                                                <td>{{ $item->level }}</td>
                                                <td>{{ $item->parent }}</td>
                                                <td>{{ $item->alloc }}</td>
                                                <td class="text-center">
                                                    @if ($item->status == 0)
                                                        <span class="badge bg-danger">DISABLED</span>
                                                    @elseif ($item->status == 1)
                                                        <span class="badge"
                                                            style="background-color: #0080ff">ACTIVE</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <button class="btn-warning"
                                                            onclick="showEditModal('{{ route('dimensions.edit', ['dim_id' => $dim_id, 'id' => $item->id]) }}')"
                                                            title="Update">
                                                            <i class="fa-solid fa-edit"></i>
                                                        </button>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input status-switch"
                                                                title="Active/Disabled" type="checkbox"
                                                                data-id="{{ $item->id }}"
                                                                data-dim="{{ $dim_id }}"
                                                                {{ $item->status == 1 ? 'checked' : '' }}>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4">No Submissions found!</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div id="no-records-message" class="text-center mt-3 text-secondary"
                                    style="display: none;">
                                    No matching records found
                                </div>
                                <br>
                                <div class="d-flex justify-content-between gap-2">
                                    <button onclick="history.back()" type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Back</button>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title text-white" id="createModalLabel">Add New Department</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="modalBody">
                            <!-- Content will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title text-white" id="editModalLabel">Edit Department</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="editModalBody">
                            <!-- Content will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
            <x-footer></x-footer>
        </main>
    @elseif ($dim_id == 3)
        <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
            <x-navbar :notifications="$notifications">Approval</x-navbar>
            <div class="container-fluid">
                <div class="d-flex justify-content-end mb-3">
                    <a href="#" class="btn btn-danger" title="Create New Entry"
                        onclick="showCreateModal('{{ route('dimensions.create', ['dim_id' => $dim_id]) }}')">
                        <i class="fa-solid fa-plus me-2"></i>Add New
                    </a>
                </div>
                <div class="row">
                    <div class="card-header bg-danger">
                        <h4 style="font-weight: bold;" class="text-white"><i
                                class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>WORKCENTER DATA
                        </h4>
                    </div>
                    <div class="card rounded-0">
                        <div class="mt-4">
                            <label class="form-label">Workcenter name or ID search</label>
                            <div class="input-group">
                                <input name="cari" type="text" id="cari" class="form-control"
                                    placeholder="Pencarian" onkeyup="searchTable()" />
                            </div>
                        </div>
                        <div id="submissions">
                            <div class="card-body table-responsive">
                                @if (session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif
                                @if (session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif
                                <table id="myTable" class="table table-striped table-bordered">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Kode</th>
                                            <th>Workcenter</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($data as $item)
                                            <tr class="text-center">
                                                <td>{{ $item->wct_id }}</td>
                                                <td>{{ $item->workcenter }}</td>
                                                <td class="text-center">
                                                    @if ($item->status == 0)
                                                        <span class="badge bg-danger">DISABLED</span>
                                                    @elseif ($item->status == 1)
                                                        <span class="badge"
                                                            style="background-color: #0080ff">ACTIVE</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <button class="btn-warning"
                                                            onclick="showEditModal('{{ route('dimensions.edit', ['dim_id' => $dim_id, 'id' => $item->id]) }}')"
                                                            title="Update">
                                                            <i class="fa-solid fa-edit"></i>
                                                        </button>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input status-switch"
                                                                title="Active/Disabled" type="checkbox"
                                                                data-id="{{ $item->id }}"
                                                                data-dim="{{ $dim_id }}"
                                                                {{ $item->status == 1 ? 'checked' : '' }}>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4">No Submissions found!</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div id="no-records-message" class="text-center mt-3 text-secondary"
                                    style="display: none;">
                                    No matching records found
                                </div>
                                <br>
                                <div class="d-flex justify-content-between gap-2">
                                    <button onclick="history.back()" type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Back</button>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title text-white" id="createModalLabel">Add New Workcenter</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="modalBody">
                            <!-- Content will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title text-white" id="editModalLabel">Edit Workcenter</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="editModalBody">
                            <!-- Content will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
            <x-footer></x-footer>
        </main>
    @elseif ($dim_id == 4)
        <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
            <x-navbar :notifications="$notifications">Approval</x-navbar>
            <div class="container-fluid">
                <div class="d-flex justify-content-end mb-3">
                    <a href="#" class="btn btn-danger" title="Create New Entry"
                        onclick="showCreateModal('{{ route('dimensions.create', ['dim_id' => $dim_id]) }}')">
                        <i class="fa-solid fa-plus me-2"></i>Add New
                    </a>
                </div>
                <div class="row">
                    <div class="card-header bg-danger">
                        <h4 style="font-weight: bold;" class="text-white"><i
                                class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>BUDGET CODE DATA
                        </h4>
                    </div>
                    <div class="card rounded-0">
                        <div class="mt-4">
                            <label class="form-label">Budget Code name or ID search</label>
                            <div class="input-group">
                                <input name="cari" type="text" id="cari" class="form-control"
                                    placeholder="Pencarian" onkeyup="searchTable()" />
                            </div>
                        </div>
                        <div id="submissions">
                            <div class="card-body table-responsive">
                                @if (session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif
                                @if (session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif
                                <table id="myTable" class="table table-striped table-bordered">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Kode</th>
                                            <th>Budget Code</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($data as $item)
                                            <tr class="text-center">
                                                <td>{{ $item->bdc_id }}</td>
                                                <td>{{ $item->budget_name }}</td>
                                                <td class="text-center">
                                                    @if ($item->status == 0)
                                                        <span class="badge bg-danger">DISABLED</span>
                                                    @elseif ($item->status == 1)
                                                        <span class="badge"
                                                            style="background-color: #0080ff">ACTIVE</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <button class="btn-warning"
                                                            onclick="showEditModal('{{ route('dimensions.edit', ['dim_id' => $dim_id, 'id' => $item->id]) }}')"
                                                            title="Update">
                                                            <i class="fa-solid fa-edit"></i>
                                                        </button>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input status-switch"
                                                                title="Active/Disabled" type="checkbox"
                                                                data-id="{{ $item->id }}"
                                                                data-dim="{{ $dim_id }}"
                                                                {{ $item->status == 1 ? 'checked' : '' }}>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4">No Submissions found!</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div id="no-records-message" class="text-center mt-3 text-secondary"
                                    style="display: none;">
                                    No matching records found
                                </div>
                                <br>
                                <div class="d-flex justify-content-between gap-2">
                                    <button onclick="history.back()" type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Back</button>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title text-white" id="createModalLabel">Add New Budget Code</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="modalBody">
                            <!-- Content will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title text-white" id="editModalLabel">Edit Budget Code</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="editModalBody">
                            <!-- Content will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
            <x-footer></x-footer>
        </main>
    @endif
    <script src="{{ asset('js/core/popper.min.js') }}"></script>
    <script src="{{ asset('js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('js/plugins/chartjs.min.js') }}"></script>
    <script src="{{ asset('js/curve-chart.js') }}"></script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="{{ asset('js/soft-ui-dashboard.min.js?v=1.0.3') }}"></script>
    <!-- Add SweetAlert2 library for beautiful pop-ups -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function searchTable() {
            var input, filter, table, tr, td, i, j, txtValue;
            var visibleRows = 0; // Initialize visibleRows
            input = document.getElementById("cari");
            filter = input.value.toUpperCase();
            table = document.getElementById("myTable");
            tr = table.getElementsByTagName("tr");

            // Start from i = 1 to skip the header row
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
                if (display) visibleRows++; // Increment visibleRows if the row is visible
            }

            // Show or hide the "No matching records found" message
            var noRecordsMessage = document.getElementById("no-records-message");
            noRecordsMessage.style.display = visibleRows === 0 ? "block" : "none";
        }
        // Function to show success message after update
        function showSuccessMessage(message) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: message,
                showConfirmButton: true,
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.reload(); // Reload setelah user klik OK
                }
            });
        }

        // Function to show confirmation dialog for status change
        function confirmStatusChange(id, dimId, isChecked) {
            const entityTypes = {
                1: 'Line of Business',
                2: 'Department',
                3: 'Workcenter',
                4: 'Budget Code'
            };
            const entityType = entityTypes[dimId] || 'Item';

            if (isChecked) {
                // Activating
                Swal.fire({
                    title: `Activate ${entityType}?`,
                    text: `Are you sure you want to activate this ${entityType.toLowerCase()}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, activate it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateStatus(id, dimId, 1, `${entityType} has been successfully activated!`);
                    } else {
                        // Revert the switch if user cancels
                        document.querySelector(`.status-switch[data-id="${id}"]`).checked = false;
                    }
                });
            } else {
                // Disabling
                Swal.fire({
                    title: `Disable ${entityType}?`,
                    text: `Are you sure you want to disable this ${entityType.toLowerCase()}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, disable it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateStatus(id, dimId, 0, `${entityType} has been disabled.`);
                    } else {
                        // Revert the switch if user cancels
                        document.querySelector(`.status-switch[data-id="${id}"]`).checked = true;
                    }
                });
            }
        }

        // Function to update status via AJAX
        function updateStatus(id, dimId, status, successMessage) {
            fetch(`/dimensions/${dimId}/status/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        status: status
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSuccessMessage(successMessage);
                    } else {
                        // Revert the switch on failure
                        const switchElement = document.querySelector(`.status-switch[data-id="${id}"]`);
                        if (switchElement) {
                            switchElement.checked = !status;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Failed to update status'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Revert the switch on error
                    const switchElement = document.querySelector(`.status-switch[data-id="${id}"]`);
                    if (switchElement) {
                        switchElement.checked = !status;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while updating status'
                    });
                });
        }

        // Update the event listeners for status switches
        document.querySelectorAll('.status-switch').forEach(switchElement => {
            switchElement.addEventListener('change', function() {
                const id = this.getAttribute('data-id');
                const dimId = this.getAttribute('data-dim');
                const isChecked = this.checked;

                confirmStatusChange(id, dimId, isChecked);
            });
        });

        // Modify the form submission handlers to show success messages
        function showCreateModal(url) {
            fetch(url)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('modalBody').innerHTML = html;
                    const modal = new bootstrap.Modal(document.getElementById('createModal'));
                    modal.show();

                    const form = document.getElementById('createForm');
                    if (form) {
                        form.addEventListener('submit', function(e) {
                            e.preventDefault();

                            const formData = new FormData(form);
                            const idValue = formData.get('lob_id') || formData.get('dpt_id') ||
                                formData.get('wct_id') || formData.get('bdc_id');

                            fetch(form.action, {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                            .content,
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        return response.json().then(err => Promise.reject(err));
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data.success) {
                                        modal.hide();
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success!',
                                            text: data.message,
                                            showConfirmButton: true,
                                            confirmButtonText: 'OK'
                                        }).then(() => {
                                            window.location.reload();
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);

                                    // ✅ Handle duplicate fields
                                    if (error.errors && error.duplicate_fields) {
                                        let duplicateMessages = '';
                                        error.duplicate_fields.forEach(field => {
                                            const label = error.field_labels?.[field] || field;
                                            const value = formData.get(field);
                                            duplicateMessages +=
                                                `The ${label} is already in use.<br>`;
                                        });

                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Duplicate Entry',
                                            html: duplicateMessages,
                                            showConfirmButton: true,
                                            confirmButtonText: 'OK'
                                        });
                                    }
                                    // 🧩 General validation errors
                                    else if (error.errors) {
                                        let errorMessages = '';
                                        for (const [field, messages] of Object.entries(error.errors)) {
                                            errorMessages += messages.join('<br>') + '<br>';
                                        }

                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Validation Error',
                                            html: errorMessages,
                                            showConfirmButton: true,
                                            confirmButtonText: 'OK'
                                        });
                                    }
                                    // ❌ Other unexpected errors
                                    else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: error.message ||
                                                'An error occurred while creating data',
                                            showConfirmButton: true,
                                            confirmButtonText: 'OK'
                                        });
                                    }
                                });
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading form:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load form',
                        showConfirmButton: true,
                        confirmButtonText: 'OK'
                    });
                });
        }


        function showEditModal(url) {
            fetch(url)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('editModalBody').innerHTML = html;
                    const modal = new bootstrap.Modal(document.getElementById('editModal'));
                    modal.show();

                    const form = document.getElementById('editForm');
                    if (form) {
                        form.addEventListener('submit', function(e) {
                            e.preventDefault();

                            // Extract dimId as a string
                            const dimId = String(form.dim_id?.value || url.match(/\/(\d+)\/\d+$/)?.[1] || '');
                            const idFieldMap = {
                                '1': 'lob_id',
                                '2': 'dpt_id',
                                '3': 'wct_id',
                                '4': 'bdc_id',
                            };
                            const idField = idFieldMap[dimId] || '';
                            const idValue = form[idField]?.value || 'Unknown';

                            // Log form data for debugging
                            const formData = new FormData(form);
                            console.log('Form data:', Object.fromEntries(formData));
                            console.log('dimId:', dimId, 'idField:', idField, 'idValue:', idValue);

                            const data = new URLSearchParams(formData);

                            fetch(form.action, {
                                    method: 'PUT',
                                    body: data,
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                            .content,
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        return response.json().then(err => Promise.reject(err));
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data.success) {
                                        modal.hide();
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success!',
                                            text: data.message,
                                            confirmButtonText: 'OK'
                                        }).then(() => window.location.reload());
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: data.message || 'Failed to update entry',
                                            confirmButtonText: 'OK'
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error('Server error:', error);

                                    if (error.errors && idField && error.errors[idField]) {
                                        // Handle duplicate ID error
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Duplicate ID',
                                            html: `The ID <strong>"${idValue}"</strong> is already in use.<br>Please use a different ID.`,
                                            confirmButtonText: 'OK'
                                        });
                                    } else if (error.errors) {
                                        // Handle other validation errors
                                        const errorMessages = Object.values(error.errors).flat().join(
                                            '<br>');
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Duplicate Entry',
                                            html: errorMessages,
                                            confirmButtonText: 'OK'
                                        });
                                    } else {
                                        // Handle unexpected errors
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: error.message || 'An unexpected error occurred',
                                            confirmButtonText: 'OK'
                                        });
                                    }
                                });
                        });
                    } else {
                        console.error('Form not found');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Form not found in modal',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading form:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load form',
                        confirmButtonText: 'OK'
                    });
                });
        }
    </script>
</body>

</html>
