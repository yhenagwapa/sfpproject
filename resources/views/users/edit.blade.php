@extends('layouts.app')

@section('content')

    <div class="pagetitle">
        <nav style="--bs-breadcrumb-divider: '>';">
            <ol class="breadcrumb mb-3 p-0">
                <li class="breadcrumb-item active">Profile</li>
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
                }, 3000);
            }
            if (alert2) {
                // Automatically close the alert after 3 seconds (3000 milliseconds)
                setTimeout(function() {
                    var bsAlert2 = new bootstrap.Alert(alert2);
                    bsAlert2.close();
                }, 3000);
            }
        });
    </script>

    <div class="wrapper">
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title uppercase">{{ $user->full_name}}</h5>

                            <form class="row" action="{{ route('users.update') }}" method="POST" autocomplete="off">
                                @csrf
                                @method("patch")

                                <input type="hidden" name="user_id" value="{{ $user->id }}">

                                <div class='col-md-12 mt-3 text-gray-400 text-xs'>
                                    Personal Information<hr>
                                </div>

                                <div class="col-md-6 mt-3 text-sm">
                                    <label for="firstname">First Name</label><b
                                        class="text-red-600">*</b>
                                    <input type="text"
                                        class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                        id="firstname" name='firstname' value="{{ old('firstname', $user->firstname) }}">
                                    @error('firstname')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mt-3 text-sm">
                                    <label for="middlename">Middle Name</label>
                                    <input type="text"
                                        class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                        id="middlename" name='middlename' value="{{ old('middlename', $user->middlename) }}">
                                    @error('middlename')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mt-3 text-sm">
                                    <label for="lastname">Last Name</label><b
                                        class="text-red-600">*</b>
                                    <input type="text"
                                        class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                        id="lastname" name='lastname' value="{{ old('lastname',$user->lastname) }}" autofocus>
                                    @error('lastname')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mt-2 text-sm">
                                    <label for="extension_name">Extension Name</label>
                                    <select class="form-control rounded border-gray-300" id="extension_name"
                                        name="extension_name">
                                        <option value=""></option>
                                        @foreach ($extNameOptions as $value1 => $label1)
                                            <option value="{{ $value1 }}"
                                                {{ old('extension_name', $user->extension_name) == $value1 ? 'selected' : '' }}>
                                                {{ $label1 }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mt-3 text-sm">
                                    <label for="contact_number">Contact Number</label><b
                                        class="text-red-600">*</b>
                                    <input type="text"
                                        class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                        id="contact_number" name='contact_number' value="{{ old('contact_number', $user->contact_number) }}">
                                    @error('contact_number')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mt-2 text-sm" style="visibility: hidden">
                                    <input type="text"
                                        class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                        name="spaceonly">
                                </div>

                                <div class='col-md-12 mt-4 text-gray-400 text-xs'>
                                    Address<hr>
                                </div>

                                <div class="col-md-6 mt-3 text-sm">
                                    <label for="region">Region</label><b
                                        class="text-red-600">*</b>
                                    <select
                                        class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                        id="region" disabled>
                                        <option value="110000000" selected>Region XI</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mt-3 text-sm">
                                    <label for="province">Province<b class="text-red-600">*</b></label>
                                    <select class="form-control rounded border-gray-300" id="province"
                                        name="province_psgc">
                                        <option value="" selected>Select Province</option>
                                        @foreach ($provinces as $psgc => $name)
                                            <option value="{{ $psgc }}"
                                                {{ $psgc == old('province_psgc', $psgcRecord->province_psgc) ? 'selected' : '' }}>
                                                {{ $name }}</option>
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
                                        <option value="" selected>Select City/Municipality</option>
                                        @foreach ($cities as $psgc => $name)
                                            <option value="{{ $psgc }}"
                                                {{ $psgc == old('city_name_psgc', $psgcRecord->city_name_psgc) ? 'selected' : '' }}>
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
                                            <option value="{{ $psgc }}"
                                                {{ $psgc == old('brgy_psgc', $psgcRecord->brgy_psgc) ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('brgy_psgc')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>

                                <input type="hidden" id="psgc_id" name="psgc_id" value="{{ $user->psgc_id }}">

                                <div class='col-md-12 mt-4 text-gray-400 text-xs'>
                                    Account Information<hr>
                                </div>

                                <div class="col-md-6 mt-3 text-sm">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control rounded border-gray-300" id="email" name="email" value="{{ $user->email }}" disabled>
                                </div>

                                <div class="col-md-6 mt-3 text-sm">
                                    <label for="roles">Role</label>
                                        <select class="form-control" id="roles" name="roles">
                                            <option value="{{ $userRole }}" selected readonly>
                                                {{ $userRole }}
                                            </option>
                                        </select>
                                </div>

                                <div class="col-md-6 mt-3 text-sm relative">
                                    <label for="password">Password</label>
                                    <input name="password" type="password" autocomplete="off"
                                        class="password form-control rounded border-gray-300 w-full pr-10"
                                        id="password">
                                    <button type="button" class="absolute top-7 right-5 text-gray-500"
                                        onclick="togglePassword('password', this)">
                                        <span class="icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5"
                                                stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                            </svg>
                                        </span>
                                    </button>
                                    @error('password')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mt-3 text-sm relative">
                                    <label for="password_confirmation">Confirm Password</label>
                                    <input name="password_confirmation" type="password" autocomplete="off"
                                        class="password form-control rounded border-gray-300 w-full pr-10"
                                        id="password_confirmation"
                                        >
                                    <button type="button" class="absolute top-7 right-5 text-gray-500"
                                        onclick="togglePassword('password_confirmation', this)">
                                        <span class="icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5"
                                                stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                            </svg>
                                        </span>
                                    </button>
                                    @error('password')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>



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

                                                    Are you sure you want to save these updates?

                                            </div>
                                            <div class="modal-footer">
                                                    <button type="submit" class="text-white bg-blue-600 rounded px-3 min-h-9">Confirm</button>

                                                <button type="button" class="text-white bg-gray-600 rounded px-3 min-h-9" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @vite(['resources/js/app.js'])
    {{-- city and barangay  --}}
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
                    if (selectedBarangay && locations.barangays[cityPsgc].some(barangay => barangay.psgc == selectedBarangay)) {
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
        });
    </script>
    {{-- toggle password --}}
    <script>
        function togglePassword(fieldId, button) {
            const input = document.getElementById(fieldId); // Target the input field
            const iconContainer = button.querySelector('.icon'); // Target the icon span

            if (input.type === "password") {
                input.type = "text"; // Change input type to text
                // Set the open eye icon
                iconContainer.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                `;
            } else {
                input.type = "password"; // Change input type to password
                // Set the closed eye icon
                iconContainer.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                `;
            }
        }
    </script>
@endsection
