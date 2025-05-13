<?php

namespace App\Http\Controllers\PDF;

use App\Models\Child;
use App\Models\ChildCenter;
use App\Models\ChildDevelopmentCenter;
use App\Models\Implementation;
use App\Models\NutritionalStatus;
use App\Models\Psgc;
use App\Models\UserCenter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait NutritionalStatusReport
{

    public function printNutritionalStatusAfter120(Request $request)
    {
        // define variables
        $report = [];

        $cycleID = session('report_cycle_id');

        // define implementation_id (cycle_id)
        $cycle = Implementation::where('id', $cycleID)->first();

        // get all child development centers under the cycle
        $cc = ChildCenter::where('implementation_id', $cycleID)->where('status', 'active')->get();
        $cdc = ChildDevelopmentCenter::whereIn('id', $cc->pluck('child_development_center_id'))->get();

        $categoriesHFA = ['normal', 'stunted', 'severely stunted', 'tall', 'total'];
        $categoriesWFA = ['normal', 'underweight', 'severely underweight', 'overweight', 'total'];
        $categoriesWFH = ['normal', 'wasted', 'severely wasted', 'overweight', 'obese', 'total'];

        foreach ($cdc as $c) {
            $report['height_for_age'][$c->id] = $this->nutritionalStatusAfter120($c, $cycleID, 'height_for_age', $categoriesHFA);
            $report['weight_for_age'][$c->id] = $this->nutritionalStatusAfter120($c, $cycleID, 'weight_for_age', $categoriesWFA);
            $report['weight_for_height'][$c->id] = $this->nutritionalStatusAfter120($c, $cycleID, 'weight_for_height', $categoriesWFH);
        }

        // additional values for PDF
        $centers = UserCenter::where('user_id', auth()->id())->get();
        $getPsgc = $centers->pluck('psgc_id');
        $province = Psgc::whereIn('psgc_id', $getPsgc)->pluck('province_name')->unique();
        $city = Psgc::whereIn('psgc_id', $getPsgc)->pluck('city_name')->unique();

        $pdf = PDF::loadView('reports.print.nutritional-status.after120',
            compact('report', 'cycle', 'province', 'city',
            'cc','centers', 'categoriesHFA', 'categoriesWFA', 'categoriesWFH'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 0.5,
                'margin-bottom' => 0.5,
                'margin-left' => 0.5
            ]);

        return $pdf->stream($cycle->name . ' Nutritional Status After 120.pdf');
    }

    public function printNutritionalStatusUponEntry(Request $request)
    {
        $report = [];

        $cycleID = session('report_cycle_id');

        // define implementation_id (cycle_id)
        $cycle = Implementation::where('id', $cycleID)->first();

        // get all child development centers under the cycle
        $cc = ChildCenter::where('implementation_id', $cycleID)->where('status','active')->get();
        $cdc = ChildDevelopmentCenter::whereIn('id', $cc->pluck('child_development_center_id'))->get();

        $categoriesHFA = ['normal', 'stunted', 'severely stunted', 'tall', 'total'];
        $categoriesWFA = ['normal', 'underweight', 'severely underweight', 'overweight', 'total'];
        $categoriesWFH = ['normal', 'wasted', 'severely wasted', 'overweight', 'obese', 'total'];

        foreach ($cdc as $c) {
            $report['height_for_age'][$c->id] = $this->nutritionalStatusAfter120($c, $cycleID, 'height_for_age', $categoriesHFA);
            $report['weight_for_age'][$c->id] = $this->nutritionalStatusAfter120($c, $cycleID, 'weight_for_age', $categoriesWFA);
            $report['weight_for_height'][$c->id] = $this->nutritionalStatusAfter120($c, $cycleID, 'weight_for_height', $categoriesWFH);
        }

        // additional values for PDF
        $centers = UserCenter::with('users')->where('user_id', auth()->id())->get();
        $getPsgc = $centers->pluck('psgc_id');
        $province = Psgc::whereIn('psgc_id', $getPsgc)->pluck('province_name')->unique();
        $city = Psgc::whereIn('psgc_id', $getPsgc)->pluck('city_name')->unique();

        $pdf = PDF::loadView('reports.print.nutritional-status.upon-entry',
            compact('report', 'cycle', 'province', 'city',
                'cc', 'centers', 'categoriesHFA', 'categoriesWFA', 'categoriesWFH'))
            ->setPaper('folio', 'landscape')
            ->setOptions([
                'margin-top' => 0.5,
                'margin-right' => 0.5,
                'margin-bottom' => 0.5,
                'margin-left' => 0.5
            ]);

        return $pdf->stream($cycle->name . ' Nutritional Status Upon Entry.pdf');
    }

    private function nutritionalStatusAfter120($cdc, $implementationId, $categoryType, $categories)
    {
        $center['center_id'] = $cdc->id;
        $center['cdc_name'] = $cdc->center_name;

        $workers = UserCenter::with('users.roles')->get();  // Load users along with their roles

// Create an array to hold the worker names per center
$centersWithWorkers = [];

foreach ($workers as $worker) {
    // Filter users based on the 'child development worker' role
    $workerNames = $worker->users->filter(function ($user) {
        return $user->roles->contains('name', 'child development worker');
    })->pluck('full_name');  // Pluck just the full_name of the workers

    // Store the worker names in the array
    $centersWithWorkers[$worker->id] = $workerNames;
}

        $genders = ['male', 'female'];
        $ages = ['2', '3', '4', '5'];

        // initialize
        foreach ($categories as $category) {
            foreach ($genders as $gender) {
                foreach ($ages as $age) {
                    $center[$category][$gender][$age] = 0;
                }
            }
        }

        $cc = ChildCenter::where('child_development_center_id', $cdc->id)
                ->where('implementation_id', $implementationId)
                ->where('status', 'active')->get();

        // for height for age
        foreach ($cc as $child) {
            $ns = DB::table('nutritional_statuses')->select('nutritional_statuses.*', 'sexes.name as gender')
                ->leftJoin('children', 'children.id', '=', 'nutritional_statuses.child_id')
                ->leftJoin('sexes', 'sexes.id', '=', 'children.sex_id')
                ->where('child_id', $child->id)
                ->where('implementation_id', $implementationId)
                ->get();

            if ($ns->isNotEmpty()) {
                foreach ($ns as $n) {

                    foreach ($categories as $category) {

                        foreach ($genders as $gender) {

                            foreach ($ages as $age) {

                                if (strtolower($n->$categoryType) == $category && strtolower($n->gender) == strtolower($gender) && $n->age_in_years == $age) {
                                    $center[$category][$gender][$age]++;
                                    $center['total'][$gender][$age]++;
                                }

//                                $center['total'][$category][$gender][$age]++;
                            }

//                            $center['total'][$category][$gender]++;
                        }

//                        $center['total'][$category]++;
                    }

//                    $center['total']++;
                }
            }

        }

        dd($center);

        return $center;
    }
}
