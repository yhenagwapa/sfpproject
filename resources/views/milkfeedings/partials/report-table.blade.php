

<table id='milkfeeding-table' class="table text-sm text-center">
    <thead class="border-bottom-2">
        <tr>
            <th class="border border-white">Last Name</th>
            <th class="border border-white">Extension Name</th>
            <th class="border border-white">First Name</th>
            <th class="border border-white">Middle Name</th>
            <th class="border border-white">Sex</th>
            <th class="border border-white">Date of Birth</th>
            <th class="border border-white">Undernourished</th>
        </tr>
    </thead>
    <tbody class="milkfeeding-table text-sm">
        @foreach ($isFunded as $fundedChild)
            <tr>
                <td>{{ $fundedChild->lastname }}</td>
                <td>{{ $fundedChild->extension_name }}</td>
                <td>{{ $fundedChild->firstname }}</td>
                <td>{{ $fundedChild->middlename }}</td>
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
        @endforeach
        @if (count($isFunded) <= 0)
            <tr>
                <td class="text-center" colspan="7">
                    @if (empty($search))
                        No Data found
                    @endif
                </td>
            </tr>
        @endif
    </tbody>
</table>
<style>
    @media print {
        /* Hide everything except the table */
        body * {
            visibility: hidden;
        }

        /* Show only the funded table and make it full width */
        #funded-table, #funded-table * {
            visibility: visible;
        }

        /* Ensure the table takes the full width */
        #funded-table {
            width: 100%;
        }

        /* Optionally hide buttons and form elements */
        form, .btn {
            display: none;
        }

        /* Styling for the table during print */
        table {
            border-collapse: collapse;
            width: 100%;
        }
        table, th, td {
            border: 1px solid black;
        }
    }
</style>
