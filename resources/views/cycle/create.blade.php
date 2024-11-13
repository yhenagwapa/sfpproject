@extends('layouts.app')

@section('content')

        <div class="pagetitle">

            <nav style="--bs-breadcrumb-divider: '>';">
                <ol class="breadcrumb mb-3 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('cycle.index') }}">Implemetations</a></li>
                    <li class="breadcrumb-item active">Cycle Implementation Details</li>
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
                                <h5 class="card-title">Cycle Implementation</h5>
                                <form class="row" method="post" action="{{ route('cycle.store') }} ">
                                    @csrf

                                    <div class='col-md-3 mt-2 text-gray-400 text-xs'>Cycle Implementation Information
                                    </div>
                                    <div class='col-md-9 mt-3 text-gray-400 text-xs'>
                                        <hr>
                                    </div>

                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="cycle_name">Cycle Name<b class="text-red-600">*</b></label>
                                        <input type="text" class="form-control rounded border-gray-300" id="cycle_name"
                                            name="cycle_name" value="{{ old('cycle_name') }}" style="text-transform: uppercase;" autofocus>

                                        @error('cycle_name')
                                            <span class="text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="cycle_school_year">School Year<b class="text-red-600">*</b></label>
                                        <input type="text" class="form-control rounded border-gray-300" id="cycle_school_year"
                                            name="cycle_school_year" value="{{ old('cycle_school_year') }}" maxlength="9">
                                        @error('cycle_school_year')
                                            <span class="text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="cycle_target">Target<b class='text-red-600'>*</b></label>
                                        <input type="text" class="form-control rounded border-gray-300" id="cycle_target"
                                            name="cycle_target" value="{{ old('cycle_target') }}" maxlength="12">
                                        @error('cycle_target')
                                            <span class="text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="cycle_allocation">Allocation<b class="text-red-600">*</b></label>
                                        <input type="text" class="form-control rounded border-gray-300"
                                            id="cycle_allocation" name='cycle_allocation'
                                            value="{{ old('cycle_allocation') }}">
                                        @error('cycle_allocation')
                                            <span class="text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mt-2 text-sm">
                                        <label for="cycle_status">Status<b class="text-red-600">*</b></label>
                                        <select type="text" class="form-control rounded border-gray-300" id="cycle_status"
                                            name='cycle_status'>
                                            <option value="" selected disabled>Select status</option>
                                            @foreach ($cycleStatuses as $cycleStatus)
                                                <option value="{{ $cycleStatus->value }}">{{ $cycleStatus->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('cycle_status')
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
                                                        class="text-white bg-blue-600 rounded px-3 min-h-9" onclick="submitForm()">Confirm</button>
                                                    <button type="button"
                                                        class="text-white bg-gray-600 rounded px-3 min-h-9"
                                                        data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            </form><!-- End floating Labels Form -->
                        </div>
                    </div>
                </div>
            </section>
        </div>
@endsection
