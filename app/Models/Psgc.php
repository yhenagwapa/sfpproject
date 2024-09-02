<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Psgc extends Model
{
    use HasFactory;
    protected $primaryKey = 'psgc_id';

    protected $table = 'psgcs';



    public function getProvinces($region_psgc)
    {
        $provinces = Psgc::where('region_psgc', $region_psgc)
                        ->distinct()
                        ->orderBy("province_name")
                        ->pluck('province_name', 'province_psgc');

        return response()->json($provinces);

    }

    public function getCities($province_psgc)
    {
        $cities = Psgc::where('province_psgc', $province_psgc)
                    ->distinct()
                    ->orderBy('city_name')
                    ->pluck('city_name', 'city_name_psgc');
            
        return response()->json($cities);
    }

    public function getBarangays($city_psgc)
    {
        $barangays = Psgc::where('city_name_psgc', $city_psgc)
                        ->distinct()
                        ->orderBy('brgy_name')
                        ->pluck('brgy_name', 'brgy_psgc');
        
        return response()->json($barangays);
    }

    public function getPsgcId($region_psgc, $province_psgc, $city_name_psgc, $brgy_psgc)
    {
        try {
            $psgc = Psgc::where('region_psgc', $region_psgc)
                        ->where('province_psgc', $province_psgc)
                        ->where('city_name_psgc', $city_name_psgc)
                        ->where('brgy_psgc', $brgy_psgc)
                        ->pluck('psgc_id');
    
            return response()->json($psgc);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error fetching PSGC ID: ' . $e->getMessage());
    
            // Return a JSON response with an error message
            return response()->json(['error' => 'Unable to fetch PSGC ID'], 500);
        }
    }

    public function getLocationData($psgc_id)
    {
        $location = Psgc::where('psgc_id', $psgc_id)->first();

        if ($location) {
            return response()->json([
                'province_psgc' => $location->province_psgc,
                'province_name' => $location->province_name,
                'city_psgc' => $location->city_name_psgc,
                'city_name' => $location->city_name,
                'barangay_psgc' => $location->brgy_psgc,
                'barangay_name' => $location->brgy_name,
            ]);
        } else {
            return response()->json(['error' => 'Location not found'], 404);
        }
    }
}