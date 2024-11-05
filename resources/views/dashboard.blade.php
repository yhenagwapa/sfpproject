@extends('layouts.app')

@section('content')

<main id="main" class="main">


    <nav style="--bs-breadcrumb-divider: '>';">
        <ol class="breadcrumb mb-3 p-0">
            <li class="breadcrumb-item active"><a href="#">Dashboard</a></li>
        </ol>
    </nav>

    <div class="wrapper">
        <section class="section dashboard">
            <div class="row">
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-xxl-4 col-md-6">
                            <div class="card info-card children-card">
                                <div class="card-body">
                                    <h5 class="card-title">Children <span>| 1,244</span></h5>

                                    <div class="d-flex align-items-center mt-3">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">

                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div
                                                class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <ul>
                                                    <li>
                                                        <svg class="w-5 h-5 fill-[#899bbd]" viewBox="0 0 320 512"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M96 64a64 64 0 1 1 128 0A64 64 0 1 1 96 64zm48 320v96c0 17.7-14.3 32-32 32s-32-14.3-32-32V287.8L59.1 321c-9.4 15-29.2 19.4-44.1 10S-4.5 301.9 4.9 287l39.9-63.3C69.7 184 113.2 160 160 160s90.3 24 115.2 63.6L315.1 287c9.4 15 4.9 34.7-10 44.1s-34.7 4.9-44.1-10L240 287.8V480c0 17.7-14.3 32-32 32s-32-14.3-32-32V384H144z">
                                                            </path>
                                                        </svg>
                                                    </li>
                                                    <li class="mt-1 mr-1">
                                                        <svg class="w-5 h-5 fill-[#899bbd]" viewBox="0 0 320 512"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M224 64A64 64 0 1 0 96 64a64 64 0 1 0 128 0zM88 400v80c0 17.7 14.3 32 32 32s32-14.3 32-32V400h16v80c0 17.7 14.3 32 32 32s32-14.3 32-32V400h17.8c10.9 0 18.6-10.7 15.2-21.1l-31.1-93.4 28.6 37.8c10.7 14.1 30.8 16.8 44.8 6.2s16.8-30.7 6.2-44.8L254.6 207c-22.4-29.6-57.5-47-94.6-47s-72.2 17.4-94.6 47L6.5 284.7c-10.7 14.1-7.9 34.2 6.2 44.8s34.2 7.9 44.8-6.2l28.7-37.8L55 378.9C51.6 389.3 59.3 400 70.2 400H88z">
                                                            </path>
                                                        </svg>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div
                                                class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <ul>
                                                    <li>Male</li>
                                                    <li class="mt-1 mr-1">Female</li>
                                                </ul>
                                            </div>
                                            <div class="ps-8">
                                                <ul>
                                                    <li>622</li>
                                                    <li class="mt-1 mr-1">622</li>
                                                </ul>
                                                {{-- <span class="text-success small pt-1 fw-bold">12%</span> <span
                                                    class="text-muted small pt-2 ps-1">increase</span> --}}

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xxl-4 col-md-6">
                            <div class="card info-card malnourish-card">
                                <div class="card-body">
                                    <h5 class="card-title">Malnourish <span>| 1,244</span></h5>

                                    <div class="d-flex align-items-center mt-3">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <ul class="text-sm">
                                                <li>Upon Entry</li>
                                                <li>After 120 Feedings</li>
                                            </ul>
                                        </div>
                                        <div class="ps-8">
                                            <ul>
                                                <li>0</li>
                                                <li>0</li>
                                            </ul>
                                            {{-- <span class="text-success small pt-1 fw-bold">12%</span> <span
                                                class="text-muted small pt-2 ps-1">increase</span> --}}
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xxl-4 col-md-6">
                            <div class="card info-card undernourish-card">
                                <div class="card-body">
                                    <h5 class="card-title">Undernourish <span>| 1,244</span></h5>

                                    <div class="d-flex align-items-center mt-3">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <ul>
                                                <li>Upon Entry</li>
                                                <li>After 120 Feedings</li>
                                            </ul>
                                        </div>
                                        <div class="ps-8">
                                            <ul>
                                                <li>0</li>
                                                <li>0</li>
                                            </ul>
                                            {{-- <span class="text-success small pt-1 fw-bold">12%</span> <span
                                                class="text-muted small pt-2 ps-1">increase</span> --}}

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card info-card province-card">
                                <div class="card-body">
                                    <h5 class="card-title">Provinces</h5>
                                    <div class="datatable-container">
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th scope="col" data-sortable="true"
                                                        style="width: 35.096385542168676%;">
                                                        <button class="datatable-sorter">Province</button>
                                                    </th>
                                                    <th scope="col" data-sortable="true"
                                                        style="width: 24.096385542168676%;"><button
                                                            class="datatable-sorter">Target</button></th>
                                                    <th scope="col" data-sortable="true"
                                                        style="width: 24.819277108433734%;"><button
                                                            class="datatable-sorter">Served</button></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr data-index="0">
                                                    <td scope="row"><a href="#">Davao City</a></td>
                                                    <td>1500</td>
                                                    <td><a href="#" class="text-primary">1400</a></td>
                                                </tr>
                                                <tr data-index="0">
                                                    <td scope="row"><a href="#">Davao del Norte</a></td>
                                                    <td>1500</td>
                                                    <td><a href="#" class="text-primary">1400</a></td>
                                                </tr>
                                                <tr data-index="0">
                                                    <td scope="row"><a href="#">Davao del Sur</a></td>
                                                    <td>1500</td>
                                                    <td><a href="#" class="text-primary">1400</a></td>
                                                </tr>
                                                <tr data-index="0">
                                                    <td scope="row"><a href="#">Davao de Oro</a></td>
                                                    <td>1500</td>
                                                    <td><a href="#" class="text-primary">1400</a></td>
                                                </tr>
                                                <tr data-index="0">
                                                    <td scope="row"><a href="#">Davao Occidental</a></td>
                                                    <td>1500</td>
                                                    <td><a href="#" class="text-primary">1400</a></td>
                                                </tr>
                                                <tr data-index="0">
                                                    <td scope="row"><a href="#">Davao Oriental</a></td>
                                                    <td>1500</td>
                                                    <td><a href="#" class="text-primary">1400</a></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

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
                                                                    data: [50, 100],
                                                                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                                                    borderColor: 'rgb(255, 99, 132)',
                                                                    borderWidth: 1
                                                                },
                                                                {
                                                                    label: 'Underweight',
                                                                    data: [150, 200],
                                                                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                                                    borderColor: 'rgb(54, 162, 235)',
                                                                    borderWidth: 1
                                                                },
                                                                {
                                                                    label: 'Normal',
                                                                    data: [250, 300],
                                                                    backgroundColor: 'rgba(255, 205, 86, 0.2)',
                                                                    borderColor: 'rgb(255, 205, 86)',
                                                                    borderWidth: 1
                                                                },
                                                                {
                                                                    label: 'Overweight',
                                                                    data: [250, 300],
                                                                    backgroundColor: 'rgba(255, 205, 86, 0.2)',
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
                                                                    data: [50, 100],
                                                                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                                                    borderColor: 'rgb(255, 99, 132)',
                                                                    borderWidth: 1
                                                                },
                                                                {
                                                                    label: 'Wasted',
                                                                    data: [150, 200],
                                                                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                                                    borderColor: 'rgb(54, 162, 235)',
                                                                    borderWidth: 1
                                                                },
                                                                {
                                                                    label: 'Normal',
                                                                    data: [250, 300],
                                                                    backgroundColor: 'rgba(255, 205, 86, 0.2)',
                                                                    borderColor: 'rgb(255, 205, 86)',
                                                                    borderWidth: 1
                                                                },
                                                                {
                                                                    label: 'Overweight',
                                                                    data: [250, 300],
                                                                    backgroundColor: 'rgba(255, 205, 86, 0.2)',
                                                                    borderColor: 'rgb(255, 205, 86)',
                                                                    borderWidth: 1
                                                                },
                                                                {
                                                                    label: 'Obese',
                                                                    data: [250, 300],
                                                                    backgroundColor: 'rgba(255, 205, 86, 0.2)',
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
                                                                    data: [50, 100],
                                                                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                                                    borderColor: 'rgb(255, 99, 132)',
                                                                    borderWidth: 1
                                                                },
                                                                {
                                                                    label: 'Stunted',
                                                                    data: [150, 200],
                                                                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                                                    borderColor: 'rgb(54, 162, 235)',
                                                                    borderWidth: 1
                                                                },
                                                                {
                                                                    label: 'Normal',
                                                                    data: [250, 300],
                                                                    backgroundColor: 'rgba(255, 205, 86, 0.2)',
                                                                    borderColor: 'rgb(255, 205, 86)',
                                                                    borderWidth: 1
                                                                },
                                                                {
                                                                    label: 'Tall',
                                                                    data: [250, 300],
                                                                    backgroundColor: 'rgba(255, 205, 86, 0.2)',
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
                    </div>
                </div>

                <!-- Left side columns -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Beneficiaries Profile</h5>
                            <div class="datatable-container">
                                <table class="table table-borderless ">

                                    <tbody>
                                        <tr data-index="0">
                                            <td scope="row"><a href="#">Pantawid Member</a></td>
                                            <td>1500</td>
                                        </tr>
                                        <tr data-index="0">
                                            <td scope="row" class="text-xs text-gray-400"><a
                                                    href="#">&nbsp;&nbsp;&nbsp;Male</a></td>
                                            <td class="text-xs text-gray-400">750</td>
                                        </tr>
                                        <tr data-index="0">
                                            <td scope="row" class="text-xs text-gray-400"><a
                                                    href="#">&nbsp;&nbsp;&nbsp;Female</a></td>
                                            <td class="text-xs text-gray-400">750</td>
                                        </tr>
                                        <tr data-index="0">
                                            <td scope="row"><a href="#">Persons with Disability</a></td>
                                            <td>1500</td>
                                        </tr>
                                        <tr data-index="0">
                                            <td scope="row" class="text-xs text-gray-400"><a
                                                    href="#">&nbsp;&nbsp;&nbsp;Male</a></td>
                                            <td class="text-xs text-gray-400">750</td>
                                        </tr>
                                        <tr data-index="0">
                                            <td scope="row" class="text-xs text-gray-400"><a
                                                    href="#">&nbsp;&nbsp;&nbsp;Female</a></td>
                                            <td class="text-xs text-gray-400">750</td>
                                        </tr>
                                        <tr data-index="0">
                                            <td scope="row"><a href="#">Indigenous People</a></td>
                                            <td>1500</td>
                                        </tr>
                                        <tr data-index="0">
                                            <td scope="row" class="text-xs text-gray-400"><a
                                                    href="#">&nbsp;&nbsp;&nbsp;Male</a></td>
                                            <td class="text-xs text-gray-400">750</td>
                                        </tr>
                                        <tr data-index="0">
                                            <td scope="row" class="text-xs text-gray-400"><a
                                                    href="#">&nbsp;&nbsp;&nbsp;Female</a></td>
                                            <td class="text-xs text-gray-400">750</td>
                                        </tr>
                                        <tr data-index="0">
                                            <td scope="row"><a href="#">Child of Solo Parent</a></td>
                                            <td>1500</td>
                                        </tr>
                                        <tr data-index="0">
                                            <td scope="row" class="text-xs text-gray-400"><a
                                                    href="#">&nbsp;&nbsp;&nbsp;Male</a></td>
                                            <td class="text-xs text-gray-400">750</td>
                                        </tr>
                                        <tr data-index="0">
                                            <td scope="row" class="text-xs text-gray-400"><a
                                                    href="#">&nbsp;&nbsp;&nbsp;Female</a></td>
                                            <td class="text-xs text-gray-400">750</td>
                                        </tr>
                                        <tr data-index="0">
                                            <td scope="row"><a href="#">Lactose Intolerant</a></td>
                                            <td>1500</td>
                                        </tr>
                                        <tr data-index="0">
                                            <td scope="row" class="text-xs text-gray-400"><a
                                                    href="#">&nbsp;&nbsp;&nbsp;Male</a></td>
                                            <td class="text-xs text-gray-400">750</td>
                                        </tr>
                                        <tr data-index="0">
                                            <td scope="row" class="text-xs text-gray-400"><a
                                                    href="#">&nbsp;&nbsp;&nbsp;Female</a></td>
                                            <td class="text-xs text-gray-400">750</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
        </section>
    </div>
</main><!-- End #main -->