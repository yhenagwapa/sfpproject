@extends('layouts.app')

@section('content')


    <div class="pagetitle">

        <nav style="--bs-breadcrumb-divider: '>';">
            <ol class="breadcrumb mb-3 p-0">
                <li class="breadcrumb-item"><a href="{{ route('child.index') }}">{{ $child->full_name }}</a></li>
                <li class="breadcrumb-item active">Additional Info</li>
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
                }, 5000);
            }
            if (alert2) {
                // Automatically close the alert after 3 seconds (3000 milliseconds)
                setTimeout(function() {
                    var bsAlert2 = new bootstrap.Alert(alert2);
                    bsAlert2.close();
                }, 5000);
            }
        });
    </script>

    <div class="wrapper">
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $child->full_name }}</h5>
                            <form class="row" method="post" action="{{ route('child.store') }} ">
                                @csrf
                                <div class='col-md-3 mt-4 text-gray-400 text-xs'>Child Development Center or Supervised Neighborhood Play</div>
                                <div class='col-md-9 mt-8 text-gray-400 text-xs'>
                                    <hr>
                                </div>
                                <div class="col-md-6 mt-3 text-sm">
                                    <label for="child_development_center_id">CDC or SNP</label><b
                                        class="text-red-600">*</b>
                                        <select class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300" id="child_development_center_id" name='child_development_center_id'>
                                            <option value="" disabled selected>Select CDC or SNP</option>
                                            @foreach ($centerNames as $center)
                                                <option value="{{ $center->id }}"
                                                    {{ $center->id == old('id') ? 'selected' : '' }}>
                                                    {{ $center->center_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @error('child_development_center_id')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mt-2 text-sm" style="visibility: hidden">
                                    <input type="text"
                                        class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                        name="spaceonly">
                                </div>
                                <div class="col-md-6 mt-3 text-sm">
                                    <label for="cycle_implementation_id">Cycle Implementation</label>
                                    <select
                                        class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                        id="implementation_id" name='implementation_id' onchange="setFundingStatus()">
                                        @if ($cycles)
                                            <option value="" selected>Not Applicable</option>
                                            @foreach ($cycles as $cycle)
                                                <option value="{{ $cycle->id }}"
                                                    {{ $cycle->id == old('implementation_id') ? 'selected' : '' }}>
                                                    {{ $cycle->name }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="" disabled selected>No active cycle implementation</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-6 mt-3 text-sm">
                                    <label for="milk_feeding_id">Milk Feeding Implementation</label>
                                    <select
                                        class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                        id="milk_feeding_id" name='milk_feeding_id'>
                                        @if ($milkFeedings)
                                            <option value="" selected>Not Applicable</option>
                                            @foreach ($milkFeedings as $milkFeeding)
                                                <option value="{{ $milkFeeding->id }}"
                                                    {{ $milkFeeding->id == old('milk_feeding_id') ? 'selected' : '' }}>
                                                    {{ $milkFeeding->name }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="" disabled selected>No active cycle implementation</option>
                                        @endif
                                    </select>
                                </div>
                                <input type="hidden" id="is_funded" name="is_funded" value="">
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
                                                @if ($cycles)
                                                    Are you sure you want to save these details?
                                                @else
                                                    No active cycle implementation.
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                @if ($cycles)
                                                    <button type="submit" class="text-white bg-blue-600 rounded px-3 min-h-9">Confirm</button>
                                                @endif
                                                <button type="button" class="text-white bg-gray-600 rounded px-3 min-h-9" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form><!-- End floating Labels Form -->
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection
