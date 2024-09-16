@extends('layouts.app')

@section('title', 'SFP Onse')

@section('content')

    <main id="main" class="main">

        <div class="pagetitle">

            <nav style="--bs-breadcrumb-divider: '>';">
                <ol class="breadcrumb mb-3 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('child.index') }}">Reports</a></li>
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
                            <div class="card-body mt-4 text-sm">
                               
                                <ul class="report-side-nav" id="report-side-nav">
                                    <li class="nav-item">
                                        <a class="report-link collapsed" href="#" data-target="masterlist">
                                            <svg class="h-3 w-3 mr-2"  width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">  <path stroke="none" d="M0 0h24v24H0z"/>  <circle cx="12" cy="12" r="9" /></svg>
                                            Masterlist
                                        </a>
                                    </li><li class="nav-item">
                                        <a class="report-link collapsed" href="#" data-target="malnourished">
                                            <svg class="h-3 w-3 mr-2"  width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">  <path stroke="none" d="M0 0h24v24H0z"/>  <circle cx="12" cy="12" r="9" /></svg>
                                            Malnourished
                                        </a>
                                    </li>
                                    </li><li class="nav-item">
                                        <a class="report-link collapsed" href="#" data-target="disability">
                                            <svg class="h-3 w-3 mr-2"  width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">  <path stroke="none" d="M0 0h24v24H0z"/>  <circle cx="12" cy="12" r="9" /></svg>
                                            Persons with Disability
                                        </a>
                                    </li>
                                    </li><li class="nav-item">
                                        <a class="report-link collapsed" href="#" data-target="undernourished">
                                            <svg class="h-3 w-3 mr-2"  width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">  <path stroke="none" d="M0 0h24v24H0z"/>  <circle cx="12" cy="12" r="9" /></svg>
                                            Undernourished
                                        </a>
                                    </li>
                                    </li><li class="nav-item">
                                        <a class="report-link collapsed" href="user-accounts.php">
                                            <svg class="h-3 w-3 mr-2"  width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">  <path stroke="none" d="M0 0h24v24H0z"/>  <circle cx="12" cy="12" r="9" /></svg>
                                            Nutritional Status
                                        </a>
                                    </li>
                                    </li><li class="nav-item">
                                        <a class="report-link collapsed" href="user-accounts.php">
                                            <svg class="h-3 w-3 mr-2"  width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">  <path stroke="none" d="M0 0h24v24H0z"/>  <circle cx="12" cy="12" r="9" /></svg>
                                            Age Bracket
                                        </a>
                                    </li>
                                    </li><li class="nav-item">
                                        <a class="report-link collapsed" href="#" data-target="monitoring">
                                            <svg class="h-3 w-3 mr-2"  width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">  <path stroke="none" d="M0 0h24v24H0z"/>  <circle cx="12" cy="12" r="9" /></svg>
                                            Monitoring
                                        </a>
                                    </li>
                                    </li><li class="nav-item">
                                        <a class="report-link collapsed" href="user-accounts.php">
                                            <svg class="h-3 w-3 mr-2"  width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">  <path stroke="none" d="M0 0h24v24H0z"/>  <circle cx="12" cy="12" r="9" /></svg>
                                            Actual Feeding Attendance
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="card">
                            <div class="card-body">
                                <div id="report-content">
                                    <div id="masterlist-content">
                                        <div class="card-title">
                                            <h5>Masterlist of Beneficiaries</h5>
                                        </div>
                                        <div style="overflow-x: auto; max-width: 100%;">
                                            @include('reports.partials.masterlist-table', ['children' => $children])
                                        </div>
                                        <div class="mt-3">
                                            {{ $children->links() }}
                                        </div>
                                    </div>

                                    <div id="malnourished-content">
                                        <div class="card-title">
                                            <h5>List of Malnourished Children</h5>
                                        </div>
                                        <div style="overflow-x: auto; max-width: 100%;">
                                            @include('reports.partials.malnourished-table', ['children' => $children])
                                        </div>
                                    </div>

                                    <div id="disability-content">
                                        <div class="card-title">
                                            <h5>Persons with Disability</h5>
                                        </div>
                                        <div style="overflow-x: auto; max-width: 100%;">
                                            @include('reports.partials.disability-table', ['childrenWithDisabilities' => $childrenWithDisabilities])
                                        </div>
                                        <div class="mt-3">
                                            {{ $childrenWithDisabilities->links() }}
                                        </div>
                                    </div>

                                    <div id="undernourished-content">
                                        <div class="card-title">
                                            <h5>Summary of Undernourished Children, Ethnicity, 4Ps, Deworming & Vitamin A
                                            </h5>
                                        </div>
                                        <div style="overflow-x: auto; max-width: 100%;">
                                            @include('reports.partials.undernourished-table', ['centers' => $centers])
                                        </div>
                                    </div>

                                    <div id="monitoring-content">
                                        <div class="card-title">
                                            <h5>Weight and Height Monitoring</h5>
                                        </div>
                                        <div style="overflow-x: auto; max-width: 100%;">
                                            @include('reports.partials.monitoring-table', ['children' => $children])
                                        </div>
                                        <div class="mt-3">
                                            {{ $children->links() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const reportLinks = document.querySelectorAll('.report-link');

                reportLinks.forEach(link => {
                    link.addEventListener('click', function (e) {
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
                showContent('masterlist');
            }); 
        </script>
          
    </main><!-- End #main -->
