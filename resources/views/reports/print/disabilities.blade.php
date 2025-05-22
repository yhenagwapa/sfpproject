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
            border: 5px;
            text-transform: uppercase;
        }

        .table td {
            padding: 10px;
            vertical-align: top;
        }

        .p {
            margin: 5px 0;
        }

        .disability-table {
            font-family: 'Arial', sans-serif;
            text-align: center;
            border-collapse: collapse;
        }

        .disability-table th, .disability-table td {
            border: 1px solid rgba(0, 0, 0, 0.5);
            text-transform: uppercase;
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
    <div class="header">
        <p>Department of Social Welfare and Development, Field Office XI</p>
        <p>Supplementary Feeding Program</p>
        <p>{{ $cycle->name}} ( SY {{ $cycle->school_year }} )</p>
        <p><b>LIST OF PWD CHILDREN</b></p>
        <br>
    </div>

    <div class="header-section">
        <table class="table">
            <tr>
                <td>
                    <p>Province: <u>{{ $province ? $province->implode(', ') : 'All Provinces' }}</u></p>
                    <p>City / Municipality: <u>{{ $city ? $city->implode(', ') : 'All Cities' }}</u></p>
                </td>
            </tr>
        </table>
    </div>

    <table id='disability-table' class="table datatable disability-table w-full text-base">
        <thead>
            <tr>
                <th>No.</th>
                <th>Name of Child <br> <i class="text-sm">(Surname, First Name, M. I.)</i></th>
                <th>Name of Child Development Center <br> <i class="text-sm">(CDC)</i></th>
                <th>Sex<br> <i class="text-sm">(M/F)</i></th>
                <th>Date of Birth<br> <i class="text-sm">(mm/dd/yyyy)</i></th>
                <th>Type of Disability</th>
            </tr>
        </thead>
        <tbody class="disability-table text-xs">
            @foreach ($isPwdChildren as $childrenWithDisability)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $childrenWithDisability->full_name }}</td>
                    <td>
                        {{ optional($childrenWithDisability->records->first()->center)->center_name ?? 'N/A' }}
                    </td>
                    <td>{{ $childrenWithDisability->sex->name }}</td>
                    <td>{{ $childrenWithDisability->date_of_birth->format('Y-m-d') }}</td>
                    <td>{{ $childrenWithDisability->person_with_disability_details }}</td>
                </tr>

            @endforeach
            @if (count($isPwdChildren) <= 0)
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
                <td>
                    <br>
                    <br>
                    <p>Prepared by:</p>
                    <br>
                    <br>
                    <p>______________________________________</p>
                    <p>SFP Focal Person</p>
                </td>
                <td>
                    <br>
                    <br>
                    <p>Noted by:</p>
                    <br>
                    <br>
                    <p>______________________________________</p>
                    <p>C/MSWDO/District Head</p>
                </td>
            </tr>
        </table>
    </div>

    <footer>

    </footer>

</body>
</html>
