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
                    }, 5000);
                }
            });
        });
    </script>
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Implementation</h5>
                            <form class="row" method="post" action="{{ route('cycle.update') }}">
                                @csrf
                                @method('patch')

                                <input type="hidden" name="cycle_id" value="{{ $cycle->id }}">

                                <!-- Cycle Information -->
                                <div class='col-md-12 text-gray-400 text-xs'>
                                    Implementation Information
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

                                    @php
                                        $startYear = $cycle->school_year_from + 1;
                                        $endYear = $cycle->school_year_from + 2;
                                        $endYearForSYTo = $cycle->school_year_from + 3;
                                    @endphp

                                    <div class="col-md-3 mt-3 text-sm">
                                        <label for="cycle_school_year_from">School Year From<b class="text-red-600">*</b></label>
                                        <select name="cycle_school_year_from" id="cycle_school_year_from" class="form-control rounded border-gray-300">
                                            @for ($year = $cycle->school_year_from; $year <= $endYear; $year++)
                                                <option value="{{ $year }}" {{ old('cycle_school_year_from', $cycle->school_year_from) == $year ? 'selected' : '' }}>
                                                    {{ $year }}
                                                </option>
                                            @endfor
                                        </select>
                                        @if ($errors->has('cycle_school_year_from'))
                                            <span class="text-xs text-red-600">{{ $errors->first('cycle_school_year_from') }}</span>
                                        @endif
                                    </div>

                                    <div class="col-md-3 mt-3 text-sm">
                                        <label for="cycle_school_year_to">School Year To<b class="text-red-600">*</b></label>
                                            <select name="cycle_school_year_to" id="cycle_school_year_to" class="form-control rounded border-gray-300">
                                                @for ($year = $startYear; $year <= $endYearForSYTo; $year++)
                                                    <option value="{{ $year }}" {{ old('cycle_school_year_to', $cycle->school_year_to) == $year ? 'selected' : '' }}>
                                                        {{ $year }}
                                                    </option>
                                                @endfor
                                            </select>
                                        @if ($errors->has('cycle_school_year_to'))
                                            <span class="text-xs text-red-600">{{ $errors->first('cycle_school_year_to') }}</span>
                                        @endif
                                    </div>

                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="cycle_target">Target<b class="text-red-600">*</b></label>
                                        <input type="text" class="form-control rounded border-gray-300" id="cycle_target"
                                               name="cycle_target" value="{{ (old('cycle_target', $cycle->target)) }}" maxlength="12">
                                        @error('cycle_target')
                                            <span class="text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="cycle_allocation">Allocation<b class="text-red-600">*</b></label>
                                        <input type="text" class="form-control rounded border-gray-300" id="cycle_allocation"
                                               name="cycle_allocation" value="{{ (old('cycle_allocation',  $cycle->allocation)) }}">
                                        @error('cycle_allocation')
                                            <span class="text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="cycle_type">Type<b class="text-red-600">*</b></label>
                                        <select class="form-control rounded border-gray-300" id="cycle_type" name="cycle_type">
                                            <option value="" selected disabled>Select type</option>
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
                                               name="cycle_status" value="{{ old('cycle_status', $cycle->status) }}" readonly>

                                    </div>
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

                            <!-- Submit and Cancel Buttons -->
                            <div class="col-md-12 flex mt-4 justify-end text-right">
                                <button type="button" class="text-white bg-blue-600 rounded px-3 mr-1 min-h-9" data-bs-toggle="modal" data-bs-target="#confirmationModal">Save Changes</button>
                                <form id="cancel-form" method="GET" action="{{ route('cycle.index') }}">
                                </form>
                                <button type="button" class="text-white bg-gray-600 rounded px-3 min-h-9" onclick="submitCancelForm()">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script>
            const startYearSelect = document.getElementById('cycle_school_year_from');
            const endYearSelect = document.getElementById('cycle_school_year_to');

            function updateEndYearOptions() {
                const startYear = parseInt(startYearSelect.value);
                const nextYear = startYear + 1;

                Array.from(endYearSelect.options).forEach(option => {
                    const year = parseInt(option.value);
                    option.disabled = year !== nextYear;
                });

                // Set end year to next year
                endYearSelect.value = nextYear;
            }

            startYearSelect.addEventListener('change', updateEndYearOptions);
            window.addEventListener('DOMContentLoaded', updateEndYearOptions);
        </script>

@endsection
