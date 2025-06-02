<?php

namespace App\Http\Controllers;

use App\Models\ChildDevelopmentCenter;
use App\Http\Requests\StoreChildDevelopmentCenterRequest;
use App\Http\Requests\UpdateChildDevelopmentCenterRequest;
use App\Models\UserCenter;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use App\Models\Psgc;
class ChildDevelopmentCenterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:create-child-development-center', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-child-development-center', ['only' => ['edit', 'update']]);options:
    }

    public function index(Request $request)
    {

        if (auth()->user()->hasRole('admin')) {
            $centers = ChildDevelopmentCenter::all();
        } else {
            $userCenters = UserCenter::where('user_id', auth()->id())->get();
            $centerIDs = $userCenters->pluck('child_development_center_id');
            $centers = ChildDevelopmentCenter::whereIn('id', $centerIDs)->get();
        }

        $centersWithRoles = [];

        foreach ($centers as $center) {
            $centerUsers = UserCenter::where('child_development_center_id', $center->id)
                ->pluck('user_id');

            $users = User::whereIn('id', $centerUsers)->get();

            $worker = $users->firstWhere(fn($user) => $user->hasRole('child development worker'));
            $encoder = $users->firstWhere(fn($user) => $user->hasRole('encoder'));
            $focal = $users->firstWhere(fn($user) => $user->hasRole('lgu focal'));
            $coordinator = $users->firstWhere(fn($user) => $user->hasRole('sfp coordinator'));
            $pdo = $users->firstWhere(fn($user) => $user->hasRole('pdo'));

            $centersWithRoles[$center->id] = [
                'center_id' => $center->id,
                'center_name' => $center->center_name,
                'worker' => $worker,
                'encoder' => $encoder,
                'focal' => $focal,
                'coordinator' => $coordinator,
                'pdo' => $pdo,
                'address' => $center->getfulladdress(),
            ];
        }

        return view('centers.index', compact('centersWithRoles', 'centers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create-child-development-center');

        $centers = ChildDevelopmentCenter::all();
        $pdos = User::role('pdo')->where('status','active')->get();
        $workers = User::role('child development worker')->where('status','active')->get();
        $focals = User::role('lgu focal')->where('status','active')->get();
        $coordinators = User::role('sfp coordinator')->where('status','active')->get();
        $encoders = User::role('encoder')->where('status','active')->get();
        $psgc = new Psgc();


        $provinces = $psgc->getProvinces();
        $cities = $psgc->allCities();
        $barangays = $psgc->allBarangays();

        // if not admin, filter provinces and cities
        if (!$request->user()->hasRole('admin')) {

            $psgcCity = Psgc::find(auth()->user()->psgc_id)->city_name_psgc;
            $focals = User::role('lgu focal')->where('status','active') // still on query builder
            ->leftJoin('psgcs', 'psgcs.psgc_id', '=', 'users.psgc_id')
                ->where('psgcs.city_name_psgc', $psgcCity)
                ->select('users.*') // optionally select fields from psgcs too
                ->get();

            $coordinators = User::role('sfp coordinator')->where('status','active') // still on query builder
            ->leftJoin('psgcs', 'psgcs.psgc_id', '=', 'users.psgc_id')
                ->where('psgcs.city_name_psgc', $psgcCity)
                ->select('users.*') // optionally select fields from psgcs too
                ->get();

            $workers = User::role('child development worker')->where('status','active') // still on query builder
            ->leftJoin('psgcs', 'psgcs.psgc_id', '=', 'users.psgc_id')
                ->where('psgcs.city_name_psgc', $psgcCity)
                ->select('users.*') // optionally select fields from psgcs too
                ->get();

            $encoders = User::role('encoder')->where('status','active') // still on query builder
            ->leftJoin('psgcs', 'psgcs.psgc_id', '=', 'users.psgc_id')
                ->where('psgcs.city_name_psgc', $psgcCity)
                ->select('users.*') // optionally select fields from psgcs too
                ->get();

            // Filter the grouped cities collection to only include entries matching the user's city PSGC
            $userCity = Psgc::find(auth()->user()->psgc_id)->city_name_psgc;
            $cities = $cities->map(function ($cityGroup) use ($userCity) {
                return $cityGroup->filter(function ($city) use ($userCity) {
                    return $city['psgc'] === $userCity;
                })->values();
            })->filter(function ($cityGroup) {
                return $cityGroup->isNotEmpty();
            });

            // Keep only the user's province in the list
            $userProvince = Psgc::find(auth()->user()->psgc_id)->province_psgc;
            $provinces = array_filter($provinces, function ($name, $psgc) use ($userProvince) {
                return $psgc === $userProvince;
            }, ARRAY_FILTER_USE_BOTH);

        }

        if ($request->has('province_psgc') && !empty($request->input('province_psgc'))) {
            $province_psgc = $request->input('province_psgc');

            $cities = $psgc->allCities()->get($province_psgc, collect([]));
        }

        if ($request->has('city_name_psgc') && !empty($request->input('city_name_psgc'))) {
            $city_psgc = $request->input('city_name_psgc');
            $barangays = $psgc->getBarangays($city_psgc);
        }

        return view('centers.create', compact('centers', 'pdos', 'workers', 'coordinators' ,'focals', 'encoders', 'provinces', 'cities', 'barangays'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChildDevelopmentCenterRequest $request)
    {
        $this->authorize('create-child-development-center');

        $validatedData = $request->validated();

        // Check if the center already exists
        $centerExists = ChildDevelopmentCenter::where('center_name', $request->center_name)->exists();

        if ($centerExists) {
            return redirect()->back()->with('error', 'Center already exists.');
        }

        $psgc = Psgc::where('province_psgc', $request->province_psgc)
                    ->where('city_name_psgc', $request->city_name_psgc)
                    ->where('brgy_psgc', $request->brgy_psgc)
                    ->first();

        if ($psgc) {
            $psgc_id = $psgc->psgc_id;
        } else {
            return redirect()->back()->withErrors(['msg' => 'Location not found']);
        }

        // dd($validatedData);

        $center = ChildDevelopmentCenter::create([
            'center_name' => $request->center_name,
            'center_type' => $request->center_type,
            'psgc_id' => $psgc_id,
            'address' => $request->address,
            'created_by_user_id' => auth()->id(),
        ]);

        $focal = null;
        $coordinator = null;
        $worker = $request->input('assigned_worker_user_id');
        $encoder = $request->input('assigned_encoder_user_id');

        if(auth()->user()->hasRole('lgu focal')){
            $focal = auth()->id();
            $coordinator = $request->input('assigned_coordinator_user_id');
        } elseif(auth()->user()->hasRole('sfp coordinator')){
            $focal = $request->input('assigned_focal_user_id');
            $coordinator = auth()->id();
        }

        $userIds = array_filter([
            $worker,
            $encoder,
            $focal,
            $coordinator,
        ]);

        $center->users()->sync($userIds);

        return redirect()->route('centers.index')->with('success', 'Child Development Center saved successfully');
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        session(['editing_center_id' => $request->input('center_id')]);

        return redirect()->route('centers.edit');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $this->authorize('edit-child-development-center');

        $centerID = session('editing_center_id');

        $center = ChildDevelopmentCenter::findOrFail($centerID);

        $workers = User::role('child development worker')->where('status','active')->get();
        $assignedWorker = $center->users()->role('child development worker')->first();

        $focals = User::role('lgu focal')->where('status','active')->get();
        $assignedFocal = $center->users()->role('lgu focal')->first();

        $coordinators = User::role('sfp coordinator')->where('status','active')->get();
        $assignedCoordinator = $center->users()->role('sfp coordinator')->first();

        $encoders = User::role('encoder')->where('status','active')->get();
        $assignedEncoder = $center->users()->role('encoder')->first();

        $pdos = User::role('pdo')->where('status','active')->get();
        $assignedPDO = $center->users()->role('pdo')->first();

        $psgc = new Psgc();

        $psgcRecord = Psgc::where('psgc_id', $center->psgc_id)->first();

        $provinces = $psgc->getProvinces();
        $cities = $psgc->allCities();
        $barangays = $psgc->allBarangays();

        // if not admin, filter provinces and cities
        if (!$request->user()->hasRole('admin')) {

            $psgcCity = Psgc::find(auth()->user()->psgc_id)->city_name_psgc;
            $focals = User::role('lgu focal')->where('status','active') // still on query builder
            ->leftJoin('psgcs', 'psgcs.psgc_id', '=', 'users.psgc_id')
                ->where('psgcs.city_name_psgc', $psgcCity)
                ->select('users.*') // optionally select fields from psgcs too
                ->get();

            $coordinators = User::role('sfp coordinator')->where('status','active') // still on query builder
            ->leftJoin('psgcs', 'psgcs.psgc_id', '=', 'users.psgc_id')
                ->where('psgcs.city_name_psgc', $psgcCity)
                ->select('users.*') // optionally select fields from psgcs too
                ->get();

            $workers = User::role('child development worker')->where('status','active') // still on query builder
            ->leftJoin('psgcs', 'psgcs.psgc_id', '=', 'users.psgc_id')
                ->where('psgcs.city_name_psgc', $psgcCity)
                ->select('users.*') // optionally select fields from psgcs too
                ->get();

            $encoders = User::role('encoder')->where('status','active') // still on query builder
            ->leftJoin('psgcs', 'psgcs.psgc_id', '=', 'users.psgc_id')
                ->where('psgcs.city_name_psgc', $psgcCity)
                ->select('users.*') // optionally select fields from psgcs too
                ->get();

            // Filter the grouped cities collection to only include entries matching the user's city PSGC
            $userCity = Psgc::find(auth()->user()->psgc_id)->city_name_psgc;
            $cities = $cities->map(function ($cityGroup) use ($userCity) {
                return $cityGroup->filter(function ($city) use ($userCity) {
                    return $city['psgc'] === $userCity;
                })->values();
            })->filter(function ($cityGroup) {
                return $cityGroup->isNotEmpty();
            });

            // Keep only the user's province in the list
            $userProvince = Psgc::find(auth()->user()->psgc_id)->province_psgc;
            $provinces = array_filter($provinces, function ($name, $psgc) use ($userProvince) {
                return $psgc === $userProvince;
            }, ARRAY_FILTER_USE_BOTH);

        }

        return view('centers.edit', [
            'center' => $center,
            'focals' => $focals,
            'coordinators', $coordinators,
            'workers' => $workers,
            'encoders' => $encoders,
            'pdos' => $pdos,
            'assignedWorker' => $assignedWorker,
            'assignedFocal' => $assignedFocal,
            'assignedCoordinator' => $assignedCoordinator,
            'assignedEncoder' => $assignedEncoder,
            'assignedPDO' => $assignedPDO,
            'psgcRecord' => $psgcRecord,
            'provinces' => $provinces,
            'cities' => $cities,
            'barangays' => $barangays,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateChildDevelopmentCenterRequest $request)
    {

        $validatedData = $request->validated();

        $centerID = $request->input('center_id'); //session('center_id');

        $center = ChildDevelopmentCenter::findOrFail($centerID);

        $query = ChildDevelopmentCenter::where('center_name', $validatedData['center_name'])
            ->where('id', '!=', $centerID);

        $existingCenter = $query->first();

        if ($existingCenter) {
            return redirect()->back()->with('error', 'CDC/SNP already exists.');
        }

        $psgc = Psgc::where('province_psgc', $request->province_psgc)
            ->where('city_name_psgc', $request->city_name_psgc)
            ->where('brgy_psgc', $request->brgy_psgc)
            ->first();

        if ($psgc) {
            $psgc_id = $psgc->psgc_id;
        } else {
            return redirect()->back()->withErrors(['psgc' => 'Selected location is not valid.']);
        }

        $updated = $center->update([
            'center_name' => $validatedData['center_name'],
            'center_type' => $validatedData['center_type'],
            'psgc_id' => $psgc_id,
            'address' => $validatedData['address'],
            'updated_by_user_id' => auth()->id(),
        ]);

        $focal = null;
        $coordinator = null;
        $worker = $request->input('assigned_worker_user_id');
        $encoder = $request->input('assigned_encoder_user_id');

        if(auth()->user()->hasRole('lgu focal')){
            $focal = auth()->id();
            $coordinator = $request->input('assigned_coordinator_user_id');
        } elseif(auth()->user()->hasRole('sfp coordinator')){
            $focal = $request->input('assigned_focal_user_id');
            $coordinator = auth()->id();
        }

        $userIds = array_filter([
            $worker,
            $encoder,
            $focal,
            $coordinator,
        ]);

        $center->users()->sync($userIds);

        return redirect()->route('centers.index')->with('success', 'CDC/SNP record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function view(Request $request)
    {
        session(['view_center_id' => $request->input('center_id')]);

        $centerID = session('view_center_id');

        $center = ChildDevelopmentCenter::findOrFail($centerID);

        $psgc = new Psgc();

        $psgcRecord = Psgc::where('psgc_id', $center->psgc_id)->first();

        return view('centers.view', compact('center', 'psgc', 'psgcRecord'));
    }
}
