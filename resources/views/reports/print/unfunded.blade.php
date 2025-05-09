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

        .unfunded-table {
            font-size: 9px;
            font-family: 'Arial', sans-serif;
            text-align: center;
            border-collapse: collapse;
        }

        .unfunded-table th, .unfunded-table td {
            border: 1px solid rgba(0, 0, 0, 0.5);
            padding: 10px;
            text-transform: uppercase;
        }

        .unfunded-table td:first-child{
            width: 20%;
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
        <p>Department of Social Welfare and Development, Field Office XI<br>
            Supplementary Feeding Program<br>
            {{ $cycle->name}} ( SY {{ $cycle->school_year_from }} - {{ $cycle->school_year_to }} )</p>
        <p><b>UNFUNDED CHILDREN</b></p>
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

    <table id='unfunded-table' class="table datatable unfunded-table w-full">
        <thead>
            <tr>
                <th class="no-wrap">Name of Child</th>
                <th>Sex</th>
                <th class="no-wrap">Date of Birth</th>
                <th class="no-wrap">Address</th>
                <th>Pantawid</th>
                <th>IP</th>
                <th>PWD</th>
                <th>Child of Solo Parent</th>
                <th>Lactose Intolerant</th>
            </tr>
        </thead>

        <tbody class="funded-table text-xs">
            @foreach ($isNotFunded as $unfundedChild)
                <tr>
                    <td>{{ $unfundedChild->full_name }}</td>
                    <td>{{ $unfundedChild->sex->name == 'Male' ? 'M' : 'F' }}</td>
                    <td>{{ $unfundedChild->date_of_birth }}</td>
                    <td>{{ $unfundedChild->psgc->brgy_name }} {{ $unfundedChild->psgc->city_name }} {{ $unfundedChild->psgc->province_name }}</td>
                    <td>{{ $unfundedChild->pantawid_details ? $unfundedChild->pantawid_details : 'No' }}</td>
                    <td>{{ $unfundedChild->is_indigenous_people ? 'Yes' : 'No' }}</td>
                    <td>{{ $unfundedChild->person_with_disability_details ? $unfundedChild->person_with_disability_details : 'No' }}</td>
                    <td>{{ $unfundedChild->is_child_of_soloparent ? 'Yes' : 'No' }}</td>
                    <td>{{ $unfundedChild->is_lactose_intolerant ? 'Yes' : 'No' }}</td>
                </tr>
            @endforeach
            @if (count($isNotFunded) <= 0)
                <tr>
                    <td class="text-center" colspan="9">
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
            </tr>
        </table>
    </div>

    <footer>

    </footer>

</body>
</html>
