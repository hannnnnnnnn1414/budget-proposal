<div class="approval-history">
    @forelse ($history as $item)
        <div
            class="history-item mb-3 p-3 border rounded position-relative {{ $item['date'] !== 'Waiting for approval' ? 'completed' : 'pending' }}">
            {{-- Status --}}
            <span class="fw-bold">{{ $loop->iteration }}. {{ $item['status'] }}</span>

            {{-- Badge & Tombol: tetap di kanan atas tapi posisi absolute --}}
            @if ($item['remarker'] !== '-')
                <div class="position-absolute top-0 end-0 p-2 text-end">
                    <span class="badge bg-primary d-block mb-1">{{ $item['remarker'] }}</span>
                    @if (Auth::check() && !in_array(Auth::user()->sect, ['Kadept', 'Kadiv', 'DIC', 'PIC']))
                        <button class="btn btn-secondary btn-sm open-add-reply-modal"
                            data-id="{{ $item['sub_id'] ?? $sub_id }}">
                            <i class="fa-solid fa-edit"></i>
                        </button>
                    @endif
                </div>
            @endif

            {{-- Tanggal --}}
            <div class="text-muted mt-2">
                <i class="far fa-clock me-1"></i> {{ $item['date'] }}
            </div>

            {{-- Remark --}}
            @if (!empty($item['remark']) || !empty($item['reply']))
                <div class="text-muted">
                    Remark :
                    <span class="fw-bold">{{ $item['remark'] }}</span>
                </div>
                <div class="text-muted">
                    Reply :
                    <span class="fw-bold">{{ $item['reply'] }}</span>
                </div>
            @endif
        </div>

    @empty
        <div class="alert alert-info">No remark history found</div>
    @endforelse
</div>
<div id="addReplyModal" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add/Edit Reply</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addReplyForm" method="POST" action="{{ route('remarks.reply') }}">
                    @csrf
                    <input type="hidden" name="sub_id" id="remark_sub_id" value="">
                    <div class="mb-3">
                        <label for="remark_text" class="form-label">Reply</label>
                        <textarea class="form-control" id="remark_text" name="remark" rows="4" placeholder="Enter your reply" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn text-white" style="background-color: #0080ff;">Submit
                            Reply</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .approval-history {
        max-height: 400px;
        overflow-y: auto;
        padding: 10px;
    }

    .history-item {
        background-color: #f8f9fa;
        border-left: 4px solid #6c757d;
        padding: 12px;
        margin-bottom: 8px;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .history-item:hover {
        background-color: #e9ecef;
    }

    .history-item.completed {
        border-left-color: #28a745;
    }

    .history-item.pending {
        border-left-color: #ffc107;
    }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $(document).ready(function() {
                // Initialize Select2 for all select elements
                $('.select').select({
                    width: '100%',
                    dropdownParent: $('#addItemModal, #editModal')
                });

                // Toggle input type for Add Item Modal
                $('#addItemModal #input_type').on('change', function() {
                    if ($(this).val() === 'select') {
                        $('#addItemModal #select_item_container').show();
                        $('#addItemModal #manual_item_container').hide();
                        $('#addItemModal #itm_id').prop('required', true);
                        $('#addItemModal #manual_item').prop('required', false);
                        $('#addItemModal #description').val('');
                    } else {
                        $('#addItemModal #select_item_container').hide();
                        $('#addItemModal #manual_item_container').show();
                        $('#addItemModal #itm_id').prop('required', false);
                        $('#addItemModal #manual_item').prop('required', true);
                        $('#addItemModal #description').val('');
                    }
                });

                $('#addItemModal #itm_id').on('input.uppercase', function() {
                    $(this).val($(this).val().toUpperCase());
                });

                $('#addItemModal #itm_id').on('blur', function() {
                    const itm_id = $(this).val().trim();
                    if (itm_id && $('#addItemModal #input_type').val() === 'select') {
                        $.ajax({
                            url: '{{ route('accounts.getItemName') }}',
                            method: 'POST',
                            data: {
                                itm_id: itm_id,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.item) {
                                    $('#addItemModal #description').val(response.item.item);
                                } else {
                                    $('#addItemModal #itm_id').val('');
                                    $('#addItemModal #description').val('');
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Item GID Not Found',
                                    });
                                }
                            },
                            error: function() {
                                $('#addItemModal #itm_id').val('');
                                $('#addItemModal #description').val('');
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Item GID Not Found',
                                });
                                console.error('AJAX Error:', status, error, xhr
                                    .responseText);
                            }
                        });
                    }
                });
                
                // Calculate amount dynamically for Add Item Modal
                $('#addItemModal').on('input', '#quantity, #price', function() {
                    const quantity = parseFloat($('#addItemModal #quantity').val()) || 0;
                    const price = parseFloat($('#addItemModal #price').val()) || 0;
                    const amount = quantity * price;

                    $('#addItemModal #amountDisplay').val('IDR ' + amount.toLocaleString('id-ID', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                    $('#addItemModal #amount').val(amount.toFixed(2));
                });

                // Handle opening the Add Item modal
                $(document).on('click', '.open-add-item-modal', function(e) {
                    e.preventDefault();
                    var subId = $(this).data('sub-id');
                    var modal = $('#addItemModal');

                    // Set the sub_id in the form
                    modal.find('#sub_id').val(subId);
                    modal.modal('show');

                    // Initialize Select2 in the modal
                    modal.find('.select').select({
                        width: '100%',
                        dropdownParent: modal
                    });

                    // Reset form fields
                    modal.find('#addItemForm')[0].reset();
                    modal.find('#amountDisplay').val('');
                    modal.find('#input_type').val('select').trigger('change'); // Reset to select
                });

                // Handle Add Item form submission
                $(document).on('submit', '#addItemForm', function(e) {
                    e.preventDefault();
                    var form = $(this);

                    $.ajax({
                        url: form.attr('action'),
                        method: form.attr('method'),
                        data: form.serialize(),
                        success: function(response) {
                            if (response.success) {
                                $('#addItemModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Item added successfully.',
                                    confirmButtonColor: '#3085d6'
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = xhr.responseJSON.message || 'Failed to add item.';
                            if (xhr.status === 422 && xhr.responseJSON.errors) {
                                errorMessage = Object.values(xhr.responseJSON.errors).flat().join(
                                    '\n');
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: errorMessage,
                                confirmButtonColor: '#d33'
                            });
                        }
                    });
                });

                // Handle Edit modal loading
                $(document).on('click', '.open-edit-modal', function(e) {
                    e.preventDefault();
                    var subId = $(this).data('id');
                    var itmId = $(this).data('itm-id');
                    var modal = $('#editModal');

                    // Load modal content via AJAX
                    $.get('/submissions/' + subId + '/id/' + itmId + '/edit', function(data) {
                        modal.find('.modal-dialog').html(data);
                        modal.modal('show');

                        // Initialize Select2 in the edit modal
                        modal.find('.select').select({
                            width: '100%',
                            dropdownParent: modal
                        });
                    }).fail(function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to load edit form.',
                            confirmButtonColor: '#d33'
                        });
                    });
                });

                // Handle Edit form submission
                $(document).on('submit', '#editModal form', function(e) {
                    e.preventDefault();
                    var form = $(this);

                    $.ajax({
                        url: form.attr('action'),
                        method: form.attr('method'),
                        data: form.serialize(),
                        success: function(response) {
                            if (response.success) {
                                $('#editModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Data has been updated successfully.',
                                    confirmButtonColor: '#3085d6'
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = xhr.responseJSON.message || 'Failed to update item.';
                            if (xhr.status === 422 && xhr.responseJSON.errors) {
                                errorMessage = Object.values(xhr.responseJSON.errors).flat().join(
                                    '\n');
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: errorMessage,
                                confirmButtonColor: '#d33'
                            });
                        }
                    });
                });

                // Handle History modal loading
                $(document).on('click', '.open-history-modal', function(e) {
                    e.preventDefault();
                    var subId = $(this).data('id');
                    var modal = $('#historyModal');

                    // Show loading state
                    modal.find('.modal-body').html(
                        '<div class="text-center py-4"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>'
                    );
                    modal.modal('show');

                    // Load history content
                    $.get('/approvals/history/' + subId)
                        .done(function(data) {
                            modal.find('.modal-body').html(data);
                        })
                        .fail(function() {
                            modal.find('.modal-body').html(
                                '<div class="alert alert-danger">Failed to load approval history</div>'
                            );
                        });
                });

                // Handle opening the Add Remark modal
                // Handle opening the Add/Edit Remark modal
                $(document).on('click', '.open-add-reply-modal', function(e) {
                    e.preventDefault();
                    var subId = $(this).data('id');
                    var modal = $('#addReplyModal');

                    // Set the sub_id in the form
                    modal.find('#remark_sub_id').val(subId);

                    // Clear the textarea first
                    modal.find('#remark_text').val('');

                    // Get existing remark if any
                    $.get('/remarks/get-remarks/' + subId, function(response) {
                        if (response.remarks && response.remarks.length > 0) {
                            modal.find('#remark_text').val(response.remarks[0].remark);
                        }
                    }).fail(function() {
                        console.log('Failed to load remarks');
                    });

                    modal.modal('show');
                });
                // Handle Add Remark form submission
                $(document).on('submit', '#addReplyForm', function(e) {
                    e.preventDefault();
                    var form = $(this);

                    $.ajax({
                        url: form.attr('action'),
                        method: form.attr('method'),
                        data: form.serialize(),
                        success: function(response) {
                            if (response.success) {
                                $('#addReplyModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Remark added successfully.',
                                    confirmButtonColor: '#3085d6'
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = xhr.responseJSON.message || 'Failed to add remark.';
                            if (xhr.status === 422 && xhr.responseJSON.errors) {
                                errorMessage = Object.values(xhr.responseJSON.errors).flat().join(
                                    '\n');
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: errorMessage,
                                confirmButtonColor: '#d33'
                            });
                        }
                    });
                });

                // Handle Remarks History modal loading
                $(document).on('click', '.open-historyremark-modal', function(e) {
                    e.preventDefault();
                    var subId = $(this).data('id');
                    var modal = $('#historyremarkModal');

                    // Show loading state
                    modal.find('.modal-body').html(
                        '<div class="text-center py-4"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>'
                    );
                    modal.modal('show');

                    // Load remarks history content
                    $.get('/remarks/remark/' + subId)
                        .done(function(data) {
                            modal.find('.modal-body').html(data);
                        })
                        .fail(function() {
                            modal.find('.modal-body').html(
                                '<div class="alert alert-danger">Failed to load remarks history</div>'
                            );
                        });
                });

                // Handle Delete form submission
                $(document).on('click', '.btn-delete', function() {
                    const form = $(this).closest('form');
                    const itemCount = form.data('item-count');

                    if (itemCount <= 1) {
                        Swal.fire({
                            title: 'Warning!',
                            text: 'There must be at least one item in the submission. You cannot delete the last item.',
                            icon: 'warning',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#d33'
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: form.attr('action'),
                                method: form.attr('method'),
                                data: form.serialize(),
                                success: function(response) {
                                    if (response.success) {
                                        Swal.fire(
                                            'Deleted!',
                                            response.message,
                                            'success'
                                        ).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire(
                                            'Error!',
                                            response.message,
                                            'error'
                                        );
                                    }
                                },
                                error: function(xhr) {
                                    Swal.fire(
                                        'Error!',
                                        xhr.responseJSON.message ||
                                        'Something went wrong',
                                        'error'
                                    );
                                }
                            });
                        }
                    });
                });

                // Handle Send form submission
                $(document).on('submit', '.send-form', function(e) {
                    e.preventDefault();
                    var form = $(this);

                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'Do you want to send this submission?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, send it!',
                        cancelButtonText: 'No, cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: form.attr('action'),
                                method: form.attr('method'),
                                data: form.serialize(),
                                success: function(response, status, xhr) {
                                    if (xhr.status === 200 || xhr.status === 302) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success!',
                                            text: 'Submission sent successfully.',
                                            confirmButtonColor: '#3085d6'
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error!',
                                            text: 'Failed to send submission.',
                                            confirmButtonColor: '#d33'
                                        });
                                    }
                                },
                                error: function(xhr) {
                                    let errorMessage = 'Something went wrong.';
                                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                        errorMessage = Object.values(xhr.responseJSON
                                            .errors).flat().join(' ');
                                    } else if (xhr.responseJSON?.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: errorMessage,
                                        confirmButtonColor: '#d33'
                                    });
                                }
                            });
                        }
                    });
                });

                // Handle Approve form submission
                $(document).on('submit', '.approve-form', function(e) {
                    e.preventDefault();
                    var form = $(this);

                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'Do you want to approve this submission?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, approve it!',
                        cancelButtonText: 'No, cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: form.attr('action'),
                                method: form.attr('method'),
                                data: form.serialize(),
                                success: function(response, status, xhr) {
                                    if (xhr.status === 200 || xhr.status === 302) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success!',
                                            text: 'Submission approved successfully.',
                                            confirmButtonColor: '#3085d6'
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error!',
                                            text: 'Failed to approve submission.',
                                            confirmButtonColor: '#d33'
                                        });
                                    }
                                },
                                error: function(xhr) {
                                    let errorMessage = 'Something went wrong.';
                                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                        errorMessage = Object.values(xhr.responseJSON
                                            .errors).flat().join(' ');
                                    } else if (xhr.responseJSON?.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: errorMessage,
                                        confirmButtonColor: '#d33'
                                    });
                                }
                            });
                        }
                    });
                });

                // Handle Disapprove form submission
                $(document).on('submit', '.disapprove-form', function(e) {
                    e.preventDefault();
                    var form = $(this);

                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'Do you want to disapprove this submission?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, disapprove it!',
                        cancelButtonText: 'No, cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: form.attr('action'),
                                method: form.attr('method'),
                                data: form.serialize(),
                                success: function(response, status, xhr) {
                                    if (xhr.status === 200 || xhr.status === 302) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success!',
                                            text: 'Submission disapproved successfully.',
                                            confirmButtonColor: '#3085d6'
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error!',
                                            text: 'Failed to disapprove submission.',
                                            confirmButtonColor: '#d33'
                                        });
                                    }
                                },
                                error: function(xhr) {
                                    let errorMessage = 'Something went wrong.';
                                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                        errorMessage = Object.values(xhr.responseJSON
                                            .errors).flat().join(' ');
                                    } else if (xhr.responseJSON?.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: errorMessage,
                                        confirmButtonColor: '#d33'
                                    });
                                }
                            });
                        }
                    });
                });
            });
        </script>