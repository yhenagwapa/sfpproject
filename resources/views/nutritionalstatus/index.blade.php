@extends('layouts.app')

@section('content')

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


    <section class="section">
        <div class="row">
            <div class="col-lg-3">
                @if (!$hasUponEntryData)
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">
                                <h5 class='col-md-12'>Upon entry details</h5>
                            </div>

                            @can(['create-nutritional-status'])
                                <form method="post" action="{{ route('nutritionalstatus.storeUponEntryDetails') }}">
                                    @csrf
                                    <input type="hidden" name="child_id" value="{{ $child->id }}">
                                    <div class="col-md-12 mt-2 text-sm">
                                        <label for="weight">Weight<b class="text-red-600">*</b></label>
                                        <input type="text"
                                            class="form-control rounded border-gray-300"
                                            id="weight" name='weight' value="{{ old('weight') }}">
                                        @error('weight')
                                            <span class="text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-12 mt-2 text-sm">
                                        <label for="height">Height<b class="text-red-600">*</b></label>
                                        <input type="text"
                                            class="form-control rounded border-gray-300"
                                            id="height" name='height' value="{{ old('height') }}">
                                        @error('height')
                                            <span class="text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-12 mt-2 text-sm">
                                        <label for="weighing_date">Actual date of weighing<b class="text-red-600">*</b></label>
                                        <input type="date"
                                            class="form-control rounded border-gray-300"
                                            id="weighing_date" name='weighing_date' value="{{ old('weighing_date') }}" max="{{ date('Y-m-d') }}">
                                        @error('weighing_date')
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
                                                    <button type="button" class="text-white bg-gray-600 rounded px-3 min-h-9"
                                                        data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            @endcan
                        </div>
                    </div>
                @endif

                @if ($hasUponEntryData && !$hasUponExitData)
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">
                                <h5 class='col-md-12'>After 120 Feedings</h5>
                            </div>

                            @can(['create-nutritional-status'])
                                <form method="post" action="{{ route('nutritionalstatus.storeExitDetails') }}">
                                    @csrf

                                    <input type="hidden" name="child_id" value="{{ $child->id }}">
                                    <div class="col-md-12 mt-2 text-sm">
                                        <label for="weight">Weight<b class="text-red-600">*</b></label>
                                        <input type="text"
                                            class="form-control rounded border-gray-300"
                                            id="weight" name='weight' value="{{ old('weight') }}">
                                        @error('weight')
                                            <span class="text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-12 mt-2 text-sm">
                                        <label for="height">Height<b class="text-red-600">*</b></label>
                                        <input type="text"
                                            class="form-control rounded border-gray-300"
                                            id="height" name='height' value="{{ old('height') }}">
                                        @error('height')
                                            <span class="text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-12 mt-2 text-sm">
                                        <label for="weighing_date">Actual date of weighing<b
                                                class="text-red-600">*</b></label>
                                        <input type="date" class="form-control rounded border-gray-300" id="weighing_date" name='weighing_date' value="{{ old('weighing_date') }}" max="{{ date('Y-m-d') }}">
                                        @error('weighing_date')
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
                                                    <button type="button" class="text-white bg-gray-600 rounded px-3 min-h-9"
                                                        data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            @endcan
                        </div>
                    </div>
                @endif
            </div>
            <div class="{{ $hasUponExitData ? 'col-lg-12' : 'col-lg-9' }}">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="text-transform: uppercase;">{{ $child->full_name }} <span>| Date of Birth: {{ $child->date_of_birth }} | {{ $child->sex->name }}</span></h5>

                        <div class='table-responsive'>
                            @include('nutritionalstatus.partials.ns-table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @vite(['resources/js/app.js'])
@endsection
