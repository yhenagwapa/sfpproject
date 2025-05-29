<!DOCTYPE html>
<html lang="en">
<head></head>
<body>
    <table border="1" cellpadding="5">
    <thead>
        <tr>
            <th rowspan="3" style="text-align: center;">Center Name</th>
            <th rowspan="3" style="text-align: center;">Center Worker</th>
            @foreach ($categories as $category)
                <th colspan="8" style="text-align: center;">{{$category}}</th>
            @endforeach
            <th colspan="8" style="text-align: center;">Total</th>
        </tr>
        <tr>
            @foreach ($categories as $category)
                @foreach ($sexes as $sex)
                    <th colspan="4" style="text-align: center;">{{$sex}}</th>
                @endforeach
            @endforeach
            <th colspan="4" style="text-align: center;">M</th>
            <th colspan="4" style="text-align: center;">F</th>
        </tr>
        <tr>
            @foreach ($categories as $category)
                @foreach ($sexes as $sex)
                    @foreach ([2, 3, 4, 5] as $age)
                        <th>{{ $age }}</th>
                    @endforeach
                @endforeach
            @endforeach
            @foreach ([2, 3, 4, 5] as $age)
                        <th>{{ $age }}</th>
                    @endforeach
                    @foreach ([2, 3, 4, 5] as $age)
                        <th>{{ $age }}</th>
                    @endforeach
        </tr>
    </thead>
    <tbody>
         @php
            $totalM = 0;
            $totalF = 0;
            $overallTotal = 0;
        @endphp
        @foreach ($centers as $center)

            <tr>
                <td>{{$center->center_name}}</td>
                <td></td>
                @foreach ($categories as $category)
                    @foreach ($sexes as $sex )
                        @foreach ($ages as $age)
                            @php $count = $hfaCounts[$center->id][$category][$sex][$age] ?? 0; @endphp
                            <td>{{ $count }}</td>
                        @endforeach

                    @endforeach
                @endforeach
                @foreach ($sexes as $sex )
                @foreach ($ages as $age)

                            <td>{{ $count }}</td>
                        @endforeach
                        @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
