<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        .header {
            font-family: 'Arial', sans-serif;
            text-align: center;
            font-size: 12px;
        }

        .section {
            width: 100%;
            margin: 0 auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-family: 'Arial', sans-serif;
            font-size: 10px;
        }

        .table td {
            padding: 10px;
            vertical-align: top;
        }

        .table td:first-child {
            width: 70%;
        }

        p {
            margin: 5px 0;
        }

        .funded-table {
            font-size: 9px;
            font-family: 'Arial', sans-serif;
            text-align: center;
            border-color: black;
        }

        .child-name{
            min-width: 20px;
        }
    </style>
</head>
<body>
    {{-- <input type="hidden" id="funded_center_name" name="funded_center_name" value="{{ $center->id}}"> --}}
    <div class="header">
        <p>Department of Social Welfare and Development, Field Office XI</p>
        <p>Supplementary Feeding Program</p>
        <p>{{ $cycleImplementation->cycle_name}} ( SY {{ $cycleImplementation->cycle_school_year }} )</p>
        <p><b>MASTERLIST OF BENEFICIARIES</b></p>
        <br>
    </div>

    <div class="section">
        <table class="table">
            <tr>
                <td>
                    <p>Province: ___________________</p>
                    <p>Child Development Center: ___________________</p>
                </td>
                <td>
                    <p>City / Municipality: ___________________</p>
                    <p>Barangay: ___________________</p>
                </td>
            </tr>
        </table>
    </div>
    <table id='funded-table' class="table datatable funded-table border border-black w-full">
        <thead class="border border-black">
            <tr>
                <th class="border border-black child-name" rowspan="2">Name of Child</th>
                <th class="border border-black" rowspan="2">Sex</th>
                <th class="border border-black" rowspan="2">Date of Birth</th>
                <th class="border border-black" rowspan="2">Actual Date of Weighing</th>
                <th class="border border-black" rowspan="2">Weight in kg.</th>
                <th class="border border-black" rowspan="2">Height in cm.</th>
                <th class="border border-black" colspan="2">Age in month/year</th>
                <th class="border border-black" colspan="3">Nutritional Status</th>
                <th class="border border-black" rowspan="2">Summary of Undernourished Children</th>
                <th class="border border-black" rowspan="2">Deworming</th>
                <th class="border border-black" rowspan="2">Vitamin A</th>
                <th class="border border-black" rowspan="2">Pantawid Member</th>
                <th class="border border-black" rowspan="2">IPs</th>
                <th class="border border-black" rowspan="2">PWD</th>
                <th class="border border-black" rowspan="2">Child of Solo Parent</th>
                <th class="border border-black" rowspan="2">Lactose Intolerant</th>
            </tr>
            <tr>
                <th class="border border-black">Month</th>
                <th class="border border-black">Year</th>
                <th class="border border-black">Weight for Age</th>
                <th class="border border-black">Weight for Height</th>
                <th class="border border-black">Height for Age</th>
            </tr>
        </thead>
        <tbody class="funded-table">
            @foreach ($isFunded as $fundedChild)
                <tr>
                    <td class="border border-black child-name>{{ $fundedChild->full_name }}</td>
                    <td>{{ $fundedChild->sex->name }}</td>
                    <td>{{ $fundedChild->date_of_birth }}</td>
    
    
                    @if ($fundedChild->nutritionalStatus)
                            @php
                                $dob = \Carbon\Carbon::parse($fundedChild->date_of_birth);
                                $actualDate = \Carbon\Carbon::parse(
                                    $fundedChild->nutritionalStatus->entry_actual_date_of_weighing,
                                );
                                $ageInYears = $actualDate->diffInYears($dob);
                                $ageInMonths = $actualDate->diffInMonths($dob) % 12;
                            @endphp
    
                            <td>{{ $fundedChild->nutritionalStatus->entry_actual_date_of_weighing }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->entry_weight }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->entry_height }}</td>
                            <td>{{ $ageInMonths }}</td>
                            <td>{{ $ageInYears }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->entry_weight_for_age }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->entry_weight_for_height }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->entry_height_for_age }}</td>
                            <td>{{ $fundedChild->nutritionalStatus->entry_is_undernourish ? 'Yes' : 'No' }}</td>
                    @else
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    @endif
    
                    <td>{{ $fundedChild->deworming_date }}</td>
                    <td>{{ $fundedChild->vitamin_a_date }}</td>
                    <td>{{ $fundedChild->pantawid_details }}</td>
                    <td>{{ $fundedChild->is_indigenous_people ? 'Yes' : 'No' }}</td>
                    <td>{{ $fundedChild->person_with_disability_details }}</td>
                    <td>{{ $fundedChild->is_child_of_soloparent ? 'Yes' : 'No' }}</td>
                    <td>{{ $fundedChild->is_lactose_intolerant ? 'Yes' : 'No' }}</td>
                </tr>
            @endforeach
            @if (count($isFunded) <= 0)
                <tr>
                    <td class="text-center" colspan="6">
                        @if (empty($search))
                            No Data found
                        @endif
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
    
    <footer>

    </footer>
    
</body>
</html>
