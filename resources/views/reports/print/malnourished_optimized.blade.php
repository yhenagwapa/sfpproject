<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>List of Malnourished Children</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 9px; }
        .header { text-align: center; font-size: 11px; margin-bottom: 10px; }
        .header p { margin: 2px 0; }
        .info { font-size: 10px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; font-size: 8px; }
        th, td { border: 1px solid #000; padding: 3px; text-align: center; }
        th { background: #f0f0f0; font-weight: bold; }
        .no-wrap { white-space: nowrap; }
        @page { margin: 15mm 10mm; }
    </style>
</head>
<body>
    <div class="header">
        <p>Department of Social Welfare and Development, Field Office XI</p>
        <p>Supplementary Feeding Program</p>
        <p>{{ $cycle->name}} ( CY {{ $cycle->school_year_from }} )</p>
        <p><strong>LIST OF MALNOURISHED CHILDREN</strong></p>
    </div>

    <div class="info">
        <p>Province: <u>{{ $province ? $province->implode(', ') : 'All Provinces' }}</u></p>
        <p>City / Municipality: <u>{{ $city ? $city->implode(', ') : 'All Cities' }}</u></p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Name</th>
                <th rowspan="2">Center</th>
                <th rowspan="2">Sex</th>
                <th rowspan="2">DOB</th>
                <th rowspan="2">Entry Date</th>
                <th rowspan="2">Wt(kg)</th>
                <th rowspan="2">Ht(cm)</th>
                <th colspan="2">Age</th>
                <th colspan="3">NS Entry</th>
                <th rowspan="2">Exit Date</th>
                <th rowspan="2">Wt(kg)</th>
                <th rowspan="2">Ht(cm)</th>
                <th colspan="2">Age</th>
                <th colspan="3">NS Exit</th>
            </tr>
            <tr>
                <th>M</th><th>Y</th>
                <th>W/A</th><th>W/H</th><th>H/A</th>
                <th>M</th><th>Y</th>
                <th>W/A</th><th>W/H</th><th>H/A</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($isFunded as $i => $child)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $child->full_name }}</td>
                <td>{{ $child->center_name }}</td>
                <td>{{ $child->sex_initial }}</td>
                <td class="no-wrap">{{ $child->date_of_birth ? \Carbon\Carbon::parse($child->date_of_birth)->format('m-d-Y') : '' }}</td>
                <td class="no-wrap">{{ $child->entry_weighing_date ? \Carbon\Carbon::parse($child->entry_weighing_date)->format('m-d-Y') : '' }}</td>
                <td>{{ $child->entry_weight ? number_format($child->entry_weight, 1) : '' }}</td>
                <td>{{ $child->entry_height ? number_format($child->entry_height, 1) : '' }}</td>
                <td>{{ $child->entry_age_months ?? '' }}</td>
                <td>{{ $child->entry_age_years ?? '' }}</td>
                <td>{{ $child->entry_weight_for_age ?? '' }}</td>
                <td>{{ $child->entry_weight_for_height ?? '' }}</td>
                <td>{{ $child->entry_height_for_age ?? '' }}</td>
                <td class="no-wrap">{{ $child->exit_weighing_date ? \Carbon\Carbon::parse($child->exit_weighing_date)->format('m-d-Y') : '' }}</td>
                <td>{{ $child->exit_weight ? number_format($child->exit_weight, 1) : '' }}</td>
                <td>{{ $child->exit_height ? number_format($child->exit_height, 1) : '' }}</td>
                <td>{{ $child->exit_age_months ?? '' }}</td>
                <td>{{ $child->exit_age_years ?? '' }}</td>
                <td>{{ $child->exit_weight_for_age ?? '' }}</td>
                <td>{{ $child->exit_weight_for_height ?? '' }}</td>
                <td>{{ $child->exit_height_for_age ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px; font-size: 10px;">
        <table style="border: none;">
            <tr style="border: none;">
                <td style="border: none; width: 50%;">
                    <p>Noted by:</p>
                    <br><br>
                    <p><u>{{ $user->hasRole('lgu focal') ? $user->full_name : str_repeat('_', 40) }}</u></p>
                    <p>SFP Focal Person</p>
                </td>
                <td style="border: none; width: 50%;">
                    <p>Approved by:</p>
                    <br><br>
                    <p><u>{{ str_repeat('_', 40) }}</u></p>
                    <p>C/MSWDO/District Head</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
