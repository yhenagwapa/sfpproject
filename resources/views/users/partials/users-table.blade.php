<table id='users-table' class="table datatable mt-3">
    <thead class="text-base">
    <tr>
        <th>No.</th>
        <th class="text-left">Name</th>
        <th>Email</th>
        <th>Email Verified?</th>
        <th>Roles</th>
        <th>Status</th>
        @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('lgu focal'))
            <th>Action</th>
        @endif
    </tr>
    </thead>
    <tbody class="text-sm">
    </tbody>
</table>

<!-- Role Change Confirmation Modal -->
<div class="modal fade" id="roleConfirmationModal" tabindex="-1" aria-labelledby="roleConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-red-600" id="roleConfirmationModalLabel">Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to change <b class="text-red-600 uppercase" id="roleModalUserName"></b>'s role?
            </div>
            <div class="modal-footer">
                <button id="confirmRoleChangeBtn" type="button" class="text-white bg-blue-600 rounded px-3 min-h-9">Confirm</button>
                <button id="cancelRoleChangeBtn" type="button" class="text-white bg-gray-600 rounded px-3 min-h-9" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Status Change Confirmation Modal -->
<div class="modal fade" id="statusConfirmationModal" tabindex="-1" aria-labelledby="statusConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-red-600" id="statusConfirmationModalLabel">Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to change <b class="text-red-600 uppercase" id="statusModalUserName"></b>'s status?
            </div>
            <div class="modal-footer">
                <button id="confirmStatusChangeBtn" type="button" class="text-white bg-blue-600 rounded px-3 min-h-9">Confirm</button>
                <button id="cancelStatusChangeBtn" type="button" class="text-white bg-gray-600 rounded px-3 min-h-9" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Confirmation Modal -->
<div class="modal fade" id="resetPasswordConfirmationModal" tabindex="-1" aria-labelledby="resetPasswordConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-red-600" id="resetPasswordConfirmationModalLabel">Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to reset <b class="text-red-600 uppercase" id="resetPasswordModalUserName"></b>'s password?
            </div>
            <div class="modal-footer">
                <button id="confirmResetPasswordBtn" type="button" class="text-white bg-blue-600 rounded px-3 min-h-9">Confirm</button>
                <button id="cancelResetPasswordBtn" type="button" class="text-white bg-gray-600 rounded px-3 min-h-9" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('load', function () {
        $(document).ready(function () {
            const canManageUsers = {{ (auth()->user()->hasRole('admin') || auth()->user()->hasRole('lgu focal')) ? 'true' : 'false' }};
            const isAdmin = {{ auth()->user()->hasRole('admin') ? 'true' : 'false' }};
            const isLguFocal = {{ auth()->user()->hasRole('lgu focal') ? 'true' : 'false' }};
            const currentUserId = {{ auth()->user()->id }};

            // Get roles data
            const rolesData = @json($roles ?? []);

            const table = $("#users-table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    data: {
                        user_id: {{ auth()->user()->id }}
                    },
                    error: function (xhr, error, code) {
                        console.error('DataTables error:', error);
                        console.error('Response:', xhr.responseText);
                    },
                    type: 'GET',
                    url: '{{ url('/') }}/api/users'
                },
                columns: [
                    {
                        data: 'no',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'name',
                        name: 'name',
                        className: 'text-left'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'email_verified_at',
                        name: 'email_verified_at',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: null,
                        name: 'roles',
                        orderable: false,
                        render: function(data, type, row) {
                            const isCurrentUser = row.is_current_user;
                            const disabled = isCurrentUser ? 'disabled' : '';

                            let options = '<option value="" disabled>Select role</option>';

                            let rolesDataArr = Object.values(rolesData);

                            rolesDataArr.forEach(role => {
                                // Hide admin and pdo roles from non-admins
                                if ((role.name === 'admin' || role.name === 'pdo') && !isAdmin) {
                                    return;
                                }

                                const selected = row.roles.includes(role.name) ? 'selected' : '';
                                options += `<option value="${role.id}" ${selected}>${role.name}</option>`;
                            });

                            return `
                                <select class="form-control uppercase w-full border-none role-select"
                                        data-user-id="${row.id}"
                                        data-original-role="${row.current_role_id}"
                                        ${disabled}>
                                    ${options}
                                </select>
                            `;
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        className: 'w-40',
                        render: function(data, type, row) {
                            const isCurrentUser = row.is_current_user;
                            const disabled = isCurrentUser ? 'disabled' : '';

                            return `
                                <select class="form-control w-40 border-none status-select"
                                        data-user-id="${row.id}"
                                        data-original-status="${row.status}"
                                        ${disabled}>
                                    <option value="" disabled>Select status</option>
                                    <option value="inactive" ${row.status === 'for activation' ? 'selected' : ''}>For Activation</option>
                                    <option value="active" ${row.status === 'active' ? 'selected' : ''}>Active</option>
                                    <option value="deactivated" ${row.status === 'deactivated' ? 'selected' : ''}>Deactivated</option>
                                </select>
                            `;
                        }
                    },
                        @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('lgu focal'))
                    {
                        data: null,
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'justify-items-center items-center',
                        render: function(data, type, row) {
                            const isCurrentUser = row.is_current_user;
                            const isLguFocalUser = row.roles.includes('lgu focal');

                            // LGU Focal cannot reset other LGU Focal's password
                            if (isLguFocal && isLguFocalUser) {
                                return '';
                            }

                            if (isCurrentUser) {
                                return '<button type="button" class="text-white bg-blue-600 rounded px-3 min-h-9 custom-disabled-btn" disabled>Reset Password</button>';
                            }

                            return `
                                <button type="button"
                                        class="text-white bg-blue-600 rounded px-3 min-h-9 reset-password-btn"
                                        data-user-id="${row.id}"
                                        data-user-name="${row.name}">
                                    Reset Password
                                </button>
                            `;
                        }
                    }
                    @endif
                ],
                order: [[1, 'asc']], // Default sort by name
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ users",
                    infoEmpty: "No users to display",
                    infoFiltered: "(filtered from _MAX_ total users)",
                    zeroRecords: "No matching users found",
                    emptyTable: "No users available"
                },
                drawCallback: function(settings) {
                    console.log('Users table redrawn');
                    initializeEventHandlers();
                }
            });

            // Initialize event handlers for dynamically created elements
            function initializeEventHandlers() {
                // Role change handler
                $('.role-select').off('change').on('change', function() {
                    const userId = $(this).data('user-id');
                    const newRoleId = $(this).val();
                    const originalRoleId = $(this).data('original-role');
                    const selectElement = $(this);

                    showRoleConfirmationModal(userId, newRoleId, originalRoleId, selectElement);
                });

                // Status change handler
                $('.status-select').off('change').on('change', function() {
                    const userId = $(this).data('user-id');
                    const newStatus = $(this).val();
                    const originalStatus = $(this).data('original-status');
                    const selectElement = $(this);

                    showStatusConfirmationModal(userId, newStatus, originalStatus, selectElement);
                });

                // Reset password handler
                $('.reset-password-btn').off('click').on('click', function() {
                    const userId = $(this).data('user-id');
                    const userName = $(this).data('user-name');

                    showResetPasswordModal(userId, userName);
                });
            }

            // Role confirmation modal
            let roleModalData = { userId: null, newRoleId: null, originalRoleId: null, selectElement: null };
            let statusModalData = { userId: null, newStatus: null, originalStatus: null, selectElement: null };
            let resetPasswordModalData = { userId: null, userName: null };

            const roleConfirmationModalEl = document.getElementById('roleConfirmationModal');
            const statusConfirmationModalEl = document.getElementById('statusConfirmationModal');
            const resetPasswordConfirmationModalEl = document.getElementById('resetPasswordConfirmationModal');

            const roleConfirmationModal = roleConfirmationModalEl ? new bootstrap.Modal(roleConfirmationModalEl) : null;
            const statusConfirmationModal = statusConfirmationModalEl ? new bootstrap.Modal(statusConfirmationModalEl) : null;
            const resetPasswordConfirmationModal = resetPasswordConfirmationModalEl ? new bootstrap.Modal(resetPasswordConfirmationModalEl) : null;

            function showRoleConfirmationModal(userId, newRoleId, originalRoleId, selectElement) {
                const userName = selectElement.closest('tr').find('td:eq(1)').text();
                document.getElementById('roleModalUserName').textContent = userName;

                roleModalData = { userId, newRoleId, originalRoleId, selectElement };

                if (roleConfirmationModal) {
                    roleConfirmationModal.show();
                }
            }

            function showStatusConfirmationModal(userId, newStatus, originalStatus, selectElement) {
                const userName = selectElement.closest('tr').find('td:eq(1)').text();
                document.getElementById('statusModalUserName').textContent = userName;

                statusModalData = { userId, newStatus, originalStatus, selectElement };

                if (statusConfirmationModal) {
                    statusConfirmationModal.show();
                }
            }

            function showResetPasswordModal(userId, userName) {
                document.getElementById('resetPasswordModalUserName').textContent = userName;

                resetPasswordModalData = { userId, userName };

                if (resetPasswordConfirmationModal) {
                    resetPasswordConfirmationModal.show();
                }
            }

            // Confirm / cancel button handlers for role change
            $('#confirmRoleChangeBtn').off('click').on('click', function() {
                const { userId, newRoleId, originalRoleId, selectElement } = roleModalData;
                if (!userId || !newRoleId) {
                    return;
                }

                $.ajax({
                    url: '{{ url('/') }}/users/' + userId + '/role',
                    type: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        role_id: newRoleId
                    },
                    success: function(response) {
                        if (roleConfirmationModal) {
                            roleConfirmationModal.hide();
                        }
                        // alert('Role updated successfully.');
                        table.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        if (roleConfirmationModal) {
                            roleConfirmationModal.hide();
                        }
                        alert('Failed to update role.');
                        if (selectElement) {
                            selectElement.val(originalRoleId);
                        }
                    }
                });
            });

            $('#cancelRoleChangeBtn').off('click').on('click', function() {
                if (roleModalData.selectElement) {
                    roleModalData.selectElement.val(roleModalData.originalRoleId);
                }
            });

            // Confirm / cancel button handlers for status change
            $('#confirmStatusChangeBtn').off('click').on('click', function() {
                const { userId, newStatus, originalStatus, selectElement } = statusModalData;
                if (!userId || !newStatus) {
                    return;
                }

                $.ajax({
                    url: '{{ url('/') }}/users/' + userId + '/status',
                    type: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: newStatus
                    },
                    success: function(response) {
                        if (statusConfirmationModal) {
                            statusConfirmationModal.hide();
                        }
                        // alert('Status updated successfully.');
                        table.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        if (statusConfirmationModal) {
                            statusConfirmationModal.hide();
                        }
                        alert('Failed to update status.');
                        if (selectElement) {
                            selectElement.val(originalStatus);
                        }
                    }
                });
            });

            $('#cancelStatusChangeBtn').off('click').on('click', function() {
                if (statusModalData.selectElement) {
                    statusModalData.selectElement.val(statusModalData.originalStatus);
                }
            });

            // Confirm / cancel button handlers for reset password
            $('#confirmResetPasswordBtn').off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const { userId } = resetPasswordModalData;

                // Disable button to prevent double-clicks
                $(this).prop('disabled', true);

                $.ajax({
                    url: '{{ url('/') }}/users/' + userId + '/reset-password',
                    type: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (resetPasswordConfirmationModal) {
                            resetPasswordConfirmationModal.hide();
                        }
                        alert('Password reset successfully.');
                    },
                    error: function(xhr) {
                        if (resetPasswordConfirmationModal) {
                            resetPasswordConfirmationModal.hide();
                        }
                        alert('Failed to reset password.');
                    },
                    complete: function() {
                        // Re-enable button
                        $('#confirmResetPasswordBtn').prop('disabled', false);
                    }
                });
            });

            $('#cancelResetPasswordBtn').off('click').on('click', function() {
                // No additional logic needed; modal simply closes.
            });

            // Optional: Refresh table function
            window.refreshUsersTable = function() {
                table.ajax.reload(null, false); // false = stay on current page
            };
        });
    });
</script>
