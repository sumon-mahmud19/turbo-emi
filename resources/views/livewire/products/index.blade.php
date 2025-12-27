<div class="max-w-6xl mx-auto p-4 md:p-6 lg:p-8 space-y-6">

    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-base-100 p-6 rounded-2xl shadow-sm border border-base-200">
        <div>
            <h2 class="text-3xl font-extrabold tracking-tight text-base-content">Products</h2>
            <p class="text-base-content/60 text-sm">Manage your inventory and associated product models</p>
        </div>
        <button wire:click="create" class="btn btn-primary shadow-lg shadow-primary/20">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Product
        </button>
    </div>

    {{-- Notifications --}}
    @if (session()->has('success'))
        <div class="alert alert-success shadow-sm rounded-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>{{ session('success') }}</span>
        </div>
    @elseif (session()->has('error'))
        <div class="alert alert-error shadow-sm rounded-xl text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Filters --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="relative w-full md:w-96">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-base-content/40">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </span>
            <input type="text" wire:model.live="search" placeholder="Search product name or model..."
                class="input input-bordered w-full pl-10 focus:ring-2 focus:ring-primary/20">
        </div>

        <div class="flex items-center gap-2 self-end">
            <span class="text-sm font-medium opacity-60">Show:</span>
            <select wire:model.live="perPage" class="select select-bordered select-sm">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="30">30</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="bg-base-100 rounded-2xl shadow-sm border border-base-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-md w-full">
                <thead class="bg-base-200/50">
                    <tr class="text-base-content/70">
                        <th class="w-20">Rank</th>
                        <th>Product Details</th>
                        <th>Associated Models</th>
                        <th class="text-right">Manage</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-200">
                    @forelse ($products as $key => $product)
                        <tr class="hover:bg-base-200/20 transition-colors">
                            <td class="font-mono text-xs opacity-50">
                                #{{ str_pad($products->firstItem() + $key, 2, '0', STR_PAD_LEFT) }}
                            </td>
                            <td>
                                <div class="font-bold text-base">{{ $product->product_name }}</div>
                            </td>
                            <td>
                                <div class="flex flex-wrap gap-2">
                                    @forelse($product->models as $model)
                                        <div class="badge badge-outline badge-ghost border-base-300 text-[11px] font-medium py-2">
                                            {{ $model->model_name }}
                                        </div>
                                    @empty
                                        <span class="text-base-content/40 text-xs italic">Unassigned</span>
                                    @endforelse
                                </div>
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <button wire:click="edit({{ $product->id }})" 
                                        class="btn btn-square btn-ghost btn-sm text-warning hover:bg-warning/10"
                                        title="Edit Product">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    </button>
                                    <button wire:click="delete({{ $product->id }})"
                                        onclick="confirm('Delete this product and its models?') || event.stopImmediatePropagation()"
                                        class="btn btn-square btn-ghost btn-sm text-error hover:bg-error/10"
                                        title="Delete Product">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-12">
                                <div class="flex flex-col items-center opacity-40">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                                    <p class="font-medium">No results found for "{{ $search }}"</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination Section --}}
    <div class="mt-2">
        {{ $products->links() }}
    </div>

    {{-- Modal --}}
    @if ($isOpen)
        <div class="modal modal-open">
            <div class="modal-box max-w-md rounded-2xl p-0 overflow-hidden">
                <div class="bg-base-200 p-6">
                    <h3 class="font-black text-xl">{{ $product_id ? 'Update Product' : 'Create New Product' }}</h3>
                    <p class="text-xs opacity-60">Enter the details below to save the product.</p>
                </div>
                
                <div class="p-6 space-y-4">
                    <div class="form-control w-full">
                        <label class="label"><span class="label-text font-bold">Product Name</span></label>
                        <input type="text" wire:model="product_name" placeholder="e.g. Samsung Galaxy"
                            class="input input-bordered w-full focus:input-primary @error('product_name') border-error @enderror">
                        @error('product_name')
                            <label class="label"><span class="label-text-alt text-error font-medium">{{ $message }}</span></label>
                        @enderror
                    </div>
                </div>

                <div class="modal-action bg-base-100 p-6 mt-0">
                    <button class="btn btn-ghost btn-sm" wire:click="closeModal">Cancel</button>
                    @if ($product_id)
                        <button class="btn btn-success btn-sm px-6" wire:click="update">Update Changes</button>
                    @else
                        <button class="btn btn-primary btn-sm px-6" wire:click="store">Confirm Save</button>
                    @endif
                </div>
            </div>
            <div class="modal-backdrop bg-black/40" wire:click="closeModal"></div>
        </div>
    @endif

</div>