<div class="p-6">

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">User Management</h2>
        <button class="btn btn-primary" wire:click="create">+ New User</button>
    </div>

    @if(session()->has('success'))
        <div class="alert alert-success mb-4">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <th>{{ $loop->iteration }}</th>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @foreach($user->roles as $role)
                            <span class="badge badge-info mr-1">{{ $role->name }}</span>
                        @endforeach
                    </td>
                    <td class="flex gap-2">
                        <button class="btn btn-sm btn-warning" wire:click="edit({{ $user->id }})">Edit</button>
                        <button class="btn btn-sm btn-error"
                                onclick="confirm('Are you sure to delete this user?') || event.stopImmediatePropagation()"
                                wire:click="delete({{ $user->id }})">
                            Delete
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>

    <!-- Modal -->
    @if($isOpen)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
        <div class="modal modal-open">
            <div class="modal-box w-11/12 max-w-md">
                <h3 class="font-bold text-lg mb-4">{{ $user_id ? 'Edit User' : 'Create User' }}</h3>

                <div class="space-y-3">
                    <input type="text" placeholder="Name" class="input input-bordered w-full" wire:model="name">
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                    <input type="email" placeholder="Email" class="input input-bordered w-full" wire:model="email">
                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                    <input type="password" placeholder="Password" class="input input-bordered w-full" wire:model="password">
                    @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                    <label class="font-semibold">Roles</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($allRoles as $role)
                            <label class="cursor-pointer label">
                                <input type="checkbox" class="checkbox checkbox-primary mr-1" wire:model="roles" value="{{ $role->name }}">
                                <span class="label-text">{{ $role->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('roles') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="modal-action mt-4">
                    @if($user_id)
                        <button class="btn btn-success" wire:click="update">Update</button>
                    @else
                        <button class="btn btn-primary" wire:click="store">Save</button>
                    @endif
                    <button class="btn btn-ghost" wire:click="closeModal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
