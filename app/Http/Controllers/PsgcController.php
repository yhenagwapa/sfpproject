<?php

namespace App\Http\Controllers;

use App\Models\Psgc;
use Illuminate\Http\Request;

class PsgcController extends Controller
{
    public function index()
    {
        $provinces = Psgc::where('region_psgc', 110000000)
                        ->distinct()
                        ->orderBy("province_name")
                        ->get(['region_psgc', 'province_name', 'province_psgc']);

        $provinceIds = $provinces->pluck('province_psgc');

        $cities = Psgc::whereIn('province_psgc', $provinceIds)
                    ->get(['city_name', 'city_name_psgc']);

        // Fetch city IDs
        $cityIds = $cities->pluck('city_name_psgc');

        // Fetch barangays based on the city IDs
        $barangays = Psgc::whereIn('city_name_psgc', $cityIds)
                        ->get(['brgy_name', 'brgy_psgc']);

        return response()->json(['provinces' => $provinces, 'cities' => $cities, 'barangays' => $barangays]);
    }

    public function show($psgc)
    {
        $brgy = Psgc::where('brgy_name', $psgc->brgy_name);
        
        return view('child.edit', compact('brgy'));

    }


}
