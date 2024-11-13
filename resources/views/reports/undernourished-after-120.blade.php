@extends('layouts.app')

@section('content')

    <!-- Page Title -->
    <div class="pagetitle">
        <nav style="--bs-breadcrumb-divider: '>';">
            <ol class="breadcrumb mb-3 p-0">
                <li class="breadcrumb-item"><a href="{{ route('cycle.index') }}">Implementations</a></li>
                <li class="breadcrumb-item">Reports</li>
            </ol>
        </nav>
    </div>

    <!-- Alerts -->
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" id="danger-alert" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" id="success-alert" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Alert Auto-Close Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alertIds = ['success-alert', 'danger-alert'];
            alertIds.forEach(alertId => {
                const alertElement = document.getElementById(alertId);
                if (alertElement) {
                    setTimeout(() => new bootstrap.Alert(alertElement).close(), 2000);
                }
            });
        });
    </script>

    <div class="wrapper">
        <section class="section">
            <div class="row">
                
                <!-- Sidebar Navigation -->
                <div class="col-lg-2">
                    <div class="card">
                            <div class="report-card-body mt-4 mb-2 text-sm">
                                <ul class="report-side-nav" id="report-side-nav">
                                    @if (auth()->user()->hasRole('admin') ||
                                            auth()->user()->hasRole('lgu focal') ||
                                            auth()->user()->hasRole('child development worker'))
                                        <li class="nav-item">
                                            <form id="masterlistForm" action="{{ route('reports.index', ['cycle' => $cycle->id]) }}" method="POST">
                                                @csrf
                                                <a class="report-link collapsed" href="#" onclick="document.getElementById('masterlistForm').submit(); return false;">
                                                    <svg class="h-2 w-2 mr-2" width="24" height="24" viewBox="0 0 24 24"
                                                        stroke-width="2" stroke="currentColor" fill="none"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" />
                                                        <circle cx="12" cy="12" r="9" />
                                                    </svg>
                                                    Masterlist
                                                </a>
                                            </form>
                                        </li>
                                    @endif

                                    @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('lgu focal'))
                                        <li class="nav-item">
                                            <form id="malnourishedForm" action="{{ route('reports.malnourish', ['cycle' => $cycle->id])}}" method="POST">
                                                @csrf
                                                <a class="report-link collapsed" href="#" onclick="document.getElementById('malnourishedForm').submit(); return false;">
                                                    <svg class="h-2 w-2 mr-2" width="24" height="24" viewBox="0 0 24 24"
                                                        stroke-width="2" stroke="currentColor" fill="none"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" />
                                                        <circle cx="12" cy="12" r="9" />
                                                    </svg>
                                                    Malnourished
                                                </a>
                                            </form>
                                        </li>
                                    @endif

                                    @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('lgu focal'))
                                        <li class="nav-item">
                                            <form id="disabilitiesForm" action="{{ route('reports.disabilities', ['cycle' => $cycle->id])}}" method="POST">
                                                @csrf
                                                <a class="report-link collapsed" href="#" onclick="document.getElementById('disabilitiesForm').submit(); return false;">
                                                    <svg class="h-2 w-2 mr-2" width="24" height="24" viewBox="0 0 24 24"
                                                        stroke-width="2" stroke="currentColor" fill="none"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" />
                                                        <circle cx="12" cy="12" r="9" />
                                                    </svg>
                                                    Persons with Disability
                                                </a>
                                            </form>
                                        </li>
                                    @endif

                                    @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('lgu focal'))
                                        <li class="nav-item">
                                            <a class="report-link-main collapsed" href="#">
                                                <svg class="h-2 w-2 mr-2" width="24" height="24" viewBox="0 0 24 24"
                                                    stroke-width="2" stroke="currentColor" fill="none"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" />
                                                    <circle cx="12" cy="12" r="9" />
                                                </svg>
                                                Undernourish
                                            </a>
                                            <ul id="forms-nav" class="report-content collapsed">
                                                <li class="nav-item">
                                                    <form id="undernourishedUponEntryForm" action="{{ route('reports.undernourished-upon-entry', ['cycle' => $cycle->id])}}" method="POST">
                                                        @csrf
                                                        <a class="report-link collapsed" href="#" onclick="document.getElementById('undernourishedUponEntryForm').submit(); return false;">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-2 size-3">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                                            </svg>
                                                            Upon Entry
                                                        </a>
                                                    </form>
                                                </li>
                                                <li class="nav-item">
                                                    <form id="undernourishedAfter120Form" action="{{ route('reports.undernourished-after-120', ['cycle' => $cycle->id])}}" method="POST">
                                                        @csrf
                                                        <a class="report-link collapsed active" href="#" onclick="document.getElementById('undernourishedAfter120Form').submit(); return false;">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-2 size-3">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                                            </svg>
                                                            After 120 Feedings
                                                        </a>
                                                    </form>
                                                </li>
                                            </ul>
                                        </li>
                                    @endif

                                    @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('lgu focal'))
                                        <li class="nav-item">
                                            <a class="report-link-main collapsed" href="#" data-target="ns-consolidated">
                                                <svg class="h-2 w-2 mr-2" width="24" height="24" viewBox="0 0 24 24"
                                                    stroke-width="2" stroke="currentColor" fill="none"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" />
                                                    <circle cx="12" cy="12" r="9" />
                                                </svg>
                                                Nutritional Status
                                            </a>
                                    
                                              
                                            <ul id="forms-nav" class="report-content collapsed">
                                                <li class="nav-item">
                                                    <a class="report-link-main collapsed">
                                                        <svg class="h-2 w-2 mr-2" width="24" height="24" viewBox="0 0 24 24"
                                                            stroke-width="2" stroke="currentColor" fill="none"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" />
                                                            <circle cx="12" cy="12" r="9" />
                                                        </svg>
                                                        Weight for Age
                                                    </a>
                                                    <ul id="forms-nav" class="report-subcontent collapsed">
                                                        <li>
                                                            <form id="WFAUponEntryForm" action="{{ route('reports.weight-for-age-upon-entry', ['cycle' => $cycle->id])}}" method="POST">
                                                                @csrf
                                                                <a class="report-link collapsed" href="#" onclick="document.getElementById('WFAUponEntryForm').submit(); return false;">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-2 size-3">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                                                    </svg>
                                                                    Upon Entry
                                                                </a>
                                                            </form>
                                                        </li>
                                                    
                                                        <li>
                                                            <form id="WFAAfter120Form" action="{{ route('reports.weight-for-age-after-120', ['cycle' => $cycle->id])}}" method="POST">
                                                                @csrf
                                                                <a class="report-link collapsed" href="#" onclick="document.getElementById('WFAAfter120Form').submit(); return false;">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-2 size-3">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                                                    </svg>
                                                                    After 120 Feedings
                                                                </a>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                            <ul id="forms-nav" class="report-content collapsed">
                                                <li class="nav-item">
                                                    <a class="report-link-main collapsed">
                                                        <svg class="h-2 w-2 mr-2" width="24" height="24" viewBox="0 0 24 24"
                                                            stroke-width="2" stroke="currentColor" fill="none"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" />
                                                            <circle cx="12" cy="12" r="9" />
                                                        </svg>
                                                        Weight for Height
                                                    </a>
                                                    <ul id="forms-nav" class="report-subcontent collapsed">
                                                        <li>
                                                            <form id="WFHUponEntryForm" action="{{ route('reports.weight-for-height-upon-entry', ['cycle' => $cycle->id])}}" method="POST">
                                                                @csrf
                                                                <a class="report-link collapsed" href="#" onclick="document.getElementById('WFHUponEntryForm').submit(); return false;">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-2 size-3">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                                                    </svg>
                                                                Upon Entry
                                                                </a>
                                                            </form>
                                                        </li>
                                                    
                                                        <li>
                                                            <form id="WFHAfter120Form" action="{{ route('reports.weight-for-height-after-120', ['cycle' => $cycle->id])}}" method="POST">
                                                                @csrf
                                                                <a class="report-link collapsed" href="#" onclick="document.getElementById('WFHAfter120Form').submit(); return false;">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-2 size-3">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                                                    </svg>
                                                                    After 120 Feedings
                                                                </a>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                            <ul id="forms-nav" class="report-content collapsed">
                                                <li class="nav-item">
                                                    <a class="report-link-main collapsed" href="#" data-target="ns-consolidated">
                                                        <svg class="h-2 w-2 mr-2" width="24" height="24" viewBox="0 0 24 24"
                                                            stroke-width="2" stroke="currentColor" fill="none"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" />
                                                            <circle cx="12" cy="12" r="9" />
                                                        </svg>
                                                        Height for Age
                                                    </a>
                                                    <ul id="forms-nav" class="report-subcontent collapsed">
                                                        <li>
                                                            <form id="HFAUponEntryForm" action="{{ route('reports.height-for-age-upon-entry', ['cycle' => $cycle->id])}}" method="POST">
                                                                @csrf
                                                                <a class="report-link collapsed" href="#" onclick="document.getElementById('HFAUponEntryForm').submit(); return false;">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-2 size-3">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                                                    </svg>
                                                                    Upon Entry
                                                                </a>
                                                            </form>
                                                        </li>
                                                    
                                                        <li>
                                                            <form id="HFAAfter120Form" action="{{ route('reports.height-for-age-after-120', ['cycle' => $cycle->id])}}" method="POST">
                                                                @csrf
                                                                <a class="report-link collapsed" href="#" onclick="document.getElementById('HFAAfter120Form').submit(); return false;">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-2 size-3">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                                                    </svg>
                                                                    After 120 Feedings
                                                                </a>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </li>
                                    @endif

                                    @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('lgu focal') || auth()->user()->hasRole('child development worker'))
                                        <li class="nav-item">
                                            <a class="report-link-main collapsed" href="#">
                                                <svg class="h-2 w-2 mr-2" width="24" height="24" viewBox="0 0 24 24"
                                                    stroke-width="2" stroke="currentColor" fill="none"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" />
                                                    <circle cx="12" cy="12" r="9" />
                                                </svg>
                                                Age Bracket
                                            </a>
                                            <ul id="forms-nav" class="report-content collapsed">
                                                    <li>
                                                        <form id="AgeBracketUponEntryForm" action="{{ route('reports.age-bracket-upon-entry', ['cycle' => $cycle->id])}}" method="POST">
                                                            @csrf
                                                            <a class="report-link collapsed" href="#" onclick="document.getElementById('AgeBracketUponEntryForm').submit(); return false;">
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-2 size-3">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                                                </svg>
                                                                Upon Entry
                                                            </a>
                                                        </form>
                                                    </li>
                                                
                                                    <li>
                                                        <form id="AgeBracketAfter120Form" action="{{ route('reports.age-bracket-after-120', ['cycle' => $cycle->id])}}" method="POST">
                                                            @csrf
                                                            <a class="report-link collapsed" href="#" onclick="document.getElementById('AgeBracketAfter120Form').submit(); return false;">
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-2 size-3">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                                                </svg>
                                                                After 120 Feedings
                                                            </a>
                                                        </form>
                                                    </li>
                                            </ul>
                                        </li>
                                    @endif

                                    @if (auth()->user()->hasRole('admin') ||
                                            auth()->user()->hasRole('lgu focal') ||
                                            auth()->user()->hasRole('child development worker'))
                                        <li class="nav-item">
                                            <form id="monitoringForm" action="{{ route('reports.monitoring', ['cycle' => $cycle->id])}}" method="POST">
                                                @csrf
                                                <a class="report-link collapsed" href="#" onclick="document.getElementById('monitoringForm').submit(); return false;">
                                                    <svg class="h-2 w-2 mr-2" width="24" height="24"
                                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" />
                                                        <circle cx="12" cy="12" r="9" />
                                                    </svg>
                                                    Monitoring
                                                </a>
                                            </form>
                                        </li>
                                    @endif

                                    @if (auth()->user()->hasRole('admin') ||
                                            auth()->user()->hasRole('lgu focal') ||
                                            auth()->user()->hasRole('child development worker'))
                                        <li class="nav-item">
                                            <form id="unfundedForm" action="{{ route('reports.unfunded', ['cycle' => $cycle->id])}}" method="POST">
                                                @csrf
                                                <a class="report-link collapsed" href="#" onclick="document.getElementById('unfundedForm').submit(); return false;">
                                                    <svg class="h-2 w-2 mr-2" width="24" height="24"
                                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" />
                                                        <circle cx="12" cy="12" r="9" />
                                                    </svg>
                                                    Unfunded Children
                                                </a>
                                            </form>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                    </div>
                </div>
                    <div class="col-lg-10">
                        <div class="card">
                            <div class="card-body">
                                <div id="report-content">
                                    @if (auth()->user()->hasRole('admin') ||
                                            auth()->user()->hasRole('lgu focal') ||
                                            auth()->user()->hasRole('child development worker'))
                                        <div id="funded-content">
                                                <h5 class="card-title">Summary of Undernourished Children, Ethnicity, 4Ps, Deworming and Vitamin A</h5>
                                                <h6 class="card-subtitle">After 120 Feeding Days</h6>

                                                <div class="row">
                                                    <div class="col-md-6 mt-2">
                                                        <a href="{{ url('/reports/{cycle}/print/undernourished-after-120') }}" target="_blank" id="printButton">
                                                            <button type="button" class="bg-blue-600 text-white rounded px-3 min-h-9 flex items-center">
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="white" class="size-5 mr-2">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                                </svg>
                                                                <span>Print</span>
                                                            </button>
                                                        </a>
                                                    </div>
                                                </div>
                                            
                                            <div class="table-responsive" style="overflow-x: auto; max-width: 100%;">
                                                @include('reports.partials.undernourished-after-120-table', [
                                                    'centers' => $centers,
                                                ])
                                            </div>
                                            <div class="mt-3">
                                                {{ $centers->links() }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </section>
    </div>
@endsection
