<table id='children-table' class="table datatable mt-3 text-center" style="width: 100%;">
    <thead class="text-base">
        <tr>
            <th rowspan="2">
                <b>Child Name</b>
            </th>
            <th rowspan="2">Sex</th>
            <th rowspan="2" data-type="date" data-format="MM/DD/YYYY">Date of Birth</th>
            <th colspan="3"> Nutritional Status</th>
            <th rowspan="2">Action</th>
        </tr>
        <tr>
            <th>Weight for Age</th>
            <th>Weight for Height</th>
            <th>Height for Age</th>
        </tr>

    </thead>
    <tbody class="children-table">
        @if(isset($maleChildren) && isset($femaleChildren))
            @foreach ($maleChildren as $maleChild)
                <tr>
                    <td>{{ $maleChild->full_name }}</td>
                    <td>{{ $maleChild->sex->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($maleChild->date_of_birth)->format('m-d-Y') }}</td>
                    <td>{{ optional($maleChild->nutritionalStatus->first())->weight_for_age }}</td>
                    <td>{{ optional($maleChild->nutritionalStatus->first())->weight_for_height }}</td>
                    <td>{{ optional($maleChild->nutritionalStatus->first())->height_for_age }}</td>

                    <td class="inline-flex items-center justify-center">
                        <div class="flex space-x-3">
                            @can(['edit-child'])
                                <a href="{{ route('child.show', ['child' => $maleChild->id]) }}" class="relative inline-flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="#3968d2" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                    <span class="font-semibold text-sm" style="color: #3968d2;">
                                        Edit
                                    </span>
                                </a>
                            @endcan
                            @can(['create-child'])
                                <a href="{{ route('child.additional-info', $maleChild['id']) }}" class="relative inline-flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#3968d2" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    <span class="font-semibold text-sm" style="color: #3968d2;">
                                        Add Info
                                    </span>
                                </a>
                            @endcan
                            @can(['create-nutritional-status'])
                                <a href="{{ route('nutritionalstatus.index', $maleChild->id) }}" class="relative inline-flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="#1e9730" class="w-5 h-5">
                                        <path d="M3 17L8 12L11 15L17 9M17 9H13M17 9V13M6 21H18C19.6569 21 21 19.6569 21 18V6C21 4.34315 19.6569 3 18 3H6C4.34315 3 3 4.34315 3 6V18C3 19.6569 4.34315 21 6 21Z"></path>
                                    </svg>
                                    <span class="font-semibold text-sm" style="color: #1e9730;">
                                        Nutritional Status
                                    </span>
                                </a>
                            @endcan
                            @can(['edit-nutritional-status'])
                                @if($maleChild->nutritionalStatus->isNotEmpty())
                                    <a href="{{ route('nutritionalstatus.edit', $maleChild->id) }}" class="relative inline-flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="#1e9730" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                        </svg>
                                        <span class="font-semibold text-sm" style="color: #1e9730;">
                                            Edit NS
                                        </span>
                                    </a>
                                @endif
                            @endcan

                            @can('add-attendance')
                                <a href="{{ route('attendance.index', $maleChild->id) }}" class="relative inline-flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="#eead30" class="w-5 h-5">
                                        <path stroke="none" d="M0 0h24v24H0z"/>  <path d="M9 5H7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2V7a2 2 0 0 0 -2 -2h-2" />  <rect x="9" y="3" width="6" height="4" rx="2" />  <path d="M9 14l2 2l4 -4" />
                                    </svg>
                                    <span class="font-semibold text-sm" style="color: #eead30;">
                                        Attendance
                                    </span>
                                </a>
                            @endcan
                        </div>
                    </td>
                </tr>
            @endforeach

            @foreach ($femaleChildren as $femaleChild)
                <tr>
                    <td>{{ $femaleChild->full_name }}</td>
                    <td>{{ $femaleChild->sex->name }}</td>
                    <td>{{ Carbon\Carbon::parse($femaleChild->date_of_birth)->format('m-d-Y') }}</td>
                    <td>{{ optional($femaleChild->nutritionalStatus->first())->weight_for_age }}</td>
                    <td>{{ optional($femaleChild->nutritionalStatus->first())->weight_for_height }}</td>
                    <td>{{ optional($femaleChild->nutritionalStatus->first())->height_for_age }}</td>

                    <td class="inline-flex items-center justify-center">
                        <div class="flex space-x-3">
                            @can(['edit-child'])
                                <a href="{{ route('child.show', ['child' => $femaleChild->id]) }}" class="relative inline-flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="#3968d2" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>

                                    <span class="font-semibold text-sm" style="color: #3968d2;">
                                        Edit
                                    </span>
                                </a>
                            @endcan
                            @can(['create-child'])
                                <a href="{{ route('child.additional-info', $femaleChild['id']) }}" class="relative inline-flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#3968d2" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    <span class="font-semibold text-sm" style="color: #3968d2;">
                                        Add Info
                                    </span>
                                </a>
                            @endcan
                            @can(['create-nutritional-status'])
                                <a href="{{ route('nutritionalstatus.index', $femaleChild->id) }}" class="relative inline-flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="#1e9730" class="w-5 h-5">
                                            <path d="M3 17L8 12L11 15L17 9M17 9H13M17 9V13M6 21H18C19.6569 21 21 19.6569 21 18V6C21 4.34315 19.6569 3 18 3H6C4.34315 3 3 4.34315 3 6V18C3 19.6569 4.34315 21 6 21Z"></path>
                                        </svg>
                                    </button>
                                    <span class="font-semibold text-sm" style="color: #1e9730;">
                                        Nutritional Status
                                    </span>
                                </a>
                            @endcan
                            @can(['edit-nutritional-status'])
                                @if($femaleChild->nutritionalStatus->isNotEmpty())
                                    <a href="{{ route('nutritionalstatus.edit', $femaleChild->id) }}" class="relative inline-flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="#1e9730" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                        </svg>
                                        <span class="font-semibold text-sm" style="color: #1e9730;">
                                            Edit NS
                                        </span>
                                    </a>
                                @endif
                            @endcan

                            @can('add-attendance')
                                <a href="{{ route('attendance.index', $femaleChild->id) }}" class="relative inline-flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="#eead30" class="w-5 h-5">
                                        <path stroke="none" d="M0 0h24v24H0z"/>  <path d="M9 5H7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2V7a2 2 0 0 0 -2 -2h-2" />  <rect x="9" y="3" width="6" height="4" rx="2" />  <path d="M9 14l2 2l4 -4" />
                                    </svg>
                                    <span class="font-semibold text-sm" style="color: #eead30;">
                                        Attendance
                                    </span>
                                </a>
                            @endcan
                        </div>
                    </td>
                </tr>
            @endforeach
        @endif

        @if (count($maleChildren) <= 0)
            <tr>
                <td class="text-center" colspan="7">
                    @if (empty($search))
                        No male children found.
                    @else
                        No search keyword match found.
                    @endif
                </td>
            </tr>
        @endif

        @if (count($femaleChildren) <= 0)
            <tr>
                <td class="text-center" colspan="7">
                    @if (empty($search))
                        No female children found.
                    @else
                        No search keyword match found.
                    @endif
                </td>
            </tr>
        @endif
    </tbody>
</table>
