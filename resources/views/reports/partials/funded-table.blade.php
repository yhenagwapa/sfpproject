<table id='funded-table' class="table datatable text-sm">
    <thead class="border-bottom-2 text-sm">
        <tr>
            <th class="text-center" rowspan="2">No.</th>
            <th rowspan="2">Child Name</th>
	        <th rowspan="2">Sex</th>
            <th rowspan="2">Date of Birth</th>
            <th colspan="3" class="text-center">Nutritional Status<br>Upon Entry</th>
            <th colspan="3" class="text-center">Nutritional Status<br>Afte 120 Feedings</th>
        </tr>
        <tr>
            <th>Weight for Age</th>
            <th>Height for Age</th>
            <th>Weight for Height</th>
            <th>Weight for Age</th>
            <th>Height for Age</th>
            <th>Weight for Height</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($isFunded as $fundedChild)
            <tr class="text-left">
                <td class="text-center">{{ $loop->iteration }}</td>
                <td><strong>{{ $fundedChild->full_name }}</strong></td>
                <td>{{ $fundedChild->sex->name }}</td>
                <td>{{ \Carbon\Carbon::parse($fundedChild->date_of_birth)->format('m-d-Y') }}</td>
                <td>{{ $fundedChild->nutritionalStatus->first()->weight_for_age }}</td>
                <td>{{ $fundedChild->nutritionalStatus->first()->height_for_age }}</td>
                <td>{{ $fundedChild->nutritionalStatus->first()->weight_for_height }}</td>
                <td>{{ $fundedChild->nutritionalStatus->get(1)?->weight_for_age }}</td>
                <td>{{ $fundedChild->nutritionalStatus->get(1)?->height_for_age }}</td>
                <td>{{ $fundedChild->nutritionalStatus->get(1)?->weight_for_height }}</td>
            </tr>
        @empty
            <tr>
                <td class="text-center"><strong>No children found.</strong></td>
		        <td></td>
		        <td></td>
		        <td></td>
                <td></td>
                <td></td>
                <td></td>
		        <td></td>
		        <td></td>
                <td></td>
            </tr>
        @endforelse
    </tbody>
</table>
