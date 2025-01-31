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
            'lastname' => 'aaa',
            'firstname' => 'aaa',
            'date_of_birth' => '2023/01/01',
            'sex_id' => '1',
            'address' => 'Suazo St.',
            'psgc_id' => '214',
            'pantawid_details' => 'rcct',
            'is_indigenous_people' => false,
            'is_child_of_soloparent' => true,
            'is_lactose_intolerant' => false,
            'created_by_user_id' => '3'
        ]);

        $child2 = Child::create([
            'lastname' => 'bbb',
            'firstname' => 'bbb',
            'date_of_birth' => '2022/02/02',
            'sex_id' => '2',
            'address' => 'Suazo St.',
            'psgc_id' => '214',
            'pantawid_details' => 'rcct',
            'person_with_disability_details' => 'hearing',
            'is_indigenous_people' => true,
            'is_child_of_soloparent' => true,
            'is_lactose_intolerant' => false,
            'created_by_user_id' => '5'
        ]);

        $child3 = Child::create([
            'lastname' => 'ccc',
            'firstname' => 'ccc',
            'date_of_birth' => '2021/03/03',
            'sex_id' => '1',
            'address' => 'Suazo St.',
            'psgc_id' => '214',
            'is_indigenous_people' => false,
            'is_child_of_soloparent' => true,
            'is_lactose_intolerant' => false,
            'created_by_user_id' => '3'
        ]);

        $child4 = Child::create([
            'lastname' => 'ddd',
            'firstname' => 'ddd',
            'date_of_birth' => '2020/06/02',
            'sex_id' => '1',
            'address' => 'Suazo St.',
            'psgc_id' => '214',
            'person_with_disability_details' => 'visually impaired',
            'is_indigenous_people' => false,
            'is_child_of_soloparent' => true,
            'is_lactose_intolerant' => false,
            'created_by_user_id' => '5'
        ]);

        $child5 = Child::create([
            'lastname' => 'eee',
            'firstname' => 'eee',
            'date_of_birth' => '2020/11/11',
            'sex_id' => '2',
            'address' => 'Suazo St.',
            'psgc_id' => '214',
            'person_with_disability_details' => 'visually impaired',
            'is_indigenous_people' => false,
            'is_child_of_soloparent' => true,
            'is_lactose_intolerant' => false,
            'created_by_user_id' => '3'
        ]);

        $child6 = Child::create([
            'lastname' => 'fff',
            'firstname' => 'fff',
            'date_of_birth' => '2021/12/12',
            'sex_id' => '1',
            'address' => 'Suazo St.',
            'psgc_id' => '214',
            'person_with_disability_details' => 'test',
            'is_indigenous_people' => false,
            'is_child_of_soloparent' => true,
            'is_lactose_intolerant' => false,
            'created_by_user_id' => '5'
        ]);


    }
}
