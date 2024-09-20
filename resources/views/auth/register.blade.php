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
    

    <div class="container">
        <main id="main" class="main">
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
                                    <form class="row" method="post" action="">
                                        <div class="flex flex-wrap -mx-3">
                                            <div class='w-full md:w-2/12 px-3 mt-2 text-gray-400 text-xs'>Personal Information</div>
                                            <div class='w-full md:w-10/12 px-3 mt-3 text-gray-400 text-xs'>
                                                <hr>
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap -mx-3">
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
                                        <div class="flex flex-wrap -mx-3">
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

                                        <div class="flex flex-wrap -mx-3">
                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm">
                                                <label for="contact">Contact Number<b class="text-red-600">*</b></label>
                                                <input type="text"
                                                    class="form-control rounded border-gray-300"
                                                    id="contact" name='contact' value="{{ old('contact') }}" autofocus>
                                                @error('contact')
                                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="flex flex-wrap -mx-3">
                                            <div class='w-full md:w-1/12 px-3 mt-4 text-gray-400 text-xs'>Address</div>
                                            <div class='w-full md:w-11/12 px-3 mt-8 text-gray-400 text-xs'>
                                                <hr>
                                            </div>
                                        </div>

                                        

                                        <div class="flex flex-wrap -mx-3">
                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm">
                                                <label for="region">Region<b class="text-red-600">*</b></label>
                                                <select class="form-control rounded border-gray-300 w-full" id="region">
                                                    <option value="110000000">Region XI</option>
                                                </select>
                                            </div>

                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm">
                                                <label for="province">Province<b class="text-red-600">*</b></label>
                                                <div class="">
                                                    <select class="form-control rounded border-gray-300 w-full" id="province">
                                                        
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap -mx-3">
                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm">
                                                <label for="citymuni">City/Municipality<b class="text-red-600">*</b></label>
                                                <select class="form-control rounded border-gray-300 w-full" id="citymuni">
                                                    <option value=""></option>
                                                </select>
                                            </div>
                                        
                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm">
                                                <label for="barangay">Barangay<b class="text-red-600">*</b></label>
                                                <div class="">
                                                    <select class="form-control rounded border-gray-300 w-full" id="barangay">
                                                        <option value=""></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap -mx-3">
                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm">
                                                <label for="address">Address<b class="text-red-600">*</b></label>
                                                <input name="address" type="text" class="form-control rounded border-gray-300 w-full" id="address" value="{{ old('address') }}">
                                            </div>
                                       

                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm">
                                                <label for="zip_code">Zip Code<b class="text-red-600">*</b></label>
                                                <div class="">
                                                    <input name="zip_code" type="text" class="form-control rounded border-gray-300 w-full" id="zip_code" value="{{ old('zip_code') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap -mx-3">
                                            <div class='w-full md:w-2/12 px-3 mt-4 text-gray-400 text-xs'>Account Information</div>
                                            <div class='w-full md:w-10/12 px-3 mt-8 text-gray-400 text-xs'>
                                                <hr>
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap -mx-3">
                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm">
                                                <label for="email">Email<b class="text-red-600">*</b></label>
                                                <input name="email" type="email" class="form-control rounded border-gray-300 w-full" id="email" value="{{ old('email')}}">
                                            </div>
                                        

                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm">
                                                <label for="role_id">Role<b class="text-red-600">*</b></label>
                                                <select class="form-control rounded border-gray-300 w-full" id="role_id">
                                                    <option value="" disabled selected>Select role</option>
                                                    @foreach ($roles as $role)
                                                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id}}>
                                                            {{ $role->name }}
                                                        </option>    
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap -mx-3">
                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm">
                                                <label for="password">Password<b class="text-red-600">*</b></label>
                                                <input name="password" type="password" class="form-control rounded border-gray-300 w-full" id="password" value="{{ old('password') }}">
                                            </div>

                                            <div class="w-full md:w-1/2 px-3 mt-3 text-sm" id="assigned-lgu-container" style="display: none;">
                                                <label for="city_name_psgc">Assigned LGU<b class="text-red-600">*</b></label>
                                                <select class="form-control rounded border-gray-300 w-full" id="role_id">
                                                    <option value="" disabled selected>Select assigned LGU</option>
                                                    @foreach ($lgus as $lgu)
                                                        <option value="{{ $lgu->city_name_psgc }}" {{ old('city_name_psgc') == $lgu->city_name_psgc}}>
                                                            {{ $lgu->city_name }}
                                                        </option>    
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap justify-end -mx-3 w-full md:w-full">
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
                document.addEventListener('DOMContentLoaded', function () {
                    const roleDropdown = document.getElementById('role_id');
                    const assignedLguContainer = document.getElementById('assigned-lgu-container');

                    roleDropdown.addEventListener('change', function () {
                        const selectedRoleId = roleDropdown.value;

                        // Assuming 'lgu focal' has a specific role ID, e.g., 3
                        const lguFocalRoleId = '3'; // Change this to the actual ID of the "lgu focal" role

                        if (selectedRoleId === lguFocalRoleId) {
                            assignedLguContainer.style.display = 'block';
                        } else {
                            assignedLguContainer.style.display = 'none';
                        }
                    });

                    // Trigger the change event on page load to handle previously selected role
                    roleDropdown.dispatchEvent(new Event('change'));
                });
            </script>
            
        </main>
        <footer id="footer" class="footer">
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

        {{-- <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a> --}}

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

        @vite(['resources/js/app.js'])
    </div>
</body>

</html>

