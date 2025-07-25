@extends('layouts.app')

@section('content')
    <div class="pagetitle">

        <nav style="--bs-breadcrumb-divider: '>';">
            <ol class="breadcrumb mb-3 p-0">
                <li class="breadcrumb-item"><a href="{{ route('centers.index') }}">Child Development Centers</a></li>
                <li class="breadcrumb-item active">Child Development Centers Details</li>
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

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Child Development Center Details</h5>
                        <form class="row" method="post" action="{{ route('centers.store') }} ">
                            @csrf

                            <div class='col-md-12 mt-2 text-gray-400 text-xs'>Child Development Center Information
                                <hr>
                            </div>

                            <div class="col-md-6 mt-3 text-sm">
                                <label for="center_name">Center Name<b class="text-red-600">*</b></label>
                                <input type="text" class="form-control rounded border-gray-300" id="center_name"
                                    name="center_name" value="{{ old('center_name') }}" autofocus>

                                @error('center_name')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mt-3 text-sm">
                                <label for="center_type">Center Type<b
                                        class='text-red-600'>*</b></label>
                                <select class="form-control rounded border-gray-300 uppercase" id="center_type"
                                    name="center_type">
                                    <option value="" selected>Select Center Type</option>
                                    <option value="CDC" {{ old('center_type') === 'CDC' ? 'selected' : ''}}>Child Development Center</option>
                                    <option value="SNP" {{ old('center_type') === 'SNP' ? 'selected' : ''}}>Supervised Neighborhood Play</option>
                                </select>
                                @error('center_type')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mt-3 text-sm">
                                <label for="assigned_worker_user_id">Assigned Child Development Worker<b
                                        class='text-red-600'>*</b></label>
                                <select class="form-control rounded border-gray-300 uppercase" id="assigned_worker_user_id"
                                    name="assigned_worker_user_id">
                                    <option value="" selected>Select Worker</option>
                                    @foreach ($workers as $worker)
                                        <option value="{{ $worker->id }}"
                                            {{ old('assigned_worker_user_id') == $worker->id ? 'selected' : '' }}>
                                            {{ $worker->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_worker_user_id')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mt-3 text-sm">
                                <label for="assigned_focal_user_id">Assigned LGU Focal<b class='text-red-600'>*</b></label>
                                <select class="form-control rounded border-gray-300 uppercase" id="assigned_focal_user_id"
                                    name="assigned_focal_user_id">
                                    <option value="" selected>Select LGU Focal</option>
                                    @foreach ($focals as $focal)
                                        <option value="{{ $focal->id }}"
                                            {{ old('assigned_focal_user_id', auth()->user()->id) == $focal->id ? 'selected' : '' }}>
                                            {{ $focal->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('assigned_focal_user_id'))
                                    <span class="text-xs text-red-600">{{ $errors->first('assigned_focal_user_id') }}</span>
                                @endif
                            </div>

                            <div class="col-md-6 mt-3 text-sm">
                                <label for="assigned_coordinator_user_id">Assigned SFP Coordinator</label>
                                <select class="form-control rounded border-gray-300 uppercase" id="assigned_coordinator_user_id"
                                    name="assigned_coordinator_user_id">
                                    <option value="" selected>Select SFP Coordinator</option>
                                    @foreach ($coordinators as $coordinator)
                                        <option value="{{ $coordinator->id }}"
                                            {{ old('assigned_coordinator_user_id', auth()->user()->id) == $coordinator->id ? 'selected' : '' }}>
                                            {{ $coordinator->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mt-3 text-sm">
                                <label for="assigned_encoder_user_id">Assigned Encoder</label>
                                <select class="form-control rounded border-gray-300 uppercase" id="assigned_encoder_user_id"
                                    name="assigned_encoder_user_id">
                                    <option value="" selected>Select Encoder</option>
                                    @foreach ($encoders as $encoder)
                                        <option value="{{ $encoder->id }}"
                                            {{ old('assigned_encoder_user_id') == $encoder->id ? 'selected' : '' }}>
                                            {{ $encoder->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_encoder_user_id')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class='col-md-12 mt-4 text-gray-400 text-xs'>Address
                                <hr>
                            </div>

                            <div class="col-md-6 mt-3 text-sm">
                                <label for="region">Region<b class="text-red-600">*</b></label>
                                <select class="form-control rounded border-gray-300" id="region">
                                    <option value="110000000" selected>Region XI</option>
                                </select>
                            </div>

                            <input type="hidden" name="region_psgc" value="110000000">

                            <div class="col-md-6 mt-3 text-sm">
                                <label for="province">Province<b class="text-red-600">*</b></label>
                                <select class="form-control rounded border-gray-300" id="province" name="province_psgc">
                                    <option value="" selected>Select Province</option>
                                    @foreach ($provinces as $psgc => $name)
                                        <option value="{{ $psgc }}"
                                            {{ old('province_psgc') == $psgc ? 'selected' : '' }}>
                                            {{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('province_psgc')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mt-2 text-sm">
                                <label for="city">City/Municipality<b class="text-red-600">*</b></label>
                                <select class="form-control rounded border-gray-300" id="city" name="city_name_psgc">
                                    <option value="" selected>Select City/Municipality</option>
                                    @foreach ($cities as $psgc => $name)
                                        <option value="{{ $psgc }}"
                                            {{ old('city_name_psgc') == $psgc ? 'selected' : '' }}>
                                            {{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('city_name_psgc')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mt-2 text-sm">
                                <label for="barangay">Barangay<b class="text-red-600">*</b></label>
                                <select class="form-control rounded border-gray-300" id="barangay" name="brgy_psgc">
                                    <option value="" selected>Select Barangay</option>
                                    @foreach ($barangays as $psgc => $name)
                                        <option value="{{ $psgc }}"
                                            {{ old('brgy_psgc') == $psgc ? 'selected' : '' }}>
                                            {{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('brgy_psgc')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>


                            <div class="col-12 mt-2 text-sm">
                                <label for="address">Street/ Purok<b class="text-red-600">*</b></label>
                                <input type="text" class="form-control rounded border-gray-300" id="address"
                                    name='address' value="{{ old('address') }}">
                                @error('address')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- <div class='col-md-1 mt-4 text-gray-400 text-xs'>Areas</div>
                                    <div class='col-md-11 mt-8 text-gray-400 text-xs'>
                                        <hr>
                                    </div> --}}

                            {{-- <div class="col-md-12 mt-3 mb-0 text-sm">
                                        <label for="cdcname" class="mr-2">Area/s</label>
                                    </div>
                                    <div class="col-md-12" id="input-container">
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control rounded border-gray-300 rounded border-gray-300 mr-3"`
                                                name="cdcname[]" value="{{ old('cdcname') }}" autofocus>
                                            <button type="button"
                                                class="text-white bg-blue-600 rounded text-sm text-nowrap px-4 min-h-9 add-more">
                                                Add More
                                            </button>
                                        </div>
                                    </div> --}}



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
                        </form><!-- End floating Labels Form -->
                        <div class="col-md-12 flex mt-4 justify-end text-right">
                            <button type="button" class="text-white bg-blue-600 rounded px-3 mr-1 min-h-9"
                                data-bs-toggle="modal" data-bs-target="#verticalycentered">Save</button>
                            <form id="cancel-form" method="GET" action="{{ route('centers.index') }}">
                            </form>
                            <button type="button" class="text-white bg-gray-600 rounded px-3 min-h-9" onclick="submitCancelForm()">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    </section>

    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get dropdown elements
            const provinceSelect = document.getElementById('province');
            const citySelect = document.getElementById('city');
            const barangaySelect = document.getElementById('barangay');

            // Convert PHP arrays to JSON for JavaScript use
            const locations = {
                provinces: @json($provinces),
                cities: {!! json_encode($cities, JSON_UNESCAPED_UNICODE) !!},
                barangays: {!! json_encode($barangays, JSON_UNESCAPED_UNICODE) !!}
            };

            // Get old selected values (from Laravel session for form validation)
            let selectedProvince = @json(old('province_psgc', session('step1Data.province_psgc')));
            let selectedCity = @json(old('city_name_psgc', session('step1Data.city_name_psgc')));
            let selectedBarangay = @json(old('brgy_psgc', session('step1Data.brgy_psgc')));

            // Disable city and barangay initially
            citySelect.disabled = true;
            barangaySelect.disabled = true;

            // Function to reset and populate cities when province changes
            function populateCities(provincePsgc) {
                citySelect.innerHTML = '<option value="">Select City/Municipality</option>';
                barangaySelect.innerHTML = '<option value="">Select Barangay</option>'; // Reset barangay

                citySelect.disabled = !provincePsgc; // Enable only if a province is selected
                barangaySelect.disabled = true; // Always reset barangay when province changes

                if (provincePsgc && locations.cities[provincePsgc]) {
                    locations.cities[provincePsgc].forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.psgc;
                        option.text = city.name;
                        citySelect.appendChild(option);
                    });

                    // Restore selected city after validation error
                    if (selectedCity && locations.cities[provincePsgc].some(city => city.psgc == selectedCity)) {
                        citySelect.value = selectedCity;
                        populateBarangays(selectedCity); // Load barangays for selected city
                    } else {
                        selectedCity = ''; // Reset if no match
                    }
                }
            }

            // Function to reset and populate barangays when city changes
            function populateBarangays(cityPsgc) {
                barangaySelect.innerHTML = '<option value="">Select Barangay</option>'; // Always clear barangay

                barangaySelect.disabled = !cityPsgc; // Enable only if a city is selected

                if (cityPsgc && locations.barangays[cityPsgc]) {
                    locations.barangays[cityPsgc].forEach(barangay => {
                        const option = document.createElement('option');
                        option.value = barangay.psgc;
                        option.text = barangay.name;
                        barangaySelect.appendChild(option);
                    });

                    // Restore selected barangay after validation error
                    if (selectedBarangay && locations.barangays[cityPsgc].some(barangay => barangay.psgc ==
                            selectedBarangay)) {
                        barangaySelect.value = selectedBarangay;
                    } else {
                        selectedBarangay = ''; // Reset if no match
                    }
                }
            }

            // Load old selections if they exist (for validation errors)
            if (selectedProvince) {
                provinceSelect.value = selectedProvince;
                populateCities(selectedProvince);
            }

            // Event Listeners
            provinceSelect.addEventListener('change', function() {
                selectedProvince = this.value;
                selectedCity = ''; // Reset city when province changes
                selectedBarangay = ''; // Reset barangay when province changes
                populateCities(this.value);
            });

            citySelect.addEventListener('change', function() {
                selectedCity = this.value;
                selectedBarangay = ''; // Reset barangay when city changes
                populateBarangays(this.value);
            });
        });
    </script>
@endsection
