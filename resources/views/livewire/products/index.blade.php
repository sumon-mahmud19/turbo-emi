<div class="max-w-5xl mx-auto p-4">

    {{-- Header --}}
    <div class="flex justify-between mb-4 items-center">
        <h2 class="text-2xl font-bold">Products</h2>
        <button wire:click="create" class="btn btn-primary btn-sm">+ New Product</button>
    </div>

    {{-- Flash Message --}}
    @if (session()->has('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @elseif (session()->has('error'))
        <div class="alert alert-error mb-4">{{ session('error') }}</div>
    @endif

    {{-- Search & Per Page --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-4">
        <input type="text" wire:model.live="search" placeholder="Search Product..."
               class="input input-bordered w-full md:w-1/3">

        <select wire:model.live="perPage" class="select select-bordered w-32">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="30">30</option>
            <option value="50">50</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="table table-zebra w-full">
            <thead>
            <tr>
                <th>#</th>
                <th>Product Name</th>
                <th class="text-right">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($products as $key => $product)
                <tr>
                    <td>{{ $products->firstItem() + $key }}</td>
                    <td>{{ $product->product_name }}</td>
                    <td class="flex justify-end gap-2">
                        <button wire:click="edit({{ $product->id }})"
                                class="btn btn-xs btn-warning">Edit
                        </button>
                        <button wire:click="delete({{ $product->id }})"
                                onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                class="btn btn-xs btn-error">Delete
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center text-gray-400">No products found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $products->links() }}
    </div>

    {{-- Modal --}}
    @if($isOpen)
        <div class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg mb-4">{{ $product_id ? 'Edit Product' : 'New Product' }}</h3>

                <input type="text" wire:model="product_name" placeholder="Product Name"
                       class="input input-bordered w-full mb-2">
                @error('product_name') <span class="text-error">{{ $message }}</span> @enderror

                <div class="modal-action">
                    @if($product_id)
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
