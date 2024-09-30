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

                                <!-- Personal info -->
                                <div class="tab-pane fade show active" id="personalinfo" role="tabpanel"
                                    aria-labelledby="personalinfo-tab">
                                    <form class="row" method="post" action="{{ route('child.update', $child->id) }}">
                                        @csrf
                                        @method('patch')


                                        <div class='col-md-2 mt-3 text-gray-400 text-xs'>Personal Information</div>
                                        <div class='col-md-10 mt-6 text-gray-400 text-xs'>
                                            <hr>
                                        </div>

                                        <div class="col-md-6 mt-3 text-sm">
                                            <label for="firstname">First Name<b class="text-red-600">*</b></label>
                                            <input type="text" class="form-control rounded border-gray-300"
                                                id="firstname" name='firstname'
                                                value="{{ old('firstname', $child->firstname) }}" autofocus>
                                            @error('firstname')
                                                <span class="text-xs text-red-600">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mt-3 text-sm">
                                            <label for="middlename">Middle Name</label>
                                            <input type="text" class="form-control rounded border-gray-300"
                                                id="middlename" name='middlename'
                                                value="{{ old('middlename', $child->middlename) }}">
                                            @error('middlename')
                                                <span class="text-xs text-red-600">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mt-2 text-sm">
                                            <label for="lastname">Last Name<b class="text-red-600">*</b></label>
                                            <input type="text" class="form-control rounded border-gray-300"
                                                id="lastname" name='lastname'
                                                value="{{ old('lastname', $child->lastname) }}">
                                            @error('lastname')
                                                <span class="text-xs text-red-600">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mt-2 text-sm">
                                            <label for="extension_name">Extension Name</label>
                                            <select class="form-control rounded border-gray-300" id="extension_name"
                                                name="extension_name">
                                                <option value="" disabled selected></option>
                                                @foreach ($extNameOptions as $value1 => $label1)
                                                    <option value="{{ $value1 }}"
                                                        {{ old('extname', $child->extension_name) == $value1 ? 'selected' : '' }}>
                                                        {{ $label1 }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 mt-2 text-sm">
                                            <label for="date_of_birth">Date of Birth<b class="text-red-600">*</b></label>
                                            <input type="date" class="form-control rounded border-gray-300"
                                                id="date_of_birth" name='date_of_birth' value="{{ old('date_of_birth', $child->date_of_birth) }}">
                                            @error('date_of_birth')
                                                <span class="text-xs text-red-600">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mt-2 text-sm">
                                            <label for="sex">Sex<b class="text-red-600">*</b></label>
                                            <select class="form-control rounded border-gray-300" id="sex" name="sex">
                                                <option value="" disabled>Select Sex</option>
                                                @foreach ($sexOptions as $sex)
                                                    <option value="{{ $sex->id }}" 
                                                        {{ old('sex', $child->sex_id) == $sex->id ? 'selected' : '' }}>
                                                        {{ $sex->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6 mt-2">
                                            <label for="deworming">Deworming Date</label>
                                            <input type="date" class="form-control rounded border-gray-300"
                                                id="deworming" name='deworming'
                                                value="{{ old('deworming', $child->deworming) }}">
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <label for="vitamin_a">Vitamin A Date</label>
                                            <input type="date" class="form-control rounded border-gray-300"
                                                id="vitamin_a" name='vitamin_a'
                                                value="{{ old('vitamin_a', $child->vitamin_a) }}">
                                        </div>
                                        <div class="col-md-4 mt-4">
                                            <label for="is_pantawid">Pantawid Member:<b class="text-red-600">*</b></label>
                                        </div>
                                        <div class="col-md-1 mt-4">
                                            <input type="radio" id="is_pantawid_yes" name="is_pantawid" value="1"
                                                {{ old('is_pantawid', $child->is_pantawid) == '1' ? 'checked' : '' }}>
                                            <label for="is_pantawid_yes">Yes</label>
                                        </div>
                                        <div class="col-md-1 mt-4">
                                            <input type="radio" id="is_pantawid_no" name="is_pantawid" value="0"
                                                {{ old('is_pantawid', $child->is_pantawid) == '0' ? 'checked' : '' }}>
                                            <label for="is_pantawid_no">No</label>
                                        </div>
                                        <div class="col-md-6 mt-4 additional-details">
                                            <select class="form-control rounded border-gray-300" id="pantawid_details"
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
                                            <label for="is_person_with_disability">Person with
                                                Disability:<b class="text-red-600">*</b></label>
                                        </div>
                                        <div class="col-md-1 mt-2">
                                            <input type="radio" id="is_person_with_disability_yes"
                                                name="is_person_with_disability" value="1"
                                                {{ old('is_person_with_disability', $child->is_person_with_disability) == '1' ? 'checked' : '' }}>
                                            <label for="is_person_with_disability_yes">Yes</label>
                                        </div>
                                        <div class="col-md-1 mt-2">
                                            <input type="radio" id="is_person_with_disability_no"
                                                name="is_person_with_disability" value="0"
                                                {{ old('is_person_with_disability', $child->is_person_with_disability) == '0' ? 'checked' : '' }}>
                                            <label for="is_person_with_disability_no">No</label>
                                        </div>
                                        <div class="col-md-6 mt-2 additional-details"
                                            id="perspn_with_disability_additionalDetails">
                                            <input type="text" class="form-control rounded border-gray-300"
                                                id="perspn_with_disability_details" name="perspn_with_disability_details"
                                                placeholder="Please specify"
                                                value="{{ old('perspn_with_disability_details', $child->person_with_disability_details) }}"
                                                {{ $child->is_person_with_disability ? '' : 'disabled' }}>
                                            @error('person_with_disability_details')
                                                <span class="text-xs text-red-600">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mt-2">
                                            <label for="is_indigenous_people">Indigenous People (IP):</label>
                                        </div>
                                        <div class="col-md-1 mt-2">
                                            <input type="radio" name="is_indigenous_people"
                                                id="is_indigenous_people_yes" value="1"
                                                {{ old('is_indigenous_people', $child->is_indigenous_people) == '1' ? 'checked' : '' }}>
                                            <label for="is_indigenous_people_yes">Yes</label>
                                        </div>
                                        <div class="col-md-1 mt-2">
                                            <input type="radio" name="is_indigenous_people"
                                                id="is_indigenous_people_no" value="0"
                                                {{ old('is_indigenous_people', $child->is_indigenous_people) == '0' ? 'checked' : '' }}>
                                            <label for="is_indigenous_people_no">No</label>
                                        </div>
                                        <div class="col-md-6 mt-2" style="visibility: hidden">
                                            <input type="text" class="form-control rounded border-gray-300"
                                                name="spaceonly">
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
                                            <input type="text" class="form-control rounded border-gray-300"
                                                name="spaceonly">
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
                                            <input type="text" class="form-control rounded border-gray-300"
                                                name="spaceonly">
                                        </div>

                                        <div class='col-md-1 mt-4 text-gray-400 text-xs'>Address</div>
                                        <div class='col-md-11 mt-8 text-gray-400 text-xs'>
                                            <hr>
                                        </div>



                                        <div class="col-md-6 mt-2 text-sm">
                                            <label for="region">Region<b class="text-red-600">*</b></label>
                                            <select class="form-control rounded border-gray-300" id="region">
                                                <option value="0">Region XI</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6 mt-3 text-sm">
                                            <label for="province">Province<b class="text-red-600">*</b></label>
                                            <select class="form-control rounded border-gray-300" id="province"
                                                name="province_psgc" onchange="filterCities()">
                                                <option value="" selected>Select Province</option>
                                                @foreach ($provinces as $psgc => $name)
                                                    <option value="{{ $psgc }}"
                                                        {{ old('province_psgc', $psgcRecord->province_psgc) == $psgc ? 'selected' : '' }}>
                                                        {{ $name }}</option>
                                                @endforeach
                                            </select>
                                            @error('province_psgc')
                                                <span class="text-xs text-red-600">{{ $message }}</span>
                                            @enderror
                                        </div>                                        

                                        <div class="col-md-6 mt-2 text-sm">
                                            <label for="city">City/Municipality<b class="text-red-600">*</b></label>
                                            <select class="form-control rounded border-gray-300" id="city" name="city_name_psgc" onchange="filterBarangays()">
                                                <option value="" selected>Select City/Municipality</option>
                                                @foreach ($cities as $psgc => $name)
                                                    <option value="{{ $psgc }}" {{ old('city_name_psgc', $psgcRecord->city_name_psgc) == $psgc ? 'selected' : '' }}>
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
                                                <option value="" selected>Select Barangay</option>
                                                @foreach ($barangays as $psgc => $name)
                                                    <option value="{{ $psgc }}" {{ old('brgy_psgc', $psgcRecord->brgy_psgc) == $psgc ? 'selected' : '' }}>
                                                        {{ $name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('brgy_psgc')
                                                <span class="text-xs text-red-600">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        

                                        <input type="hidden" id="psgc_id" name="psgc_id"
                                            value="{{ $child->psgc_id }}">

                                        <div class="col-md-6 mt-2 text-sm">
                                            <label for="address">House No./ Street/ Purok<b class="text-red-600">*</b></label>
                                            <input type="text" class="form-control rounded border-gray-300"
                                                id="address" name='address'
                                                value="{{ old('address', $child->address) }}">
                                            @error('address')
                                                <span class="text-xs text-red-600">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <label for="zip_code">Zip Code<b class="text-red-600">*</b></label>
                                            <input type="text" class="form-control rounded border-gray-300"
                                                id="zip_code" name='zip_code'
                                                value="{{ old('zip_code', $child->zip_code) }}" maxlength="4">
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
                                            <select class="form-control rounded border-gray-300"
                                                id="child_development_center_id" name="child_development_center_id">
                                                <option value="">Select a center</option>
                                                @foreach ($centers as $center)
                                                    <option value="{{ $center->id }}"
                                                        {{ $center->id == old('child_development_center_id', $child->child_development_center_id) ? 'selected' : '' }}>
                                                        {{ $center->center_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('child_development_center_id')
                                                <span class="text-xs text-red-600">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-md-12 mt-4 text-right">
                                            <button type="button" class="text-white bg-blue-600 rounded px-3 min-h-9"
                                                data-bs-toggle="modal" data-bs-target="#verticalycentered">Submit</button>
                                            <a href="{{ route('child.index') }}"></a><button type="reset"
                                                class="text-white bg-gray-600 rounded px-3 min-h-9">Cancel</button></a>
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
                                                        Are you sure you want to save these update?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit"
                                                            class="text-white bg-blue-600 rounded px-3 min-h-9">Confirm</button>
                                                        <button type="button"
                                                            class="text-white bg-gray-600 rounded px-3 min-h-9"
                                                            data-bs-dismiss="modal">Close</button>
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
            </section>
        </div>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                                additionalDetailsElement.setAttribute('required', 'required');
                            } else if (radio.value === '0' && radio.checked) {
                                additionalDetailsSelect.disabled = true;
                                additionalDetailsElement.removeAttribute('required');
                            }
                        });
                    });

                }
                // Apply the function to each set of radio buttons and additional details
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
        
                    // Clear existing options
                    citySelect.innerHTML = '<option value="">Select City/Municipality</option>';
                    barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
        
                    if (provincePsgc) {
                        citySelect.style.display = 'block'; // Show the city dropdown
                        if (locations.cities[provincePsgc]) {
                            locations.cities[provincePsgc].forEach(city => {
                                const option = document.createElement('option');
                                option.value = city.city_name_psgc;
                                option.text = city.city_name;
                                citySelect.appendChild(option);
                            });
                        }
                    } else {
                        citySelect.style.display = 'disabled'; // Hide the city dropdown if no province is selected
                    }
        
                    // Reset city and barangay selects
                    citySelect.value = '';
                    barangaySelect.value = '';
                    barangaySelect.style.display = 'disabled'; // Hide barangay dropdown by default
                }
        
                function filterBarangays() {
                    const cityPsgc = citySelect.value;
        
                    // Clear existing options
                    barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
        
                    if (cityPsgc) {
                        barangaySelect.style.display = 'block'; // Show the barangay dropdown
                        if (locations.barangays[cityPsgc]) {
                            locations.barangays[cityPsgc].forEach(barangay => {
                                const option = document.createElement('option');
                                option.value = barangay.brgy_psgc;
                                option.text = barangay.brgy_name;
                                barangaySelect.appendChild(option);
                            });
                        }
                    } else {
                        barangaySelect.style.display = 'disabled'; // Hide the barangay dropdown if no city is selected
                    }
                }
        
                // Initialize visibility based on current selection
                filterCities();
                filterBarangays();
            });
        </script>
    </main><!-- End #main -->
