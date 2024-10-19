<table id='users-table' class="table datatable mt-3 text-sm text-center">
    <thead>
        <tr>
            <th class="text-left" scope="col">Name</th>
            <th scope="col">Email</th>
            <th scope="col">Roles</th>
            <th scope="col">Status</th>
            @if (auth()->user()->hasRole('admin'))
                <th scope="col">Action</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @forelse ($users as $user)
            <tr>
                <td class="text-left">{{ $user->full_name }}</td>
                <td>{{ $user->email }}</td>

                <td class="justify-items-center items-center">
                    <select class="form-control w-full border-none" id="role_id-{{ $user->id }}" name="role_id" @if (auth()->user()->id === $user->id) disabled @endif>
                        <option value="" disabled>Select role</option>
                        @foreach ($roles as $role)
                            @if (!($role->name === 'admin' && !auth()->user()->hasRole('admin')))
                                <option value="{{ $role->id }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </td>

                <!-- Modal for role change -->
                <div class="modal fade" id="role_idConfirmationModal-{{ $user->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title text-red-600">Confirmation</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to change <b class="text-red-600">{{ $user->full_name }}</b>'s
                                role?
                            </div>
                            <div class="modal-footer">
                                <button id="confirmRoleChange-{{ $user->id }}" type="button"
                                    class="text-white bg-blue-600 rounded px-3 min-h-9">Confirm</button>
                                <button id="cancelRoleChange-{{ $user->id }}" type="button"
                                    class="text-white bg-gray-600 rounded px-3 min-h-9"
                                    data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>

                <form id="roleForm-{{ $user->id }}" method="POST"
                    action="{{ route('users.update-role', $user->id) }}">
                    @csrf

                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" id="roleValue-{{ $user->id }}" name="role_id" value="">
                </form>

                <script>
                    let selectedRole{{ $user->id }} = '';
                    let originalRole{{ $user->id }} = document.getElementById('role_id-{{ $user->id }}').value;

                    document.getElementById('role_id-{{ $user->id }}').addEventListener('change', function() {
                        selectedRole{{ $user->id }} = this.value;
                        let modal = new bootstrap.Modal(document.getElementById(
                            'role_idConfirmationModal-{{ $user->id }}'));
                        modal.show();
                    });

                    document.getElementById('confirmRoleChange-{{ $user->id }}').addEventListener('click', function() {
                        document.getElementById('roleValue-{{ $user->id }}').value = selectedRole{{ $user->id }};
                        document.getElementById('roleForm-{{ $user->id }}').submit();
                    });

                    document.getElementById('cancelRoleChange-{{ $user->id }}').addEventListener('click', function() {
                        document.getElementById('role_id-{{ $user->id }}').value = originalRole{{ $user->id }};
                    });
                </script>



                <td class="w-40">
                    <select id="statusSelect-{{ $user->id }}" name="status" class="form-control w-40 border-none" @if (auth()->user()->id === $user->id) disabled @endif>
                        <option value="" disabled>Select status</option>
                        <option value="inactive" {{ $user->status == 'for activation' ? 'selected' : '' }}>
                            For Activation
                        </option>
                        <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>
                            Active
                        </option>
                        <option value="deactivated" {{ $user->status == 'deactivated' ? 'selected' : '' }}>
                            Deactivated
                        </option>
                    </select>
                </td>

                <!-- Modal for status change -->
                <div class="modal fade" id="statusConfirmationModal-{{ $user->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title text-red-600">Confirmation</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to change <b class="text-red-600">{{ $user->full_name }}</b>'s
                                status?
                            </div>
                            <div class="modal-footer">
                                <button id="confirmStatusChange-{{ $user->id }}" type="button"
                                    class="text-white bg-blue-600 rounded px-3 min-h-9">Confirm</button>
                                <button id="cancelStatusChange-{{ $user->id }}" type="button"
                                    class="text-white bg-gray-600 rounded px-3 min-h-9"
                                    data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>

                <form id="statusForm-{{ $user->id }}" method="POST"
                    action="{{ route('users.update-status', $user->id) }}">
                    @csrf

                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" id="statusValue-{{ $user->id }}" name="status" value="">
                </form>

                <script>
                    let selectedStatus{{ $user->id }} = '';
                    let originalStatus{{ $user->id }} = document.getElementById('statusSelect-{{ $user->id }}').value;

                    document.getElementById('statusSelect-{{ $user->id }}').addEventListener('change', function() {
                        selectedStatus{{ $user->id }} = this.value;
                        let modal = new bootstrap.Modal(document.getElementById(
                        'statusConfirmationModal-{{ $user->id }}'));
                        modal.show();
                    });

                    document.getElementById('confirmStatusChange-{{ $user->id }}').addEventListener('click', function() {
                        document.getElementById('statusValue-{{ $user->id }}').value = selectedStatus{{ $user->id }};
                        document.getElementById('statusForm-{{ $user->id }}').submit();
                    });

                    document.getElementById('cancelStatusChange-{{ $user->id }}').addEventListener('click', function() {
                        document.getElementById('statusSelect-{{ $user->id }}').value = originalStatus{{ $user->id }};
                    });
                </script>

                {{-- <form action="{{ route('users.destroy', $user->id) }}" method="post">
                    @csrf
                    @method('DELETE')

                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-warning btn-sm"><i class="bi bi-eye"></i> Show</a>

                    
                        <a href="{{ route('users.edit', $user->id) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="2" stroke="#3968d2" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                            
                            <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1">
                                Edit
                            </div>
                        </a>   

                        @can('delete-user')
                            @if (Auth::user()->id != $user->id)
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Do you want to delete this user?');"><i class="bi bi-trash"></i> Delete</button>
                            @endif
                        @endcan
                    
                        
                    
                </form> --}}
                <!-- Reset Password Button -->
                @if (auth()->user()->hasRole('admin'))
                    <td class="justify-items-center items-center">
                        <button type="button" class="text-white bg-blue-600 rounded px-3 min-h-9 custom-disabled-btn" data-bs-toggle="modal"
                            data-bs-target="#resetPasswordModal-{{ $user->id }}" @if (auth()->user()->id === $user->id) disabled  @endif>
                            Reset Password
                        </button>
                    </td>

                    <!-- Modal for Reset Password -->
                    <div class="modal fade" id="resetPasswordModal-{{ $user->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-red-600">Confirmation</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to reset <b class="text-red-600">{{ $user->full_name }}</b>'s
                                    password?
                                </div>
                                <div class="modal-footer">
                                    <button id="confirmResetPassword-{{ $user->id }}" type="button"
                                        class="text-white bg-blue-600 rounded px-3 min-h-9">Confirm</button>
                                    <button type="button" class="text-white bg-gray-600 rounded px-3 min-h-9"
                                        data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form id="resetPasswordForm-{{ $user->id }}" method="POST"
                        action="{{ route('users.reset-password', $user->id) }}">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                    </form>

                    <script>
                        document.getElementById('confirmResetPassword-{{ $user->id }}').addEventListener('click', function() {
                            document.getElementById('resetPasswordForm-{{ $user->id }}').submit();
                        });
                    </script>
                @endif
                

            </tr>
        @empty
            <td colspan="5">
                <span class="text-danger">
                    <strong>No User Found!</strong>
                </span>
            </td>
        @endforelse
    </tbody>
</table>
