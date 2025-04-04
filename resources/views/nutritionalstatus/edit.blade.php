@extends('layouts.app')

@section('content')

    <div class="pagetitle">
        <nav style="--bs-breadcrumb-divider: '>';">
            <ol class="breadcrumb mb-3 p-0">
                <li class="breadcrumb-item"><a href="{{ route('child.index') }}">Children</a></li>
                <li class="breadcrumb-item active" style="text-transform: uppercase;">{{ $child->full_name }}</li>
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

                setTimeout(function() {
                    var bsAlert1 = new bootstrap.Alert(alert1);
                    bsAlert1.close();
                }, 5000);
            }
            if (alert2) {

                setTimeout(function() {
                    var bsAlert2 = new bootstrap.Alert(alert2);
                    bsAlert2.close();
                }, 5000);
            }
        });
    </script>


    <section class="section">
        <div class="row">
            <div class="col-lg-3">
                @can(['edit-nutritional-status'])
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">
                                <h5 class='col-md-12'>Upon entry details</h5>
                            </div>
                            <form id="main-form" method="POST"
                                action="{{ route('nutritionalstatus.updateUponEntryDetails') }}">
                                @csrf
                                @method('PATCH')

                                <input type="hidden" name="form_type" value="entry">
                                <input type="hidden" name="child_id" value="{{ $child->id }}">

                                <div class="col-md-12 mt-2 text-sm">
                                    <label for="deworming_date">Deworming Date:<b class="text-red-600">*</b></label>
                                    <input type="date" class="form-control rounded border-gray-300" id="deworming_date"
                                        name='deworming_date' value="{{ old('deworming_date', $entryDetails->deworming_date) }}"
                                        min="{{ $child->date_of_birth->format('Y-m-d') }}" max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                    @if ($errors->has('deworming_date'))
                                        <span class="text-xs text-red-600">{{ $errors->first('deworming_date') }}</span>
                                    @endif
                                </div>
                                <div class="col-md-12 mt-2 text-sm">
                                    <label for="vitamin_a_date">Vitamin A Date:<b class="text-red-600">*</b></label>
                                    <input type="date" class="form-control rounded border-gray-300" id="vitamin_a_date"
                                        name='vitamin_a_date' value="{{ old('vitamin_a_date', $entryDetails->vitamin_a_date) }}"
                                        min="{{ $child->date_of_birth->format('Y-m-d') }}" max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                    @if ($errors->has('vitamin_a_date'))
                                        <span class="text-xs text-red-600">{{ $errors->first('vitamin_a_date') }}</span>
                                    @endif
                                </div>
                                <div class="col-md-12 mt-2 text-sm">
                                    <label for="weight">Weight<b class="text-red-600">*</b></label>
                                    <input type="text"
                                        class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                        id="weight" name='weight' value="{{ old('weight', $entryDetails->weight) }}">
                                    @if ($errors->has('weight'))
                                        <span class="text-xs text-red-600">{{ $errors->first('weight') }}</span>
                                    @endif
                                </div>
                                <div class="col-md-12 mt-2 text-sm">
                                    <label for="height">Height<b class="text-red-600">*</b></label>
                                    <input type="text"
                                        class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                        id="height" name='height' value="{{ old('height', $entryDetails->height) }}">
                                    @if ($errors->has('height'))
                                        <span class="text-xs text-red-600">{{ $errors->first('height') }}</span>
                                    @endif
                                </div>

                                <div class="col-md-12 mt-2 text-sm">
                                    <label for="actual_weighing_date">Actual date of weighing<b
                                            class="text-red-600">*</b></label>
                                    <input type="date"
                                        class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                        id="actual_weighing_date" name='actual_weighing_date'
                                        value="{{ old('actual_weighing_date', $entryDetails->actual_weighing_date) }}"
                                        min="{{ $child->date_of_birth->format('Y-m-d') }}" max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                    @if ($errors->has('actual_weighing_date'))
                                        <span class="text-xs text-red-600">{{ $errors->first('actual_weighing_date') }}</span>
                                    @endif
                                </div>

                                <div class="col-md-12 mt-4 text-right">
                                    <button type="button" class="text-white bg-blue-600 rounded px-3 min-h-9"
                                        data-bs-toggle="modal" data-bs-target="#verticalycentered">Save Changes</button>

                                    <form id="cancel-form" method="POST" action="{{ route('nutritionalstatus.index') }}">
                                        @csrf
                                        <input type="hidden" name="child_id" value="{{ $child->id }}">
                                    </form>

                                    <button type="button" class="text-white bg-gray-600 rounded px-3 min-h-9" onclick="submitCancelForm()">
                                        Cancel
                                    </button>
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
                                                Are you sure you want to save these changes?
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
                        </div>
                    </div>
                    @if($hasUponExitData)
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h5 class='col-md-12'>After 120-feeding details</h5>
                                </div>
                                <form method="POST"
                                    action="{{ route('nutritionalstatus.updateAfter120Details') }}">
                                    @csrf
                                    @method('PATCH')

                                    <input type="hidden" name="form_type" value="exit">
                                    <input type="hidden" name="exitchild_id" value="{{ $child->id }}">

                                    <div class="col-md-12 mt-2 text-sm">
                                        <label for="exitweight">Weight<b class="text-red-600">*</b></label>
                                        <input type="text"
                                            class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                            id="exitweight" name='exitweight' value="{{ old('exitweight', $exitDetails->weight) }}">
                                        @if ($errors->has('exitweight'))
                                            <span class="text-xs text-red-600">{{ $errors->first('exitweight') }}</span>
                                        @endif
                                    </div>
                                    <div class="col-md-12 mt-2 text-sm">
                                        <label for="exitheight">Height<b class="text-red-600">*</b></label>
                                        <input type="text"
                                            class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                            id="exitheight" name='exitheight' value="{{ old('exitheight', $exitDetails->height) }}">
                                        @if ($errors->has('exitheight'))
                                            <span class="text-xs text-red-600">{{ $errors->first('exitheight') }}</span>
                                        @endif
                                    </div>

                                    <div class="col-md-12 mt-2 text-sm">
                                        <label for="exitweighing_date">Actual date of weighing<b
                                                class="text-red-600">*</b></label>
                                        <input type="date"
                                            class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                            id="exitweighing_date" name='exitweighing_date'
                                            value="{{ old('exitweighing_date', $exitDetails->actual_weighing_date) }}"
                                            min="{{ $child->date_of_birth->format('Y-m-d') }}" max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                        @if ($errors->has('exitweighing_date'))
                                            <span class="text-xs text-red-600">{{ $errors->first('exitweighing_date') }}</span>
                                        @endif
                                    </div>

                                    <div class="col-md-12 mt-4 text-right">
                                        <button type="button" class="text-white bg-blue-600 rounded px-3 min-h-9"
                                            data-bs-toggle="modal" data-bs-target="#verticalycentered1">Save Changes</button>
                                        <form method="POST"
                                            action="{{ route('nutritionalstatus.index') }}">
                                            @csrf
                                            <button type="button" class="text-white bg-blue-600 rounded px-3 min-h-9"
                                            data-bs-toggle="modal" data-bs-target="#verticalycentered1">Cancel</button>
                                        </form>
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
                                                    Are you sure you want to save these changes in exit details?
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
                            </div>
                        </div>
                    @endif
                @endcan
            </div>
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="text-transform: uppercase;">{{ $child->full_name }}</h5>
                        <p class="text-sm">Date of Birth: {{ $child->date_of_birth }}</p>
                        <p class="text-sm" style="text-transform: uppercase;">Sex: {{ $child->sex->name }}</p>
                        <div class="table-responsive">
                            @include('nutritionalstatus.partials.ns-table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @vite(['resources/js/app.js'])

    <script>
        function submitCancelForm() {
        var cancelForm = document.getElementById('cancel-form');

        // Ensure the form exists before submitting
        if (cancelForm) {
            cancelForm.submit();
        } else {
            console.error('Form not found!');
        }
    }
    </script>
@endsection
