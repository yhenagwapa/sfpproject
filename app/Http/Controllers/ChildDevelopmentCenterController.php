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

    public function index()
    {
        $centersQuery = ChildDevelopmentCenter::query();

        if (auth()->user()->hasRole('admin')) {
            $centers = $centersQuery->get();
        } else {
            $userCenters = UserCenter::where('user_id', auth()->id())->get();
            $centerIDs = $userCenters->pluck('child_development_center_id');
            $centers = $centersQuery->whereIn('id', $centerIDs)->get();
        }

        $centersWithRoles = [];

        foreach ($centers as $center) {
            $centerUsers = UserCenter::where('child_development_center_id', $center->id)
                ->pluck('user_id');
            
            $users = User::whereIn('id', $centerUsers)->get();
            
            $worker = $users->firstWhere(fn($user) => $user->hasRole('child development worker'));
            $encoder = $users->firstWhere(fn($user) => $user->hasRole('encoder'));
            $focal = $users->firstWhere(fn($user) => $user->hasRole('lgu focal'));
            $pdo = $users->firstWhere(fn($user) => $user->hasRole('pdo'));

            $centersWithRoles[$center->id] = [
                'center_id' => $center->id,
                'center_name' => $center->center_name,
                'worker' => $worker,
                'encoder' => $encoder,
                'focal' => $focal,
                'pdo' => $pdo,
                'address' => $center->getfulladdress(),
            ];
        }

        return view('centers.index', compact('centersWithRoles'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create-child-development-center');

        $centers = ChildDevelopmentCenter::all();
        $workers = User::role('child development worker')->get();
        $focals = User::role('lgu focal')->get();
        $encoders = User::role('encoder')->get();
        $psgc = new Psgc();

        $provinces = $psgc->getProvinces();
        $cities = $psgc->allCities();
        $barangays = $psgc->allBarangays();

        if ($request->has('province_psgc') && !empty($request->input('province_psgc'))) {
            $province_psgc = $request->input('province_psgc');

            $cities = $psgc->allCities()->get($province_psgc, collect([]));
        }

        if ($request->has('city_name_psgc') && !empty($request->input('city_name_psgc'))) {
            $city_psgc = $request->input('city_name_psgc');
            $barangays = $psgc->getBarangays($city_psgc);
        }

        return view('centers.create', compact('centers', 'workers', 'focals', 'encoders', 'provinces', 'cities', 'barangays'));
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

        ChildDevelopmentCenter::create([
            'center_name' => $request->center_name,
            'psgc_id' => $psgc_id,
            'address' => $request->address,
            'created_by_user_id' => auth()->id(),
        ]);

        return redirect()->route('centers.index')->with('success', 'Child Development Center saved successfully');
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $this->authorize('edit-child-development-center');

        $center = ChildDevelopmentCenter::findOrFail($id);
        $workers = User::role('child development worker')->get();
        $focals = User::role('lgu focal')->get();
        $psgc = new Psgc();

        $psgcRecord = Psgc::find($center->psgc_id);

        $provinces = $psgc->getProvinces();  // Assuming this returns associative array
        $cities = $psgc->getCities($psgcRecord->province_psgc);
        $barangays = $psgc->getBarangays($psgcRecord->city_name_psgc);
        $changedCities = $psgc->allCities();      // Fetch all cities once
        $changedBrgys = $psgc->allBarangays();

        // dd([
        //     'provinces' => $provinces,
        //     'cities' => $cities,
        //     'barangays' => $barangays,
        //     'psgcRecord' => $psgcRecord,
        // ]);


        return view('centers.edit', [
            'center' => $center,
            'focals' => $focals,
            'workers' => $workers,
            'psgcRecord' => $psgcRecord,
            'provinces' => $provinces,
            'cities' => $cities,
            'barangays' => $barangays,
            'changedCities' => $changedCities,
            'changedBrgys' => $changedBrgys,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateChildDevelopmentCenterRequest $request, ChildDevelopmentCenter $center)
    {
        $validatedData = $request->validated();

        $center->update(array_merge($validatedData, [
            'updated_by_user_id' => auth()->id(),
        ]));

        return redirect()->route('centers.index')->with('success', 'Child development center record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChildDevelopmentCenter $childDevelopmentCenter)
    {
        //
    }
}
