<?php

namespace App\Http\Controllers;

use App\Models\ChildDevelopmentCenter;
use App\Http\Requests\StoreChildDevelopmentCenterRequest;
use App\Http\Requests\UpdateChildDevelopmentCenterRequest;
use App\Models\Child;
use App\Models\User;
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
        $this->middleware('permission:create-child-development-center', ['only' => ['index','create', 'store']]);
        $this->middleware('permission:edit-child-develpment-center', ['only' => ['index', 'edit', 'update']]);options: 
    }

    public function index()
    {
        $centers = ChildDevelopmentCenter::with(['user', 'province', 'city', 'barangay'])->get();

        return view('centers.index', compact('centers'));
    }

    public function passToChildCreate()
    {
        $centers = ChildDevelopmentCenter::all();
        
        // Pass the centers to the view
        return view('child.index', compact('centers'));
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

        
        // No need to use dd($request->all()) here in production
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
            'assigned_user_id' => $request->assigned_user_id,
            'created_by_user_id' => auth()->id(),
        ]);

        return redirect()->route('centers.index')->with('success', 'Child Development Center saved successfully');
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $centers = ChildDevelopmentCenter::find($id);
        
        return view('child.edit', compact('centers'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ChildDevelopmentCenter $childDevelopmentCenter)
    {
        $centers = ChildDevelopmentCenter::all();
        
        // Return the edit view with the child details and centers
        return view('child.edit', compact('centers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateChildDevelopmentCenterRequest $request, ChildDevelopmentCenter $childDevelopmentCenter)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChildDevelopmentCenter $childDevelopmentCenter)
    {
        //
    }
}
