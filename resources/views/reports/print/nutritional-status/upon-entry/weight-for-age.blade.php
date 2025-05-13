<div class="header">
    @include('reports.print.nutritional-status.header')
    <p><b>CONSOLIDATED NUTRITIONAL STATUS REPORT</b></p>
    <p>Upon Entry <i><br />(Weight-for-Age)</i></p>
    <br>
</div>

<div class="header-section">
    <table class="table">
        <tr>
            <td>
                <p>Province: <u>{{ count($province) > 0 ? $province->implode(', ') : 'All Provinces' }}</u></p>
                <p>City / Municipality: <u>{{ count($city) > 0 ? $city->implode(', ') : 'All Cities' }}</u></p>
            </td>
        </tr>
    </table>
</div>

<table id='after-120-hfa' class="table datatable nutritional-status w-full">

    <thead class="border bg-gray-200 header-bg">
    <tr>
        <th rowspan="3">No.</th>
        <th rowspan="3">Name of Child Development Center (CDC)</th>
        <th rowspan="3">Name of Child Development Workers (Surname, First name, M.I)</th>
        <th rowspan="3">Total No. Served</th>
        <th colspan="2">Total No. of CDCCh/SNP Served</th>
        <th colspan="8">Normal (N)</th>
        <th colspan="8">Underweight (UW)</th>
        <th colspan="8">Severely Underweight (SUW)</th>
        <th colspan="8">Overweight (OW)</th>
        <th colspan="8">Total</th>
    </tr>
    <tr>
        <th class="subheader-bg" rowspan="2">Male</th>
        <th class="subheader-bg" rowspan="2">Female</th>
        @for($i = 0; $i < 5; $i++)
            <th colspan="4">Male</th>
            <th colspan="4">Female</th>
        @endfor
    </tr>
    <tr class="subheader-bg">
        @for($i = 0; $i < 10; $i++)
            <th>2</th>
            <th>3</th>
            <th>4</th>
            <th>5</th>
        @endfor
    </tr>
    </thead>
    <tbody class="nutritional-status text-xs ">
    @php $i = 0; @endphp
    @foreach ($report['weight_for_age'] as $key => $center)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $center['cdc_name'] }}</td>
            <td></td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            @foreach ($categoriesWFA as $category)
                @foreach (['male', 'female'] as $gender)
                    @foreach (['2', '3', '4', '5'] as $age)
                        <td>{{ $center[$category][$gender][$age] }}</td>
                    @endforeach
                @endforeach
            @endforeach
        </tr>
    @endforeach
    @if (count($report) <= 0)
        <tr>
            <td class="text-center" colspan="54">
                @if (empty($search))
                    No Data found
                @endif
            </td>
        </tr>
    @endif
    </tbody>
    <tfoot>
    <tr>
        <th colspan="3" style="text-align: right; padding-right: 5px;">Total per Age Bracket &gt; </th>
        <th rowspan="3">0</th>
        @for($i = 0; $i <= 41; $i++)
        <th>0</th>
        @endfor
    </tr>
    <tr>
        <th colspan="3" style="text-align: right; padding-right: 5px;">Total Male/Female &gt;</th>
        <th rowspan="2" colspan="2">0</th>
        @for($i = 0; $i < 10; $i++)
        <th colspan="4">0</th>
        @endfor
    </tr>
    <tr>
        <th colspan="3" style="text-align: right; padding-right: 5px;">Total children beneficiaries &gt; </th>
        @for($i = 0; $i < 5; $i++)
        <th colspan="8">0</th>
        @endfor
    </tr>
    </tfoot>
</table>

<div class="footer-section">
    <table class="footer-table">
        <tr></tr>
        <tr></tr>
        <tr>
            <td>
                <br>
                <br>
                <p>Noted by:</p>
                <br>
                <br>
                <p>______________________________________</p>
                <p>SFP Focal Person</p>
            </td>
            <td>
                <br>
                <br>
                <p>Approved by:</p>
                <br>
                <br>
                <p>______________________________________</p>
                <p>C/MSWDO/District Head</p>
            </td>
        </tr>
    </table>
</div>
