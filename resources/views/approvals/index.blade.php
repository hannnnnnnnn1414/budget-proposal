<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Proposal Detail</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-8 font-sans">

    <!-- Header -->
    <h1 class="text-2xl font-bold mb-6">Proposal Detail</h1>

    <!-- Grid: Characteristic | Financial | Approval -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        <!-- Approval Status -->
        <div class="bg-green-100 p-4 rounded shadow">
            <h2 class="text-lg font-semibold mb-2">Approval Status</h2>
            <p>Status: <span class="font-bold text-green-700">
                    @forelse ($approvals as $approval)
                        @if ($approval->status == 1)
                            <span class="font-bold text-warning">Draft</span>
                        @elseif ($approval->status == 2)
                            <span class="font-bold text-info">Under Review</span>
                        @elseif ($approval->status == 3)
                            <span class="font-bold text-success">Acknowledged by DIC</span>
                        @elseif ($approval->status == 4)
                            <span class="font-bold text-danger">Rejected</span>
                        @else
                            <span class="font-bold text-secondary">Unknown</span>
                        @endif
                        {{-- <p>Date: {{ $approvals->updated_at->format('d-m-Y') }}</p> --}}
                    @empty
                    @endforelse
                </span></p>
            <p>Date: 03-03-2025</p>
            <button class="mt-4 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                Preview
            </button>
        </div>

    </div>

    <!-- Remarks -->
    <div class="bg-white p-4 rounded shadow mb-8">
        <h2 class="text-lg font-semibold mb-4">Remarks</h2>

        <!-- Previous Remark -->
        <div class="mb-4">
            <label class="block font-medium mb-1">Previous Remark:</label>
            <textarea readonly class="w-full border border-gray-300 rounded p-2 bg-gray-100" rows="3">
Indent barangnya apakah tidak terlalu lama? di plan delivery Juni
      </textarea>
        </div>

        <!-- Add Remark -->
        <div>
            <label class="block font-medium mb-1">Add Remark:</label>
            <textarea class="w-full border border-gray-300 rounded p-2" rows="3" placeholder="Add your remark..."></textarea>
            <button class="mt-2 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                Add Remark
            </button>
        </div>
    </div>

    <!-- Item Table -->
    <div class="bg-white p-4 rounded shadow overflow-x-auto">
        <h2 class="text-lg font-semibold mb-4">Item of Purchase</h2>
        <table class="min-w-full border border-gray-300">
            <thead class="bg-gray-200">
                <tr>
                    <th class="text-left border p-2">Pos</th>
                    <th class="text-left border p-2">Item</th>
                    <th class="text-left border p-2">Qty</th>
                    <th class="text-left border p-2">Price</th>
                    <th class="text-left border p-2">Dept</th>
                    <th class="text-left border p-2">Line</th>
                    <th class="text-left border p-2">
                        <input type="checkbox" id="select-all" class="form-checkbox">
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr class="hover:bg-gray-50">
                    <td class="border p-2">1</td>
                    <td class="border p-2">NO401 - One Sprockets Chain</td>
                    <td class="border p-2">1.00 pcs</td>
                    <td class="border p-2">IDR 14,623,714.00</td>
                    <td class="border p-2">SA</td>
                    <td class="border p-2">512-1-105</td>
                    <td class="border p-2">
                        <input type="checkbox" class="item-checkbox form-checkbox">
                    </td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="border p-2">2</td>
                    <td class="border p-2">NO402 - Two Sprockets Chain</td>
                    <td class="border p-2">2.00 pcs</td>
                    <td class="border p-2">IDR 25,000,000.00</td>
                    <td class="border p-2">SB</td>
                    <td class="border p-2">512-1-106</td>
                    <td class="border p-2">
                        <input type="checkbox" class="item-checkbox form-checkbox">
                    </td>
                </tr>
            </tbody>

        </table>
    </div>

    <!-- Purpose of Purchase & Detail Analyze -->
    <div class="bg-white p-4 rounded shadow mb-8">
        <h2 class="text-lg font-semibold mb-2">Purpose of Purchase</h2>
        <p class="mb-4">REPLACE DAMAGED SPROCKET PART CONVEYOR</p>

        <h2 class="text-lg font-semibold mb-2">Detail Analyze</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div><strong>1. Why</strong><br>DAMAGED SPROCKET</div>
            <div><strong>2. Where</strong><br>CONVEYOR MC</div>
            <div><strong>3. How</strong><br>PRIORITY CONTROL</div>
            <div><strong>4. Who</strong><br>PE4W</div>
            <div><strong>5. When</strong><br>MIDDLE OF JUNY 2025</div>
        </div>
    </div>

    <!-- Total Estimated Amount + Buttons -->
    <div class="bg-white p-4 rounded shadow mb-8">
        <h2 class="text-lg font-semibold mb-2">Total Estimated Amount :</h2>
        <div class="text-xl font-bold text-gray-800 mb-4">IDR 14,623,714.00</div>

        <div class="flex gap-2">
            <button class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                Approve
            </button>
            <button class="px-6 py-2 bg-gray-300 text-black rounded hover:bg-gray-400">
                Back
            </button>
        </div>
    </div>
    <script>
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.item-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    </script>

</body>

</html>
