<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 "
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" target="_blank">
            <img src="{{ url('https://upload.wikimedia.org/wikipedia/commons/thumb/7/7b/KYB_Corporation_company_logo.svg/2560px-KYB_Corporation_company_logo.svg.png') }}"
                class="navbar-brand-img h-100" alt="main_logo">
            <span class="ms-1 font-weight-bold">Kayaba Indonesia</span>
        </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse  w-auto  max-height-vh-100 h-100" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            {{-- Untuk dept 6121 dengan sect Kadept atau PIC --}}
            @if (session('dept') === '6121' && session('sect') === 'PIC')
                <x-navlink href="{{ route('index') }}" :active="request()->is('/')" icon="fa-house">Dashboard</x-navlink>
                <x-navlink href="{{ route('reports.report') }}" :active="request()->is('sumarries')" icon="fa-file-invoice">Report
                    Budget</x-navlink>
                <x-navlink href="{{ route('approvals.pending') }}" :active="request()->is('approvals/pending')"
                    icon="fa-hourglass-half">Outstanding Approvals</x-navlink>
                <x-navlink href="{{ route('approvals.detail') }}" :active="request()->is('approvals')" icon="fa-list-check">History
                    Approvals</x-navlink>
                <x-navlink href="{{ route('departments.index') }}" :active="request()->is('departments')"
                    icon="fa-globe">Departments</x-navlink>
                <x-navlink href="{{ route('suppliers.index') }}" :active="request()->is('suppliers')"
                    icon="fa-warehouse">Suppliers</x-navlink>
                <x-navlink href="{{ route('dimensions.index') }}" :active="request()->is('dimensions')"
                    icon="fa-solid fa-diagram-project">Dimensions</x-navlink>
                <x-navlink href="{{ route('currencies.index') }}" :active="request()->is('currencies')"
                    icon="fa-solid fa-coins">Currency</x-navlink>
            @elseif (session('dept') === '6121' && session('sect') === 'Kadept')
                <x-navlink href="{{ route('index') }}" :active="request()->is('/')" icon="fa-house">Dashboard</x-navlink>
                <x-navlink href="{{ route('approvals.pending') }}" :active="request()->is('approvals/pending')"
                    icon="fa-hourglass-half">Outstanding Approvals</x-navlink>
                <x-navlink href="{{ route('approvals.detail') }}" :active="request()->is('approvals')" icon="fa-list-check">History
                    Approvals</x-navlink>
                <x-navlink href="{{ route('departments.index') }}" :active="request()->is('departments')"
                    icon="fa-globe">Departments</x-navlink>
                <x-navlink href="{{ route('suppliers.index') }}" :active="request()->is('suppliers')"
                    icon="fa-warehouse">Suppliers</x-navlink>
                <x-navlink href="{{ route('dimensions.index') }}" :active="request()->is('dimensions')"
                    icon="fa-solid fa-diagram-project">Dimensions</x-navlink>
                <x-navlink href="{{ route('reports.report') }}" :active="request()->is('sumarries')" icon="fa-file-invoice">Report
                    Budget</x-navlink>
                <x-navlink href="{{ route('currencies.index') }}" :active="request()->is('currencies')"
                    icon="fa-solid fa-coins">Currency</x-navlink>

                {{-- Untuk semua dept dengan sect Kadept, kecuali 6121 --}}
            @elseif(session('sect') === 'Kadept' && session('dept') !== '6121')
                <x-navlink href="{{ route('index') }}" :active="request()->is('/')" icon="fa-house">Dashboard</x-navlink>
                <x-navlink href="{{ route('approvals.pending') }}" :active="request()->is('approvals/pending')"
                    icon="fa-hourglass-half">Outstanding Approvals</x-navlink>
                <x-navlink href="{{ route('approvals.detail') }}" :active="request()->is('approvals')" icon="fa-list-check">History
                    Approvals</x-navlink>
                {{-- Untuk semua dept dengan sect Kadiv --}}
            @elseif(session('dept') === '6121' && session('sect') === 'DIC')
                <x-navlink href="{{ route('index') }}" :active="request()->is('/')" icon="fa-house">Dashboard</x-navlink>
                <x-navlink href="{{ route('reports.report') }}" :active="request()->is('sumarries')" icon="fa-file-invoice">Report
                    Budget</x-navlink>
                <x-navlink href="{{ route('approvals.pending') }}" :active="request()->is('approvals/pending')"
                    icon="fa-hourglass-half">Outstanding Approvals</x-navlink>
                <x-navlink href="{{ route('approvals.detail') }}" :active="request()->is('approvals')" icon="fa-list-check">History
                    Approvals</x-navlink>
                <x-navlink href="{{ route('departments.index') }}" :active="request()->is('departments')"
                    icon="fa-globe">Departments</x-navlink>
                <x-navlink href="{{ route('suppliers.index') }}" :active="request()->is('suppliers')"
                    icon="fa-warehouse">Suppliers</x-navlink>
                <x-navlink href="{{ route('dimensions.index') }}" :active="request()->is('dimensions')"
                    icon="fa-solid fa-diagram-project">Dimensions</x-navlink>
                <x-navlink href="{{ route('currencies.index') }}" :active="request()->is('currencies')"
                    icon="fa-solid fa-coins">Currency</x-navlink>
            @elseif(session('sect') === 'Kadiv' && !in_array(session('dept'), ['6121', '4211', '6111']))
                <x-navlink href="{{ route('index-all') }}" :active="request()->is('/all')" icon="fa-house">Dashboard</x-navlink>
                <x-navlink href="{{ route('approvals.pending') }}" :active="request()->is('approvals/pending')"
                    icon="fa-hourglass-half">Outstanding Approvals</x-navlink>
                <x-navlink href="{{ route('approvals.detail') }}" :active="request()->is('approvals')" icon="fa-list-check">History
                    Approvals</x-navlink>

                {{-- Untuk semua dept dengan sect DIC --}}
            @elseif(session('sect') === 'DIC')
                <x-navlink href="{{ route('index-all') }}" :active="request()->is('/all')" icon="fa-house">Dashboard</x-navlink>
                <x-navlink href="{{ route('approvals.pending') }}" :active="request()->is('approvals/pending')"
                    icon="fa-hourglass-half">Outstanding Approvals</x-navlink>
                <x-navlink href="{{ route('approvals.detail') }}" :active="request()->is('approvals')" icon="fa-list-check">History
                    Approvals</x-navlink>
                <x-navlink href="{{ route('reports.report-all') }}" :active="request()->is('reports')"
                    icon="fa-file-invoice">Report</x-navlink>

                {{-- Untuk semua dept, kecuali jika sect adalah Kadept, Kadiv, DIC, atau (dept 6121 dengan sect PIC atau Kadept) --}}
            @elseif(
                !in_array(session('sect'), ['Kadept', 'Kadiv', 'DIC']) &&
                    !(session('dept') === '6121' && in_array(session('sect'), ['Kadept', 'PIC'])))
                <x-navlink href="{{ route('index-all') }}" :active="request()->is('/all')" icon="fa-house">Dashboard</x-navlink>
                <x-navlink href="{{ route('submissions.index') }}" :active="request()->is('submissions')"
                    icon="fa-list-check">Submission</x-navlin>
                    @if (!in_array(session('sect'), ['Kadept', 'Kadiv', 'DIC', 'PIC']) && session('dept') === '6121')
                        <x-navlink href="{{ route('reports.report') }}" :active="request()->is('sumarries')"
                            icon="fa-file-invoice">Report
                            Budget</x-navlink>
                        <x-navlink href="{{ route('approvals.pending') }}" :active="request()->is('approvals/pending')"
                            icon="fa-hourglass-half">Outstanding Approvals</x-navlink>
                        <x-navlink href="{{ route('approvals.detail') }}" :active="request()->is('approvals')"
                            icon="fa-list-check">History
                            Approvals</x-navlink>
                        <x-navlink href="{{ route('departments.index') }}" :active="request()->is('departments')"
                            icon="fa-globe">Departments</x-navlink>
                        <x-navlink href="{{ route('suppliers.index') }}" :active="request()->is('suppliers')"
                            icon="fa-warehouse">Suppliers</x-navlink>
                        <x-navlink href="{{ route('dimensions.index') }}" :active="request()->is('dimensions')"
                            icon="fa-solid fa-diagram-project">Dimensions</x-navlink>
                        <x-navlink href="{{ route('currencies.index') }}" :active="request()->is('currencies')"
                            icon="fa-solid fa-coins">Currency</x-navlink>
                    @endif
            @endif

            @if (auth()->check() && auth()->user()->dept === '6121')
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('budget-final*') ? 'active' : '' }}"
                        href="{{ route('budget-final.index') }}">
                        <div
                            class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-file-contract text-lg {{ Request::is('budget-final*') ? 'text-white' : 'text-dark' }}"
                                style="opacity: .8;"></i>
                        </div>
                        <span class="nav-link-text ms-1">Budget Final</span>
                    </a>
                </li>
            @endif

            @if (auth()->check() && auth()->user()->dept !== '6121')
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('reupload*') ? 'active' : '' }}"
                        href="{{ route('reupload.index') }}">
                        <div
                            class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-sync-alt text-lg {{ Request::is('reupload*') ? 'text-white' : 'text-dark' }}"
                                style="opacity: .8;"></i>
                        </div>
                        <span class="nav-link-text ms-1">ReUpload</span>
                    </a>
                </li>
            @endif

        </ul>
    </div>
</aside>
