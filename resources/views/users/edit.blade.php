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
                            <h5 class="card-title">{{ $user->full_name}}</h5>

                            <form class="row" action="{{ route('users.update', $user->id) }}" method="post">
                                @csrf
                                @method("PUT")

                                <div class='col-md-2 mt-3 text-gray-400 text-xs'>Personal Information</div>
                                        <div class='col-md-10 mt-6 text-gray-400 text-xs'>
                                            <hr>
                                        </div>

                                <div class="col-md-6 mt-3 text-sm">
                                    <label for="firstname">First Name</label><b
                                        class="text-red-600">*</b>
                                    <input type="text"
                                        class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                        id="firstname" name='firstname' value="{{ $user->firstname }}" autofocus>
                                    @error('firstname')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mt-3 text-sm">
                                    <label for="middlename">Middle Name</label>
                                    <input type="text"
                                        class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                        id="middlename" name='middlename' value="{{ $user->middlename }}" autofocus>
                                    @error('middlename')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mt-3 text-sm">
                                    <label for="lastname">Last Name</label><b
                                        class="text-red-600">*</b>
                                    <input type="text"
                                        class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                        id="lastname" name='lastname' value="{{ $user->lastname }}" autofocus>
                                    @error('lastname')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mt-3 text-sm">
                                    <label for="extension_name">Extension Name</label>
                                    <input type="text"
                                        class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                        id="extension_name" name='extension_name' value="{{ $user->extension_name }}" autofocus>
                                    @error('extension_name')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mt-3 text-sm">
                                    <label for="contact_no">Contact Number</label><b
                                        class="text-red-600">*</b>
                                    <input type="text"
                                        class="form-control required:border-red-500 invalid:border-red-500 rounded border-gray-300"
                                        id="contact_no" name='contact_no' value="{{ $user->contact_no }}" autofocus>
                                    @error('contact_no')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
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
                                        name="province_psgc" onchange="filterCities()">
                                        <option value="" selected>Select Province</option>
                                        {{-- @foreach ($provinces as $psgc => $name)
                                            <option value="{{ $psgc }}"
                                                {{ $psgc == old('province_psgc', $psgcRecord->province_psgc) ? 'selected' : '' }}>
                                                {{ $name }}</option>
                                        @endforeach --}}
                                    </select>
                                    @error('province_psgc')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>                                        

                                <div class="col-md-6 mt-2 text-sm">
                                    <label for="city">City/Municipality<b class="text-red-600">*</b></label>
                                    <select class="form-control rounded border-gray-300" id="city" name="city_name_psgc" onchange="filterBarangays()">
                                        <option value="" selected>Select City/Municipality</option>
                                        {{-- @foreach ($cities as $psgc => $name)
                                            <option value="{{ $psgc }}" {{ $psgc == old('city_name_psgc', $psgcRecord->city_name_psgc) ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach --}}
                                    </select>
                                    @error('city_name_psgc')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mt-2 text-sm">
                                    <label for="barangay">Barangay<b class="text-red-600">*</b></label>
                                    <select class="form-control rounded border-gray-300" id="barangay" name="brgy_psgc">
                                        <option value="" selected>Select Barangay</option>
                                        {{-- @foreach ($barangays as $psgc => $name)
                                            <option value="{{ $psgc }}" {{ $psgc == old('brgy_psgc', $psgcRecord->brgy_psgc) ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach --}}
                                    </select>
                                    @error('brgy_psgc')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                                

                                {{-- <input type="hidden" id="psgc_id" name="psgc_id"
                                    value="{{ $child->psgc_id }}"> --}}

                                <div class="col-md-6 mt-2 text-sm">
                                    <label for="address">House No./ Street/ Purok<b class="text-red-600">*</b></label>
                                    <input type="text" class="form-control rounded border-gray-300"
                                        id="address" name='address'
                                        value="{{ old('address', $user->address) }}">
                                    @error('address')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mt-2">
                                    <label for="zip_code">Zip Code<b class="text-red-600">*</b></label>
                                    <input type="text" class="form-control rounded border-gray-300"
                                        id="zip_code" name='zip_code'
                                        value="{{ old('zip_code', $user->zip_code) }}" maxlength="4">
                                    @error('zip_code')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class='col-md-3 mt-4 text-gray-400 text-xs'>Child Development Center or Supervised Neighborhood Play</div>
                                    <div class='col-md-9 mt-8 text-gray-400 text-xs'>
                                        <hr>
                                    </div>

                                <div class="col-md-6 mt-3 text-sm">
                                    <label for="email">Email<b class="text-red-600">*</b></label>
                                    <input type="email" class="form-control rounded border-gray-300" id="email" name="email" value="{{ $user->email }}">
                                        @error('email')
                                            <span class="text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                </div>

                                <div class="col-md-6 mt-3 text-sm">
                                    <label for="password">Password<b class="text-red-600">*</b></label>
                                    <input type="password" class="form-control rounded border-gray-300" id="password" name="password">
                                    @error('password')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mt-3 text-sm">
                                    <label for="password_confirmation">Confirm Password<b class="text-red-600">*</b></label>
                                    <input type="password" class="form-control rounded border-gray-300" id="password_confirmation" name="password_confirmation">
                                    @error('password')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- <div class="col-md-6 mt-3 text-sm">
                                    <label for="roles" class="col-md-4 col-form-label text-md-end text-start">Roles</label>
                                    <div class="col-md-6">           
                                        <select class="form-select @error('roles') is-invalid @enderror" multiple aria-label="Roles" id="roles" name="roles[]">
                                            @forelse ($roles as $role)

                                                @if ($role!='Super Admin')
                                                <option value="{{ $role }}" {{ in_array($role, $userRoles ?? []) ? 'selected' : '' }}>
                                                    {{ $role }}
                                                </option>
                                                @else
                                                    @if (Auth::user()->hasRole('Super Admin'))   
                                                    <option value="{{ $role }}" {{ in_array($role, $userRoles ?? []) ? 'selected' : '' }}>
                                                        {{ $role }}
                                                    </option>
                                                    @endif
                                                @endif

                                            @empty

                                            @endforelse
                                        </select>
                                        @if ($errors->has('roles'))
                                            <span class="text-danger">{{ $errors->first('roles') }}</span>
                                        @endif
                                    </div>
                                </div> --}}
                                
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
@endsection