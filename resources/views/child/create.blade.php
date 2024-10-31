@extends('layouts.app')

@section('content')

<main id="main" class="main">

    <div class="pagetitle">

        <nav style="--bs-breadcrumb-divider: '>';">
            <ol class="breadcrumb mb-3 p-0">
                <li class="breadcrumb-item"><a href="{{ route('child.index') }}">Children</a></li>
                <li class="breadcrumb-item active">Child Details</li>
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
                            <h5 class="card-title">Child Details</h5>
                            <form class="row" method="post" action="{{ route('child.store') }} ">
                                @csrf

                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="firstname">First Name</label><label for="firstname"
                                            class="text-red-600">*</label>
                                        <input type="text"
                                            class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                            id="firstname" name='firstname' value="{{ old('firstname') }}" autofocus>
                                        @error('firstname')
                                            <span class="text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="middlename">Middle Name</label>
                                        <input type="text"
                                            class="form-control invalid:border-red-500 rounded border-gray-300"
                                            id="middlename" name='middlename' value="{{ old('middlename') }}">
                                        @error('middlename')
                                            <span class="text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mt-2 text-sm">
                                        <label for="lastname">Last Name</label><label for="lastname"
                                            class="text-red-600">*</label>
                                        <input type="text"
                                            class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                            id="lastname" name='lastname' value="{{ old('lastname') }}">
                                        @error('lastname')
                                            <span class="text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mt-2 text-sm">
                                        <label for="extension_name">Extension Name</label>
                                        <select
                                            class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                            id="extension_name" name='extension_name'>
                                            <option value="" disabled selected></option>
                                            <option value="Jr" {{ old('extension_name') == 'jr' ? 'selected' : '' }}>Jr
                                            </option>
                                            <option value="Sr" {{ old('extension_name') == 'sr' ? 'selected' : '' }}>Sr
                                            </option>
                                            <option value="I" {{ old('extension_name') == 'i' ? 'selected' : '' }}>I</option>
                                            <option value="II" {{ old('extension_name') == 'ii' ? 'selected' : '' }}>II
                                            </option>
                                            <option value="III" {{ old('extension_name') == 'iii' ? 'selected' : '' }}>III
                                            </option>
                                            <option value="IV" {{ old('extension_name') == 'iv' ? 'selected' : '' }}>IV
                                            </option>
                                            <option value="V" {{ old('extension_name') == 'v' ? 'selected' : '' }}>V</option>
                                            <option value="VI" {{ old('extension_name') == 'vi' ? 'selected' : '' }}>VI
                                            </option>
                                            <option value="VII" {{ old('extension_name') == 'vii' ? 'selected' : '' }}>VII
                                            </option>
                                            <option value="VIII" {{ old('extension_name') == 'viii' ? 'selected' : '' }}>VIII
                                            </option>
                                            <option value="IX" {{ old('extension_name') == 'ix' ? 'selected' : '' }}>IX
                                            </option>
                                            <option value="X" {{ old('extension_name') == 'x' ? 'selected' : '' }}>X</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mt-2 text-sm">
                                        <label for="date_of_birth">Date of Birth</label><label for="date_of_birth"
                                            class="text-red-600">*</label>
                                        <input type="date"
                                            class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                            id="date_of_birth" name='date_of_birth' value="{{ old('date_of_birth') }}">
                                        @error('date_of_birth')
                                            <span class="text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mt-2 text-sm">
                                        <label for="sex">Sex</label><label for="sex"
                                            class="text-red-600">*</label>
                                        <select
                                            class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                            id="sex_id" name='sex_id'>
                                            <option value="" disabled selected>Select sex</option>
                                            @foreach ($sexOptions as $sex)
                                                <option value="{{ $sex->id }}"
                                                {{ $sex->id == old('sex_id', $sex->id) }}>
                                                {{ $sex->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('sex_id')
                                            <span class="text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>

                                

                                <div class="col-md-6 mt-2 text-sm">
                                    <label for="deworming_date">Deworming Date</label>
                                    <input type="date"
                                        class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                        id="deworming_date" name='deworming_date' value="{{ old('deworming') }}">
                                </div>

                                <div class="col-md-6 mt-2 text-sm">
                                    <label for="vitamin_a_date">Vitamin A</label>
                                    <input type="date"
                                        class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                        id="vitamin_a_date" name='vitamin_a_date' value="{{ old('vitamin_a') }}">
                                </div>

                                <div class="col-md-4 mt-4 text-sm">
                                    <label for="is_pantawid">Pantawid Member:</label><label for="is_pantawid"
                                        class="text-red-600">*</label>
                                </div>
                                <div class="col-md-1 mt-4 text-sm">
                                    <input type="radio" id="is_pantawid_yes" name="is_pantawid" value="1"
                                        {{ old('is_pantawid') == '1' ? 'checked' : '' }}>
                                    <label for="is_pantawid_yes">Yes</label>
                                </div>
                                <div class="col-md-1 mt-4 text-sm">
                                    <input type="radio" id="is_pantawid_no" name="is_pantawid" value="0"
                                        {{ old('is_pantawid', '0') == '0' ? 'checked' : '' }}>
                                    <label for="is_pantawid_no">No</label>
                                </div>
                                <div class="col-md-6 mt-4 text-sm additional-details">
                                    <select
                                        class="form-control rounded border-gray-300"
                                        id="pantawid_details" name="pantawid_details"
                                        placeholder="Please specify if RCCT or MCCT" disabled>
                                        <option value="" disabled selected>Please specify </option>
                                        <option value="rcct">RCCT</option>
                                        <option value="mcct">MCCT</option>
                                    </select>
                                    @error('pantawid_details')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-4 mt-2 text-sm">
                                    <label for="is_person_with_disability">Person with Disability:</label><label
                                        for="is_person_with_disability" class="text-red-600">*</label>
                                </div>
                                <div class="col-md-1 mt-2 text-sm">
                                    <input type="radio" id="is_person_with_disability_yes"
                                        name="is_person_with_disability" value="1"
                                        {{ old('is_person_with_disability') == '1' ? 'checked' : '' }}>
                                    <label for="is_person_with_disability_yes">Yes</label>
                                </div>
                                <div class="col-md-1 mt-2 text-sm">
                                    <input type="radio" id="is_person_with_disability_no"
                                        name="is_person_with_disability" value="0"
                                        {{ old('is_person_with_disability', '0') == '0' ? 'checked' : '' }}>
                                    <label for="is_person_with_disability_no">No</label>
                                </div>
                                <div class="col-md-6 mt-2 text-sm additional-details">
                                    <input type="text"
                                        class="form-control rounded border-gray-300"
                                        id="person_with_disability_details" name="person_with_disability_details"
                                        placeholder="Please specify" disabled>
                                    @error('person_with_disability_details')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-4 mt-2 text-sm">
                                    <label for="is_indigenous_people">Indigenous People (IP):</label>
                                </div>
                                <div class="col-md-1 mt-2 text-sm">
                                    <input type="radio" name="is_indigenous_people" id="is_indigenous_people_yes"
                                        value="1" {{ old('is_ip') == '1' ? 'checked' : '' }}>
                                    <label for="is_indigenous_people_yes">Yes</label>
                                </div>
                                <div class="col-md-1 mt-2 text-sm">
                                    <input type="radio" name="is_indigenous_people" id="is_indigenous_people_no"
                                        value="0" {{ old('is_ip', '0') == '0' ? 'checked' : '' }}>
                                    <label for="is_indigenous_people_no">No</label>
                                </div>
                                <div class="col-md-6 mt-2 text-sm" style="visibility: hidden">
                                    <input type="text"
                                        class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                        name="spaceonly">
                                </div>

                                <div class="col-md-4 mt-2 text-sm">
                                    <label for="is_child_of_soloparent">Child of Solo Parent:</label>
                                </div>
                                <div class="col-md-1 mt-2 text-sm">
                                    <input type="radio" name="is_child_of_soloparent"
                                        id="is_child_of_soloparent_yes" value="1"
                                        {{ old('is_child_of_soloparent') == '1' ? 'checked' : '' }}>
                                    <label for="is_child_of_soloparent_yes">Yes</label>
                                </div>
                                <div class="col-md-1 mt-2 text-sm">
                                    <input type="radio" name="is_child_of_soloparent"
                                        id="is_child_of_soloparent_no" value="0"
                                        {{ old('is_child_of_soloparent', '0') == '0' ? 'checked' : '' }}>
                                    <label for="is_child_of_soloparent_no">No</label>
                                </div>
                                <div class="col-md-6 mt-2 text-sm" style="visibility: hidden">
                                    <input type="text"
                                        class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                        name="spaceonly">
                                </div>

                                <div class="col-md-4 mt-2 text-sm">
                                    <label for="is_lactose_intolerant">Lactose Intolerant:</label>
                                </div>
                                <div class="col-md-1 mt-2 text-sm">
                                    <input type="radio" name="is_lactose_intolerant" id="is_lactose_intolerant_yes"
                                        value="1" {{ old('is_lactose_intolerant') == '1' ? 'checked' : '' }}>
                                    <label for="is_lactose_intolerant_yes">Yes</label>
                                </div>
                                <div class="col-md-1 mt-2 text-sm">
                                    <input type="radio" name="is_lactose_intolerant" id="is_lactose_intolerant_no"
                                        value="0"
                                        {{ old('is_lactose_intolerant', '0') == '0' ? 'checked' : '' }}>
                                    <label for="is_lactose_intolerant_no">No</label>
                                </div>
                                <div class="col-md-6 mt-2 text-sm" style="visibility: hidden">
                                    <input type="text"
                                        class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                        name="spaceonly">
                                </div>

                                <div class='col-md-1 mt-4 text-gray-400 text-xs'>Address</div>
                                <div class='col-md-11 mt-8 text-gray-400 text-xs'>
                                    <hr>
                                </div>

                                <div class="col-md-6 mt-3 text-sm">
                                    <label for="region">Region</label><label for="region"
                                        class="text-red-600">*</label>
                                    <select
                                        class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                        id="region" disabled>
                                        <option value="110000000" selected>Region XI</option>
                                    </select>
                                </div>

                                <input type="hidden" name="region_psgc" value="110000000">

                                <div class="col-md-6 mt-3 text-sm">
                                    <label for="province">Province</label><label for="province"
                                        class="text-red-600">*</label>
                                    <select class="form-control" id="province" name="province_psgc"
                                        onchange="filterCities()">
                                        <option value="" selected>Select Province</option>
                                        @foreach ($provinces as $psgc => $name)
                                        <option value="{{ $psgc }}"
                                            {{ old('province_psgc') == $psgc ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('province_psgc')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mt-2 text-sm">
                                    <label for="city">City/Municipality</label><label for="city"
                                        class="text-red-600">*</label>
                                    <select class="form-control" id="city" name="city_name_psgc"
                                        onchange="filterBarangays()">
                                        <option value="" selected>Select City/Municipality</option>
                                        @foreach ($cities as $psgc => $name)
                                        <option value="{{ $psgc }}"
                                            {{ old('city_name_psgc') == $psgc ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('city_name_psgc')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mt-2 text-sm">
                                    <label for="barangay">Barangay</label><label for="barangay"
                                        class="text-red-600">*</label>
                                    <select class="form-control" id="barangay" name="brgy_psgc">
                                        <option value="" selected>Select Barangay</option>
                                        @foreach ($barangays as $psgc => $name)
                                        <option value="{{ $psgc }}"
                                            {{ old('brgy_psgc') == $psgc ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('brgy_psgc')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>


                                <div class="col-6 mt-2 text-sm">
                                    <label for="address">House No./ Street/ Purok</label><label for="address"
                                        class="text-red-600">*</label>
                                    <input type="text"
                                        class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                        id="address" name='address' value="{{ old('address') }}">
                                    @error('address')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mt-2 text-sm">
                                    <label for="zip_code">Zip Code</label><label for="zip_code"
                                        class="text-red-600">*</label>
                                    <input type="text"
                                        class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                        id="zip_code" name='zip_code' value="{{ old('zip_code') }}"
                                        maxlength="4">
                                    @error('zip_code')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>

                                <input type="hidden" id="psgc_id" name="psgc_id" value="">

                                    

                                    <div class='col-md-3 mt-4 text-gray-400 text-xs'>Child Development Center or Supervised Neighborhood Play</div>
                                    <div class='col-md-9 mt-8 text-gray-400 text-xs'>
                                        <hr>
                                    </div>

                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="child_development_center_id">CDC or SNP</label><b
                                            class="text-red-600">*</b>
                                            <select class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300" id="child_development_center_id" name='child_development_center_id'>
                                                <option value="" disabled selected>Select CDC or SNP</option>
                                                @foreach ($centers as $center)
                                                    <option value="{{ $center->id }}"
                                                        {{ $center->id == old('child_development_center_id') ? 'selected' : '' }}>
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
                                            id="cycle_implementation_id" name='cycle_implementation_id' onchange="setFundingStatus()">
                                            @if ($cycleImplementation)
                                                <option value="" selected>Not Applicable</option>
                                                <option value="{{ $cycleImplementation->id }}"
                                                    {{ $cycleImplementation->id == old('cycle_implementation_id') ? 'selected' : '' }}>
                                                    {{ $cycleImplementation->cycle_name }}
                                                </option>
                                            @else
                                                <option value="" disabled selected>No active cycle implementation</option>
                                            @endif
                                        </select>
                                    </div>

                                    <div class="col-md-6 mt-3 text-sm">
                                        <label for="milk_feeding_id">Milk Feeding</label>
                                        <select
                                            class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                            id="milk_feeding_id" name='milk_feeding_id'>
                                            @if ($milkFeeding)
                                                <option value="" selected>Not Applicable</option>
                                                <option value="{{ $milkFeeding->id }}"
                                                    {{ $milkFeeding->id == old('milk_feeding_id') ? 'selected' : '' }}>
                                                    {{ $milkFeeding->name }}
                                                </option>
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
                                                    @if ($cycleImplementation)
                                                        Are you sure you want to save these details?
                                                    @else
                                                        No active cycle implementation.
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    @if ($cycleImplementation)
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
            </div>
    </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- {{-- pantawid and pwd additional details --}} -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function toggleAdditionalDetails(radioName, additionalDetailsId) {
                const radios = document.getElementsByName(radioName);
                const additionalDetailsSelect = document.getElementById(additionalDetailsId);

                radios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        if (radio.value === '1' && radio.checked) {
                            additionalDetailsSelect.disabled = false;
                            additionalDetailsElement.setAttribute('required', 'required');
                        } else if (radio.value === '0' && radio.checked) {
                            additionalDetailsSelect.disabled = true;
                            additionalDetailsElement.removeAttribute('required');
                        }
                    });
                });

            }

            toggleAdditionalDetails('is_pantawid', 'pantawid_details');
            toggleAdditionalDetails('is_person_with_disability', 'person_with_disability_details');

        });
    </script>

    {{-- city and barangay  --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const locations = {
                provinces: @json($provinces),
                cities: @json($cities),
                barangays: @json($barangays)
            };

            const provinceSelect = document.getElementById('province');
            const citySelect = document.getElementById('city');
            const barangaySelect = document.getElementById('barangay');

            function filterCities() {
                const provincePsgc = provinceSelect.value;

                citySelect.innerHTML = '<option value="">Select City/Municipality</option>';
                barangaySelect.innerHTML = '<option value="">Select Barangay</option>';

                if (provincePsgc) {
                    citySelect.style.display = 'block';
                    if (locations.cities[provincePsgc]) {
                        locations.cities[provincePsgc].forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.psgc;
                            option.text = city.name;
                            citySelect.appendChild(option);
                        });
                    }
                } else {
                    citySelect.style.display = 'disabled';
                }

                citySelect.value = '';
                barangaySelect.value = '';
                barangaySelect.style.display = 'disabled';
            }

            function filterBarangays() {
                const cityPsgc = citySelect.value;

                barangaySelect.innerHTML = '<option value="">Select Barangay</option>';

                if (cityPsgc) {
                    barangaySelect.style.display = 'block';
                    if (locations.barangays[cityPsgc]) {
                        locations.barangays[cityPsgc].forEach(barangay => {
                            const option = document.createElement('option');
                            option.value = barangay.psgc;
                            option.text = barangay.name;
                            barangaySelect.appendChild(option);
                        });
                    }
                } else {
                    barangaySelect.style.display = 'disabled';
                }
            }

            provinceSelect.addEventListener('change', filterCities);
            citySelect.addEventListener('change', filterBarangays);

            filterCities();
            filterBarangays();
        });
    </script>

    <script>
        function setFundingStatus() {
            const selectElement = document.getElementById('cycle_implementation_id');
            const isFundedInput = document.getElementById('is_funded');

            isFundedInput.value = selectElement.value ? 1 : 0;
        }

        document.addEventListener('DOMContentLoaded', setFundingStatus);
    </script>
</main><!-- End #main -->