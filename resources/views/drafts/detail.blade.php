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
            Account Budgeting</x-navbar>
        <div class="container-fluid">
            <div class="row">
                <div class="card-header bg-danger">
                    <h4 style="font-weight: bold;" class="text-white"><i
                            class="fa-solid fa-file-invoice fs-4 me-2 text-white me-3"></i>APPROVAL SUBMISSIONS</h4>
                </div>
                <div class="card rounded-0">
                    <div class="mt-4">
                        <label class="form-label">Submission name or ID search</label>
                        <div class="input-group">
                            <input name="cari" type="text" id="cari" class="form-control"
                                placeholder="Pencarian" onkeyup="searchTable()" />
                        </div>
                    </div>
                    <div id="drafts">
                        <div class="main-wrapper">
                            <div class="main-content">
                                <div class="container">
                                    <div class="card mt-3">
                                        <div class="card-body">
                                            @if (Session('success'))
                                                <div class="alert alert-success">{{ session('success') }}</div>
                                            @endif

                                            @if (Session('error'))
                                                <div class="alert alert-danger">{{ session('error') }}</div>
                                            @endif
                                            <table id="myTable" class="table table-striped table-bordered">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>Kode</th>
                                                        <th>Description</th>
                                                        <th>Submission Date</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($drafts as $draft)
                                                        <tr class="text-center">
                                                            <td>{{ $draft->sub_id }}</td>
                                                            <td>Submission Advertising & Promotion</td>
                                                            <td>{{ $draft->month }}</td>
                                                            <td class="text-center">
                                                                @if ($draft->status == 1)
                                                                    Draft
                                                                @elseif ($draft->status == 2)
                                                                    Under Review
                                                                @elseif ($draft->status == 3)
                                                                    Approved
                                                                @elseif ($draft->status == 4)
                                                                    Rejected
                                                                @else
                                                                    Tidak diketahui
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                @if (in_array($draft->status, [2, 3, 4]))
                                                                    <button
                                                                        class="btn btn-danger d-inline-flex align-items-center justify-content-center"
                                                                        style="width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                                                                        title="Detail">
                                                                        <i class="fa-solid fa-circle-info fs-6"></i>
                                                                    </button>
                                                                @else
                                                                    <button
                                                                        class="btn btn-danger d-inline-flex align-items-center justify-content-center"
                                                                        style="width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                                                                        type="submit" title="Update">
                                                                        <i class="fa-solid fa-edit fs-6"></i>
                                                                    </button>
                                                                    <form
                                                                        action="{{ route('drafts.submit', $draft->sub_id) }}"
                                                                        method="POST" style="display:inline;">
                                                                        @csrf
                                                                        <button
                                                                            class="btn btn-danger d-inline-flex align-items-center justify-content-center"
                                                                            style="width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                                                                            type="submit" title="Send">
                                                                            <i class="fa-solid fa-paper-plane fs-6"></i>
                                                                        </button>
                                                                    </form>
                                                                    <form
                                                                        action="{{ route('drafts.destroy', $draft->sub_id) }}"
                                                                        method="POST" style="display:inline;"
                                                                        onsubmit="return confirm('Are you sure you want to delete this draft?');">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button
                                                                            class="btn btn-danger d-inline-flex align-items-center justify-content-center"
                                                                            style="width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                                                                            title="Delete">
                                                                            <i class="fa-solid fa-trash fs-6"></i>
                                                                        </button>
                                                                    </form>
                                                                    <button
                                                                        class="btn btn-danger d-inline-flex align-items-center justify-content-center"
                                                                        style="width: 20px; height: 30px; border-radius: 3px; margin: 4px;"
                                                                        title="Detail">
                                                                        <i class="fa-solid fa-circle-info fs-6"></i>
                                                                    </button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="8">No Submissions found!</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                            <div id="no-records-message" class="text-center mt-3 text-secondary"
                                                style="display: none;">
                                                No matching records found
                                            </div>
                                        </div>
                                    </div>
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
                tr[i].style.display = display ? "" : "none";
                if (display) visibleRows++;
            }

            var noRecordsMessage = document.getElementById("no-records-message");
            noRecordsMessage.style.display = visibleRows === 0 ? "block" : "none";
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
