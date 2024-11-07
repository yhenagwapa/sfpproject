<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SFP Onse</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />


    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="../img/dswd.png" rel="icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Fonts and Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="{{ asset('js/main.js') }}" defer></script>


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
        <main id="register-main" class="register-main">
            <div class="pagetitle">
                <nav style="--bs-breadcrumb-divider: '>';">
                    <ol class="breadcrumb mb-3 p-0">
                        <li class="breadcrumb-item italic"><a href="{{ route('login') }}">Go back to Login</a></li>
                    </ol>
                </nav>
            </div>

            <div class="wrapper">
                <section class="section">
                    <div class="row">
                        <div class="w-full md:w-full">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Register</h5>
                                    <form class="row" method="post" action="{{ route('register') }}">
                                        @csrf
                                        <div class="flex flex-wrap">
                                            <div class='w-full md:w-2/12 px-3 mt-2 text-gray-400 text-xs'>Personal Information</div>
                                            <div class='w-full md:w-10/12 px-3 mt-3 text-gray-400 text-xs'>
                                                <hr>
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap">
                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm">
                                                <label for="firstname">First Name</label><label for="firstname"
                                                    class="text-red-600">*</label>
                                                <input type="text"
                                                    class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                                    id="firstname" name='firstname' value="{{ old('firstname') }}" autofocus>
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
                                        </div>

                                        <div class="flex flex-wrap">
                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm">
                                                <label for="contact_no">Contact Number<b class="text-red-600">*</b></label>
                                                <input type="text"
                                                    class="form-control rounded border-gray-300"
                                                    id="contact_no" name='contact_no' value="{{ old('contact_no') }}" maxlength="11" >
                                                @error('contact_no')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="flex flex-wrap">
                                            <div class='w-full md:w-2/12 px-3 mt-4 text-gray-400 text-xs'>Address</div>
                                            <div class='w-full md:w-10/12 px-3 mt-8 text-gray-400 text-xs'>
                                                <hr>
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap">
                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm">
                                                <label for="region">Region</label><label for="region"
                                                    class="text-red-600">*</label>
                                                <select
                                                    class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                                    id="region" disabled>
                                                    <option value="110000000" selected>Region XI</option>
                                                </select>
                                            </div>

                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm">
                                                <label for="province">Province</label><label for="province"
                                                    class="text-red-600">*</label>
                                                <select class="form-control" id="province" name="province_psgc">
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
                                        </div>
                                        <div class="flex flex-wrap">
                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm">
                                                <label for="city">City/Municipality</label><label for="city"
                                                    class="text-red-600">*</label>
                                                <select class="form-control" id="city" name="city_name_psgc">
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
                                        
                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm">
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
                                        </div>

                                        <div class="flex flex-wrap">
                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm">
                                                <label for="address">Address<b class="text-red-600">*</b></label>
                                                <input name="address" type="text" class="form-control rounded border-gray-300 w-full" id="address" value="{{ old('address') }}">
                                                @error('address')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div>
                                       

                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm">
                                                <label for="zip_code">Zip Code<b class="text-red-600">*</b></label>
                                                <input name="zip_code" type="text" class="form-control rounded border-gray-300 w-full" id="zip_code" value="{{ old('zip_code') }}" maxlength="4">
                                                @error('zip_code')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap">
                                            <div class='w-full md:w-2/12 px-3 mt-4 text-gray-400 text-xs'>Account Information</div>
                                            <div class='w-full md:w-10/12 px-3 mt-8 text-gray-400 text-xs'>
                                                <hr>
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap">
                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm">
                                                <label for="email">Email<b class="text-red-600">*</b></label>
                                                <input name="email" type="text" class="form-control rounded border-gray-300 w-full" id="email" value="{{ old('email')}}">
                                                @error('email')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        

                                            {{-- <div class="w-full md:w-1/2 px-3 mt-3 text-sm">
                                                <label for="role_id">Role<b class="text-red-600">*</b></label>
                                                <select class="form-control rounded border-gray-300 w-full" id="role_id" name="role_id">
                                                    <option value="" disabled selected>Select role</option>
                                                    @foreach ($roles as $role)
                                                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id}}>
                                                            {{ $role->name }}
                                                        </option>    
                                                    @endforeach
                                                </select>
                                                @error('role_id')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div> --}}
                                        </div>

                                        <div class="flex flex-wrap">
                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm">
                                                <label for="password">Password<b class="text-red-600">*</b></label>
                                                <input name="password" type="password" class="form-control rounded border-gray-300 w-full" id="password" value="{{ old('password') }}">
                                                @error('password')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm">
                                                <label for="password_confirmation">Confirm Password<b class="text-red-600">*</b></label>
                                                <input name="password_confirmation" type="password" class="form-control rounded border-gray-300 w-full" id="password_confirmation" value="{{ old('password_confirmation') }}">
                                                @error('password')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap justify-end w-full md:w-full">
                                            <div class="mt-4">
                                                <button type="submit"
                                                    class="text-white bg-blue-600 rounded px-3 min-h-9">Register</button>
                                                <button type="reset"
                                                    class="text-white bg-gray-600 rounded px-3 min-h-9">Cancel</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
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
                });
            </script>
            
            
        </main>
        

        {{-- <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a> --}}

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

        @vite(['resources/js/app.js'])
    </div>
    <footer id="register-footer" class="register-footer">
        <div class="copyright">
            &copy; Copyright <strong><span>NiceAdmin</span></strong>. All Rights Reserved
        </div>
        <div class="credits">
            <!-- All the links in the footer should remain intact. -->
            <!-- You can delete the links only if you purchased the pro version. -->
            <!-- Licensing information: https://bootstrapmade.com/license/ -->
            <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
            Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
        </div>
    </footer><!-- End Footer -->
</body>

</html>

