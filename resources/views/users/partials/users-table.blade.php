<table id='users-table' class="table datatable mt-3 text-sm text-center">
    <thead>
        <tr>
        <th class="text-left" scope="col">Name</th>
        <th scope="col">Email</th>
        <th scope="col">Roles</th>
        <th scope="col">Status</th>
        <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($users as $user)
        <tr>
            {{-- <th scope="row">{{ $loop->iteration }}</th> --}}
            <td class="text-left">{{ $user->full_name }}</td>
            <td>{{ $user->email }}</td>
            <td>
                @forelse ($user->getRoleNames() as $role)
                    <span>{{ $role }}</span>
                @empty
                @endforelse
            </td>
            <td class="w-40">
                <select name="status" class="form-control w-40 border-none">
                    <option value="Inactive" {{ $user->status == 'for activation' ? 'selected' : '' }}>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#1e9730" class="size-5 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                          </svg>
                          For Activation
                        </option>
                    <option value="Active" {{ $user->status == 'active' ? 'selected' : '' }}>Active</option>
                </select>
            </td>
            <td class="justify-items-center items-center">
                <form action="{{ route('users.destroy', $user->id) }}" method="post">
                    @csrf
                    @method('DELETE')

                    {{-- <a href="{{ route('users.show', $user->id) }}" class="btn btn-warning btn-sm"><i class="bi bi-eye"></i> Show</a> --}}

                    
                        <a href="{{ route('users.edit', $user->id) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="2" stroke="#3968d2" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                            
                            <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1">
                                Edit
                            </div>
                        </a>   

                        {{-- @can('delete-user')
                            @if (Auth::user()->id!=$user->id)
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Do you want to delete this user?');"><i class="bi bi-trash"></i> Delete</button>
                            @endif
                        @endcan --}}
                    
                </form>
            </td>
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