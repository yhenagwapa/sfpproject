<?php

namespace Database\Seeders;

use App\Models\psgc;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class PsgcSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = $this->json();
        // dd($json);
        // return false;
        $json = json_decode($this->json(), true);
        // return var_dump($json);
        $i = 0;
        foreach ($json as $key => $psgc_data) {
            $i++;
            if ($i == 1) {
                // var_dump($psgc_data);
                continue;
            }

            $insert_data['province_name'] = $psgc_data[0];
            $insert_data['province_psgc'] = $psgc_data[1];
            $insert_data['city_name'] = $psgc_data[2];
            $insert_data['city_name_psgc'] = $psgc_data[3];
            $insert_data['brgy_name'] = $psgc_data[4];
            $insert_data['brgy_psgc'] = $psgc_data[5];
            $insert_data['district'] = $psgc_data[6];
            $insert_data['subdistrict'] = $psgc_data[7];
            $insert_data['region_name'] = $psgc_data[8];
            $insert_data['region_psgc'] = $psgc_data[9];
            $insert_data['region_name_short'] = $psgc_data[10];
            $psgc = Psgc::create($insert_data);
            // echo "created psgc: $psgc->brgy_psgc - $psgc->brgy_name \n";
        }
    }

    public function json()
    {
        $reader = Reader::createFromPath(public_path('/dataseeders/psgc.csv'), 'r');
        $results = $reader->getRecords();
        $data = [];
        foreach ($results as $key => $row) {
            $data[] = $row;
        }

        return $this->safe_json_encode($data);
    }

    private function safe_json_encode($value, $options = 0, $depth = 512)
    {
        $encoded = json_encode($value, $options, $depth);
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return $encoded;
            case JSON_ERROR_DEPTH:
                return 'Maximum stack depth exceeded'; // or trigger_error() or throw new Exception()
            case JSON_ERROR_STATE_MISMATCH:
                return 'Underflow or the modes mismatch'; // or trigger_error() or throw new Exception()
            case JSON_ERROR_CTRL_CHAR:
                return 'Unexpected control character found';
            case JSON_ERROR_SYNTAX:
                return 'Syntax error, malformed JSON'; // or trigger_error() or throw new Exception()
            case JSON_ERROR_UTF8:
                $clean = $this->utf8ize($value);

                return $this->safe_json_encode($clean, $options, $depth);
            default:
                return 'Unknown error'; // or trigger_error() or throw new Exception()

        }
    }

    private function utf8ize($d)
    {
        if (is_array($d)) {
            foreach ($d as $k => $v) {
                $d[$k] = $this->utf8ize($v);
            }
        } elseif (is_string($d)) {
            return utf8_encode($d);
        }

        return $d;
    }
}