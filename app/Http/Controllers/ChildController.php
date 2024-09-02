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
    public function create()
    {
        $this->authorize('create-child');
        
        $centers = ChildDevelopmentCenter::all();

        return view('child.create', compact('centers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChildRequest $request)
    {
        $this->authorize('create-child');

        $validatedData = $request->validated();$request->validated();

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
        
        $child = Child::create([
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'lastname' => $request->lastname,
            'extension_name' => $request->extension_name,
            'date_of_birth' => $request->date_of_birth,
            'sex' => $request->sex,
            'address' => $request->address,
            'psgc_id' => $request->psgc_id,
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
    public function show($id)
    {
        $child = Child::findOrFail($id);
        
        // Fetch the province, city, and barangay based on psgc_id
        $psgc = Psgc::find($child->psgc_id);
        
        // Check if the PSGC record exists
        if ($psgc) {
            $selectedProvincePsgc = $psgc->province_psgc;
            $selectedCityPsgc = $psgc->city_name_psgc; // Adjust field names as necessary
            $selectedBrgyPsgc = $psgc->brgy_psgc; // Adjust field names as necessary
        } else {
            $selectedProvincePsgc = null;
            $selectedCityPsgc = null;
            $selectedBrgyPsgc = null;
        }
    
        $childAttendance = Attendance::where('child_id', $id)->get();
        $centers = ChildDevelopmentCenter::all();
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
    
        if (request()->ajax()) {
            return response()->json([
                'child' => $child,
                'childAttendance' => $childAttendance,
                'centers' => $centers,
                'sexOptions' => $sexOptions,
                'pantawidDetails' => $pantawidDetails,
            ]);
        }
    
        return view('child.edit', compact('child', 'childAttendance', 'centers', 'sexOptions', 'extNameOptions', 'pantawidDetails', 'selectedProvincePsgc', 'selectedCityPsgc', 'selectedBrgyPsgc'));
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
        $validatedData = $request->validated();$request->validated();

        // Check if the child already exists
        
        $child->update($request->validated());

        // Redirect to the index page with a success message
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
