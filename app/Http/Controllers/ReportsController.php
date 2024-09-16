<?php

namespace App\Http\Controllers;

use App\Models\ChildDevelopmentCenter;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Child;
use App\Models\EntryNutritionalStatus;
use Carbon\Carbon;

class ReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function index()
     {
         $children = Child::with('nutritionalStatus')->paginate(10);
         $childrenWithDisabilities = Child::where('is_person_with_disability', true)
             ->with('center')
             ->paginate(10);
     
         // Calculate counts per center and per gender
         $countsPerCenterAndGender = Child::select('child_development_center_id', 'sex')
             ->selectRaw('count(*) as total')
             ->groupBy('child_development_center_id', 'sex')
             ->get()
             ->mapToGroups(function ($item) {
                 return [$item->center_id => [$item->sex => $item->total]];
             })
             ->map(function ($items) {
                 return [
                     'male' => $items->get('male', 0),
                     'female' => $items->get('female', 0),
                 ];
             });
     
         // Calculate other counts per center
         $countsPerCenter = Child::with('center')
             ->get()
             ->groupBy('child_development_center_id')
             ->map(function ($childrenByCenter) {
                 return [
                    'indigenous_people' => [
                    'male' => $childrenByCenter->where('is_indigenous_people', true)->where('sex', 'male')->count(),
                    'female' => $childrenByCenter->where('is_indigenous_people', true)->where('sex', 'female')->count(),
                    ],
                    'pantawid' => [
                        'male' => $childrenByCenter->where('pantawid_details', '!=', '')->where('sex', 'male')->count(),
                        'female' => $childrenByCenter->where('pantawid_details', '!=', '')->where('sex', 'female')->count(),
                    ],
                    'pwd' => [
                        'male' => $childrenByCenter->where('person_with_disability_details', '!=', '')->where('sex', 'male')->count(),
                        'female' => $childrenByCenter->where('person_with_disability_details', '!=', '')->where('sex', 'female')->count(),
                    ],
                    'lactose_intolerant' => [
                        'male' => $childrenByCenter->where('is_lactose_intolerant', true)->where('sex', 'male')->count(),
                        'female' => $childrenByCenter->where('is_lactose_intolerant', true)->where('sex', 'female')->count(),
                    ],
                    'child_of_solo_parent' => [
                        'male' => $childrenByCenter->where('is_child_of_soloparent', true)->where('sex', 'male')->count(),
                        'female' => $childrenByCenter->where('is_child_of_soloparent', true)->where('sex', 'female')->count(),
                    ],
                    'dewormed' => [
                        'male' => $childrenByCenter->where('deworming_date', '!=', '')->where('sex', 'male')->count(),
                        'female' => $childrenByCenter->where('deworming_date', '!=', '')->where('sex', 'female')->count(),
                    ],
                    'vitamin_a' => [
                        'male' => $childrenByCenter->where('vitamin_a_date', '!=', '')->where('sex', 'male')->count(),
                        'female' => $childrenByCenter->where('vitamin_a_date', '!=', '')->where('sex', 'female')->count(),
                    ],
                 ];
             });
     
         // Retrieve center names for display
         $centers = ChildDevelopmentCenter::all()->keyBy('id');
     
         return view('reports.index', compact('children', 'childrenWithDisabilities', 
             'countsPerCenter', 'countsPerCenterAndGender', 'centers'));
     }
     

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
