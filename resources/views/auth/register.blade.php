<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('https://172.31.176.49/sfpproject/public') }}">

    <title>SFP Onse</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />


    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="{{ asset('img/SFP-LOGO-2024.png') }}" rel="icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    <!-- Fonts and Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])


    {{-- * Template Name: NiceAdmin
        * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
        * Updated: Apr 20 2024 with Bootstrap v5.3.3
        * Author: BootstrapMade.com
        * License: https://bootstrapmade.com/license/ --}}

</head>

<body class="font-sans antialiased">
    <div class="flex justify-center items-center">
        <div class="wrapper">
            <main id="register-main" class="register-main">
                <div class="pagetitle">
                    <nav style="--bs-breadcrumb-divider: '>';">
                        <ol class="breadcrumb mb-3 p-0">
                            <li class="breadcrumb-item italic"><a href="{{ route('login') }}">Go back to Login</a></li>
                        </ol>
                    </nav>
                </div>


                <section class="section">
                    <div class="row">
                        <div class="w-full md:w-full">
                            <div class="card">
                                <div class="card-body">

                                    <h5 class="card-title">Register</h5>
                                    <form class="row" method="post" action="{{ route('register') }}">
                                        @csrf
                                        <div class="flex flex-wrap">
                                            <div class='w-full px-3 mt-2 text-gray-400 text-xs'>Personal Information
                                                <hr>
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap">
                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm">
                                                <label for="firstname">First Name</label><label for="firstname"
                                                    class="text-red-600">*</label>
                                                <input type="text"
                                                    class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                                    id="firstname" name='firstname' value="{{ old('firstname') }}"
                                                    autofocus>
                                                @error('firstname')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm">
                                                <label for="middlename">Middle Name</label>
                                                <input type="text"
                                                    class="form-control invalid:border-red-500 rounded border-gray-300"
                                                    id="middlename" name='middlename' value="{{ old('middlename') }}">
                                                @error('middlename')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap">
                                            <div class="w-full md:w-1/2 px-3 mt-2 text-sm">
                                                <label for="lastname">Last Name</label><label for="lastname"
                                                    class="text-red-600">*</label>
                                                <input type="text"
                                                    class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                                    id="lastname" name='lastname' value="{{ old('lastname') }}">
                                                @error('lastname')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="w-full md:w-1/2 px-3 mt-2 text-sm">
                                                <label for="extension_name">Extension Name</label>
                                                <select
                                                    class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                                    id="extension_name" name='extension_name'>
                                                    <option value="" disabled selected></option>
                                                    <option value="Jr"
                                                        {{ old('extension_name') == 'jr' ? 'selected' : '' }}>Jr
                                                    </option>
                                                    <option value="Sr"
                                                        {{ old('extension_name') == 'sr' ? 'selected' : '' }}>Sr
                                                    </option>
                                                    <option value="I"
                                                        {{ old('extension_name') == 'i' ? 'selected' : '' }}>I</option>
                                                    <option value="II"
                                                        {{ old('extension_name') == 'ii' ? 'selected' : '' }}>II
                                                    </option>
                                                    <option value="III"
                                                        {{ old('extension_name') == 'iii' ? 'selected' : '' }}>III
                                                    </option>
                                                    <option value="IV"
                                                        {{ old('extension_name') == 'iv' ? 'selected' : '' }}>IV
                                                    </option>
                                                    <option value="V"
                                                        {{ old('extension_name') == 'v' ? 'selected' : '' }}>V</option>
                                                    <option value="VI"
                                                        {{ old('extension_name') == 'vi' ? 'selected' : '' }}>VI
                                                    </option>
                                                    <option value="VII"
                                                        {{ old('extension_name') == 'vii' ? 'selected' : '' }}>VII
                                                    </option>
                                                    <option value="VIII"
                                                        {{ old('extension_name') == 'viii' ? 'selected' : '' }}>VIII
                                                    </option>
                                                    <option value="IX"
                                                        {{ old('extension_name') == 'ix' ? 'selected' : '' }}>IX
                                                    </option>
                                                    <option value="X"
                                                        {{ old('extension_name') == 'x' ? 'selected' : '' }}>X</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap">
                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm">
                                                <label for="contact_number">Contact Number<b
                                                        class="text-red-600">*</b></label>
                                                <input type="text" class="form-control rounded border-gray-300"
                                                    id="contact_number" name='contact_number'
                                                    value="{{ old('contact_number') }}" maxlength="11">
                                                @error('contact_number')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap mt-4">
                                            <div class='w-full px-3 mt-2 text-gray-400 text-xs'>Address
                                                <hr>
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap">
                                            <div class="w-full px-3 mt-3 text-sm">
                                                <label for="address">Address<b class="text-red-600">*</b></label>
                                                <input name="address" type="text"
                                                    class="form-control rounded border-gray-300 w-full" id="address"
                                                    value="{{ old('address') }}">
                                                @error('address')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap">
                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm">
                                                <label for="region">Region</label><label for="region"
                                                    class="text-red-600">*</label>
                                                <select
                                                    class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                                    id="region_psgc" name="region_psgc" disabled>
                                                    <option value="110000000" selected>Region XI</option>
                                                </select>
                                            </div>

                                            <input type="hidden" name="region_psgc" value="110000000">

                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm">
                                                <label for="province">Province</label><label for="province"
                                                    class="text-red-600">*</label>
                                                <select class="form-control" id="province" name="province_psgc">
                                                    <option value="" selected>Select Province</option>
                                                    @foreach ($provinces as $psgc => $name)
                                                        <option value="{{ $psgc }}"
                                                            {{ old('province_psgc', session('step1Data.province_psgc')) == $psgc ? 'selected' : '' }}>
                                                            {{ $name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('province_psgc')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap">
                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm">
                                                <label for="city">City/Municipality</label><label for="city"
                                                    class="text-red-600">*</label>
                                                <select class="form-control" id="city" name="city_name_psgc">
                                                    <option value="" selected>Select City/Municipality</option>
                                                    @foreach ($cities as $psgc => $name)
                                                        <option value="{{ $psgc }}"
                                                            {{ old('city_name_psgc', session('step1Data.city_name_psgc')) == $psgc ? 'selected' : '' }}>
                                                            {{ $name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('city_name_psgc')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm">
                                                <label for="barangay">Barangay</label><label for="barangay"
                                                    class="text-red-600">*</label>
                                                <select class="form-control" id="barangay" name="brgy_psgc">
                                                    <option value="" selected>Select Barangay</option>
                                                    @foreach ($barangays as $psgc => $name)
                                                        <option value="{{ $psgc }}"
                                                            {{ old('brgy_psgc', session('step1Data.brgy_psgc')) == $psgc ? 'selected' : '' }}>
                                                            {{ $name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('brgy_psgc')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>



                                        <div class="flex flex-wrap mt-4">
                                            <div class='w-full px-3 mt-2 text-gray-400 text-xs'>Account Information
                                                <hr>
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap">
                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm">
                                                <label for="email">Email<b class="text-red-600">*</b></label>
                                                <input name="email" type="text"
                                                    class="email form-control rounded border-gray-300 w-full"
                                                    id="email" value="{{ old('email') }}">
                                                @error('email')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap">
                                            <!-- Password Field -->
                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm relative">
                                                <label for="password">Password<b class="text-red-600">*</b></label>
                                                <input name="password" type="password"
                                                    class="password form-control rounded border-gray-300 w-full pr-10"
                                                    id="password" value="{{ old('password') }}">
                                                <!-- Toggle Button for Password -->
                                                <button type="button" class="absolute top-7 right-5 text-gray-500"
                                                    onclick="togglePassword('password', this)">
                                                    <span class="icon">
                                                        <!-- Default: Closed Eye Icon -->
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

                                                <div class="mt-1 text-xs text-gray-500">
                                                    <ul>
                                                        <li>Must be at least 8 characters.</li>
                                                        <li>Must include uppercase letter.</li>
                                                        <li>Must include number.</li>
                                                        <li>Must include special character.</li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <!-- Confirm Password Field -->
                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm relative">
                                                <label for="password_confirmation">Confirm Password<b
                                                        class="text-red-600">*</b></label>
                                                <input name="password_confirmation" type="password"
                                                    class="password form-control rounded border-gray-300 w-full pr-10"
                                                    id="password_confirmation"
                                                    value="{{ old('password_confirmation') }}">
                                                <!-- Toggle Button for Confirm Password -->
                                                <button type="button" class="absolute top-7 right-5 text-gray-500"
                                                    onclick="togglePassword('password_confirmation', this)">
                                                    <span class="icon">
                                                        <!-- Default: Closed Eye Icon -->
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
                                        </div>



                                        <div class="flex flex-wrap justify-end w-full md:w-full">
                                            <div class="mt-4">
                                                <button type="button"
                                                    class="text-white bg-blue-600 rounded px-3 min-h-9"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#verticalycentered">Register</button>
                                            </div>
                                        </div>

                                        <div class="modal fade" id="verticalycentered" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title text-red-600">Confirmation</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Are you sure you want to register these details?
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
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

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


            </main>

            <footer id="register-footer" class="register-footer">
                <div class="footer-dswd">
                    &copy; {{ now()->year }} Department of Social Welfare and Development.
                </div>
                {{-- <div class="copyright">
                    &copy; Copyright <strong><span>NiceAdmin</span></strong>. All Rights Reserved
                </div>
                <div class="credits">
                    <!-- All the links in the footer should remain intact. -->
                    <!-- You can delete the links only if you purchased the pro version. -->
                    <!-- Licensing information: https://bootstrapmade.com/license/ -->
                    <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
                    Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
                </div> --}}
            </footer><!-- End Footer -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    @vite(['resources/js/app.js'])

</body>

</html>
