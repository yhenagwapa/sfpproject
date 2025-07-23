@extends('layouts.app')

@section('content')
    <div class="page-title">
        <nav style="--bs-breadcrumb-divider: '>'; margin-bottom: 1rem;">
            <ol class="breadcrumb p-0">
                <li class="breadcrumb-item active"><a href="#">Dashboard</a></li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">

            <!-- Children Card -->
            <div class="col-lg-2">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Children <span>| {{ $totalChildCount }}</span></h5>
                        <div class="d-flex align-items-center mt-3">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <ul>
                                    <li>Male: {{ $totalMaleCount }}</li>
                                    <li>Female: {{ $totalFemaleCount }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Malnourished Card -->
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Malnourished <span>| 1,244</span></h5>
                        <div class="d-flex align-items-center mt-3">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <ul>
                                    <li>Upon Entry: 0</li>
                                    <li>After 120 Feedings: 0</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Undernourished Card -->
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Undernourished <span>| 1,244</span></h5>
                        <div class="d-flex align-items-center mt-3">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <ul>
                                    <li>Upon Entry: 0</li>
                                    <li>After 120 Feedings: 0</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Log Card -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-0">Recent Activity</h5>
                        <ul class="text-sm">
                            <li>Activity 1</li>
                            <li>Activity 2</li>
                            <li>Activity 3</li>
                        </ul>
                    </div>
                </div>
            </div>

            @foreach ($ageCounts as $ageGroup => $counts)
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                {{ ucwords(str_replace('_', ' ', $ageGroup)) }}
                                <span>| {{ $counts['total'] ?? 0 }}</span>
                            </h5>
                            <ul>
                                <li>Male: {{ $counts['male'] ?? 0 }}</li>
                                <li>Female: {{ $counts['female'] ?? 0 }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach


            <div class="col-12">
                <div class="card info-card chart-card">
                    <div class="card-body">
                        <h5 class="card-title">Nutritional Status <br><i>(Weight for Age)</i></h5>
                        <span>
                            <canvas id="barChart1" class="max-h-200 max-w-full"></canvas>
                            <script>
                                document.addEventListener("DOMContentLoaded", () => {
                                    new Chart(document.querySelector('#barChart1'), {
                                        type: 'bar',
                                        data: {
                                            labels: ['Upon Entry', 'After 120 Feedings'],
                                            datasets: [{
                                                    label: 'Severely Underweight',
                                                    data: [100, 50],
                                                    backgroundColor: 'rgba(0, 38, 77, 0.8)',
                                                    borderColor: 'rgb(0, 38, 77)',
                                                    borderWidth: 1
                                                },
                                                {
                                                    label: 'Underweight',
                                                    data: [200, 150],
                                                    backgroundColor: 'rgba(0, 102, 204, 0.8)',
                                                    borderColor: 'rgb(0, 102, 204)',
                                                    borderWidth: 1
                                                },
                                                {
                                                    label: 'Normal',
                                                    data: [250, 350],
                                                    backgroundColor: 'rgba(255, 187, 51, 0.8)',
                                                    borderColor: 'rgb(255, 187, 51)',
                                                    borderWidth: 1
                                                },
                                                {
                                                    label: 'Overweight',
                                                    data: [250, 250],
                                                    backgroundColor: 'rgba(102, 102, 153, 0.8)',
                                                    borderColor: 'rgb(102, 102, 153)',
                                                    borderWidth: 1
                                                }
                                            ]
                                        },
                                        options: {
                                            scales: {
                                                y: {
                                                    beginAtZero: true
                                                }
                                            }
                                        }
                                    });
                                });
                            </script>
                        </span>
                    </div>
                </div>
            </div>



            <div class="col-12">
                <div class="card info-card chart-card">
                    <div class="card-body">
                        <h5 class="card-title">Nutritional Status <br><i>(Weight for Height)</i></h5>
                        <span>
                            <canvas id="barChart2" class="max-h-200 max-w-full"></canvas>
                            <script>
                                document.addEventListener("DOMContentLoaded", () => {
                                    new Chart(document.querySelector('#barChart2'), {
                                        type: 'bar',
                                        data: {
                                            labels: ['Upon Entry', 'After 120 Feedings'],
                                            datasets: [{
                                                    label: 'Severely Wasted',
                                                    data: [100, 50],
                                                    backgroundColor: 'rgba(0, 38, 77, 0.8)',
                                                    borderColor: 'rgb(0, 38, 77)',
                                                    borderWidth: 1
                                                },
                                                {
                                                    label: 'Wasted',
                                                    data: [200, 100],
                                                    backgroundColor: 'rgba(0, 102, 204, 0.8)',
                                                    borderColor: 'rgb(0, 102, 204)',
                                                    borderWidth: 1
                                                },
                                                {
                                                    label: 'Normal',
                                                    data: [200, 300],
                                                    backgroundColor: 'rgba(255, 187, 51, 0.8)',
                                                    borderColor: 'rgb(255, 187, 51)',
                                                    borderWidth: 1
                                                },
                                                {
                                                    label: 'Overweight',
                                                    data: [250, 250],
                                                    backgroundColor: 'rgba(102, 102, 153, 0.8)',
                                                    borderColor: 'rgb(102, 102, 153)',
                                                    borderWidth: 1
                                                },
                                                {
                                                    label: 'Obese',
                                                    data: [250, 250],
                                                    backgroundColor: 'rgba(0, 30, 77, 0.8)',
                                                    borderColor: 'rgb(255, 205, 86)',
                                                    borderWidth: 1
                                                }
                                            ]
                                        },
                                        options: {
                                            scales: {
                                                y: {
                                                    beginAtZero: true
                                                }
                                            }
                                        }
                                    });
                                });
                            </script>
                        </span>
                    </div>
                </div>
            </div>



            <div class="col-12">
                <div class="card info-card chart-card">
                    <div class="card-body">
                        <h5 class="card-title">Nutritional Status <br><i>(Height for Age)</i></h5>
                        <span>
                            <canvas id="barChart3" class="max-h-200 max-w-full"></canvas>
                            <script>
                                document.addEventListener("DOMContentLoaded", () => {
                                    new Chart(document.querySelector('#barChart3'), {
                                        type: 'bar',
                                        data: {
                                            labels: ['Upon Entry', 'After 120 Feedings'],
                                            datasets: [{
                                                    label: 'Severely Stunted',
                                                    data: [300, 250],
                                                    backgroundColor: 'rgba(0, 38, 77, 0.8)',
                                                    borderColor: 'rgb(0, 38, 77)',
                                                    borderWidth: 1
                                                },
                                                {
                                                    label: 'Stunted',
                                                    data: [300, 250],
                                                    backgroundColor: 'rgba(0, 102, 204, 0.8)',
                                                    borderColor: 'rgb(0, 102, 204)',
                                                    borderWidth: 1
                                                },
                                                {
                                                    label: 'Normal',
                                                    data: [250, 300],
                                                    backgroundColor: 'rgba(255, 187, 51, 0.8)',
                                                    borderColor: 'rgb(255, 187, 51)',
                                                    borderWidth: 1
                                                },
                                                {
                                                    label: 'Tall',
                                                    data: [250, 250],
                                                    backgroundColor: 'rgba(102, 102, 153, 0.8)',
                                                    borderColor: 'rgb(102, 102, 153)',
                                                    borderWidth: 1
                                                }
                                            ]
                                        },
                                        options: {
                                            scales: {
                                                y: {
                                                    beginAtZero: true
                                                }
                                            }
                                        }
                                    });
                                });
                            </script>
                        </span>
                    </div>
                </div>
            </div>


            <!-- Beneficiaries Profile Table -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Beneficiaries Profile</h5>
                        <div class="datatable-container">
                            <table class="table table-borderless">
                                <tbody>
                                    @foreach ([
            'pantawid' => 'Pantawid Member',
            'pwd' => 'Persons with Disability',
            'ip' => 'Indigenous People',
            'soloparent' => 'Child of Solo Parent',
            'lactoseintolerant' => 'Lactose Intolerant',
        ] as $key => $category)
                                        <tr>
                                            <td><a href="#">{{ $category }}</a></td>
                                            <td>{{ $profileCounts[$key]['total'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-xs text-gray-400">&nbsp;&nbsp;&nbsp;Male</td>
                                            <td class="text-xs text-gray-400">{{ $profileCounts[$key]['male'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-xs text-gray-400">&nbsp;&nbsp;&nbsp;Female</td>
                                            <td class="text-xs text-gray-400">{{ $profileCounts[$key]['female'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Province Table -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Provinces</h5>
                        <div class="datatable-container">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>Province</th>
                                        <th>Served</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ([
            'davao_city' => 'Davao City',
            'davao_del_norte' => 'Davao del Norte',
            'davao_del_sur' => 'Davao del Sur',
            'davao_de_oro' => 'Davao de Oro',
            'davao_occidental' => 'Davao Occidental',
            'davao_oriental' => 'Davao Oriental',
        ] as $key => $province)
                                        <tr>
                                            <td><a href="#">{{ $province }}</a></td>
                                            <td><a href="#"
                                                    class="text-primary">{{ $provinceCounts[$key]['served'] }}</a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> --}}
    {{-- @vite(['resources/js/app.js']) --}}
@endsection
