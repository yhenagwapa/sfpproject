<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Psgc extends Model
{
    use HasFactory;
    protected $primaryKey = 'psgc_id';

    protected $table = 'psgcs';



    public function getProvinces()
    {
        return Psgc::where('region_psgc', '110000000')
            ->orderBy("province_name")
            ->pluck('province_name', 'province_psgc');
    }

    // Fetch all cities grouped by province PSGC
    public function allCities()
{
    // Get all cities with distinct names
    $cities = Psgc::whereNotNull('city_name_psgc')
                   ->orderBy('city_name')
                   ->distinct('city_name_psgc')
                   ->get(['city_name_psgc', 'city_name', 'province_psgc']);

    // Group cities by province
    $groupedCities = $cities->groupBy('province_psgc')->map(function($group) {
        return $group->map(function($item) {
            return [
                'psgc' => $item->city_name_psgc,
                'name' => $item->city_name
            ];
        });
    });

    return $groupedCities;
}


    // Fetch all barangays grouped by city PSGC
    public function allBarangays()
    {
        return Psgc::whereNotNull('brgy_psgc')
                    ->orderBy('brgy_name')
                    ->get()
                    ->groupBy('city_name_psgc')
                    ->map(function($group) {
                        return $group->map(function($item) {
                            return ['psgc' => $item->brgy_psgc, 'name' => $item->brgy_name];
                        });
                    });
    }

    public function getPsgcId($region_psgc, $province_psgc, $city_name_psgc, $brgy_psgc)
    {
        return Psgc::where('region_psgc', $region_psgc)
            ->where('province_psgc', $province_psgc)
            ->where('city_name_psgc', $city_name_psgc)
            ->where('brgy_psgc', $brgy_psgc)
            ->pluck('psgc_id')
            ->first();
    }

    public function getLocationData($psgc_id)
    {
        return Psgc::where('psgc_id', $psgc_id)->first();
    }

}