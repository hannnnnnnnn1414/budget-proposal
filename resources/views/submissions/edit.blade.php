<div class="modal-header bg-danger">
    <h5 class="modal-title text-white">Edit Item</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <form id="editItemForm" method="POST"
        action="{{ route('submissions.update', ['sub_id' => $submission->sub_id, 'id' => $submission->id]) }}">
        @csrf
        @method('PUT')
        <input type="hidden" name="sub_id" id="sub_id" value="{{ $submission->sub_id }}">
        <input type="hidden" name="acc_id" id="acc_id" value="{{ $submission->acc_id ?? '' }}">
        <input type="hidden" name="purpose" id="purpose" value="{{ $submission->purpose ?? '' }}">

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Item <span class="text-danger">*</span></label>
                    <input type="text" name="itm_id" id="itm_id" class="form-control" placeholder="Enter Item ID"
                        required value="{{ $submission->itm_id }}">
                </div>
                <div class="mb-3">
                    <label for="kwh" class="form-label">KWH <span class="text-danger">*</span></label>
                    <input type="number" name="kwh" id="kwh" class="form-control" required
                        value="{{ $submission->kwh }}">
                </div>
                <div class="mb-3">
                    <label for="wct_id" class="form-label">Workcenter <span class="text-danger">*</span></label>
                    <select name="wct_id" id="wct_id" class="form-control select" required>
                        <option value="">-- Select Workcenter --</option>
                        @foreach ($workcenters as $workcenter)
                            <option value="{{ $workcenter->wct_id }}"
                                {{ $submission->wct_id === $workcenter->wct_id ? 'selected' : '' }}>
                                {{ $workcenter->workcenter }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="cur_id" class="form-label">Currency <span class="text-danger">*</span></label>
                        <select name="cur_id" id="cur_id" class="form-select select" required>
                            <option value="">-- Select Currency --</option>
                            @foreach ($currencies as $currency)
                                <option value="{{ $currency->cur_id }}" data-nominal="{{ $currency->nominal }}"
                                    {{ $submission->cur_id === $currency->cur_id ? 'selected' : '' }}>
                                    {{ $currency->currency }}
                                </option>
                            @endforeach
                        </select>
                        <small id="currencyNote" class="form-text text-muted" style="display: none;"></small>
                    </div>
                    <div class="col-md-6">
                        <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                        <input type="number" name="price" id="price" class="form-control" required min="0"
                            step="0.01" value="{{ $submission->price }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="amountDisplay" class="form-label">Amount (IDR)</label>
                    <input type="text" id="amountDisplay" class="form-control" readonly
                        value="IDR {{ number_format($submission->amount, 2, ',', '.') }}">
                    <input type="hidden" name="amount" id="amount" value="{{ $submission->amount }}">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Department <span class="text-danger">*</span></label>
                    <input type="hidden" name="dpt_id" value="{{ Auth::user()->dept }}">
                    <input class="form-control" value="{{ Auth::user()->department->department ?? '-' }}" readonly>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="month" class="form-label">Month <span class="text-danger">*</span></label>
                    <select class="form-control select" name="month" id="month" required>
                        <option value="">-- Select Month --</option>
                        @foreach ($months as $month)
                            <option value="{{ $month }}"
                                {{ $submission->month === $month ? 'selected' : '' }}>
                                {{ $month }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="lob_id" class="form-label">Line of Business <span
                            class="text-danger">*</span></label>
                    <select name="lob_id" id="lob_id" class="form-control select" required>
                        <option value="">-- Select Line of Business --</option>
                        @foreach ($lineBusinesses as $lob)
                            <option value="{{ $lob->lob_id }}"
                                {{ $submission->lob_id === $lob->lob_id ? 'selected' : '' }}>
                                {{ $lob->line_business }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn text-white" style="background-color: #0080ff;">Update Item</button>
        </div>
    </form>
</div>
