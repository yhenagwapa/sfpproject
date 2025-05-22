<table id='cycle-table' class="table datatable mt-3 text-sm">
    <thead>
        <tr>
            <th><b>Cycle Implementation</b></th>
            @if(auth()->user()->hasRole('admin'))
                <th>Target</th>
                <th>Allocation</th>
            @endif
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody class="cycle-table">
        @foreach ($allCycles as $cycle)
            <tr>
                <td>{{ $cycle->name }}</td>
                @if(auth()->user()->hasRole('admin'))
                    <td>{{ number_format($cycle->target) }}</td>
                    <td>P {{ number_format($cycle->allocation,2) }}</td>
                @endif
                <td class="w-40">
                    <select id="statusSelect-{{ $cycle->status }}" name="cycle_status" class="form-control w-40 border-none" @if ($cycle->status === 'closed' || !auth()->user()->hasRole('admin')) disabled @endif>
                        @foreach ($cycleStatuses as $cycleStatus)
                            <option value="{{ $cycleStatus->value }}"
                                {{ $cycleStatus->value == $cycle->status ? 'selected' : '' }}>
                                {{ $cycleStatus->name }}
                            </option>
                        @endforeach
                    </select>
                </td>

                <!-- Modal for status change -->
                <div class="modal fade" id="statusConfirmationModal-{{ $cycle->status }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title text-red-600">Confirmation</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to change close the status of this implementation?
                            </div>
                            <div class="modal-footer">
                                <button id="confirmStatusChange-{{ $cycle->status }}" type="button"
                                    class="text-white bg-blue-600 rounded px-3 min-h-9">Confirm</button>
                                <button id="cancelStatusChange-{{ $cycle->status }}" type="button"
                                    class="text-white bg-gray-600 rounded px-3 min-h-9"
                                    data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>

                <form id="statusForm-{{ $cycle->status }}" method="POST" action="{{ route('cycle.update-cycle-status') }}">
                    @csrf

                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" id="statusValue-{{ $cycle->status }}" name="cycle_status" value="">
                    <input type="hidden" id="cycle_id" name="cycle_id" value="{{ $cycle->id }}">
                </form>

                <script>
                    let selectedStatus{{ $cycle->status }} = '';
                    let originalStatus{{ $cycle->status }} = document.getElementById('statusSelect-{{ $cycle->status }}').value;

                    document.getElementById('statusSelect-{{ $cycle->status }}').addEventListener('change', function() {
                        selectedStatus{{ $cycle->status }} = this.value;
                        let modal = new bootstrap.Modal(document.getElementById(
                        'statusConfirmationModal-{{ $cycle->status }}'));
                        modal.show();
                    });

                    document.getElementById('confirmStatusChange-{{ $cycle->status }}').addEventListener('click', function() {
                        document.getElementById('statusValue-{{ $cycle->status }}').value = selectedStatus{{ $cycle->status }};
                        document.getElementById('statusForm-{{ $cycle->status }}').submit();
                    });

                    document.getElementById('cancelStatusChange-{{ $cycle->status }}').addEventListener('click', function() {
                        document.getElementById('statusSelect-{{ $cycle->status }}').value = originalStatus{{ $cycle->status }};
                    });
                </script>

                <td class="">
                    <div class="inline-flex space-x-3">
                        @can('edit-cycle-implementation')
                            @if($cycle->status !== 'closed')
                                <form id="editCycle-{{ $cycle->id }}" action="{{ route('cycle.show') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="cycle_id" id="cycle_id_{{ $cycle->id }}" value="{{ $cycle->id }}">
                                    <button type="submit"class="flex relative group">
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
                        @endcan
                        @can('view-cycle-implementation')
                            <form id="reportForm-{{ $cycle->id }}" action="{{ route('reports.show') }}" method="POST">
                                @csrf
                                <input type="hidden" name="cycle_id" value="{{ $cycle->id }}">
                                <button type="submit" class="flex relative group">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="#3968d2" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z" />
                                    </svg>
                                    <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 scale-0 group-hover:scale-100 transition-all duration-200 bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-10">
                                        Reports
                                    </div>
                                </button>
                            </form>
                        @endcan
                    </div>
                </td>
            </tr>
        @endforeach
        @if (count($allCycles) <= 0)
            <tr>
                <td class="text-center" colspan="6">
                    @if (empty($search))
                        No Data found
                    @else
                        No search keyword match found.
                    @endif
                </td>
            </tr>
        @endif
    </tbody>
</table>
