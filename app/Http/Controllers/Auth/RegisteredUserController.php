<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Psgc;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use App\Http\Requests\StoreUserRequest;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $roles = Role::all();
        $psgc = new Psgc();

        $provinces = $psgc->getProvinces();
        $cities = $psgc->allCities();
        $barangays = $psgc->allBarangays();


        return view('auth.register', compact('roles', 'provinces', 'cities', 'barangays'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(StoreUserRequest $request)
    {
        $request->validated();

        $psgc = Psgc::where('region_psgc', $request->input('region_psgc'))
            ->where('province_psgc', $request->input('province_psgc'))
            ->where('city_name_psgc', $request->input('city_name_psgc'))
            ->where('brgy_psgc', $request->input('brgy_psgc'))
            ->first();

        $user = User::create([
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'lastname' => $request->lastname,
            'extension_name' => $request->extension_name,
            'contact_number' => $request->contact_number,
            'psgc_id' => $psgc->psgc_id,
            'address' => '-',
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $user->assignRole('child development worker');

        return redirect('login')->with('success', 'Registration successful. Kindly inform the admin to activate your account.');
    }
}
