@extends('layouts.app')

@section('content')

        <div class="pagetitle">

            <nav style="--bs-breadcrumb-divider: '>';">
                <ol class="breadcrumb mb-3 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('cycle.index') }}">Implemetations</a></li>
                    <li class="breadcrumb-item active">Implementation Details</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

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

        @if (session('exists'))
            <!-- Trigger Modal on Load -->
            <script>
                window.addEventListener('DOMContentLoaded', function () {
                    const modal = new bootstrap.Modal(document.getElementById('confirmReplaceModal'));
                    modal.show();
                });
            </script>
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
                                <form id="cycleForm" class="row" method="post" action="{{ route('cycle.checkActiveStatus') }} ">
                                    @csrf

                                    <div class='col-md-12 mt-2 text-gray-400 text-xs'>
                                        Implementation Information
                                        <hr>
                                    </div>

                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="cycle_name">Implementation Name<b class="text-red-600">*</b></label>
                                        <input type="text" class="form-control rounded border-gray-300" id="cycle_name"
                                            name="cycle_name" value="{{ old('cycle_name') }}" style="text-transform: uppercase;" required autofocus>
                                        @if ($errors->has('cycle_name'))
                                            <span class="text-xs text-red-600">{{ $errors->first('cycle_name') }}</span>
                                        @endif
                                    </div>

                                    @php
                                        $currentYear = date('Y');
                                        $startYear = $currentYear + 1;
                                        $endYear = $currentYear + 2;
                                        $endYearForSYTo = $currentYear + 3;
                                    @endphp

                                    <div class="col-md-3 mt-3 text-sm">
                                        <label for="cycle_school_year_from">School Year From<b class="text-red-600">*</b></label>
                                        <select name="cycle_school_year_from" id="cycle_school_year_from" class="form-control rounded border-gray-300" required>
                                            @for ($year = $currentYear; $year <= $endYear; $year++)
                                                <option value="{{ $year }}" {{ old('cycle_school_year_from') == $year ? 'selected' : '' }}>
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
                                            <select name="cycle_school_year_to" id="cycle_school_year_to" class="form-control rounded border-gray-300" required>
                                                @for ($year = $startYear; $year <= $endYearForSYTo; $year++)
                                                    <option value="{{ $year }}" {{ old('cycle_school_year_to') == $year ? 'selected' : '' }}>
                                                        {{ $year }}
                                                    </option>
                                                @endfor
                                            </select>
                                        @if ($errors->has('cycle_school_year_to'))
                                            <span class="text-xs text-red-600">{{ $errors->first('cycle_school_year_to') }}</span>
                                        @endif
                                    </div>

                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="cycle_target">Target<b class='text-red-600'>*</b></label>
                                        <input type="number" class="form-control rounded border-gray-300" id="cycle_target"
                                            name="cycle_target" value="{{ old('cycle_target') }}" maxlength="12" required>
                                        @if ($errors->has('cycle_target'))
                                            <span class="text-xs text-red-600">{{ $errors->first('cycle_target') }}</span>
                                        @endif
                                    </div>

                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="cycle_allocation">Allocation<b class="text-red-600">*</b></label>
                                        <input type="number" class="form-control rounded border-gray-300"
                                            id="cycle_allocation" name='cycle_allocation'
                                            value="{{ old('cycle_allocation') }}" maxlength="12" required>
                                        @if ($errors->has('cycle_allocation'))
                                            <span class="text-xs text-red-600">{{ $errors->first('cycle_allocation') }}</span>
                                        @endif
                                    </div>

                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="cycle_type">Type<b class="text-red-600">*</b></label>
                                        <select type="text" class="form-control rounded border-gray-300" id="cycle_type"
                                            name='cycle_type' required>
                                            <option value="" selected disabled>Select Type</option>
                                                <option value="regular" {{ old('cycle_type') }}>REGULAR</option>
                                                <option value="milk" {{ old('cycle_type') }}>MILK</option>
                                        </select>
                                        @if ($errors->has('cycle_type'))
                                            <span class="text-xs text-red-600">{{ $errors->first('cycle_type') }}</span>
                                        @endif
                                    </div>

                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="cycle_status">Status<b class="text-red-600">*</b></label>
                                        <select type="text" class="form-control rounded border-gray-300" id="cycle_status"
                                            name='cycle_status' required>
                                            <option value="" selected disabled>Select status</option>
                                            <option value="active" {{ old('cycle_status') ? 'selected' : ''}}>Active</option>
                                            <option value="inactive" {{ old('cycle_status') ? 'selected' : ''}}>Inactive</option>
                                            <option value="closed" {{ old('cycle_status') ? 'selected' : ''}}>Closed</option>
                                        </select>
                                        @if ($errors->has('cycle_status'))
                                            <span class="text-xs text-red-600">{{ $errors->first('cycle_status') }}</span>
                                        @endif
                                    </div>
                                    <div class="modal fade" id="cycleConfirmationModal" tabindex="-1">
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
                                                        data-bs-dismiss="modal">Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form><!-- End floating Labels Form -->

                                <div class="col-md-12 flex mt-4 justify-end text-right">
                                    <button type="button" class="text-white bg-blue-600 rounded px-3 mr-1 min-h-9"
                                    onclick="checkFormBeforeModal()">Save</button>
                                    <form id="cancel-form" method="GET" action="{{ route('cycle.index') }}">
                                    </form>
                                    <button type="button" class="text-white bg-gray-600 rounded px-3 min-h-9" onclick="submitCancelForm()">
                                        Cancel
                                    </button>
                                </div>

                                <div class="modal fade" id="confirmReplaceModal" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title text-red-600">Confirmation</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                {{ session('message') }}
                                            </div>
                                            <div class="modal-footer">
                                                <form method="POST" action="{{ route('cycle.store') }}">
                                                    @csrf
                                                    <input type="hidden" name="cycle_name" value="{{ old('cycle_name') }}">
                                                    <input type="hidden" name="cycle_school_year_from" value="{{ old('cycle_school_year_from') }}">
                                                    <input type="hidden" name="cycle_school_year_to" value="{{ old('cycle_school_year_to') }}">
                                                    <input type="hidden" name="cycle_target" value="{{ old('cycle_target') }}">
                                                    <input type="hidden" name="cycle_allocation" value="{{ old('cycle_allocation') }}">
                                                    <input type="hidden" name="cycle_type" value="{{ old('cycle_type') }}">
                                                    <input type="hidden" name="cycle_status" value="{{ old('cycle_status') }}">
                                                    <input type="hidden" name="active_cycle_id" value="{{ session('active_cycle_id') }}">
                                                    <button type="submit" class="text-white bg-blue-600 rounded px-3 mr-1 min-h-9">Yes</button>
                                                </form>
                                                <button type="button" class="text-white bg-gray-600 rounded px-3 min-h-9" data-bs-dismiss="modal">
                                                    Cancel
                                                </button>
                                            </div>
                                        </form>
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

    @include('cycle.script')

@endsection
