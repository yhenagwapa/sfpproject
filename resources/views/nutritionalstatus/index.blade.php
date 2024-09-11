@extends('layouts.app')
@section('content')

    <main id="main" class="main">

        <div class="pagetitle">

            <nav style="--bs-breadcrumb-divider: '>';">
                <ol class="breadcrumb mb-3 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('child.index') }}">Children</a></li>
                    <li class="breadcrumb-item active">{{ $child->full_name }}</li>
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
                            <div class="card-body">
                                <div class="card-title">
                                    <h5 class='col-md-12'>Upon entry details</h5>
                                </div>

                                @canany(['nutrition-status-entry'])
                                    <form method="post" action="{{ route('nutritionalstatus.store') }}">
                                        @csrf
                                        <input type="hidden" name="child_id" value="{{ $child->id }}">
                                        <div class="col-md-12 mt-2 text-sm">
                                            <label for="weight">Weight</label><label for="weight"
                                                class="text-red-600">*</label>
                                            <input type="text"
                                                class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                                id="weight" name='weight' value="{{ old('weight') }}">
                                            @error('weight')
                                                <span class="text-xs text-red-600">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-12 mt-2 text-sm">
                                            <label for="height">Height</label><label for="height"
                                                class="text-red-600">*</label>
                                            <input type="text"
                                                class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                                id="height" name='height' value="{{ old('height') }}">
                                            @error('height')
                                                <span class="text-xs text-red-600">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-md-12 mt-2 text-sm">
                                            <label for="actual_date_of_weighing">Actual date of weighing</label><label
                                                for="actual_date_of_weighing" class="text-red-600">*</label>
                                            <input type="date"
                                                class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                                id="actual_date_of_weighing" name='actual_date_of_weighing'
                                                value="{{ old('actual_date_of_weighing') }}">
                                            @error('actual_date_of_weighing')
                                                <span class="text-xs text-red-600">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-md-12 mt-4 text-right">
                                            <button type="button" class="text-white bg-blue-600 rounded px-3 min-h-9"
                                                data-bs-toggle="modal" data-bs-target="#verticalycentered">Submit</button>
                                            <button type="reset"
                                                class="text-white bg-gray-600 rounded px-3 min-h-9">Cancel</button>
                                        </div>

                                        <div class="modal fade" id="verticalycentered" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title text-red-600">Confirmation</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Are you sure you want to save these details?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit"
                                                            class="text-white bg-blue-600 rounded px-3 min-h-9">Confirm</button>
                                                        <button type="button"
                                                            class="text-white bg-gray-600 rounded px-3 min-h-9"
                                                            data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                @endcanany
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h5 class='col-md-12'>After 120 Feedings</h5>
                                </div>

                                @canany(['nutrition-status-exit'])
                                    <form method="post" action="{{ route('nutritionalstatus.store') }}">
                                        @csrf
                                        <input type="hidden" name="child_id" value="{{ $child->id }}">
                                        <div class="col-md-12 mt-2 text-sm">
                                            <label for="weight">Weight</label><label for="weight"
                                                class="text-red-600">*</label>
                                            <input type="text"
                                                class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                                id="weight" name='weight' value="{{ old('weight') }}">
                                            @error('weight')
                                                <span class="text-xs text-red-600">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-12 mt-2 text-sm">
                                            <label for="height">Height</label><label for="height"
                                                class="text-red-600">*</label>
                                            <input type="text"
                                                class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                                id="height" name='height'value="{{ old('height') }}">
                                            @error('height')
                                                <span class="text-xs text-red-600">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-md-12 mt-2 text-sm">
                                            <label for="actual_date_of_weighing">Actual date of weighing</label><label
                                                for="actual_date_of_weighing" class="text-red-600">*</label>
                                            <input type="date"
                                                class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                                id="actual_date_of_weighing" name='actual_date_of_weighing'
                                                value="{{ old('actual_date_of_weighing') }}">
                                            @error('actual_date_of_weighing')
                                                <span class="text-xs text-red-600">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-md-12 mt-4 text-right">
                                            <button class="text-white bg-blue-600 rounded px-3 min-h-9" data-bs-toggle="modal"
                                                data-bs-target="#verticalycentered">Submit</button>
                                            <button type="reset"
                                                class="text-white bg-gray-600 rounded px-3 min-h-9">Cancel</button>
                                        </div>


                                        <div class="modal fade" id="verticalycentered" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title text-red-600">Confirmation</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Are you sure you want to save these details?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button"
                                                            class="text-white bg-blue-600 rounded px-3 min-h-9"
                                                            onclick="document.querySelector('form').submit();">Confirm</button>
                                                        <button type="button"
                                                            class="text-white bg-gray-600 rounded px-3 min-h-9"
                                                            data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                @endcanany
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-10">
                        <div class="card">
                            <div class="card-body">
                                
                                <h5 class="card-title">Nutritional Status</h5>
                                
                                <table class="table-auto mt-3 text-xs text-center w-full">
                                    <thead class="bg-gray-200">
                                        <tr>
                                            <th class="border border-white" rowspan="2">Actual Date of Weighing</th>
                                            <th class="border border-white p-2" rowspan="2">Weight</th>
                                            <th class="border border-white p-2" rowspan="2">Height</th>
                                            <th class="border border-white" colspan="2">Age in month/year</th>
                                            <th class="border border-white" colspan="3">NS UPON ENTRY</th>
                                            <th class="border border-white" rowspan="2">Actual Date of Weighing</th>
                                            <th class="border border-white p-2" rowspan="2">Weight</th>
                                            <th class="border border-white p-2" rowspan="2">Height</th>
                                            <th class="border border-white" colspan="2" vx>Age in month/year</th>
                                            <th class="border border-white" colspan="3">NS AFTER 120 FEEDINGS</th>
                                        </tr>
                                        <tr>
                                             
                                            <th class="border border-white">Month</th>
                                            <th class="border border-white">Year</th>
                                            <th class="border border-white">Weight for Age</th>
                                            <th class="border border-white">Weight for Height</th>
                                            <th class="border border-white">Height for Age</th>
                                            <th class="border border-white">Month</th>
                                            <th class="border border-white">Year</th>
                                            <th class="border border-white">Weight for Age</th>
                                            <th class="border border-white">Weight for Height</th>
                                            <th class="border border-white">Height for Age</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-sm">
                                        @foreach($results as $result)
                                            <tr>
                                                <td class="border border-gray-300 px-4 py-2">{{ $result->entry_date }}</td>
                                                <td class="border border-gray-300 px-4 py-2">{{ $result->entry_weight }}</td>
                                                <td class="border border-gray-300 px-4 py-2">{{ $result->entry_height }}</td>
                                                <td class="border border-gray-300 px-4 py-2">{{ $ageInMonths }}</td>
                                                <td class="border border-gray-300 px-4 py-2">{{ $ageInYears }}</td>
                                                <td class="border border-gray-300 px-4 py-2">{{ $result->entry_weight_for_age }}</td>
                                                <td class="border border-gray-300 px-4 py-2">{{ $result->entry_weight_for_height }}</td>
                                                <td class="border border-gray-300 px-4 py-2">{{ $result->entry_height_for_age }}</td>
                                                <td class="border border-gray-300 px-4 py-2">{{ $result->exit_date }}</td>
                                                <td class="border border-gray-300 px-4 py-2">{{ $result->exit_weight }}</td>
                                                <td class="border border-gray-300 px-4 py-2">{{ $result->exit_height }}</td>
                                                <td class="border border-gray-300 px-4 py-2">{{ $ageInMonths }}</td>
                                                <td class="border border-gray-300 px-4 py-2">{{ $ageInYears }}</td>
                                                <td class="border border-gray-300 px-4 py-2">{{ $result->exit_weight_for_age }}</td>
                                                <td class="border border-gray-300 px-4 py-2">{{ $result->exit_weight_for_height }}</td>
                                                <td class="border border-gray-300 px-4 py-2">{{ $result->exit_height_for_age }}</td>
                                            </tr>
                                        
                                        @endforeach
                                        
                                    </tbody>
                                </table>


                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        @vite(['resources/js/app.js'])
    </main><!-- End #main -->
