<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('https://172.31.176.49/sfpproject/public') }}">

    <title>List of PWD Children</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="{{ asset('img/SFP-LOGO-2024.png') }}" rel="icon">
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
            text-transform: uppercase;
        }

        .footer-table td {
            padding: 10px;
            vertical-align: top;
        }

        .no-wrap {
            white-space: nowrap;
        }

        @page {
            margin-top: 20px;
            margin-bottom: 50px;
            margin-right: 30px;
            margin-left: 30px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <p>Department of Social Welfare and Development, Field Office XI</p>
        <p>Supplementary Feeding Program</p>
        <p>{{ $cycle->name}} ( CY {{ $cycle->school_year_from }} )</p>
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
                    <td>{{ $childrenWithDisability->date_of_birth->format('m-d-Y') }}</td>
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
                    <p>
                        @if (auth()->user()->hasRole('lgu focal'))
                            <u>{{ auth()->user()->full_name }}</u>
                        @else
                            ______________________________________
                        @endif
                    </p>
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

    <div class="footer">
        SFP Forms 7 (c/o SFP Focal Person)
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Arial", "normal");
                $fontSize = 8;
                $text = "Page $PAGE_NUM of $PAGE_COUNT";
                $width = $fontMetrics->get_text_width($text, $font, $fontSize);
                $x = (612 / 2) - ($width / 2);
                $y = 890;
                $pdf->text($x, $y, $text, $font, $fontSize);
            ');
        }
    </script>

</body>
</html>
