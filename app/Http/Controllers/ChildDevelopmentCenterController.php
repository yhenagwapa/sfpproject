<?php

namespace App\Http\Controllers;

use App\Models\ChildDevelopmentCenter;
use App\Http\Requests\StoreChildDevelopmentCenterRequest;
use App\Http\Requests\UpdateChildDevelopmentCenterRequest;
use App\Models\Child;
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
        if(auth()->user()->hasRole('admin')){
            $centers = ChildDevelopmentCenter::with(['user', 'focal', 'psgc'])->get();

        } elseif(auth()->user()->hasRole('lgu focal')){
            $focalID = auth()->id();
            $centers = ChildDevelopmentCenter::with(['user', 'focal', 'psgc'])
                ->where('assigned_focal_user_id', $focalID)->get();
        }

        return view('centers.index', compact('centers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create-child-development-center');

        $centers = ChildDevelopmentCenter::all();
        $users = User::role('child development worker')->get();
        $psgc = new Psgc();

        // Fetch all provinces for the form
        $provinces = $psgc->getProvinces();
        $cities = $psgc->allCities();      // Fetch all cities once
        $barangays = $psgc->allBarangays(); // Fetch all barangays once

        if ($request->has('province_psgc') && !empty($request->input('province_psgc'))) {
            $province_psgc = $request->input('province_psgc');
            // Fetch cities for the selected province
            $cities = $psgc->allCities()->get($province_psgc, collect([]));
        }
    
        // Check if city is selected
        if ($request->has('city_name_psgc') && !empty($request->input('city_name_psgc'))) {
            $city_psgc = $request->input('city_name_psgc');
            $barangays = $psgc->getBarangays($city_psgc);
        }

        return view('centers.create', compact('centers', 'users', 'provinces', 'cities', 'barangays'));
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
            'zip_code' => $request->zip_code,
            'assigned_focal_user_id' => auth()->id(),
            'assigned_worker_user_id' => $request->assigned_worker_user_id,
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
        $users = User::role('child development worker')->get();
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
            'users' => $users,
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

        // dd($validatedData);
        
        $center->update($validatedData);

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
