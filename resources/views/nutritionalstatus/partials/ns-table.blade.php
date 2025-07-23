
    <h5 class="card-title">Upon Entry</h5>
    <table id="ns-table" class="table t-3 text-sm text-center w-full">
        <thead>
            <tr>
                <th>Actual Date of Weighing</th>
                <th>Weight</th>
                <th>Height</th>
                <th>Age in Months</th>
                <th>Age in Years</th>
                <th>Weight for Age</th>
                <th>Weight for Height</th>
                <th>Height for Age</th>
                <th>Malnourish</th>
                <th>Undernourish</th>
                <th class="@if (Route::is('nutritionalstatus.edit')) hidden @endif">Action</th>
            </tr>

        </thead>
        <tbody class="text-base">
            @if (!$hasUponEntryData)
                <tr>
                    <td class="text-center text-red-600" colspan="11">
                        No data found.
                    </td>
                </tr>
            @else
                <tr>
                    <td>{{ $entryDetails->actual_weighing_date }}</td>
                    <td>{{ $entryDetails->weight }}</td>
                    <td>{{ $entryDetails->height }}</td>
                    <td>{{ $entryDetails->age_in_months }}</td>
                    <td>{{ $entryDetails->age_in_years }}</td>
                    <td class="{{ $entryDetails->weight_for_age !== 'Normal' ? 'text-red-500' : '' }}">{{ $entryDetails->weight_for_age }}</td>
                    <td class="{{ $entryDetails->weight_for_height !== 'Normal' ? 'text-red-500' : '' }}">{{ $entryDetails->weight_for_height }}</td>
                    <td class="{{ $entryDetails->height_for_age !== 'Normal' ? 'text-red-500' : '' }}">{{ $entryDetails->height_for_age }}</td>
                    <td class="{{ $entryDetails->is_malnourish ? 'text-red-500' : '' }}">{{ $entryDetails->is_malnourish ? 'Yes' : 'No' }}</td>
                    <td class="{{ $entryDetails->is_undernourish ? 'text-red-500' : '' }}">{{ $entryDetails->is_undernourish ? 'Yes' : 'No' }}</td>
                    <td class="@if (Route::is('nutritionalstatus.edit')) hidden @endif">
                    @if(session('temp_can_edit') || auth()->user()?->can('edit-nutritional-status'))
                        @if( $childStatus != 'dropped')
                            @if(auth()->user()->hasRole('admin') || $entryDetails->edit_counter != 2)
                                <form action="{{ route('nutritionalstatus.show') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="child_id" value="{{ $child->id }}">
                                    <button type="submit" class="flex relative group">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="#00000099" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                        <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 scale-0 group-hover:scale-100 transition-all duration-200 bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-10">
                                            Edit
                                        </div>
                                    </button>
                                </form>
                            @else
                                <button class="flex relative group" disabled>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="#D1D5DB80" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </button>
                            @endif
                        @else
                            <button class="flex relative group" disabled>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="#D1D5DB80" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                            </button>
                        @endif
                    @endif
                </td>
                </tr>
            @endif
        </tbody>
    </table>

<div></div>
    <h5 class="card-title">After 120 Feeding Days</h5>
    <table class="table mt-3 text-sm text-center w-full">
        <thead>
            <tr>
                <th>Actual Date of Weighing</th>
                <th>Weight</th>
                <th>Height</th>
                <th>Age in Months</th>
                <th>Age in Years</th>
                <th>Weight for Age</th>
                <th>Weight for Height</th>
                <th>Height for Age</th>
                <th>Malnourish</th>
                <th>Undernourish</th>
                <th class="@if (Route::is('nutritionalstatus.edit')) hidden @endif">Action</th>
            </tr>
        </thead>
        <tbody class="text-base">
            @if (!$hasUponExitData)
                <tr>
                    <td class="text-center text-red-600" colspan="11">
                        No data found.
                    </td>
                </tr>
            @else
                <td>{{ $exitDetails->actual_weighing_date }}</td>
                <td>{{ $exitDetails->weight }}</td>
                <td>{{ $exitDetails->height }}</td>
                <td>{{ $exitDetails->age_in_months }}</td>
                <td>{{ $exitDetails->age_in_years }}</td>
                <td class="{{ $exitDetails->weight_for_age !== 'Normal' ? 'text-red-500' : '' }}">{{ $exitDetails->weight_for_age }}</td>
                <td class="{{ $exitDetails->weight_for_height !== 'Normal' ? 'text-red-500' : '' }}">{{ $exitDetails->weight_for_height }}</td>
                <td class="{{ $exitDetails->height_for_age !== 'Normal' ? 'text-red-500' : '' }}">{{ $exitDetails->height_for_age }}</td>
                <td class="{{ $exitDetails->is_malnourish ? 'text-red-500' : '' }}">{{ $exitDetails->is_malnourish ? 'Yes' : 'No' }}</td>
                <td class="{{ $exitDetails->is_undernourish ? 'text-red-500' : '' }}">{{ $exitDetails->is_undernourish ? 'Yes' : 'No' }}</td>
                <td class="@if (Route::is('nutritionalstatus.edit')) hidden @endif">
                    @if(session('temp_can_edit') || auth()->user()?->can('edit-nutritional-status'))
                        @if( $childStatus != 'dropped')
                            @if(auth()->user()->hasRole('admin') || $exitDetails->edit_counter != 2)
                                <form action="{{ route('nutritionalstatus.show') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="child_id" value="{{ $child->id }}">
                                    <button type="submit" class="flex relative group">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="#00000099" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                        <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 scale-0 group-hover:scale-100 transition-all duration-200 bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-10">
                                            Edit
                                        </div>
                                    </button>
                                </form>
                            @else
                                <button class="flex relative group" disabled>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="#D1D5DB80" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </button>
                            @endif
                        @else
                            <button class="flex relative group" disabled>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="#D1D5DB80" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                            </button>
                        @endif
                    @endif
                </td>
            @endif
        </tbody>
    </table>

