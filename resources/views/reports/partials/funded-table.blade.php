<div class="row">
    <div class="col-md-6 mt-4 text-sm">
        <form action="{{ route('reports.index') }}" method="POST" id="filterForm">
            @csrf
            <label for="center_name">Filter per center:</label>
            <select class="form-control" name="center_name" id="center_name" onchange="this.form.submit()">
                <option value="all_center" {{ old('center_name', $cdcId) == 'all_center' ? 'selected' : '' }}>
                    All Child Development Center
                </option>
                @foreach ($centers as $center)
                    <option value="{{ $center->id }}" {{ old('center_name') == $center->id || $cdcId == $center->id ? 'selected' : '' }}>
                        {{ $center->center_name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="col-md-6 mt-11 text-sm">
        <a href="#" class="text-white bg-blue-600 rounded px-3 min-h-9 align-items-right" target="_blank" id="printButton">Print</a>
    </div>
</div>

<script>
    document.getElementById('center_name').addEventListener('change', function() {
        let selectedCenterId = this.value;
        let printButton = document.getElementById('printButton');
        
        printButton.href = '/reports/print/masterlist?center_name=' + selectedCenterId;
    });

    window.onload = function() {
        let selectedCenterId = document.getElementById('center_name').value;
        let printButton = document.getElementById('printButton');
        
        printButton.href = '/reports/print/masterlist?center_name=' + selectedCenterId;
    };
</script>

<table id='funded-table' class="table datatable text-xs text-center" style="min-width: 1800px;">
    <thead>
        <tr>
            <th class="border border-white w-40" rowspan="2">Name of Child</th>
            <th class="border border-white" rowspan="2">Sex</th>
            <th class="border border-white w-24" rowspan="2">Date of Birth</th>
            <th class="border border-white w-24" rowspan="2">Actual Date of Weighing</th>
            <th class="border border-white" rowspan="2">Weight in kg.</th>
            <th class="border border-white" rowspan="2">Height in cm.</th>
            <th class="border border-white" colspan="2">Age in month/year</th>
            <th class="border border-white" colspan="3">Nutritional Status</th>
            <th class="border border-white w-10" rowspan="2">Summary of Undernourished Children</th>
            <th class="border border-white" rowspan="2">Deworming</th>
            <th class="border border-white" rowspan="2">Vitamin A</th>
            <th class="border border-white w-10" rowspan="2">Pantawid Member</th>
            <th class="border border-white" rowspan="2">IPs</th>
            <th class="border border-white" rowspan="2">PWD</th>
            <th class="border border-white  w-10" rowspan="2">Child of Solo Parent</th>
            <th class="border border-white  w-10" rowspan="2">Lactose Intolerant</th>
        </tr>
        <tr>
            <th class="border border-white">Month</th>
            <th class="border border-white">Year</th>
            <th class="border border-white">Weight for Age</th>
            <th class="border border-white">Weight for Height</th>
            <th class="border border-white">Height for Age</th>
        </tr>
    </thead>
    <tbody class="funded-table text-xs">
        @foreach ($isFunded as $fundedChild)
            <tr>
                <td>{{ $fundedChild->full_name }}</td>
                <td>{{ $fundedChild->sex->name }}</td>
                <td>{{ $fundedChild->date_of_birth }}</td>

                <td>{{ optional($fundedChild->nutritionalStatus->first())->weighing_date }}</td>
                <td>{{ optional($fundedChild->nutritionalStatus->first())->weight }}</td>
                <td>{{ optional($fundedChild->nutritionalStatus->first())->height }}</td>
                <td>{{ optional($fundedChild->nutritionalStatus->first())->age_in_months }}</td>
                <td>{{ optional($fundedChild->nutritionalStatus->first())->age_in_years }}</td>
                <td>{{ optional($fundedChild->nutritionalStatus->first())->weight_for_age }}</td>
                <td>{{ optional($fundedChild->nutritionalStatus->first())->weight_for_height }}</td>
                <td>{{ optional($fundedChild->nutritionalStatus->first())->height_for_age }}</td>
                <td>
                    @if ($fundedChild->nutritionalStatus->isNotEmpty() && $fundedChild->nutritionalStatus->first()->is_undernourish)
                        Yes
                    @elseif ($fundedChild->nutritionalStatus->isNotEmpty())
                        No
                    @endif
                </td>
                <td>{{ $fundedChild->deworming_date }}</td>
                <td>{{ $fundedChild->vitamin_a_date }}</td>
                <td>{{ $fundedChild->pantawid_details ?  $fundedChild->pantawid_details : 'No'}}</td>
                <td>{{ $fundedChild->is_indigenous_people ? 'Yes' : 'No' }}</td>
                <td>{{ $fundedChild->person_with_disability_details ? $fundedChild->person_with_disability_details : 'No'}}</td>
                <td>{{ $fundedChild->is_child_of_soloparent ? 'Yes' : 'No' }}</td>
                <td>{{ $fundedChild->is_lactose_intolerant ? 'Yes' : 'No' }}</td>
            </tr>
        @endforeach
        @if (count($isFunded) <= 0)
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