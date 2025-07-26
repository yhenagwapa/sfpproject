<table id='children-table' class="table datatable mt-3 text-sm">
    <thead class="text-sm">
        <tr>
            <th>No.</th>
            <th>Child Name</th>
            <th>Sex</th>
            <th>Date of Birth</th>
            <th>CDC/SNP</th>
            <th>Funded</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @if ($children)
            @foreach ($children as $child)
                <tr>
                    <td class="text-center"></td>
                    <td>{{ $child->full_name }}</td>
                    <td>{{ $child->sex->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($child->date_of_birth)->format('m-d-Y') }}</td>
                    <td>
                        {{ $child->records->first()?->center->center_name }}
                    </td>
                    <td>
                        {{ $child->records->first()?->funded ? 'Yes' : 'No' }}
                    </td>
                    <td>
                        <select id="statusSelect-{{ $child->id }}" name="status" class="form-control uppercase w-full border-none"
                            @if ($child->records->first()?->status == 'dropped') disabled @endif onchange="handleSelectChange(this, {{ $sample->id }})">
                            <option value="" disabled>Select status</option>
                            <option value="active"
                                {{ $child->records->first()?->status === 'active' ? 'selected' : '' }}>
                                Active
                            </option>
                            @if (!auth()->user()->hasRole(['child development worker', 'encoder']))
                                <option value="transferred" {{ $child->records->first()?->status === 'transferred' ? 'selected' : '' }}>
                                    Transferred
                                </option>
                            @endif
                            <option value="dropped"
                                {{ $child->records->first()?->status === 'dropped' ? 'selected' : '' }}>
                                Dropped
                            </option>
                        </select>
                    </td>

                    {{-- modal for drop student --}}
                    <div class="modal fade" id="dropStudentModal-{{ $child->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-red-600">Confirmation</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to change <b class="text-red-600 uppercase">{{ $child->full_name }}</b>'s
                                    status?
                                </div>
                                <div class="modal-footer">
                                    <button id="confirmStatusChange-{{ $child->id }}" type="button"
                                        class="text-white bg-blue-600 rounded px-3 min-h-9">Confirm</button>
                                    <button id="cancelStatusChange-{{ $child->id }}" type="button"
                                        class="text-white bg-gray-600 rounded px-3 min-h-9"
                                        data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- modal for transfer student --}}
                    <div class="modal fade" id="transferStudentModal-{{ $child->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-red-600">Confirmation</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <label for="child_development_center_id">CDC or SNP <span
                                        class="text-red-600">*</span></label>
                                    <select class="form-control rounded border-gray-300 uppercase" id="child_development_center_id"
                                        name='child_development_center_id' required>
                                        <option value="" selected>Select CDC or SNP</option>
                                        @foreach ($centerNames as $center)
                                            <option value="{{ $center->id }}"
                                                {{ $center->id == old('child_development_center_id') ? 'selected' : '' }}>
                                                {{ $center->center_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('child_development_center_id'))
                                        <span
                                            class="text-xs text-red-600">{{ $errors->first('child_development_center_id') }}</span>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button id="confirmStatusChange-{{ $child->id }}" type="button"
                                        class="text-white bg-blue-600 rounded px-3 min-h-9">Confirm</button>
                                    <button id="cancelStatusChange-{{ $child->id }}" type="button"
                                        class="text-white bg-gray-600 rounded px-3 min-h-9"
                                        data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form id="statusForm-{{ $child->id }}" method="POST"
                        action="{{ route('child.update-status') }}">
                        @csrf

                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" id="statusValue-{{ $child->id }}" name="status" value="">
                        <input type="hidden" id="statusValue-{{ $child->id }}" name="child_id" value="{{ $child->id }}">
                    </form>

                    <script>
                        let selectedStatus{{ $child->id }} = '';
                        let originalStatus{{ $child->id }} = document.getElementById('statusSelect-{{ $child->id }}').value;

                        document.getElementById('statusSelect-{{ $child->id }}').addEventListener('change', function() {
                            selectedStatus{{ $child->id }} = this.value;
                            let modal = new bootstrap.Modal(document.getElementById(
                                'dropStudentModal-{{ $child->id }}'));
                            modal.show();
                        });

                        document.getElementById('confirmStatusChange-{{ $child->id }}').addEventListener('click', function() {
                            document.getElementById('statusValue-{{ $child->id }}').value = selectedStatus{{ $child->id }};
                            document.getElementById('statusForm-{{ $child->id }}').submit();
                        });

                        document.getElementById('cancelStatusChange-{{ $child->id }}').addEventListener('click', function() {
                            document.getElementById('statusSelect-{{ $child->id }}').value = originalStatus{{ $child->id }};
                        });
                    </script>

                    {{-- actions column --}}
                    <td>
                        <div class="flex space-x-3">
                            <form action="{{ route('child.view') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="child_id" value="{{ $child->id }}">
                                <button class="flex child-ns-btn relative group">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="#3968d2" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>

                                    <div
                                        class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 scale-0 group-hover:scale-100 transition-all duration-200 bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-10">
                                        View
                                    </div>
                                </button>
                            </form>

                            @if (session('temp_can_edit') || auth()->user()?->can('edit-child'))
                                @if ($child->records->first()?->status != 'dropped')
                                    @if (auth()->user()->hasRole('admin') || $child->edit_counter != 2)
                                        <form id="editChild-{{ $child->id }}" action="{{ route('child.show') }}"
                                            method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="child_id" value="{{ $child->id }}">
                                            <button type="submit" class="flex edit-child-btn relative group">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="#00000099"
                                                    class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
                                                <div
                                                    class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 scale-0 group-hover:scale-100 transition-all duration-200 bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-10">
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

                            {{-- @canany(['create-nutritional-status', 'edit-nutritional-status']) --}}
                            <form id="childNS-{{ $child->id }}" action="{{ route('nutritionalstatus.create') }}"
                                method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="child_id" value="{{ $child->id }}">
                                <button class="flex child-ns-btn relative group">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="#1e9730" class="w-5 h-5">
                                        <path
                                            d="M3 17L8 12L11 15L17 9M17 9H13M17 9V13M6 21H18C19.6569 21 21 19.6569 21 18V6C21 4.34315 19.6569 3 18 3H6C4.34315 3 3 4.34315 3 6V18C3 19.6569 4.34315 21 6 21Z">
                                        </path>
                                    </svg>
                                    <div
                                        class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 scale-0 group-hover:scale-100 transition-all duration-200 bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-10">
                                        Nutritional Status
                                    </div>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>

<script>
    function handleTypeChange(select, childId) {
        const value = select.value;

        if (value === 'dropped') {
            const modal = new bootstrap.Modal(document.getElementById(`dropStudentModal-${childId}`));
            modal.show();
        } else if (value === 'transferred') {
            const modal = new bootstrap.Modal(document.getElementById(`transferStudentModal-${childId}`));
            modal.show();
        }
    }
</script>
