<table id='children-table' class="table datatable mt-3">
    <thead class="text-base">
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
    <tbody class="text-sm">
        @if ($children)
            @foreach ($children as $child)
                <tr>
                    <td class="text-center"></td>
                    <td>{{ $child->full_name }}</td>
                    <td>{{ $child->sex->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($child->date_of_birth)->format('m-d-Y') }}</td>
                    <td>
                        @if ($child->records->first()?->status === 'transferred')
                            {{ $child->records->get(1)?->center->center_name }}
                        @else
                            {{ $child->records->first()?->center->center_name }}
                        @endif
                        
                    </td>
                    <td>
                        {{ $child->records->first()?->funded ? 'Yes' : 'No' }}
                    </td>

                    <td>
                        <select id="statusSelect-{{ $child->id }}" name="status"
                            class="@if ($child->records->first()?->status === 'dropped') rounded border-gray-300 bg-gray-500 text-white uppercase w-full
                                @else
                                    rounded border-gray-300 uppercase w-full @endif">

                            <option value="" selected disabled>{{ $child->records->first()?->status }}</option>

                            @if ($child->records->first()?->status == 'transferred' || $child->records->first()?->status == 'active')
                                <option value="active" hidden>
                                    Active
                                </option>
                            @else
                                <option value="active">
                                    Active
                                </option>
                            @endif

                            @if (!auth()->user()->hasAnyRole(['child development worker', 'encoder']))
                                <option value="transferred">
                                    Transferred
                                </option>
                            {{-- @elseif ($child->records->first()?->status === 'transferred')
                                <option value="transferred" disabled>
                                    Transferred
                                </option> --}}
                            @endif

                            @if ($child->records->first()?->status === 'dropped')
                                <option value="dropped" disabled>
                                    Dropped
                                </option>
                            @else
                                @if (!auth()->user()->hasAnyRole(['lgu focal', 'sfp coordinator', 'admin']))
                                    <option value="dropped">
                                        Dropped
                                    </option>
                                @endif
                            @endif
                        </select>
                    </td>

                    {{-- modal for drop student --}}
                    <div class="modal fade" id="dropStudentModal-{{ $child->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-red-600">Confirmation</h5>
                                    <button id="cancelDropClose-{{ $child->id }}" type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to drop <b
                                        class="text-red-600 uppercase">{{ $child->full_name }}</b>?
                                </div>
                                <div class="modal-footer">
                                    <button id="confirmDrop-{{ $child->id }}" type="button"
                                        class="text-white bg-blue-600 rounded px-3 min-h-9">Confirm</button>
                                    <button id="cancelDropBtn-{{ $child->id }}" type="button"
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
                                    <button id="cancelTransferClose-{{ $child->id }}" type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <label for="child_development_center_id">CDC or SNP <span
                                            class="text-red-600">*</span></label>
                                    <select id="selectedCenter{{ $child->id }}" class="form-control rounded border-gray-300 uppercase"
                                            name='newCenter' required>
                                        <option value="" selected>Select CDC or SNP</option>
                                        @foreach ($centerNames as $center)
                                            @if ($center->id != $child->records->first()?->center->id)
                                                <option value="{{ $center->id }}"
                                                    {{ $center->id == old('child_development_center_id') ? 'selected' : '' }}>
                                                    {{ $center->center_name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('newCenter')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="modal-footer">
                                    <button id="confirmTransfer-{{ $child->id }}" type="button"
                                        class="text-white bg-blue-600 rounded px-3 min-h-9" disabled>Confirm</button>
                                    <button id="cancelTransferBtn-{{ $child->id }}" type="button"
                                        class="text-white bg-gray-600 rounded px-3 min-h-9"
                                        data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($errors->has('newCenter'))
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                let modal = new bootstrap.Modal(document.getElementById(
                                    'transferStudentModal-{{ $child->id }}'));
                                modal.show();
                            });
                        </script>
                    @endif

                    <form id="statusForm-{{ $child->id }}" method="POST" action="{{ route('child.update-status') }}">
                        @csrf

                        <input type="hidden" name="_method" value="PUT">

                        <input type="hidden" name="child_id" value="{{ $child->id }}">
                        <input type="hidden" name="status" id="statusValue-{{ $child->id }}" value="">
                        <input type="hidden" name="oldCenter" value="{{ $child->records->first()?->center->id }}">
                        <input type="hidden" name="newCenter" id="newCenter-{{ $child->id }}" value="">
                    </form>

                    {{-- script for showing transfer/drop modal --}}
                    <script>
                        let selectedStatus{{ $child->id }} = '';
                        let originalStatus{{ $child->id }} = "{{ $child->records->first()?->status }}";

                        document.getElementById('statusSelect-{{ $child->id }}').addEventListener('change', function() {
                            selectedStatus{{ $child->id }} = this.value;

                            if (selectedStatus{{ $child->id }} === 'dropped') {
                                let modal = new bootstrap.Modal(document.getElementById('dropStudentModal-{{ $child->id }}'));
                                modal.show();
                            } else if (selectedStatus{{ $child->id }} === 'transferred') {
                                let modal = new bootstrap.Modal(document.getElementById(
                                    'transferStudentModal-{{ $child->id }}'));
                                modal.show();
                            }
                        });

                        document.addEventListener("DOMContentLoaded", function () {
                            let dropdown = document.getElementById("selectedCenter{{ $child->id }}");
                            let confirmBtn = document.getElementById("confirmTransfer-{{ $child->id }}");

                            // save initial value outside the event listener
                            let initialValue = dropdown.value;

                            // start disabled & gray
                            confirmBtn.disabled = true;
                            confirmBtn.classList.remove("bg-blue-600");
                            confirmBtn.classList.add("bg-gray-500");

                            dropdown.addEventListener("change", function () {
                                if (dropdown.value !== "" && dropdown.value !== initialValue) {
                                    confirmBtn.disabled = false;
                                    confirmBtn.classList.remove("bg-gray-500");
                                    confirmBtn.classList.add("bg-blue-600");
                                } else {
                                    confirmBtn.disabled = true;
                                    confirmBtn.classList.remove("bg-blue-600");
                                    confirmBtn.classList.add("bg-gray-500");
                                }
                            });
                        });

                        document.getElementById('confirmDrop-{{ $child->id }}').addEventListener('click', function() {
                            document.getElementById('statusValue-{{ $child->id }}').value = 'dropped';
                            document.getElementById('statusForm-{{ $child->id }}').submit();
                        });

                        document.getElementById('confirmTransfer-{{ $child->id }}').addEventListener('click', function() {

                            let selectedCenter = document.getElementById('selectedCenter{{ $child->id }}').value;

                            document.getElementById('statusValue-{{ $child->id }}').value = 'transferred';
                            document.getElementById('newCenter-{{ $child->id }}').value = selectedCenter;

                            document.getElementById('statusForm-{{ $child->id }}').submit();
                        });

                        document.getElementById('cancelDropClose-{{ $child->id }}').addEventListener('click', function() {
                            document.getElementById('statusSelect-{{ $child->id }}').value = originalStatus{{ $child->id }};
                        });

                        document.getElementById('cancelDropBtn-{{ $child->id }}').addEventListener('click', function() {
                            document.getElementById('statusSelect-{{ $child->id }}').value = "";
                        });

                        document.getElementById('cancelTransferClose-{{ $child->id }}').addEventListener('click', function() {
                            document.getElementById('statusSelect-{{ $child->id }}').value = originalStatus{{ $child->id }};
                        });

                        document.getElementById('cancelTransferBtn-{{ $child->id }}').addEventListener('click', function() {
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
                                @if ($child->records->first()?->status != 'dropped' || auth()->user()->hasRole('admin'))
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
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="#D1D5DB80"
                                                class="w-5 h-5">
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
