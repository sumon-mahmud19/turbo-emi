<div class="max-w-5xl mx-auto p-4">

    {{-- Header --}}
    <div class="flex justify-between mb-4 items-center">
        <h2 class="text-2xl font-bold">Product Models</h2>
        <button wire:click="create" class="btn btn-primary btn-sm">+ New Model</button>
    </div>

    {{-- Flash Message --}}
    @if(session()->has('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @elseif(session()->has('error'))
        <div class="alert alert-error mb-4">{{ session('error') }}</div>
    @endif

    {{-- Search & Per Page --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-4">
        <input type="text" wire:model.live="search" placeholder="Search Models..."
               class="input input-bordered w-full md:w-1/3">

        <select wire:model="perPage" class="select select-bordered w-32">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Model Name</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($models as $key => $model)
                <tr>
                    <td>{{ $models->firstItem() + $key }}</td>
                    <td>{{ $model->product->product_name ?? '-' }}</td>
                    <td>{{ $model->model_name }}</td>
                    <td class="flex justify-end gap-2">
                        <button wire:click="edit({{ $model->id }})" class="btn btn-xs btn-warning">Edit</button>
                        <button wire:click="delete({{ $model->id }})"
                                onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                class="btn btn-xs btn-error">Delete
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-gray-400">No models found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $models->links() }}
    </div>

    {{-- Modal --}}
    @if($isOpen)
        <div class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg mb-4">{{ $model_id ? 'Edit Model' : 'New Model' }}</h3>

                <select wire:model="product_id" class="select select-bordered w-full mb-2">
                    <option value="">Select Product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                    @endforeach
                </select>
                @error('product_id') <span class="text-error">{{ $message }}</span> @enderror

                <input type="text" wire:model="model_name" placeholder="Model Name"
                       class="input input-bordered w-full mb-2">
                @error('model_name') <span class="text-error">{{ $message }}</span> @enderror

                <div class="modal-action">
                    @if($model_id)
                        <button class="btn btn-success" wire:click="update">Update</button>
                    @else
                        <button class="btn btn-primary" wire:click="store">Save</button>
                    @endif
                    <button class="btn btn-ghost" wire:click="closeModal">Cancel</button>
                </div>
            </div>
        </div>
    @endif

</div>
