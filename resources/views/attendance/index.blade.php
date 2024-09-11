@extends('layouts.app')

@section('title', 'SFP Onse')

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
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h5 class='col-md-6'>{{ $child->full_name }}</h5>
                                </div>
                                <div class="w-full">
                                    <form method="post" action="{{ route('attendance.store', $child->id) }}" class="flex flex-wrap items-center gap-4">
                                        @csrf
                                        <input type="hidden" name="child_id" value="{{ $child->id }}">
                                        
                                        <!-- Feeding Date -->
                                        <div class="flex items-center space-x-2 w-6/12">
                                            <label for="feeding_date" class="col-form-label">Feeding date:</label>
                                            <input type="date" class="form-control rounded border-gray-300"
                                                id="feeding_date" name='feeding_date' value="{{ old('feeding_date') }}">
                                            @error('feeding_date')
                                                <span class="text-xs text-red-600">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        
                                        <!-- With Milk Checkbox -->
                                        <div class="flex items-center space-x-2 w-4/12">
                                            <input class="form-check-input form-control" type="checkbox" id="with_milk"
                                                name='with_milk' value='1' {{ old('with_milk') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="with_milk">
                                                With milk
                                            </label>
                                        </div>
                                        
                                        <!-- Save Button -->
                                        <div>
                                            <button type="submit" class="btn btn-primary w-full">Save</button>
                                        </div>
                                    </form>
                                </div>
                                

                                <div>
                                    <hr>
                                </div>

                                <table class="table datatable mt-3">
                                    <thead>
                                        <tr>
                                            <th scope="col">Feeding No.</th>
                                            <th scope="col">Date</th>
                                            <th scope="col">With Milk</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($childAttendance as $attendance)
                                            <tr>
                                                <td>{{ $attendance->feeding_no }}</td>
                                                <td>{{ $attendance->date }}</td>
                                                <td><input type="checkbox" id="milk{{ $attendance->id }}"
                                                        value="{{ $attendance->with_milk }}"
                                                        {{ $attendance->with_milk ? 'checked' : '' }} disabled>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <div class="text-center">
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#verticalycentered3">After 120
                                        Feedings</a>
                                </div>

                                <div class="modal fade" id="verticalycentered3" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">After 120 Feedings</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <!-- After 120 feedins form/modal -->
                                                <form class="row g-3 mt-3" method="" action="">
                                                    <div class="row mt-3 mb-3">
                                                        <label for="endweight" class="col-form-label">Actual Date
                                                            of Weighing</label>
                                                        <div class="col-sm-10">
                                                            <input type="date" class="form-control"
                                                                id="actualdateof_endweight">
                                                        </div>
                                                        <div class="mt-3 col-sm-10">
                                                            <div class="form-floating">
                                                                <input class="form-control" type="text" id="endweight"
                                                                    placeholder="Weight" required>
                                                                <label for="endweight">Weight</label>
                                                            </div>
                                                        </div>
                                                        <div class="mt-3 col-sm-10">
                                                            <div class="form-floating">
                                                                <input class="form-control" type="text" id="endheight"
                                                                    placeholder="Height" required>
                                                                <label for="endheight">Height</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Close</button>
                                                        <button type="button" class="btn btn-primary">Save
                                                            changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        
