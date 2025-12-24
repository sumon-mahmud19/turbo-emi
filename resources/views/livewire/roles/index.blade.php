<div class="max-w-4xl mx-auto p-4">

    {{-- Header --}}
    <div class="flex justify-between mb-4 items-center">
        <h2 class="text-2xl font-bold">Roles</h2>
        @can('role-create')
            <button wire:click="create" class="btn btn-primary btn-sm">+ New Role</button>
        @endcan
    </div>

    {{-- Flash Messages --}}
    @if(session()->has('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @elseif(session()->has('error'))
        <div class="alert alert-error mb-4">{{ session('error') }}</div>
    @endif

    {{-- Search & Per Page --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-4">
        <input type="text" wire:model.live="search" placeholder="Search Roles..."
               class="input input-bordered w-full md:w-1/3">

        <select wire:model="perPage" class="select select-bordered w-32">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="25">25</option>
        </select>
    </div>

    {{-- Roles Table --}}
    <div class="overflow-x-auto">
        <table class="table table-zebra w-full">
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Permissions</th>
                <th class="text-right">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($roles as $key => $role)
                <tr>
                    <td>{{ $roles->firstItem() + $key }}</td>
                    <td>{{ $role->name }}</td>
                    <td>
                        @foreach($role->permissions as $perm)
                            <span class="badge badge-info mr-1">{{ $perm->name }}</span>
                        @endforeach
                    </td>
                    <td class="flex justify-end gap-2">
                        @can('role-edit')
                            <button wire:click="edit({{ $role->id }})" class="btn btn-xs btn-warning">Edit</button>
                        @endcan
                        @can('role-delete')
                            <button wire:click="delete({{ $role->id }})"
                                    onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                    class="btn btn-xs btn-error">Delete
                            </button>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-gray-400">No roles found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">{{ $roles->links() }}</div>

    {{-- Modal --}}
    @if($isOpen)
        <div class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg mb-4">{{ $role_id ? 'Edit Role' : 'New Role' }}</h3>

                <input type="text" wire:model="name" placeholder="Role Name"
                       class="input input-bordered w-full mb-2">
                @error('name') <span class="text-error">{{ $message }}</span> @enderror

                <label class="label">Permissions</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2">
                    @foreach($allPermissions as $perm)
                        <label class="cursor-pointer flex items-center gap-2">
                            <input type="checkbox" wire:model="permissions" value="{{ $perm->name }}">
                            <span>{{ $perm->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('permissions') <span class="text-error">{{ $message }}</span> @enderror

                <div class="modal-action">
                    @if($role_id)
                        <button wire:click="update" class="btn btn-success">Update</button>
                    @else
                        <button wire:click="store" class="btn btn-primary">Save</button>
                    @endif
                    <button wire:click="closeModal" class="btn btn-ghost">Cancel</button>
                </div>
            </div>
        </div>
    @endif

</div>
w