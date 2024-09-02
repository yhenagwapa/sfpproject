@extends('layouts.app')

@section('title', 'SFP Onse')

@section('content')

    <main id="main" class="main">

        <div class="pagetitle">

            <nav style="--bs-breadcrumb-divider: '>';">
                <ol class="breadcrumb mb-3 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('child.index') }}">Children</a></li>
                    <li class="breadcrumb-item active">{{ $child->full_name }}</li>
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
                                <div class="card-title">
                                    <h5 class='col-md-6'>{{ $child->full_name }}</h5>

                                </div>

                                <!-- Tab Menu -->
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="personalinfo-tab" data-bs-toggle="tab"
                                            data-bs-target="#personalinfo" href="#personalinfo" type="button"
                                            role="tab" aria-controls="personalindfo" aria-selected="true">Child
                                            Details</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="feeding-tab" data-bs-toggle="tab"
                                            data-bs-target="#feeding" href="#feeding" type="button" role="tab"
                                            aria-controls="feeding" aria-selected="false">Feeding Details</button>
                                    </li>
                                </ul>

                                <div class="tab-content pt-2" id="myTabContent">

                                    <!-- Personal info tab -->
                                    <div class="tab-pane fade show active" id="personalinfo" role="tabpanel"
                                        aria-labelledby="personalinfo-tab">
                                        <form class="row" method="post"
                                            action="{{ route('child.update', $child->id) }}">
                                            @csrf
                                            @method('patch')


                                            <div class='col-md-2 mt-3 text-gray-400 text-xs'>Personal Information</div>
                                            <div class='col-md-10 mt-6 text-gray-400 text-xs'>
                                                <hr>
                                            </div>

                                            <div class="col-md-6 mt-3 text-sm">
                                                <label for="firstname">First Name</label><label for="firstname"
                                                    class="text-red-600">*</label>
                                                <input type="text" class="form-control" id="firstname" name='firstname'
                                                    value="{{ old('firstname', $child->firstname) }}" autofocus>
                                                @error('firstname')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mt-3 text-sm">
                                                <label for="middlename">Middle Name</label>
                                                <input type="text" class="form-control" id="middlename" name='middlename'
                                                    value="{{ old('middlename', $child->middlename) }}">
                                                @error('middlename')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mt-2 text-sm">
                                                <label for="lastname">Last Name</label><label for="lastname"
                                                    class="text-red-600">*</label>
                                                <input type="text" class="form-control" id="lastname" name='lastname'
                                                    value="{{ old('lastname', $child->lastname) }}">
                                                @error('lastname')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mt-2 text-sm">
                                                <label for="extname">Extension Name</label>
                                                <select class="form-control" id="extname" name="extname">
                                                    <option value="" disabled selected></option>
                                                    @foreach ($extNameOptions as $value1 => $label1)
                                                        <option value="{{ $value1 }}"
                                                            {{ old('extname', $child->extname) == $value1 ? 'selected' : '' }}>
                                                            {{ $label1 }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6 mt-2 text-sm">
                                                <label for="dob">Date of Birth</label><label for="dob"
                                                    class="text-red-600">*</label>
                                                <input type="date" class="form-control" id="dob" name='dob'
                                                    value="{{ old('dob', $child->dob) }}">
                                                @error('dob')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mt-2 text-sm">
                                                <label for="sex">Sex</label><label for="sex"
                                                    class="text-red-600">*</label>
                                                <select class="form-control" id="sex" name="sex">
                                                    <option value="" disabled></option>
                                                    @foreach ($sexOptions as $value => $label)
                                                        <option value="{{ $value }}"
                                                            {{ old('sex', $child->sex) == $value ? 'selected' : '' }}>
                                                            {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-3 mt-2 text-sm">
                                                <label for="weight">Weight</label><label for="weight"
                                                    class="text-red-600">*</label>
                                                <input type="text" class="form-control" id="weight"
                                                    name='weight'value="{{ old('weight', $child->weight) }}">
                                                @error('weight')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-3 mt-2 text-sm">
                                                <label for="height">Height</label><label for="height"
                                                    class="text-red-600">*</label>
                                                <input type="text" class="form-control" id="height"
                                                    name='height'value="{{ old('height', $child->height) }}">
                                                @error('height')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mt-2 text-sm">
                                                <label for="actual_date_of_weighing">Actual date of weighing</label><label
                                                    for="actual_date_of_weighing" class="text-red-600">*</label>
                                                <input type="date" class="form-control" id="actual_date_of_weighing"
                                                    name='actual_date_of_weighing'
                                                    value="{{ old('actual_date_of_weighing', $child->actual_date_of_weighing) }}">
                                                @error('actual_date_of_weighing')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mt-2">
                                                <label for="deworming">Deworming Date</label>
                                                <input type="date" class="form-control" id="deworming"
                                                    name='deworming' value="{{ old('deworming', $child->deworming) }}">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label for="vitamin_a">Vitamin A Date</label>
                                                <input type="date" class="form-control" id="vitamin_a"
                                                    name='vitamin_a' value="{{ old('vitamin_a', $child->vitamin_a) }}">
                                            </div>
                                            <div class="col-md-4 mt-4">
                                                <label for="is_pantawid">Pantawid Member:</label><label for="is_pantawid"
                                                    class="text-red-600">*</label>
                                            </div>
                                            <div class="col-md-1 mt-4">
                                                <input type="radio" id="is_pantawid_yes" name="is_pantawid"
                                                    value="1"
                                                    {{ old('is_pwd', $child->is_pantawid) == '1' ? 'checked' : '' }}>
                                                <label for="is_pantawid_yes">Yes</label>
                                            </div>
                                            <div class="col-md-1 mt-4">
                                                <input type="radio" id="is_pantawid_no" name="is_pantawid"
                                                    value="0"
                                                    {{ old('is_pwd', $child->is_pantawid) == '0' ? 'checked' : '' }}>
                                                <label for="is_pantawid_no">No</label>
                                            </div>
                                            <div class="col-md-6 mt-4 additional-details">
                                                <select class="form-control" id="pantawid_details"
                                                    name="pantawid_details" required>
                                                    <option value="" disabled selected>---</option>
                                                    @foreach ($pantawidDetails as $value => $label)
                                                        <option value="{{ $value }}"
                                                            {{ old('pantawid_details', $child->pantawid_details) == $value ? 'selected' : '' }}>
                                                            {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('pantawid_details')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="col-md-4 mt-2">
                                                <label for="is_perspn_with_disability">Person with
                                                    Disability:</label><label for="is_perspn_with_disability"
                                                    class="text-red-600">*</label>
                                            </div>
                                            <div class="col-md-1 mt-2">
                                                <input type="radio" id="is_perspn_with_disability_yes"
                                                    name="is_perspn_with_disability" value="1"
                                                    {{ old('is_perspn_with_disability', $child->is_perspn_with_disability) == '1' ? 'checked' : '' }}>
                                                <label for="is_perspn_with_disability_yes">Yes</label>
                                            </div>
                                            <div class="col-md-1 mt-2">
                                                <input type="radio" id="is_perspn_with_disability_no"
                                                    name="is_perspn_with_disability" value="0"
                                                    {{ old('is_perspn_with_disability', $child->is_pwd) == '0' ? 'checked' : '' }}>
                                                <label for="is_perspn_with_disability_no">No</label>
                                            </div>
                                            <div class="col-md-6 mt-2 additional-details"
                                                id="perspn_with_disability_additionalDetails">
                                                <input type="text" class="form-control"
                                                    id="perspn_with_disability_details"
                                                    name="perspn_with_disability_details" placeholder="Please specify"
                                                    value="{{ old('perspn_with_disability_details', $child->perspn_with_disability_details) }}"
                                                    {{ $child->is_perspn_with_disability ? '' : 'disabled' }}>
                                                @error('perspn_with_disability_details')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mt-2">
                                                <label for="is_indigenous_people">Indigenous People (IP):</label>
                                            </div>
                                            <div class="col-md-1 mt-2">
                                                <input type="radio" name="is_indigenous_people"
                                                    id="is_indigenous_people_yes" value="1"
                                                    {{ old('is_indigenous_people', $child->is_ip) == '1' ? 'checked' : '' }}>
                                                <label for="is_indigenous_people_yes">Yes</label>
                                            </div>
                                            <div class="col-md-1 mt-2">
                                                <input type="radio" name="is_indigenous_people"
                                                    id="is_indigenous_peoplep_no" value="0"
                                                    {{ old('is_indigenous_people', $child->is_indigenous_people) == '0' ? 'checked' : '' }}>
                                                <label for="is_indigenous_people_no">No</label>
                                            </div>
                                            <div class="col-md-6 mt-2" style="visibility: hidden">
                                                <input type="text" class="form-control" name="spaceonly">
                                            </div>
                                            <div class="col-md-4 mt-2">
                                                <label for="is_child_of_soloparent">Child of Solo Parent:</label>
                                            </div>
                                            <div class="col-md-1 mt-2">
                                                <input type="radio" name="is_child_of_soloparent"
                                                    id="is_child_of_soloparent_yes" value="1"
                                                    {{ old('is_child_of_soloparent', $child->is_child_of_soloparent) == '1' ? 'checked' : '' }}>
                                                <label for="is_child_of_soloparent_yes">Yes</label>
                                            </div>
                                            <div class="col-md-1 mt-2">
                                                <input type="radio" name="is_child_of_soloparent"
                                                    id="is_child_of_soloparent_no" value="0"
                                                    {{ old('is_child_of_soloparent', $child->is_child_of_soloparent) == '0' ? 'checked' : '' }}>
                                                <label for="is_child_of_soloparent_no">No</label>
                                            </div>
                                            <div class="col-md-6 mt-2" style="visibility: hidden">
                                                <input type="text" class="form-control" name="spaceonly">
                                            </div>
                                            <div class="col-md-4 mt-2">
                                                <label for="is_lactose_intolerant">Lactose Intolerant:</label>
                                            </div>
                                            <div class="col-md-1 mt-2">
                                                <input type="radio" name="is_lactose_intolerant"
                                                    id="is_lactose_intolerant_yes" value="1"
                                                    {{ old('is_lactose_intolerant', $child->is_lactose_intolerant) == '1' ? 'checked' : '' }}>
                                                <label for="is_lactose_intolerant_yes">Yes</label>
                                            </div>
                                            <div class="col-md-1 mt-2">
                                                <input type="radio" name="is_lactose_intolerant"
                                                    id="is_lactose_intolerant_no" value="0"
                                                    {{ old('is_lactose_intolerant', $child->is_lactose_intolerant) == '0' ? 'checked' : '' }}>
                                                <label for="is_lactose_intolerant_no">No</label>
                                            </div>
                                            <div class="col-md-6 mt-2" style="visibility: hidden">
                                                <input type="text" class="form-control" name="spaceonly">
                                            </div>

                                            <div class='col-md-1 mt-4 text-gray-400 text-xs'>Address</div>
                                            <div class='col-md-11 mt-8 text-gray-400 text-xs'>
                                                <hr>
                                            </div>



                                            <div class="col-md-6 mt-2 text-sm">
                                                <label for="region">Region</label>
                                                <select class="form-control" id="region">
                                                    <option value="0">Region XI</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mt-2 text-sm">
                                                <label for="province">Province</label><label for="province"
                                                    class="text-red-600">*</label>
                                                <select class="form-control" id="province" name="province" disabled>
                                                    <option value=""></option>
                                                </select>
                                                @error('province')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mt-2 text-sm">
                                                <label for="city">City/Municipality</label><label for="city"
                                                    class="text-red-600">*</label>
                                                <select class="form-control" id="city" name="city" disabled>
                                                    <option value=""></option>
                                                </select>
                                                @error('city')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mt-2 text-sm">
                                                <label for="barangay">Barangay</label><label for="barangay"
                                                    class="text-red-600">*</label>
                                                <select class="form-control" id="barangay" name="barangay" disabled>
                                                    <option value=""></option>
                                                </select>
                                                @error('barangay')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <input type="hidden" id="psgc_id" name="psgc_id"
                                                value="{{ $child->psgc_id }}">

                                            <div class="col-md-6 mt-2 text-sm">
                                                <label for="house_no">House No./ Street/ Purok</label><label
                                                    for="house_no" class="text-red-600">*</label>
                                                <input type="text" class="form-control" id="house_no"
                                                    name='house_no' value="{{ old('house_no', $child->house_no) }}">
                                                @error('house_no')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label for="zip_code">Zip Code</label><label for="zip_code"
                                                    class="text-red-600">*</label>
                                                <input type="text" class="form-control" id="zip_code"
                                                    name='zip_code' value="{{ old('zip_code', $child->zipcode) }}"
                                                    maxlength="4">
                                                @error('zip_code')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class='col-md-3 mt-4 text-gray-400 text-xs'>Child Development Center</div>
                                            <div class='col-md-9 mt-8 text-gray-400 text-xs'>
                                                <hr>
                                            </div>

                                            <div class="col-md-12 mt-2">
                                                <label for="child_development_center_id">Child Development
                                                    Center</label><label for="child_development_center_id"
                                                    class="text-red-600">*</label>
                                                <select class="form-control" id="child_development_center_id"
                                                    name="child_development_center_id">
                                                    <option value="">Select a center</option>
                                                    @foreach ($centers as $center)
                                                        <option value="{{ $center->id }}"
                                                            {{ $center->id == old('cdc_id', $child->child_development_center_id) ? 'selected' : '' }}>
                                                            {{ $center->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('child_development_center_id')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div>



                                            <div class="col-md-12 mt-4 text-right">
                                                <button type="submit"
                                                    class="text-white bg-blue-600 rounded px-3 min-h-9">Save
                                                    Changes</button>
                                                <a href="{{ route('child.index') }}"><button type="reset"
                                                        class="text-white bg-gray-600 rounded px-3 min-h-9">Cancel</button></a>
                                            </div>
                                        </form><!-- End floating Labels Form -->

                                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

                                        {{-- displaying fetched details (location) from db --}}
                                        <script>
                                            $(document).ready(function() {
                                                var regionPsgc = 110000000; // Assuming Region XI is fixed

                                                function loadProvinces(regionPsgc, selectedProvincePsgc = null) {
                                                    $.ajax({
                                                        url: '{{ route('get.provinces', ':region_psgc') }}'.replace(':region_psgc',
                                                            regionPsgc),
                                                        type: 'GET',
                                                        dataType: 'json',
                                                        success: function(data) {
                                                            $('#province').empty().append(
                                                                '<option value="" disabled>Select Province</option>');
                                                            $.each(data, function(index, province) {
                                                                var isSelected = selectedProvincePsgc == province.province_psgc ?
                                                                    'selected' : '';
                                                                $('#province').append('<option value="' + province.province_psgc +
                                                                    '" ' + isSelected + '>' + province.province_name +
                                                                    '</option>');
                                                            });
                                                            $('#province').prop('disabled', false);
                                                            if (selectedProvincePsgc) {
                                                                loadCities(selectedProvincePsgc, '{{ $selectedCityPsgc }}');
                                                            }
                                                        },
                                                        error: function(xhr, status, error) {
                                                            alert("An error occurred while loading provinces: " + error);
                                                        }
                                                    });
                                                }

                                                function loadCities(provincePsgc, selectedCityPsgc = null) {
                                                    $.ajax({
                                                        url: '{{ route('get.cities', ':province_psgc') }}'.replace(':province_psgc',
                                                            provincePsgc),
                                                        type: 'GET',
                                                        dataType: 'json',
                                                        success: function(data) {
                                                            $('#city').empty().append(
                                                                '<option value="" disabled>Select City/Municipality</option>');
                                                            $.each(data, function(key, value) {
                                                                var isSelected = selectedCityPsgc == key ? 'selected' : '';
                                                                $('#city').append('<option value="' + key + '" ' + isSelected +
                                                                    '>' + value + '</option>');
                                                            });
                                                            $('#city').prop('disabled', false);
                                                            if (selectedCityPsgc) {
                                                                loadBarangays(selectedCityPsgc, '{{ $selectedBrgyPsgc }}');
                                                            }
                                                        },
                                                        error: function(xhr, status, error) {
                                                            alert("An error occurred while loading cities: " + error);
                                                        }
                                                    });
                                                }

                                                function loadBarangays(cityPsgc, selectedBrgyPsgc = null) {
                                                    $.ajax({
                                                        url: '{{ route('get.barangays', ':city_name_psgc') }}'.replace(':city_name_psgc',
                                                            cityPsgc),
                                                        type: 'GET',
                                                        dataType: 'json',
                                                        success: function(data) {
                                                            $('#barangay').empty().append(
                                                                '<option value="" disabled>Select Barangay</option>');
                                                            $.each(data, function(key, value) {
                                                                var isSelected = selectedBrgyPsgc == key ? 'selected' : '';
                                                                $('#barangay').append('<option value="' + key + '" ' + isSelected +
                                                                    '>' + value + '</option>');
                                                            });
                                                            $('#barangay').prop('disabled', false);
                                                        },
                                                        error: function(xhr, status, error) {
                                                            alert("An error occurred while loading barangays: " + error);
                                                        }
                                                    });
                                                }

                                                // Initial load based on selected values
                                                loadProvinces(regionPsgc, '{{ $selectedProvincePsgc }}');

                                                // Load cities on province change
                                                $('#province').change(function() {
                                                    loadCities($(this).val());
                                                    $('#barangay').empty().append('<option value="" disabled>Select Barangay</option>').prop(
                                                        'disabled', true);
                                                });

                                                // Load barangays on city change
                                                $('#city').change(function() {
                                                    loadBarangays($(this).val());
                                                });

                                                // Fetch PSGC ID when barangay changes
                                                $('#barangay').change(function() {
                                                    var provincePsgc = $('#province').val();
                                                    var cityPsgc = $('#city').val();
                                                    var brgyPsgc = $(this).val();

                                                    $.ajax({
                                                        url: '/psgc/' + regionPsgc + '/' + provincePsgc + '/' + cityPsgc + '/' +
                                                            brgyPsgc,
                                                        type: 'GET',
                                                        success: function(data) {
                                                            console.log('PSGC ID:', data[0]);
                                                            $('#psgc_id').val(data[
                                                                0]); // Set the hidden input field with the PSGC ID
                                                        },
                                                        error: function(xhr, status, error) {
                                                            console.error('Error fetching PSGC ID');
                                                        }
                                                    });
                                                });
                                            });
                                        </script>

                                        {{-- pantawid and pwd additional details --}}
                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                // Function to toggle additional details based on radio button selection
                                                function toggleAdditionalDetails(radioName, additionalDetailsId) {
                                                    const radios = document.getElementsByName(radioName);
                                                    const additionalDetailsSelect = document.getElementById(additionalDetailsId);

                                                    radios.forEach(radio => {
                                                        radio.addEventListener('change', function() {
                                                            if (radio.value === '1' && radio.checked) {
                                                                additionalDetailsSelect.disabled = false;
                                                            } else if (radio.value === '0' && radio.checked) {
                                                                additionalDetailsSelect.disabled = true;
                                                            }
                                                        });
                                                    });

                                                }
                                                // Apply the function to each set of radio buttons and additional details
                                                toggleAdditionalDetails('is_pantawid', 'pantawid_details');
                                                toggleAdditionalDetails('is_pwd', 'pwd_details');

                                            });
                                        </script>

                                    </div>

                                    <!-- Feeding information -->
                                    <div class="tab-pane fade" id="feeding" role="tabpanel"
                                        aria-labelledby="feeding-tab">

                                        {{-- attendance form/modal --}}



                                    </div>
                                </div>
                            </div><!-- End Default Tabs -->
                        </div>
                    </div>
                </div>
        </div>


        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Function to toggle additional details based on radio button selection
                function toggleAdditionalDetails(radioName, additionalDetailsId) {
                    const radios = document.getElementsByName(radioName);
                    const additionalDetailsSelect = document.getElementById(additionalDetailsId);

                    radios.forEach(radio => {
                        radio.addEventListener('change', function() {
                            if (radio.value === '1' && radio.checked) {
                                additionalDetailsSelect.disabled = false;
                                additionalDetailsSelect.required = true;
                            } else if (radio.value === '0' && radio.checked) {
                                additionalDetailsSelect.disabled = true;
                                additionalDetailsSelect.required = false;
                            }
                        });
                    });

                    // Initial check in case the page is loaded with a radio already checked
                    const checkedRadio = Array.from(radios).find(radio => radio.checked);
                    if (checkedRadio && checkedRadio.value === '1') {
                        additionalDetailsSelect.disabled = false;
                    } else {
                        additionalDetailsSelect.disabled = true;
                    }
                }


                // Apply the function to each set of radio buttons and additional details
                toggleAdditionalDetails('is_pantawid', 'pantawid_details');
                toggleAdditionalDetails('is_pwd', 'pwd_details');
            });
        </script>

        </section>
        </div>
    </main><!-- End #main -->
