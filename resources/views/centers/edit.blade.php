@extends('layouts.app')

@section('content')
    <!-- Page Title -->
    <div class="pagetitle">

        <nav style="--bs-breadcrumb-divider: '>';">
            <ol class="breadcrumb mb-3 p-0">
                <li class="breadcrumb-item"><a href="{{ route('centers.index') }}">Child Development Centers</a></li>
                <li class="breadcrumb-item active uppercase">{{ $center->center_name }}</li>
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

    <!-- Center Details Form -->
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Child Development Center Details</h5>
                        <form class="row" method="POST" action="{{ route('centers.update') }}" id="centerUpdateForm">
                            @csrf
                            @method('post')

                            <input type="hidden" name="center_id" value="{{ $center->id }}">

                            <div class='col-md-12 mt-2 text-gray-400 text-xs'>Child Development Center Information
                                <hr>
                            </div>

                            <div class="col-md-12 mt-3 text-sm">
                                <label for="center_name">Center Name<b class="text-red-600">*</b></label>
                                <input type="text" class="form-control rounded border-gray-300" id="center_name"
                                    name="center_name" value="{{ old('center_name', $center->center_name) }}" autofocus>
                                @error('center_name')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <input id="assigned_pdo_user_id" name="assigned_pdo_user_id" value="1" type="hidden">
                            <div class="col-md-6 mt-3 text-sm hidden">
                                <label for="assigned_user_id">Assigned PDO<b class='text-red-600'>*</b></label>
                                <select class="form-control rounded border-gray-300" id="assigned_pdo_user_id_"
                                    name="assigned_pdo_user_id_">
                                    <option value="" selected>Select PDO</option>
                                    @foreach ($pdos as $pdo)
                                        <option value="{{ $pdo->id }}"
                                            @if (old('assigned_pdo_user_id')) {{ old('assigned_pdo_user_id') == $pdo->id ? 'selected' : '' }}
                                                @elseif ($assignedPDO && $assignedPDO->id == $pdo->id)
                                                    selected @endif>

                                            {{ $pdo->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_pdo_user_id')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mt-3 text-sm">
                                <label for="assigned_user_id">Assigned LGU Focal<b class='text-red-600'>*</b></label>
                                <select class="form-control rounded border-gray-300" id="assigned_focal_user_id"
                                    name="assigned_focal_user_id">
                                    <option value="" selected>Select LGU Focal</option>
                                    @foreach ($focals as $focal)
                                        <option value="{{ $focal->id }}"
                                            @if (old('assigned_focal_user_id')) {{ old('assigned_focal_user_id') == $focal->id ? 'selected' : '' }}
                                                @elseif ($assignedFocal && $assignedFocal->id == $focal->id)
                                                    selected @endif>

                                            {{ $focal->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_focal_user_id')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mt-3 text-sm">
                                <label for="assigned_user_id">Assigned Child Development Worker<b
                                        class='text-red-600'>*</b></label>
                                <select class="form-control rounded border-gray-300" id="assigned_worker_user_id"
                                    name="assigned_worker_user_id">
                                    <option value="" selected>Select Worker</option>
                                    @foreach ($workers as $worker)
                                        <option value="{{ $worker->id }}"
                                            @if (old('assigned_worker_user_id')) {{ old('assigned_worker_user_id') == $worker->id ? 'selected' : '' }}
                                                @elseif ($assignedWorker && $assignedWorker->id == $worker->id)
                                                    selected @endif>

                                            {{ $worker->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_worker_user_id')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mt-3 text-sm">
                                <label for="assigned_user_id">Assigned Encoder</label>
                                <select class="form-control rounded border-gray-300" id="assigned_encoder_user_id"
                                    name="assigned_encoder_user_id">
                                    <option value="" selected>Select Encoder</option>
                                    @foreach ($encoders as $encoder)
                                        <option value="{{ $encoder->id }}"
                                            @if (old('assigned_encoder_user_id')) {{ old('assigned_encoder_user_id') == $encoder->id ? 'selected' : '' }}
                                                @elseif ($assignedEncoder && $assignedEncoder->id == $encoder->id)
                                                    selected @endif>

                                            {{ $encoder->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_encoder_user_id')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class='col-md-12 mt-4 text-gray-400 text-xs'>Address<hr>
                            </div>

                            <div class="col-md-6 mt-3 text-sm">
                                <label for="region">Region<b class="text-red-600">*</b></label>
                                <select class="form-control rounded border-gray-300" id="region" name="region_psgc">
                                    <option value="110000000" selected>Region XI</option>
                                </select>
                            </div>

                            <!-- Hidden Region (pre-selected) -->
                            <input type="hidden" name="region_psgc" value="110000000">

                            <div class="col-md-6 mt-3 text-sm">
                                <label for="province">Province<b class="text-red-600">*</b></label>
                                <select class="form-control rounded border-gray-300" id="province" name="province_psgc">
                                    <option value="" selected>Select Province</option>
                                    @foreach ($provinces as $psgc => $name)
                                        <option value="{{ $psgc }}"
                                            {{ old('province_psgc', $psgcRecord->province_psgc) == $psgc ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('province_psgc')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mt-2 text-sm">
                                <label for="city">City/Municipality<b class="text-red-600">*</b></label>
                                <select class="form-control rounded border-gray-300" id="city"
                                    name="city_name_psgc">
                                    <option value="">Select City/Municipality</option>
                                    @foreach ($cities as $psgc => $name)
                                        <option value="{{ $psgc }}"
                                            {{ old('city_name_psgc', $psgcRecord->city_name_psgc) == $psgc ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('city_name_psgc')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mt-2 text-sm">
                                <label for="barangay">Barangay<b class="text-red-600">*</b></label>
                                <select class="form-control rounded border-gray-300" id="barangay" name="brgy_psgc">
                                    <option value="">Select Barangay</option>
                                    @foreach ($barangays as $psgc => $name)
                                        <option value="{{ $psgc }}"
                                            {{ old('brgy_psgc', $psgcRecord->brgy_psgc) == $psgc ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('brgy_psgc')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-12 mt-2 text-sm">
                                <label for="address">Address<b class="text-red-600">*</b></label>
                                <input type="text" class="form-control rounded border-gray-300" id="address"
                                    name='address' value="{{ old('address', $center->address) }}">
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
                                        <input type="text"
                                            class="form-control rounded border-gray-300 rounded border-gray-300 mr-3"
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
                                            Are you sure you want to save these changes?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" id="centerUpdateConfirm"
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
                                data-bs-toggle="modal" data-bs-target="#verticalycentered">Save Changes</button>
{{--                            <form id="cancel-form" method="GET" action="{{ route('centers.index') }}">--}}
{{--                            </form>--}}
                            <button type="button" class="text-white bg-gray-600 rounded px-3 min-h-9" onclick="submitCancelForm()">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Location Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
            let selectedProvince = @json(old('province_psgc', $psgcRecord->province_psgc));
            let selectedCity = @json(old('city_name_psgc', $psgcRecord->city_name_psgc));
            let selectedBarangay = @json(old('brgy_psgc', $psgcRecord->brgy_psgc));

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
                populateCities(selectedProvince); // <-- This loads cities

                // Delay populateBarangays until after cities are loaded
                if (selectedCity) {
                    citySelect.value = selectedCity;
                    populateBarangays(selectedCity);

                    if (selectedBarangay) {
                        barangaySelect.value = selectedBarangay;
                    }
                }
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

            document.getElementById('centerUpdateConfirm').addEventListener('click', function () {
                document.getElementById('centerUpdateForm').submit();
            });

        });
    </script>
@endsection
