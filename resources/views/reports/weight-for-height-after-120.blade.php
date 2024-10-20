@extends('layouts.app')

@section('title', 'SFP Onse')

@section('content')

    <main id="main" class="main">

        <div class="pagetitle">

            <nav style="--bs-breadcrumb-divider: '>';">
                <ol class="breadcrumb mb-3 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('cycle.index') }}">Cycle Implementations</a></li>
                    <li class="breadcrumb-item ">Reports</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->


        @if (session('error'))
            <div class="alert alert-danger alert-primary alert-dismissible fade show" id="danger-alert" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-primary alert-dismissible fade show" id="success-alert" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var alert1 = document.getElementById('success-alert');
                var alert2 = document.getElementById('danger-alert');
                if (alert1) {
                    // Automatically close the alert after 3 seconds (3000 milliseconds)
                    setTimeout(function() {
                        var bsAlert1 = new bootstrap.Alert(alert1);
                        bsAlert1.close();
                    }, 2000);
                }
                if (alert2) {
                    // Automatically close the alert after 3 seconds (3000 milliseconds)
                    setTimeout(function() {
                        var bsAlert2 = new bootstrap.Alert(alert2);
                        bsAlert2.close();
                    }, 2000);
                }
            });
        </script>
        <div class="wrapper">
            <section class="section">
                <div class="row">
                    <div class="col-lg-2">
                        <div class="card">
                            <div class="report-card-body mt-4 mb-2 text-sm">
                                <ul class="report-side-nav" id="report-side-nav">
                                    @if (auth()->user()->hasRole('admin') ||
                                            auth()->user()->hasRole('lgu focal') ||
                                            auth()->user()->hasRole('child development worker'))
                                        <li class="nav-item">
                                            <form id="masterlistForm" action="{{ route('reports.index') }}" method="POST">
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
                                            <form id="malnourishedForm" action="{{ route('reports.malnourish')}}" method="POST">
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
                                            <form id="disabilitiesForm" action="{{ route('reports.disabilities')}}" method="POST">
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
                                                    <form id="undernourishedUponEntryForm" action="{{ route('reports.undernourished-upon-entry')}}" method="POST">
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
                                                    <form id="undernourishedAfter120Form" action="{{ route('reports.undernourished-after-120')}}" method="POST">
                                                        @csrf
                                                        <a class="report-link collapsed" href="#" onclick="document.getElementById('undernourishedAfter120Form').submit(); return false;">
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
                                                            <form id="WFAUponEntryForm" action="{{ route('reports.weight-for-age-upon-entry')}}" method="POST">
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
                                                            <form id="WFAAfter120Form" action="{{ route('reports.weight-for-age-after-120')}}" method="POST">
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
                                                            <form id="WFHUponEntryForm" action="{{ route('reports.weight-for-height-upon-entry')}}" method="POST">
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
                                                            <form id="WFHAfter120Form" action="{{ route('reports.weight-for-height-after-120')}}" method="POST">
                                                                @csrf
                                                                <a class="report-link collapsed active" href="#" onclick="document.getElementById('WFHAfter120Form').submit(); return false;">
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
                                                            <form id="HFAUponEntryForm" action="{{ route('reports.height-for-age-upon-entry')}}" method="POST">
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
                                                            <form id="HFAAfter120Form" action="{{ route('reports.height-for-age-after-120')}}" method="POST">
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
                                                        <form id="AgeBracketUponEntryForm" action="{{ route('reports.age-bracket-upon-entry')}}" method="POST">
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
                                                        <form id="AgeBracketAfter120Form" action="{{ route('reports.age-bracket-after-120')}}" method="POST">
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
                                            <form id="monitoringForm" action="{{ route('reports.monitoring')}}" method="POST">
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
                                            <form id="unfundedForm" action="{{ route('reports.unfunded')}}" method="POST">
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
                                            <h5 class="card-title">CONSOLIDATED NUTRITIONAL STATUS REPORT</h5>
                                            <h6 class="card-subtitle">Weight for Height<br>After 120 Feeding Days</h6>

                                            <div style="overflow-x: auto; max-width: 100%;">
                                                @include('reports.partials.weight-for-height-after-120-table')
                                            </div>
                                            {{-- <div class="mt-3">
                                                @foreach ($isFunded as $fundedChildren)
                                                    <!-- Display pagination links for each center -->
                                                    {{ $fundedChildren->links() }}
                                                @endforeach
                                            </div> --}}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </section>
        </div>
        {{-- <script>
            document.addEventListener('DOMContentLoaded', function() {
                const reportLinks = document.querySelectorAll('.report-link');

                reportLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const target = this.getAttribute('data-target');
                        showContent(target);
                    });
                });

                function showContent(target) {
                    document.querySelectorAll('#report-content > div').forEach(div => div.style.display = 'none');

                    const selectedContent = document.getElementById(`${target}-content`);
                    if (selectedContent) {
                        selectedContent.style.display = 'block';
                    }
                }

                showContent('funded');
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const reportLinks = document.querySelectorAll('.report-link');

                reportLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        reportLinks.forEach(l => l.classList.remove('active'));

                        this.classList.add('');
                    });
                });
            });
        </script> --}}

    </main><!-- End #main -->
