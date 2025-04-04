@extends('layouts.app')

@section('content')

    <!-- Page Title -->
    <div class="pagetitle">
        <nav style="--bs-breadcrumb-divider: '>';">
            <ol class="breadcrumb mb-3 p-0">
                <li class="breadcrumb-item"><a href="{{ route('cycle.index') }}">Implementations</a></li>
                <li class="breadcrumb-item active" style="text-transform: uppercase;">{{ $cycle->name }}</li>
            </ol>
        </nav>
    </div>

    <!-- Alerts -->
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" id="danger-alert" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" id="success-alert" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            ['success-alert', 'danger-alert'].forEach(id => {
                const alert = document.getElementById(id);
                if (alert) {
                    setTimeout(() => {
                        new bootstrap.Alert(alert).close();
                    }, 2000);
                }
            });
        });
    </script>



    <!-- Cycle Implementation Form -->

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Implementation</h5>
                            <form class="row" method="post" action="{{ route('cycle.update', $cycle->id) }}">
                                @csrf
                                @method('put')

                                <!-- Cycle Information -->
                                <div class='col-md-3 mt-2 text-gray-400 text-xs'>Implementation Information
                                </div>
                                <div class='col-md-9 mt-3 text-gray-400 text-xs'>
                                    <hr>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="cycle_name">Implementation Name<b class="text-red-600">*</b></label>
                                        <input type="text" class="form-control rounded border-gray-300" id="cycle_name"
                                               name="cycle_name" value="{{ old('cycle_name', $cycle->name) }}" style="text-transform: uppercase;" autofocus>
                                        @error('cycle_name')
                                            <span class="text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="cycle_school_year">School Year<b class="text-red-600">*</b></label>
                                        <input type="text" class="form-control rounded border-gray-300" id="cycle_school_year"
                                               name="cycle_school_year" value="{{ old('cycle_school_year', $cycle->school_year) }}" maxlength="9" style="text-transform: uppercase;">
                                        @error('cycle_school_year')
                                            <span class="text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="cycle_target">Target<b class="text-red-600">*</b></label>
                                        <input type="text" class="form-control rounded border-gray-300" id="cycle_target"
                                               name="cycle_target" value="{{ (old('cycle_target', number_format($cycle->target))) }}" maxlength="12">
                                        @error('cycle_target')
                                            <span class="text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="cycle_allocation">Allocation<b class="text-red-600">*</b></label>
                                        <input type="text" class="form-control rounded border-gray-300" id="cycle_allocation"
                                               name="cycle_allocation" value="{{ (old('cycle_allocation', number_format((float) $cycle->allocation), 2)) }}">
                                        @error('cycle_allocation')
                                            <span class="text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="cycle_type">Status<b class="text-red-600">*</b></label>
                                        <select class="form-control rounded border-gray-300" id="cycle_type" name="cycle_type">
                                            <option value="" selected disabled>Select status</option>
                                            @foreach ($cycleType as $type => $name)
                                                <option value="{{ $type }}" {{ old('cycle_type', $cycle->type) == $type ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('cycle_type')
                                            <span class="text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="cycle_status">Status<b class="text-red-600">*</b></label>
                                        <input type="text" class="form-control rounded border-gray-300" id="cycle_status"
                                               name="cycle_status" value="{{ old('cycle_status', $cycle->status) }}" disabled>

                                    </div>
                                </div>

                                <!-- Submit and Cancel Buttons -->
                                <div class="col-md-12 mt-4 text-right">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#confirmationModal">Submit</button>
                                    <button type="reset" class="btn btn-secondary">Cancel</button>
                                </div>

                                <!-- Confirmation Modal -->
                                <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="confirmationModalLabel">Confirmation</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to save these updates?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Confirm</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

@endsection
