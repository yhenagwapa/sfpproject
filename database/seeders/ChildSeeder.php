<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Child;

class ChildSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $child1 = Child::create([
            'cycle_implementation_id' => '1',
            'firstname' => 'aaa',
            'lastname' => 'aaa',
            'date_of_birth' => '2023/01/01',
            'address' => 'Suazo St.',
            'psgc_id' => '214',
            'zip_code' => '8000',
            'is_pantawid' => true,
            'pantawid_details' => 'rcct',
            'is_person_with_disability' => false,
            'is_indigenous_people' => false,
            'is_child_of_soloparent' => true,
            'is_lactose_intolerant' => false,
            'is_funded' => true,
            'child_development_center_id' => '3',
            'sex_id' => '1',
            'created_by_user_id' => '3'
        ]);

        $child2 = Child::create([
            'cycle_implementation_id' => '1',
            'firstname' => 'bbb',
            'lastname' => 'bbb',
            'date_of_birth' => '2022/02/02',
            'address' => 'Suazo St.',
            'psgc_id' => '214',
            'zip_code' => '8000',
            'is_pantawid' => true,
            'pantawid_details' => 'rcct',
            'is_person_with_disability' => true,
            'person_with_disability_details' => 'hearing',
            'is_indigenous_people' => true,
            'is_child_of_soloparent' => true,
            'is_lactose_intolerant' => false,
            'is_funded' => false,
            'child_development_center_id' => '4',
            'sex_id' => '2',
            'created_by_user_id' => '5'
        ]);

        $child3 = Child::create([
            'cycle_implementation_id' => '1',
            'firstname' => 'ccc',
            'lastname' => 'ccc',
            'date_of_birth' => '2021/03/03',
            'address' => 'Suazo St.',
            'psgc_id' => '214',
            'zip_code' => '8000',
            'is_pantawid' => false,
            'is_person_with_disability' => false,
            'is_indigenous_people' => false,
            'is_child_of_soloparent' => true,
            'is_lactose_intolerant' => false,
            'is_funded' => true,
            'child_development_center_id' => '3',
            'sex_id' => '1',
            'created_by_user_id' => '3'
        ]);

        $child4 = Child::create([
            'cycle_implementation_id' => '1',
            'firstname' => 'ddd',
            'lastname' => 'ddd',
            'date_of_birth' => '2020/06/02',
            'address' => 'Suazo St.',
            'psgc_id' => '214',
            'zip_code' => '8000',
            'is_pantawid' => false,
            'is_person_with_disability' => true,
            'person_with_disability_details' => 'visually impaired',
            'is_indigenous_people' => false,
            'is_child_of_soloparent' => true,
            'is_lactose_intolerant' => false,
            'is_funded' => false,
            'child_development_center_id' => '4',
            'sex_id' => '1',
            'created_by_user_id' => '5'
        ]);

        $child5 = Child::create([
            'cycle_implementation_id' => '1',
            'firstname' => 'eee',
            'lastname' => 'eee',
            'date_of_birth' => '2020/11/11',
            'address' => 'Suazo St.',
            'psgc_id' => '214',
            'zip_code' => '8000',
            'is_pantawid' => false,
            'is_person_with_disability' => true,
            'person_with_disability_details' => 'visually impaired',
            'is_indigenous_people' => false,
            'is_child_of_soloparent' => true,
            'is_lactose_intolerant' => false,
            'deworming_date' => '2023/03/01',
            'vitamin_a_date' => '2023/03/01',
            'is_funded' => true,
            'child_development_center_id' => '3',
            'sex_id' => '2',
            'created_by_user_id' => '3'
        ]);

        $child6 = Child::create([
            'cycle_implementation_id' => '1',
            'firstname' => 'fff',
            'lastname' => 'fff',
            'date_of_birth' => '2021/12/12',
            'address' => 'Suazo St.',
            'psgc_id' => '214',
            'zip_code' => '8000',
            'is_pantawid' => false,
            'is_person_with_disability' => true,
            'person_with_disability_details' => 'test',
            'is_indigenous_people' => false,
            'is_child_of_soloparent' => true,
            'is_lactose_intolerant' => false,
            'deworming_date' => '2023/04/01',
            'vitamin_a_date' => '2023/04/01',
            'is_funded' => true,
            'child_development_center_id' => '4',
            'sex_id' => '1',
            'created_by_user_id' => '5'
        ]);


    }
}
