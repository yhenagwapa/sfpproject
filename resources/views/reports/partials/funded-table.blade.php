<table id='funded-table' class="table datatable text-sm text-center">
    <thead class="border-bottom-2 text-lg">
        <tr class="border border-white">
            <th class="text-center">No.</th>
            <th>Child Name</th>
	        <th>Sex</th>
            <th>Date of Birth</th>
            <th>Undernourished</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($isFunded as $fundedChild)
            <tr class="text-left">
                <td class="text-center">{{ $loop->iteration }}</td>
                <td><strong>{{ $fundedChild->full_name }}</strong></td>
                <td>{{ $fundedChild->sex->name }}</td>
                <td>{{ \Carbon\Carbon::parse($fundedChild->date_of_birth)->format('m-d-Y') }}</td>
                <td>
                    @if ($fundedChild->nutritionalStatus->isNotEmpty() && $fundedChild->nutritionalStatus->first()->is_undernourish)
                        Yes
                    @elseif ($fundedChild->nutritionalStatus->isNotEmpty() && !$fundedChild->nutritionalStatus->first()->is_undernourish)
                        No
                    @else
                        Not Applicable
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td class="text-center"><strong>No children found.</strong></td>
		        <td></td>
		        <td></td>
		        <td></td>
                <td></td>
            </tr>
           @endforelse

    </tbody>
</table>
