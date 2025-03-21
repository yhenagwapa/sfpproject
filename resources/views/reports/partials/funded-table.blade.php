

<table id='funded-table' class="table datatable text-sm text-center">
    <thead class="border-bottom-2">
        <tr>
            <th class="border border-white">Child Name</th>            
	    <th class="border border-white">Sex</th>
            <th class="border border-white">Date of Birth</th>
            <th class="border border-white">Undernourished</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($isFunded as $fundedChild)
            <tr>
                <td>{{ $fundedChild->full_name }}</td>
                <td>{{ $fundedChild->sex->name }}</td>
                <td>{{ $fundedChild->date_of_birth }}</td>
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
               	    <td class="text-center">No children found.</td>
		    <td class="text-center"></td>
		    <td class="text-center"></td>
		    <td class="text-center"></td>
            	</tr>
           @endforelse

    </tbody>
</table>
