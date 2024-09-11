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
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h5 class='col-md-6'>Reports</h5>

                                </div>

                                <!-- Tab Menu -->
                                <div class="tabs-wrapper">
                                    <ul class="nav nav-tabs text-sm d-flex flex-nowrap" id="myTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="masterlist-tab" data-bs-toggle="tab"
                                                data-bs-target="#masterlist" href="#masterlist" type="button"
                                                role="tab" aria-controls="masterlist" aria-selected="true">Masterlist</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="malnourished-tab" data-bs-toggle="tab"
                                                data-bs-target="#malnourished" href="#malnourished" type="button" role="tab"
                                                aria-controls="malnourished" aria-selected="false">Malnourished</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="pwd-tab" data-bs-toggle="tab"
                                                data-bs-target="#pwd" href="#pwd" type="button" role="tab"
                                                aria-controls="pwd" aria-selected="false">Persons with Disability</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="undernourished-tab" data-bs-toggle="tab"
                                                data-bs-target="#undernourished" href="#undernourished" type="button" role="tab"
                                                aria-controls="undernourished" aria-selected="false">Undernourished</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="weight-for-age-tab" data-bs-toggle="tab"
                                                data-bs-target="#weight-for-age" href="#weight-for-age" type="button" role="tab"
                                                aria-controls="weight-for-age" aria-selected="false">Nutritional Status</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="age-braket-entry-tab" data-bs-toggle="tab"
                                                data-bs-target="#age-braket-entry" href="#age-braket-entry" type="button" role="tab"
                                                aria-controls="age-braket-entry" aria-selected="false">Age Bracket</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="weight-and-height-monitoring-tab" data-bs-toggle="tab"
                                                data-bs-target="#weight-and-height-monitoring" href="#weight-and-height-monitoring" type="button" role="tab"
                                                aria-controls="weight-and-height-monitoring" aria-selected="false">Monitoring</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="actual-feeding-attendance-tab" data-bs-toggle="tab"
                                                data-bs-target="#actual-feeding-attendance" href="#actual-feeding-attendance" type="button" role="tab"
                                                aria-controls="actual-feeding-attendance" aria-selected="false">Actual Feeding Attendance</button>
                                        </li>
                                    </ul>
                                </div>
                                

                                <div class="tab-content pt-2 text-sm" id="myTabContent">

                                    <!-- Masterlist tab -->
                                    <div class="tab-pane fade show active" id="masterlist" role="tabpanel"
                                        aria-labelledby="masterlist-tab">
                                        <div class="col-md-12" id="masterlist-table">
                                            @include('reports.partials.masterlist-table', ['children' => $children])
                                        </div>
                                    </div>

                                    <!-- Malnourished Children tab -->
                                    <div class="tab-pane fade" id="malnourished" role="tabpanel"
                                        aria-labelledby="malnourished-tab">
                                    </div>

                                    <!-- PWD tab -->
                                    <div class="tab-pane fade" id="pwd" role="tabpanel"
                                        aria-labelledby="pwd-tab">
                                    </div>

                                    <!-- Undernourished Children tab -->
                                    <div class="tab-pane fade" id="undernourished" role="tabpanel"
                                        aria-labelledby="undernourished-tab">
                                    </div>

                                    <!-- Weight for Age tab -->
                                    <div class="tab-pane fade" id="weight-for-age" role="tabpanel"
                                        aria-labelledby="weight-for-age-tab">
                                    </div>

                                    <!-- Weight for Height tab -->
                                    <div class="tab-pane fade" id="weight-for-height" role="tabpanel"
                                        aria-labelledby="weight-for-height-tab">
                                    </div>

                                    <!-- Height for Age tab -->
                                    <div class="tab-pane fade" id="height-for-age" role="tabpanel"
                                        aria-labelledby="height-for-age-tab">
                                    </div>

                                    <!-- Age Braket Upon Entry tab -->
                                    <div class="tab-pane fade" id="age-braket-upon-entry" role="tabpanel"
                                        aria-labelledby="age-braket-upon-entry-tab">
                                    </div>

                                    <!-- Age Braket after 120 Feedings tab -->
                                    <div class="tab-pane fade" id="age-braket-after-120feedings" role="tabpanel"
                                        aria-labelledby="age-braket-after-120feedings-tab">
                                    </div>

                                    <!-- Weight and Height Monitoring tab -->
                                    <div class="tab-pane fade" id="weight-and-height-monitoring" role="tabpanel"
                                        aria-labelledby="weight-and-height-monitoring-tab">
                                    </div>

                                    <!-- Actual Feeding Attendance tab -->
                                    <div class="tab-pane fade" id="actual-feeding-attendance" role="tabpanel"
                                        aria-labelledby="actual-feeding-attendance-tab">
                                    </div>
                                </div>
                            </div><!-- End Default Tabs -->
                        </div>
                    </div>
                </div>
        </div>


        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Function to toggle additional details based on radio button selection
                function toggleAdditionalDetails(radioName, additionalDetailsId) {
                    const radios = document.getElementsByName(radioName);
                    const additionalDetailsSelect = document.getElementById(additionalDetailsId);

                    radios.forEach(radio => {
                        radio.addEventListener('change', function() {
                            if (radio.value === '1' && radio.checked) {
                                additionalDetailsSelect.disabled = false;
                                additionalDetailsSelect.required = true;
                            } else if (radio.value === '0' && radio.checked) {
                                additionalDetailsSelect.disabled = true;
                                additionalDetailsSelect.required = false;
                            }
                        });
                    });

                    // Initial check in case the page is loaded with a radio already checked
                    const checkedRadio = Array.from(radios).find(radio => radio.checked);
                    if (checkedRadio && checkedRadio.value === '1') {
                        additionalDetailsSelect.disabled = false;
                    } else {
                        additionalDetailsSelect.disabled = true;
                    }
                }


                // Apply the function to each set of radio buttons and additional details
                toggleAdditionalDetails('is_pantawid', 'pantawid_details');
                toggleAdditionalDetails('is_pwd', 'pwd_details');
            });
        </script>

        </section>
        </div>
    </main><!-- End #main -->
