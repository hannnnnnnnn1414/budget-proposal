<!DOCTYPE html>
<html lang="en">

<x-head></x-head>

<body class="g-sidenav-show bg-gray-100">
    <x-sidebar></x-sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
        <x-navbar :notifications="$notifications">Departments</x-navbar>
        <div class="container-fluid">
            <div class="d-flex justify-content-end mb-3">
                <a href="#" class="btn btn-danger" title="Create New Entry"
                    onclick="showCreateModal('{{ route('currencies.create') }}')">
                    <i class="fa-solid fa-plus me-2"></i>Add New
                </a>
            </div>
            <div class="row">
                <div class="card-header bg-danger">
                    <h4 style="font-weight: bold;" class="text-white"><i
                            class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>PT. KAYABA INDONESIA
                        SUPLLIERS
                    </h4>
                </div>
                <div class="card rounded-0">
                    <div class="mt-4">
                        <label class="form-label">Currency name or ID search</label>
                        <div class="input-group">
                            <input name="cari" type="text" id="cari" class="form-control"
                                placeholder="Pencarian" onkeyup="searchTable()" />
                        </div>
                    </div>
                    <div id="dimensions">
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif
                            <table id="myTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th>ID</th>
                                        <th>Currency</th>
                                        <th>Nominal</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($currencies as $currency)
                                        <tr class="text-center">
                                            <td>{{ $currency->cur_id }}</td>
                                            <td>{{ $currency->currency }}</td>
                                            <td>{{ $currency->nominal }}</td>
                                            <td class="text-center">
                                                @if ($currency->status == 0)
                                                    <span class="badge bg-danger">DISABLED</span>
                                                @elseif ($currency->status == 1)
                                                    <span class="badge" style="background-color: #0080ff">ACTIVE</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <button class="btn-warning"
                                                        onclick="showEditModal('{{ route('currencies.edit', $currency->id) }}')"
                                                        title="Update">
                                                        <i class="fa-solid fa-edit"></i>
                                                    </button>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input status-switch" type="checkbox"
                                                            title="Active/Disabled" data-id="{{ $currency->id }}"
                                                            {{ $currency->status == 1 ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5">No Currency found!</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div id="no-records-message" class="text-center mt-3 text-secondary" style="display: none;">
                                No matching records found
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title text-white" id="createModalLabel">Add New Currency</h5>
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
                        <h5 class="modal-title text-white" id="editModalLabel">Edit Currency</h5>
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <script src="{{ asset('js/core/popper.min.js') }}"></script>
    <script src="{{ asset('js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/plugins/chartjs.min.js') }}"></script>
    <script src="{{ asset('js/curve-chart.js') }}"></script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="{{ asset('js/soft-ui-dashboard.min.js?v=1.0.3') }}"></script>
    <!-- Add SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Function to show success message
        function showSuccessMessage(message) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: message,
                showConfirmButton: true,
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.reload();
                }
            });
        }

        // Function to show error message
        function showErrorMessage(message) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message,
                showConfirmButton: true,
                confirmButtonText: 'OK'
            });
        }

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

                            const curId = form.cur_id.value;

                            fetch(form.action, {
                                    method: 'POST',
                                    body: new FormData(form),
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
                                        showSuccessMessage(data.message);
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);

                                    // Handle duplicate fields
                                    if (error.errors && error.duplicate_fields) {
                                        let duplicateMessages = '';

                                        // Custom messages for each field
                                        const fieldLabels = {
                                            'cur_id': 'ID',
                                            'currency': 'Currency'
                                        };

                                        error.duplicate_fields.forEach(field => {
                                            const label = fieldLabels[field] || field;
                                            const value = form.querySelector(`[name="${field}"]`)
                                                .value;
                                            duplicateMessages += `The ${label} is already in use.`;
                                        });

                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Duplicate Entry',
                                            html: duplicateMessages,
                                            showConfirmButton: true,
                                            confirmButtonText: 'OK'
                                        });
                                    }
                                    // General validation errors
                                    else if (error.errors) {
                                        let errorMessages = '';
                                        for (const [field, messages] of Object.entries(error.errors)) {
                                            const label = fieldLabels[field] || field;
                                            errorMessages +=
                                                `<strong>${label}:</strong> ${messages.join('<br>')}<br>`;
                                        }

                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Validation Error',
                                            html: errorMessages,
                                            showConfirmButton: true,
                                            confirmButtonText: 'OK'
                                        });
                                    }
                                    // Other errors
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

                            const formData = new FormData(form);
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
                                        showSuccessMessage(data.message);
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: data.message || 'Failed to update currency',
                                            showConfirmButton: true,
                                            confirmButtonText: 'OK'
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);

                                    // Handle duplicate fields
                                    if (error.errors && error.duplicate_fields) {
                                        let duplicateMessages = '';

                                        const fieldLabels = {
                                            'cur_id': 'ID',
                                            'currency': 'Currency'
                                        };

                                        error.duplicate_fields.forEach(field => {
                                            const label = fieldLabels[field] || field;
                                            const value = form.querySelector(`[name="${field}"]`)
                                                .value;
                                            duplicateMessages += `The ${label} is already in use.`;
                                        });

                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Duplicate Entry',
                                            html: duplicateMessages,
                                            showConfirmButton: true,
                                            confirmButtonText: 'OK'
                                        });
                                    }
                                    // General validation errors
                                    else if (error.errors) {
                                        let errorMessages = '';
                                        for (const [field, messages] of Object.entries(error.errors)) {
                                            const label = fieldLabels[field] || field;
                                            errorMessages +=
                                                `<strong>${label}:</strong> ${messages.join('<br>')}<br>`;
                                        }

                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Validation Error',
                                            html: errorMessages,
                                            showConfirmButton: true,
                                            confirmButtonText: 'OK'
                                        });
                                    }
                                    // Other errors
                                    else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: error.message ||
                                                'An error occurred while updating data',
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
                    showErrorMessage('Failed to load form');
                });
        }

        function searchTable() {
            var input, filter, table, tr, td, i, j, txtValue, visibleRows = 0;
            input = document.getElementById("cari");
            filter = input.value.toUpperCase();
            table = document.getElementById("myTable");
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

            var noRecordsMessage = document.getElementById("no-records-message");
            noRecordsMessage.style.display = visibleRows === 0 ? "block" : "none";
        }

        document.querySelectorAll('.status-switch').forEach(switchElement => {
            switchElement.addEventListener('change', function() {
                const id = this.getAttribute('data-id');
                const isChecked = this.checked;
                const action = isChecked ? 'activate' : 'disable';

                Swal.fire({
                    title: `${action.charAt(0).toUpperCase() + action.slice(1)} Currency?`,
                    text: `Are you sure you want to ${action} this currency?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: `Yes, ${action} it!`
                }).then((result) => {
                    if (result.isConfirmed) {
                        const status = isChecked ? 1 : 0;
                        fetch(`/currencies/status/${id}`, {
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
                                    const actionText = isChecked ? 'activated' : 'disabled';
                                    showSuccessMessage(
                                        `Currency has been ${actionText} successfully!`);
                                } else {
                                    this.checked = !isChecked;
                                    showErrorMessage(data.message ||
                                        `Failed to ${action} currency`);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                this.checked = !isChecked;
                                showErrorMessage(
                                    `An error occurred while trying to ${action} currency`);
                            });
                    } else {
                        // Revert the switch if user cancels
                        this.checked = !isChecked;
                    }
                });
            });
        });
    </script>
    <x-sidebar-plugin></x-sidebar-plugin>
</body>

</html>
