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
                    <div class="col-lg-3">
                        <div class="card">
                            <div class="report-card-body mt-4 mb-2 text-sm">

                                <ul class="report-side-nav" id="report-side-nav">
                                    @if (auth()->user()->hasRole('admin') ||
                                            auth()->user()->hasRole('lgu focal') ||
                                            auth()->user()->hasRole('child development worker'))
                                        <li class="nav-item">
                                            <a class="report-link collapsed " href="#" data-target="funded">
                                                <svg class="h-2 w-2 mr-2" width="24" height="24" viewBox="0 0 24 24"
                                                    stroke-width="2" stroke="currentColor" fill="none"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" />
                                                    <circle cx="12" cy="12" r="9" />
                                                </svg>
                                                Masterlist
                                            </a>
                                        </li>
                                    @endif

                                    @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('lgu focal'))
                                        <li class="nav-item">
                                            <a class="report-link collapsed" href="#" data-target="malnourished">
                                                <svg class="h-2 w-2 mr-2" width="24" height="24" viewBox="0 0 24 24"
                                                    stroke-width="2" stroke="currentColor" fill="none"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" />
                                                    <circle cx="12" cy="12" r="9" />
                                                </svg>
                                                Malnourished
                                            </a>
                                        </li>
                                    @endif

                                    @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('lgu focal'))
                                        <li class="nav-item">
                                            <a class="report-link collapsed" href="#" data-target="disability">
                                                <svg class="h-2 w-2 mr-2" width="24" height="24" viewBox="0 0 24 24"
                                                    stroke-width="2" stroke="currentColor" fill="none"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" />
                                                    <circle cx="12" cy="12" r="9" />
                                                </svg>
                                                Persons with Disability
                                            </a>
                                        </li>
                                    @endif

                                    @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('lgu focal'))
                                        <li class="nav-item">
                                            <a class="report-link collapsed" href="#" data-target="undernourished">
                                                <svg class="h-2 w-2 mr-2" width="24" height="24" viewBox="0 0 24 24"
                                                    stroke-width="2" stroke="currentColor" fill="none"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" />
                                                    <circle cx="12" cy="12" r="9" />
                                                </svg>
                                                Undernourished
                                            </a>
                                        </li>
                                    @endif

                                    @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('lgu focal'))
                                        <li class="nav-item">
                                            <a class="report-link collapsed" href="#" data-target="ns-consolidated">
                                                <svg class="h-2 w-2 mr-2" width="24" height="24" viewBox="0 0 24 24"
                                                    stroke-width="2" stroke="currentColor" fill="none"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" />
                                                    <circle cx="12" cy="12" r="9" />
                                                </svg>
                                                Nutritional Status
                                            </a>
                                        </li>
                                    @endif

                                    @if (auth()->user()->hasRole('admin') ||
                                            auth()->user()->hasRole('lgu focal') ||
                                            auth()->user()->hasRole('child development worker'))
                                        
                                            <a class="report-link collapsed active" class="report-link collapsed" href="#" data-target="entry-age-bracket">
                                                <svg class="h-2 w-2 mr-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" />
                                                    <circle cx="12" cy="12" r="9" />
                                                </svg>
                                                Age Bracket Upon Entry
                                            </a>
                                           
                                    @endif

                                    @if (auth()->user()->hasRole('admin') ||
                                            auth()->user()->hasRole('lgu focal') ||
                                            auth()->user()->hasRole('child development worker'))
                                        
                                            <a class="report-link collapsed" class="report-link collapsed" href="#" data-target="exit-age-bracket">
                                                <svg class="h-2 w-2 mr-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" />
                                                    <circle cx="12" cy="12" r="9" />
                                                </svg>
                                                Age Bracket After 120 Feeding Days
                                            </a>
                                           
                                    @endif

                                    @if (auth()->user()->hasRole('admin') ||
                                            auth()->user()->hasRole('lgu focal') ||
                                            auth()->user()->hasRole('child development worker'))
                                        <li class="nav-item">
                                            <a class="report-link collapsed" href="#" data-target="monitoring">
                                                <svg class="h-2 w-2 mr-2" width="24" height="24"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" />
                                                    <circle cx="12" cy="12" r="9" />
                                                </svg>
                                                Monitoring
                                            </a>
                                        </li>
                                    @endif

                                    @if (auth()->user()->hasRole('admin') ||
                                            auth()->user()->hasRole('lgu focal') ||
                                            auth()->user()->hasRole('child development worker'))
                                        <li class="nav-item">
                                            <a class="report-link collapsed" href="#" data-target="attendance">
                                                <svg class="h-2 w-2 mr-2" width="24" height="24"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" />
                                                    <circle cx="12" cy="12" r="9" />
                                                </svg>
                                                Feeding Attendance
                                            </a>
                                        </li>
                                    @endif

                                    @if (auth()->user()->hasRole('admin') ||
                                            auth()->user()->hasRole('lgu focal') ||
                                            auth()->user()->hasRole('child development worker'))
                                        <li class="nav-item">
                                            <a class="report-link collapsed" href="#" data-target="unfunded">
                                                <svg class="h-2 w-2 mr-2" width="24" height="24"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" />
                                                    <circle cx="12" cy="12" r="9" />
                                                </svg>
                                                Unfunded Children
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="card">
                            <div class="card-body">
                                <div id="report-content">
                                    @if (auth()->user()->hasRole('admin') ||
                                            auth()->user()->hasRole('lgu focal') ||
                                            auth()->user()->hasRole('child development worker'))
                                        <div id="funded-content">
                                            <div class="card-title">
                                                <h5>Masterlist of Funded Beneficiaries</h5>
                                            </div>
                                            
                                            <div style="overflow-x: auto; max-width: 100%;">
                                                @include('reports.partials.funded-table', [
                                                    'isFunded' => $isFunded,
                                                ])
                                            </div>
                                            <div class="mt-3">
                                                {{ $isFunded->links() }}
                                            </div>
                                        </div>
                                    @endif

                                    @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('lgu focal'))
                                        <div id="malnourished-content">
                                            <div class="card-title">
                                                <h5>List of Malnourished Children</h5>
                                            </div>
                                            <div style="overflow-x: auto; max-width: 100%;">
                                                @include('reports.partials.malnourished-table', ['isFunded' => $isFunded])
                                            </div>
                                        </div>
                                    @endif

                                    @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('lgu focal'))
                                        <div id="disability-content">
                                            <div class="card-title">
                                                <h5>Persons with Disability</h5>
                                            </div>
                                            <div style="overflow-x: auto; max-width: 100%;">
                                                @include('reports.partials.disability-table', [
                                                    'isPwdChildren' => $isPwdChildren,
                                                ])
                                            </div>
                                        </div>
                                    @endif

                                    @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('lgu focal'))
                                        <div id="undernourished-content">
                                            <div class="card-title">
                                                <h5>Summary of Undernourished Children, Ethnicity, 4Ps, Deworming & Vitamin
                                                    A
                                                </h5>
                                            </div>
                                            <div style="overflow-x: auto; max-width: 100%;">
                                                @include('reports.partials.undernourished-table', ['centers' => $centers])
                                            </div>
                                        </div>
                                    @endif

                                    @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('lgu focal'))
                                        <div id="ns-consolidated-content">
                                            <div class="card-title">
                                                <h5>Consolidated Nutritional Status</h5>
                                            </div>
                                            <div style="overflow-x: auto; max-width: 100%;">
                                                @include('reports.partials.ns-consolidated-table', ['centers' => $centers])
                                            </div>
                                        </div>
                                    @endif

                                    @if (auth()->user()->hasRole('admin') ||
                                            auth()->user()->hasRole('lgu focal') ||
                                            auth()->user()->hasRole('child development worker'))
                                        <div id="entry-age-bracket-content">
                                            <div class="card-title">
                                                <h5>Age Bracket Upon Entry</h5>
                                            </div>
                                            <div style="overflow-x: auto; max-width: 100%;">
                                                @include('reports.partials.entry-age-bracket-table', ['centers' => $centers])
                                            </div>
                                        </div>
                                    @endif

                                    @if (auth()->user()->hasRole('admin') ||
                                            auth()->user()->hasRole('lgu focal') ||
                                            auth()->user()->hasRole('child development worker'))
                                        <div id="exit-age-bracket-content">
                                            <div class="card-title">
                                                <h5>Age Bracket After 120 Feeding Days</h5>
                                            </div>
                                            <div style="overflow-x: auto; max-width: 100%;">
                                                @include('reports.partials.exit-age-bracket-table', ['centers' => $centers])
                                            </div>
                                        </div>
                                    @endif

                                    @if (auth()->user()->hasRole('admin') ||
                                            auth()->user()->hasRole('lgu focal') ||
                                            auth()->user()->hasRole('child development worker'))
                                        <div id="monitoring-content">
                                            <div class="card-title">
                                                <h5>Weight and Height Monitoring</h5>
                                            </div>
                                            <div style="overflow-x: auto; max-width: 100%;">
                                                @include('reports.partials.monitoring-table', ['isFunded' => $isFunded])
                                            </div>
                                        </div>
                                    @endif

                                    @if (auth()->user()->hasRole('admin') ||
                                            auth()->user()->hasRole('lgu focal') ||
                                            auth()->user()->hasRole('child development worker'))
                                        <div id="attendance-content">
                                            <div class="card-title">
                                                <h5>Actual Feeding Attendance</h5>
                                            </div>
                                            <div style="overflow-x: auto; max-width: 100%;">
                                                @include('reports.partials.attendance-table', ['centers' => $centers])
                                            </div>
                                        </div>
                                    @endif

                                    @if (auth()->user()->hasRole('admin') ||
                                            auth()->user()->hasRole('lgu focal') ||
                                            auth()->user()->hasRole('child development worker'))
                                        <div id="unfunded-content">
                                            <div class="card-title">
                                                <h5>Unfunded Children</h5>
                                            </div>
                                            
                                            <div style="overflow-x: auto; max-width: 100%;">
                                                @include('reports.partials.unfunded-table', [
                                                    'isNotFunded' => $isNotFunded,
                                                ])
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
        <script>
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
                    // Hide all content sections
                    document.querySelectorAll('#report-content > div').forEach(div => div.style.display = 'none');

                    // Show the selected content
                    const selectedContent = document.getElementById(`${target}-content`);
                    if (selectedContent) {
                        selectedContent.style.display = 'block';
                    }
                }

                // Show "masterlist" content by default
                showContent('funded');
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const reportLinks = document.querySelectorAll('.report-link');

                reportLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        // Remove 'active' class from all links
                        reportLinks.forEach(l => l.classList.remove('active'));

                        // Add 'active' class to the clicked link
                        this.classList.add('');
                    });
                });
            });
        </script>

    </main><!-- End #main -->
