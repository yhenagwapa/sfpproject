
<table id='activitylogs-table' class="table datatable mt-3 text-sm">
    <thead>
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Description</th>
            <th>Subject</th>
            <th>User</th>
            <th>View Properties</th>
        </tr>
    </thead>
    <tbody class="activitylogs-table">
        @foreach($groupedActivities  as $activity)
            <tr>
                <td>{{ $activity['activity_id'] }}</td>
                <td>{{ $activity['created_at'] }}</td>
                <td>{{ $activity['description'] }}</td>
                <td>{{ $activity['subject_type'] }}</td>
                <td>{{ $activity['causer'] }}</td>
                <td class="">
                    <div class="flex space-x-3">
                        <button class="relative inline-flex items-center justify-center" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="#3968d2" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <span class="font-semibold text-sm" style="color: #3968d2;">
                                View
                            </span>
                        </button>
                    </div>
                </td>

                <div class="modal fade" id="verticalycentered" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title text-red-600">Activity Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            @if ($activity['event_type'] === 'Updated')
                                <div class="modal-body">

                                    <h5>Updated Values:</h5>
                                    <ul>
                                        @foreach ($activity['changed_fields'] as $field => $values)
                                            <li>{{ $field }}: from "{{ $values['old'] }}" to "{{ $values['new'] }}"</li>
                                        @endforeach
                                    </ul>

                                </div>
                            @else
                                <div class="modal-body">
                                    <h5>Created Values:</h5>
                                    <ul>
                                        @foreach ($activity['changed_fields'] as $field => $value)
                                            <li>{{ $field }}: {{ $value }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="modal-footer">
                                <button type="button" class="text-white bg-gray-600 rounded px-3 min-h-9" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </tr>
        @endforeach
    </tbody>
</table>



<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</script>

