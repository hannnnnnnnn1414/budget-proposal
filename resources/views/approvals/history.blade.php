<div class="approval-history">
    @forelse ($history as $item)
        <div
            class="history-item mb-3 p-3 border rounded {{ $item['date'] !== 'Waiting for approval' ? 'completed' : 'pending' }}">
            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-bold">{{ $loop->iteration }}. {{ $item['status'] }}</span>
                @if ($item['approver'] !== '-')
                    <span class="badge bg-primary">{{ $item['approver'] }}</span>
                @endif
            </div>
            <div class="text-muted mt-1">
                <i class="far fa-clock me-1"></i> {{ $item['date'] }}
            </div>
            @if ($item['status_code'] !== 1 && $item['status_code'] !== 2)

            <div class="text-muted mt-1">
                Remark: <span class="fw-bold">{{ $item['remark'] ?? '-' }}</span>
                </span>
            </div>
            @endif
        </div>
    @empty
        <div class="alert alert-info">No approval history found</div>
    @endforelse
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
