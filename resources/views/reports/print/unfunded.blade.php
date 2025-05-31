<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('https://172.31.176.49/sfpproject/public') }}">

    <title>Unfunded Children</title>

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
            width: 5%;
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
                <th>No.</th>
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
            @forelse ($isNotFunded as $unfundedChild)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $unfundedChild->full_name }}</td>
                    <td>{{ $unfundedChild->sex->name == 'Male' ? 'M' : 'F' }}</td>
                    <td>{{ $unfundedChild->date_of_birth->format('Y-m-d') }}</td>
                    <td>{{ $unfundedChild->psgc->brgy_name }} {{ $unfundedChild->psgc->city_name }} {{ $unfundedChild->psgc->province_name }}</td>
                    <td>{{ $unfundedChild->pantawid_details ? $unfundedChild->pantawid_details : 'No' }}</td>
                    <td>{{ $unfundedChild->is_indigenous_people ? 'Yes' : 'No' }}</td>
                    <td>{{ $unfundedChild->person_with_disability_details ? $unfundedChild->person_with_disability_details : 'No' }}</td>
                    <td>{{ $unfundedChild->is_child_of_soloparent ? 'Yes' : 'No' }}</td>
                    <td>{{ $unfundedChild->is_lactose_intolerant ? 'Yes' : 'No' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10">No Data Found.</td>
                </tr>
            @endforelse

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
                    <p><u>
                        @if($selectedCenter)
                            @php
                                $users = $selectedCenter->users->filter(function ($user) {
                                    return $user->roles->contains('name', 'child development worker');
                                });
                            @endphp

                            @if ($users->isNotEmpty())
                                @foreach ($users as $user)
                                    {{ $user->fullname }}
                                @endforeach
                            @else
                                No Worker Assigned
                            @endif
                        @else
                            ______________________________________
                        @endif
                    </u></p>
                    <p>Child Development Worker/Teacher</p>
                </td>
                <td>
                    <br>
                    <br>
                    <p>Noted by:</p>
                    <br>
                    <p>
                        <u>
                            @if($selectedCenter)
                                @php
                                    $users = $selectedCenter->users->filter(function ($user) {
                                        return $user->roles->contains('name', 'lgu focal');
                                    });
                                @endphp

                                @if ($users->isNotEmpty())
                                    @foreach ($users as $user)
                                        {{ $user->fullname }}
                                    @endforeach
                                @else
                                    No Worker Assigned
                                @endif
                        @else
                            ______________________________________
                        @endif
                        </u>
                    </p>
                    <p>SFP Focal Person</p>
                </td>
            </tr>
        </table>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Arial", "normal");
                $fontSize = 8;
                $text = "Page $PAGE_NUM of $PAGE_COUNT";
                $width = $fontMetrics->get_text_width($text, $font, $fontSize);
                $x = (936 / 2) - ($width / 2);
                $y = 580;
                $pdf->text($x, $y, $text, $font, $fontSize);
            ');
        }
    </script>

</body>
</html>
