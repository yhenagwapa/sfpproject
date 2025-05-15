<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rules;
use App\Models\Psgc;

class UserController extends Controller
{
    /**
     * Instantiate a new UserController instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:create-user|edit-user-profile|delete-user', ['only' => ['index','show']]);
        $this->middleware('permission:create-user', ['only' => ['create','store']]);
        $this->middleware('permission:edit-user-profile', ['only' => ['edit','update']]);
        $this->middleware('permission:delete-user', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $roles = Role::all();

        $search = $request->get('search');
        // Start with the query builder
        $query = User::query();

        // If not admin, filter by city
        if (!$request->user()->hasRole('admin')) {
            $psgcCity = Psgc::find(auth()->user()->psgc_id)->city_name_psgc;

            // Apply left join and where clause
            $query->leftJoin('psgcs', 'psgcs.psgc_id', '=', 'users.psgc_id')
                ->where('psgcs.city_name_psgc', $psgcCity)
                ->select('users.*'); // Optional: make sure only User columns are selected
        }

        // Get the result
        $users = $query->get();


        return view('users.index', compact('users', 'roles', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('users.create', [
            'roles' => Role::pluck('name')->all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['password'] = Hash::make($request->password);

        $user = User::create($input);
        $user->assignRole($request->roles);

        return redirect()->route('users.index')
                ->withSuccess('New user is added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        session(['editing_user_id' => $request->input('user_id')]);

        return redirect()->route('users.edit');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $userID = session('editing_user_id');

        $user = User::findOrFail($userID);
        $psgc = new Psgc();

        $psgcRecord = Psgc::where('psgc_id', $user->psgc_id)->first();

        $provinces = $psgc->getProvinces();
        $cities = $psgc->allCities();
        $barangays = $psgc->allBarangays();

        $extNameOptions = [
            'Jr' => 'Jr',
            'Sr' => 'Sr',
            'I' => 'I',
            'II' => 'II',
            'III' => 'III',
            'IV' => 'IV',
            'V' => 'V',
            'VI' => 'VI',
            'VII' => 'VII',
            'VIII' => 'VIII',
            'IX' => 'IX',
            'X' => 'X',
        ];

        if ($user->hasRole('admin')){
            if($user->id != auth()->user()->id){
                abort(403, 'USER DOES NOT HAVE THE RIGHT PERMISSIONS');
            }
        }

        return view('users.edit', [
            'user' => $user,
            'roles' => Role::pluck('name')->all(),
            'userRole' => $user->roles->pluck('name')->first(),
            'psgcRecord' => $psgcRecord,
            'provinces' => $provinces,
            'cities' => $cities,
            'barangays' => $barangays,
            'extNameOptions' => $extNameOptions
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        $userID = session('editing_user_id');

        $user = User::findOrFail($userID);

        $psgc = Psgc::where('province_psgc', $request->province_psgc)
            ->where('city_name_psgc', $request->city_name_psgc)
            ->where('brgy_psgc', $request->brgy_psgc)
            ->first();

        $request->merge(['psgc_id' => $psgc->psgc_id]);

        $user->fill($request->only(['firstname', 'middlename', 'lastname','extension_name', 'contact_number', 'psgc_id']));

        if($user->isClean()){
            return redirect()->route('child.index')->with('warning', 'No changes were made.');
        }

        $query = User::where('firstname', $validatedData['firstname'])
            ->where('middlename', $validatedData['middlename'])
            ->where('lastname', $validatedData['lastname'])
            ->where('id', '!=', $user->id);

        if (isset($validatedData['extension_name'])) {
            $query->where('extension_name', $validatedData['extension_name']);
        }

        $existingUser= $query->first();

        if ($existingUser) {
            return redirect()->back()->with('error', 'User already exists.');
        }

        if ($psgc) {
            $validatedData['psgc_id'] = $psgc->psgc_id;
        } else {
            return redirect()->back()->withErrors(['psgc' => 'Selected location is not valid.']);
        }

        if(!empty($request->password)){
            $validatedData['password'] = Hash::make($request->password);
        }else{
            $validatedData = $request->except('password');
        }

        $user->update($validatedData);

        $user->syncRoles($request->roles);

        return redirect()->back()->withSuccess('User is updated successfully.');
    }

    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|string|in:inactive,active,deactivated',
        ]);

        $user->update([
            'status' => $request->status,
        ]);

        return redirect()->back()
                ->withSuccess('User status updated successfully.');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::find($request->role_id);

        if ($role) {
            $user->syncRoles([$role->name]);
        }

        return redirect()->back()
                ->withSuccess('User role updated successfully.');
    }

    public function resetPassword(User $user)
    {
        $user->password = bcrypt('Sfp@12345');
        $user->save();

        return redirect()->back()
                ->withSuccess('Password reset successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->hasRole('admin') || $user->id == auth()->user()->id)
        {
            abort(403, 'USER DOES NOT HAVE THE RIGHT PERMISSIONS');
        }

        $user->syncRoles([]);
        $user->delete();
        return redirect()->route('users.index')
                ->withSuccess('User is deleted successfully.');
    }
}
