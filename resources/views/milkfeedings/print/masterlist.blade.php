<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        .header {
            font-family: 'Arial', sans-serif;
            text-align: center;
            font-size: 12px;
        }

        .header-section .footer-section {
            width: 100%;
            margin: 0 auto;
        }

        .table {
            width: 100%;
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            border: 5px
        }

        .table td {
            padding: 10px;
            vertical-align: top;
        }

        .table td:first-child {
            width: 70%;
        }

        .p {
            margin: 5px 0;
        }

        .funded-table {
            font-size: 9px;
            font-family: 'Arial', sans-serif;
            text-align: center;
            border-collapse: collapse;
        }

        .funded-table th, .funded-table td {
            border: 1px solid rgba(0, 0, 0, 0.5);
            text-transform: uppercase;
        }


        .funded-table td:first-child{
            width: 15%;
        }

        .footer-table {
            width: 100%;
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            border: 5px
        }

        .footer-table p{
            margin: 0;
        }

        .footer-table td {
            padding: 10px;
            vertical-align: top;
        }

        .no-wrap {
            white-space: nowrap;
        }
    </style>
</head>
<body>
    {{-- <input type="hidden" id="funded_center_name" name="funded_center_name" value="{{ $center->id}}"> --}}
    <div class="header">
        <p>Department of Social Welfare and Development, Field Office XI</p>
        <p>Supplementary Feeding Program</p>
        <p>{{ $milkfeeding->name}} ( SY {{ $milkfeeding->school_year }} )</p>
        <p><b>MASTERLIST OF BENEFICIARIES</b></p>
        <br>
    </div>

    <div class="header-section">
        @if($selectedCenter)
            <table class="table">
                <tr>
                    <td>
                        <p>Province: <u>{{ $selectedCenter->psgc->province_name }}</u></p>
                        <p>Child Development Center: <u>{{ $selectedCenter->center_name }}</u></p>
                    </td>
                    <td>
                        <p>City / Municipality: <u>{{ $selectedCenter->psgc->city_name }}</u></p>
                        <p>Barangay: <u>{{ $selectedCenter->psgc->brgy_name }}</u></p>
                    </td>
                </tr>
            </table>
        @else
            <p>Child Development Center: <u>All Child Development Centers</u></p>
        @endif
    </div>

    <table id='funded-table' class="table datatable funded-table w-full">
        <thead>
            <tr>
                <th rowspan="2">Name of Child</th>
                <th rowspan="2">Sex</th>
                <th rowspan="2">Date of Birth</th>
                <th rowspan="2">Actual Date of Weighing</th>
                <th rowspan="2">Weight in kg.</th>
                <th rowspan="2">Height in cm.</th>
                <th colspan="2">Age in months/years</th>
                <th colspan="3">Nutritional Status</th>
                <th rowspan="2">Summary of Undernourished Children</th>
                <th rowspan="2">Deworming</th>
                <th rowspan="2">Vitamin A</th>
                <th rowspan="2">Pantawid Member</th>
                <th rowspan="2">IPs</th>
                <th rowspan="2">PWD</th>
                <th rowspan="2">Child of Solo Parent</th>
                <th rowspan="2">Lactose Intolerant</th>
            </tr>
            <tr>
                <th>Month</th>
                <th>Year</th>
                <th>Weight for Age</th>
                <th>Weight for Height</th>
                <th>Height for Age</th>
            </tr>
        </thead>
        <tbody class="funded-table text-xs">
            @foreach ($isFunded as $fundedChild)
                <tr>
                    <td>{{ $fundedChild->full_name }}</td>
                    <td>{{ $fundedChild->sex->name == 'Male' ? 'M' : 'F' }}</td>
                    <td style="white-space: nowrap;">{{ $fundedChild->date_of_birth }}</td>

                    <td style="white-space: nowrap;">{{ optional($fundedChild->nutritionalStatus->first())->weighing_date }}</td>
                    <td>{{ optional($fundedChild->nutritionalStatus->first())->weight }}</td>
                    <td>{{ optional($fundedChild->nutritionalStatus->first())->height }}</td>
                    <td>{{ optional($fundedChild->nutritionalStatus->first())->age_in_months }}</td>
                    <td>{{ optional($fundedChild->nutritionalStatus->first())->age_in_years }}</td>
                    <td>{{ optional($fundedChild->nutritionalStatus->first())->weight_for_age }}</td>
                    <td>{{ optional($fundedChild->nutritionalStatus->first())->weight_for_height }}</td>
                    <td>{{ optional($fundedChild->nutritionalStatus->first())->height_for_age }}</td>
                    <td>
                        @if ($fundedChild->nutritionalStatus->isNotEmpty() && $fundedChild->nutritionalStatus->first()->is_undernourish)
                            Yes
                        @elseif ($fundedChild->nutritionalStatus->isNotEmpty())
                            No
                        @endif
                    </td>
                    <td style="white-space: nowrap;">{{ $fundedChild->deworming_date }}</td>
                    <td style="white-space: nowrap;">{{ $fundedChild->vitamin_a_date }}</td>
                    <td>{{ $fundedChild->pantawid_details ?  $fundedChild->pantawid_details : 'No'}}</td>
                    <td>{{ $fundedChild->is_indigenous_people ? 'Yes' : 'No' }}</td>
                    <td>{{ $fundedChild->person_with_disability_details ? $fundedChild->person_with_disability_details : 'No'}}</td>
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

    <div class="footer-section">
        <table class="footer-table">
            <tr></tr>
            <tr></tr>
            <tr>
                <td colspan="3">
                    <br>
                    <br>
                    <p>Prepare by:</p>
                    <br>
                    <p>______________________________________</p>
                    <p>Child Development Worker/Teacher</p>
                </td>
                <td>
                    <br>
                    <br>
                    <p>Noted by:</p>
                    <br>
                    <p>______________________________________</p>
                    <p>SFP Focal Person</p>
                </td>
                <td>
                    <br>
                    <br>
                    <p>Approved by:</p>
                    <br>
                    <p>______________________________________</p>
                    <p>C/MSWDO/District Head</p>
                </td>
                <td style="border: 1px solid #6b7280;">
                    <p>This is to certify that the above list of children<br> beneficiaries has been Dewormed and received <br>Vitamin A Supplementation with the date indicated<br> prior to the feeding implementation.</p>
                    <br>
                    <p>Certified by:</p>
                    <br>
                    <p>______________________________________</p>
                    <p>Midwife/Nurse/BNS/BHW</p>
                </td>
            </tr>
        </table>
    </div>

    <footer>

    </footer>

</body>
</html>
