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
                    <select id="statusSelect-{{ $cycle->status }}" name="cycle_status" class="form-control w-40 border-none" @if ($cycle->status === 'closed' && !auth()->user()->hasRole('admin')) disabled @endif>
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

                <td class="inline-flex items-center justify-center">
                    <div class="inline-flex space-x-3">
                        @can('edit-cycle-implementation')

                        @if($cycle->status !== 'closed')
                            <form id="editCycle-{{ $cycle->id }}" action="{{ route('cycle.show') }}" method="POST">
                                @csrf
                                <input type="hidden" name="cycle_id" id="cycle_id_{{ $cycle->id }}" value="{{ $cycle->id }}">
                                <button class="relative inline-flex items-center" href="#">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="#3968d2" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>

                                    <span class="font-semibold text-sm" style="color: #3968d2;">
                                        Edit
                                    </span>
                                </button>
                            </form>
                        @endif
                        @endcan
                        @can('view-cycle-implementation')
                            <form id="reportForm-{{ $cycle->id }}" action="{{ route('reports.show') }}" method="POST">
                                @csrf
                                <input type="hidden" name="cycle_id" id="cycle_id_{{ $cycle->id }}" value="{{ $cycle->id }}">
                                <button class="relative inline-flex items-center" href="#" onclick="saveAndSubmit('{{ $cycle->id }}', 'editCycle')">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="#3968d2" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    <span class="font-semibold text-sm" style="color: #3968d2;">
                                        Reports
                                    </span>
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
