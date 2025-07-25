@extends('layouts.app')

@section('content')

    <div class="pagetitle">
        <nav style="--bs-breadcrumb-divider: '>';">
            <ol class="breadcrumb mb-3 p-0">
                <li class="breadcrumb-item"><a href="{{ route('child.index') }}">Children</a></li>
                <li class="breadcrumb-item active uppercase">{{ $child->full_name }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    @if (session('error'))
        <div class="alert alert-danger alert-primary alert-dismissible fade show" id="danger-alert" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->has('ageError'))
        <div class="alert alert-danger alert-primary alert-dismissible fade show" id="danger-alert" role="alert">
            {{ $errors->first('ageError') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" id="warning-alert" role="alert">
            {{ session('warning') }}
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
            var alert3 = document.getElementById('warning-alert');
            if (alert1) {
                setTimeout(function() {
                    var bsAlert1 = new bootstrap.Alert(alert1);
                    bsAlert1.close();
                }, 3000);
            }
            if (alert2) {
                setTimeout(function() {
                    var bsAlert2 = new bootstrap.Alert(alert2);
                    bsAlert2.close();
                }, 3000);
            }
            if (alert3) {
                setTimeout(function() {
                    var bsAlert3 = new bootstrap.Alert(alert3);
                    bsAlert3.close();
                }, 3000);
            }
        });
    </script>

    <section class="section">
        <div class="row">
            <div class="col-lg-3">
                @can(['create-nutritional-status'])
                    @if (!$hasUponEntryData)
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h5 class='col-md-12'>Upon entry details</h5>
                                </div>
                                <form method="post" action="{{ route('nutritionalstatus.storeUponEntryDetails') }}" novalidate>
                                    @csrf

                                    <input type="hidden" name="form_type" value="entry">
                                    <input type="hidden" name="child_id" value="{{ $child->id }}">
                                    <input type="hidden" name="implementation_id" value="{{ $implementation->id }}">

                                    <div class="col-md-12 mt-2 text-sm">
                                        <label for="deworming_date">Deworming Date:<b class="text-red-600">*</b></label>
                                        <input type="date" class="form-control rounded border-gray-300 date-field" id="deworming_date"
                                            name='deworming_date' value="{{ old('deworming_date') }}" min="{{ $child->date_of_birth->addDay()->format('Y-m-d') }}" max="{{ date('Y-m-d') }}"
                                            >
                                        @if ($errors->has('deworming_date'))
                                            <span class="text-xs text-red-600">{{ $errors->first('deworming_date') }}</span>
                                        @endif
                                        @error('deworming_date')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                                    </div>
                                    <div class="col-md-12 mt-2 text-sm">
                                        <label for="vitamin_a_date">Vitamin A Date:<b class="text-red-600">*</b></label>
                                        <input type="date" class="form-control rounded border-gray-300 date-field" id="vitamin_a_date"
                                            name='vitamin_a_date' value="{{ old('vitamin_a_date') }}" min="{{ $child->date_of_birth->addDay()->format('Y-m-d') }}" max="{{ date('Y-m-d') }}"
                                            >
                                        @if ($errors->has('vitamin_a_date'))
                                            <span class="text-xs text-red-600">{{ $errors->first('vitamin_a_date') }}</span>
                                        @endif
                                    </div>
                                    <div class="col-md-12 mt-2 text-sm">
                                        <label for="weight">Weight (kg)<b class="text-red-600">*</b></label>
                                        <input type="number" class="form-control rounded border-gray-300" id="weight"
                                            name='weight' value="{{ old('weight') }}" step="0.1">
                                        @if ($errors->has('weight'))
                                            <span class="text-xs text-red-600">{{ $errors->first('weight') }}</span>
                                        @endif
                                    </div>
                                    <div class="col-md-12 mt-2 text-sm">
                                        <label for="height">Height (cm)<b class="text-red-600">*</b></label>
                                        <input type="number" class="form-control rounded border-gray-300" id="height"
                                            name='height' value="{{ old('height') }}" step="0.1">
                                        @if ($errors->has('height'))
                                            <span class="text-xs text-red-600">{{ $errors->first('height') }}</span>
                                        @endif
                                    </div>

                                    <div class="col-md-12 mt-2 text-sm">
                                        <label for="actual_weighing_date">Actual date of weighing<b
                                                class="text-red-600">*</b></label>
                                        <input type="date" class="form-control rounded border-gray-300 date-field"
                                            id="actual_weighing_date" name='actual_weighing_date'
                                            value="{{ old('actual_weighing_date') }}" min="{{ $minDate }}" max="{{ date('Y-m-d') }}">
                                        @if ($errors->has('actual_weighing_date'))
                                            <span
                                                class="text-xs text-red-600">{{ $errors->first('actual_weighing_date') }}</span>
                                        @endif
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
                                                        data-bs-dismiss="modal">Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <div class="col-md-12 flex mt-4 justify-end text-right">
                                    <button type="button" class="text-white bg-blue-600 rounded px-3 mr-1 min-h-9"
                                        data-bs-toggle="modal" data-bs-target="#verticalycentered">Save</button>

                                    <form id="cancel-form" method="GET" action="{{ route('child.index') }}">
                                    </form>

                                    <button type="button" class="text-white bg-gray-600 rounded px-3 min-h-9" onclick="submitCancelForm()">
                                        Cancel
                                    </button>
                                </div>

                            </div>
                        </div>
                    @endif
                @endcan

                @can(abilities: ['create-nutritional-status'])
                    @php
                        $minDateExit = \Carbon\Carbon::create($entryWeighingDate)->addDays(60)->format('Y-m-d');
                        $today = \Carbon\Carbon::today()->format('Y-m-d');
                    @endphp

                    @if ($today === $minDateExit)
                        @if ($hasUponEntryData && !$hasUponExitData)
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-title">
                                        <h5 class='col-md-12'>After 120 Feedings</h5>
                                    </div>
                                    <form method="post" action="{{ route('nutritionalstatus.storeExitDetails') }}" id="statusAfter120Form" novalidate>
                                        @csrf

                                        @method('post')
                                        <input type="hidden" name="form_type" value="exit">
                                        <input type="hidden" name="exitchild_id" value="{{ $child->id }}">
                                        <input type="hidden" name="entryWeighing" value="{{ $entryDetails->actual_weighing_date }}">
                                        <input type="hidden" name="implementation_id" value="{{ $implementation->implementation_id }}">

                                        <div class="col-md-12 mt-2 text-sm">
                                            <label for="exitweight">Weight (kg)<b class="text-red-600">*</b></label>
                                            <input type="number" class="form-control rounded border-gray-300" id="exitweight"
                                                name='exitweight' value="{{ old('exitweight') }}" step="0.1">
                                            @if ($errors->has('exitweight'))
                                                <span class="text-xs text-red-600">{{ $errors->first('weight') }}</span>
                                            @endif

                                        </div>
                                        <div class="col-md-12 mt-2 text-sm">
                                            <label for="exitheight">Height (cm)<b class="text-red-600">*</b></label>
                                            <input type="number" class="form-control rounded border-gray-300" id="exitheight"
                                                name='exitheight' value="{{ old('exitheight') }}" step="0.1">
                                            @if ($errors->has('exitheight'))
                                                <span class="text-xs text-red-600">{{ $errors->first('exitheight') }}</span>
                                            @endif
                                        </div>

                                        <div class="col-md-12 mt-2 text-sm">
                                            <label for="exitweighing_date">Actual date of weighing<b
                                                    class="text-red-600">*</b></label>
                                            <input type="date" class="form-control rounded border-gray-300 date-field"
                                                id="exitweighing_date" name='exitweighing_date'
                                                value="{{ old('exitweighing_date') }}" min="{{ $minDateExit }}" max="{{ date('Y-m-d') }}">
                                            @if ($errors->has('exitweighing_date'))
                                                <span
                                                    class="text-xs text-red-600">{{ $errors->first('exitweighing_date') }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="modal fade" id="verticalycentered1" tabindex="-1">
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
                                                        <button type="submit" id="statusAfter120Submit"
                                                            class="text-white bg-blue-600 rounded px-3 min-h-9">Confirm</button>
                                                        <button type="button" class="text-white bg-gray-600 rounded px-3 min-h-9"
                                                            data-bs-dismiss="modal">Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                    <div class="col-md-12 flex mt-4 justify-end text-right">
                                        <button type="button" class="text-white bg-blue-600 rounded px-3 mr-1 min-h-9"
                                            data-bs-toggle="modal" data-bs-target="#verticalycentered1">Save</button>

                                        <form id="cancel-form" method="GET" action="{{ route('child.index') }}">
                                        </form>

                                        <button type="button" class="text-white bg-gray-600 rounded px-3 min-h-9" onclick="submitCancelForm()">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                @endcan
            </div>

            <div class="@if (auth()->user()->hasRole('admin'))
                    col-lg-12
                @elseif ( $today === $minDateExit || !$hasUponEntryData)
                    col-lg-9
                @elseif ( $today === $minDateExit && !$hasUponExitData)
                    col-lg-9
                @else
                    col-lg-12
                @endif">
                <div class="card">
                    <div class="card-body">
                            <div class="col-md-12 flex text-right">
                                <a href={{ route('child.index') }} class="flex italic" style="text-decoration: none;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#1e9730" class="mr-1 mt-1 size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                                    </svg>
                                    <span class="text-green-600">
                                        Back
                                    </span>
                                </a>
                            </div>

                        <h5 class="card-title" style="text-transform: uppercase;">{{ $child->full_name }} <span>| Date of
                                Birth: {{ $child->date_of_birth->format('Y-m-d') }} | {{ $child->sex->name }}</span></h5>
                        <div class="col-md-6">

                        </div>
                        <div class='table-responsive'>
                            @include('nutritionalstatus.partials.ns-table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    {{-- @vite(['resources/js/app.js']) --}}
    <script>
        document.getElementById('statusAfter120Submit').addEventListener('click', function () {
            document.getElementById('statusAfter120Form').submit();
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deworming = document.getElementById('deworming_date');
            const vitamin_a= document.getElementById('vitamin_a_date');
            const weighing_date = document.getElementById('actual_weighing_date');

            if(deworming.value != null && vitamin_a.value != null){
                weighing_date.min =
            }
        });
    </script>
@endsection
