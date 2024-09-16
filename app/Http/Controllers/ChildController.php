<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Http\Requests\StoreChildRequest;
use App\Http\Requests\UpdateChildRequest;
use App\Models\ChildDevelopmentCenter;
use App\Models\Attendance;
use App\Http\Controllers\Log;
use Illuminate\Http\Request;
use App\Models\Psgc;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ChildController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:create-child|edit-child|delete-child', ['only' => ['index','show']]);
        $this->middleware('permission:create-child', ['only' => ['create','store']]);
        $this->middleware('permission:edit-child', ['only' => ['edit','update']]);
        $this->middleware('permission:delete-child', ['only' => ['destroy']]);
    }
    public function index(Request $request)
    {
        $search = $request->input('search.value', '');

        if ($request->ajax()) {
            $query = Child::query();

            if ($search) {
                $query->where('firstname', 'like', '%' . $search . '%')
                      ->orWhere('middlename', 'like', '%' . $search . '%')
                      ->orWhere('lastname', 'like', '%' . $search . '%');
            }

            $children = $query->paginate(5);
        }

        return view('child.index', compact('search'));
    }


    public function search(Request $request)
    {
        $search = $request->search;
        // Use when for cleaner query building
        $children = Child::when($search, function ($query, $search) {
            $query->where('firstname', 'like', "%{$search}%")
                ->orWhere('middlename', 'like', "%{$search}%")
                ->orWhere('lastname', 'like', "%{$search}%");
        })->paginate(5)->appends(['search' => $search]); // Ensure to append the correct search query

        $childrenWithFullNames = $children->map(function ($child) {
            return [
                'id' => $child->id,
                'full_name' => $child->full_name, // Ensure full_name is defined on the Child model
                'sex' => $child->sex,
                'date_of_birth' => $child->date_of_birth,
            ];
        });

        return response()->json([
            'children' => $childrenWithFullNames,
            'pagination_links' => (string) $children->links(), // Convert pagination links to a string
        ]);
    
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create-child');

        $centers = ChildDevelopmentCenter::all();

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

        return view('child.create', compact('centers', 'provinces', 'cities', 'barangays'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChildRequest $request)
    {
        $this->authorize('create-child');

        $validatedData = $request->validated();

        // Check if the child already exists
        $childExists = Child::where('firstname', $request->firstname)
                            ->where('middlename', $request->middlename)
                            ->where('lastname', $request->lastname)
                            ->where('extension_name', $request->extension_name)
                            ->where('date_of_birth', $request->date_of_birth)
                            ->where('sex', $request->sex)
                            ->exists();

        if ($childExists) {
            return redirect()->back()->with('error', 'Child already exists.');
        }

        $psgc = Psgc::where('province_psgc', $request->input('province_psgc'))
                ->where('city_name_psgc', $request->input('city_name_psgc'))
                ->where('brgy_psgc', $request->input('brgy_psgc'))
                ->first();

        if ($psgc) {
            $psgc_id = $psgc->psgc_id;
        } else {
            return redirect()->back()->withErrors(['msg' => 'Location not found']);
        }
        
        $child = Child::create([
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'lastname' => $request->lastname,
            'extension_name' => $request->extension_name,
            'date_of_birth' => $request->date_of_birth,
            'sex' => $request->sex,
            'address' => $request->address,
            'psgc_id' => $psgc_id,
            'zip_code' => $request->zip_code,
            'is_pantawid' => $request->is_pantawid,
            'pantawid_details' => $request->pantawid_details,
            'is_person_with_disability' => $request->is_person_with_disability,
            'person_with_disability_details' => $request->person_with_disability_details,
            'is_indigenous_people' => $request->is_indigenous_people,
            'is_child_of_soloparent' => $request->is_child_of_soloparent,
            'is_lactose_intolerant' => $request->is_lactose_intolerant,
            'created_by_user_id' => auth()->id(),
        ]);

        return redirect()->route('child.index')->with('success', 'Child details saved successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $this->authorize('edit-child');

        $child = Child::findOrFail($id); 
        $centers = ChildDevelopmentCenter::all();

        $psgc = new Psgc();

        $psgcRecord = Psgc::find($child->psgc_id);
        
        $provinces = $psgc->getProvinces();
        $cities = $psgc->getCities($psgcRecord->province_psgc);
        $barangays = $psgc->getBarangays($psgcRecord->city_name_psgc);

        $getallcities = $psgc->allCities();      // Fetch all cities once
        $getallbarangays = $psgc->allBarangays();

        // dd($provinces);

        $sexOptions = [
            'male' => 'Male',
            'female' => 'Female',
        ];
        $extNameOptions = [
            'jr' => 'Jr',
            'sr' => 'Sr',
            'i' => 'I',
            'ii' => 'II',
            'iii' => 'III',
            'iv' => 'IV',
            'v' => 'V',
            'vi' => 'VI',
            'vii' => 'VII',
            'viii' => 'VIII',
            'ix' => 'IX',
            'x' => 'X',
        ];
        $pantawidDetails = [
            'rcct' => 'RCCT',
            'mcct' => 'MCCT'
        ];

        return view('child.edit', compact('child', 'centers', 'sexOptions', 'extNameOptions', 'pantawidDetails', 'psgcRecord', 'provinces', 'cities', 'barangays', 'getallcities', 'getallbarangays'));
    }


    


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateChildRequest $request, Child $child)
    {
        $validatedData = $request->validated();
        
        $child->update($validatedData);

        return redirect()->route('child.index')->with('success', 'Child record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Child $child)
    {
        //
    }
}
