@extends('layouts.app')

@section('content')

        <div class="page-title">
            <nav style="--bs-breadcrumb-divider: '>'; margin-bottom: 1rem;">
                <ol class="breadcrumb p-0">
                    <li class="breadcrumb-item active"><a href="#">Dashboard</a></li>
                </ol>
            </nav>
        </div>

        <div class="wrapper">
            <section class="section">
                <div class="row">

                    <!-- Children Card -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Children <span>| 1,244</span></h5>
                                <div class="d-flex align-items-center mt-3">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <ul>
                                            <li>Male: 622</li>
                                            <li>Female: 622</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Malnourished Card -->
                    <div class="col-lg-4">
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
                    <div class="col-lg-4">
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

                    

                    <!-- Nutritional Status Charts -->
                    {{-- <div class="container">
                        @foreach (['Weight for Age', 'Weight for Height', 'Height for Age'] as $type)
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Nutritional Status <br><i>({{ $type }})</i></h5>
                                        <canvas id="barChart{{ $loop->index + 1 }}" class="max-h-200 max-w-full"></canvas>
                                        <script>
                                            document.addEventListener("DOMContentLoaded", () => {
                                                new Chart(document.querySelector('#barChart{{ $loop->index + 1 }}'), {
                                                    type: 'bar',
                                                    data: {
                                                        labels: ['Upon Entry', 'After 120 Feedings'],
                                                        datasets: [{
                                                                label: 'Severely Underweight',
                                                                data: [50, 100],
                                                                backgroundColor: 'rgba(0, 38, 77, 0.8)', 
                                                                borderColor: 'rgb(0, 38, 77)',
                                                                borderWidth: 1
                                                            },
                                                            {
                                                                label: 'Underweight',
                                                                data: [150, 200],
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
                                                                label: 'Overweight',
                                                                data: [250, 300],
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
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div> --}}

                    <div class="container">
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
                                                        datasets: [
                                                                {
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
                    </div>

                    <div class="container">
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
                                                        datasets: [
                                                                {
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
                    </div>

                    <div class="container">
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
                                                        datasets: [
                                                                {
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
                    </div>

                    <!-- Beneficiaries Profile Table -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Beneficiaries Profile</h5>
                                <div class="datatable-container">
                                    <table class="table table-borderless">
                                        <tbody>
                                            @foreach ([['category' => 'Pantawid Member', 'count' => 1500, 'male' => 750, 'female' => 750], ['category' => 'Persons with Disability', 'count' => 1500, 'male' => 750, 'female' => 750], ['category' => 'Indigenous People', 'count' => 1500, 'male' => 750, 'female' => 750], ['category' => 'Child of Solo Parent', 'count' => 1500, 'male' => 750, 'female' => 750], ['category' => 'Lactose Intolerant', 'count' => 1500, 'male' => 750, 'female' => 750]] as $beneficiary)
                                                <tr>
                                                    <td><a href="#">{{ $beneficiary['category'] }}</a></td>
                                                    <td>{{ $beneficiary['count'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-xs text-gray-400">&nbsp;&nbsp;&nbsp;Male</td>
                                                    <td class="text-xs text-gray-400">{{ $beneficiary['male'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-xs text-gray-400">&nbsp;&nbsp;&nbsp;Female</td>
                                                    <td class="text-xs text-gray-400">{{ $beneficiary['female'] }}</td>
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
                                                <th>Target</th>
                                                <th>Served</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach (['Davao City', 'Davao del Norte', 'Davao del Sur', 'Davao de Oro', 'Davao Occidental', 'Davao Oriental'] as $province)
                                                <tr>
                                                    <td><a href="#">{{ $province }}</a></td>
                                                    <td>1500</td>
                                                    <td><a href="#" class="text-primary">1400</a></td>
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
        </div>

        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        @vite(['resources/js/app.js'])
@endsection
