<div class="p-4 max-w-6xl mx-auto">

    {{-- Success/Error Messages --}}
    @if (session()->has('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @elseif (session()->has('error'))
        <div class="alert alert-error mb-4">{{ session('error') }}</div>
    @endif


    <div class="p-4 max-w-6xl mx-auto">

        {{-- Messages --}}
        @if (session()->has('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @elseif(session()->has('error'))
            <div class="alert alert-error mb-4">{{ session('error') }}</div>
        @endif

        {{-- Form --}}
        <form wire:submit.prevent="storeOrUpdate" class="space-y-4 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Customer --}}
                <div>
                    <label class="label">Customer</label>
                    <select wire:model="customer_id" class="select select-bordered w-full">
                        <option value="">Select Customer</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>

                    <!-- Product -->
                    <div wire:ignore>
                        <label>Product</label>
                        <select id="productSelect" class="form-control">
                            <option value="">-- Select Product --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Product Model -->
                    <div class="mt-3" wire:ignore>
                        <label>Product Model</label>
                        <select id="modelSelect" class="form-control">
                            <option value="">-- Select Model --</option>
                            @foreach ($models as $model)
                                <option value="{{ $model->id }}">{{ $model->model_name }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <script>
                    document.addEventListener('livewire:load', function() {

                        $('#productSelect').select2();
                        $('#modelSelect').select2();

                        $('#productSelect').on('change', function() {
                            @this.set('product_id', $(this).val());
                        });

                        $('#modelSelect').on('change', function() {
                            @this.set('product_model_id', $(this).val());
                        });

                        Livewire.on('refreshSelect2', () => {
                            $('#modelSelect').select2();
                        });

                    });
                </script>


            </div>

            <button type="submit" class="btn btn-primary w-full mt-4">
                {{ $updateMode ? 'Update Purchase' : 'Save Purchase & Generate EMI' }}
            </button>
        </form>

    </div>


    {{-- Purchase List Table --}}
    <div class="overflow-x-auto">
        <table class="table table-zebra w-full text-center">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Location</th>
                    <th>Product</th>
                    <th>Model</th>
                    <th>Price</th>
                    <th>Down</th>
                    <th>EMI Plan</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $index => $purchase)
                    <tr>
                        {{-- <td>{{ $purchases->firstItem() + $index }}</td> --}}
                        <td>{{ $purchase->customer->customer_name ?? 'N/A' }}</td>
                        <td>{{ $purchase->customer->customer_phone ?? 'N/A' }}</td>
                        <td>{{ $purchase->customer->location->name ?? 'N/A' }}</td>
                        <td>{{ $purchase->product->product_name ?? 'N/A' }}</td>
                        <td>{{ $purchase->model->model_name ?? 'N/A' }}</td>
                        <td>{{ number_format($purchase->sales_price, 2) }} ৳</td>
                        <td>{{ number_format($purchase->down_price, 2) }} ৳</td>
                        <td>{{ $purchase->emi_plan }} মাস</td>
                        <td class="space-x-1">
                            <button wire:click="edit({{ $purchase->id }})" class="btn btn-warning btn-sm">Edit</button>
                            <button wire:click="delete({{ $purchase->id }})" class="btn btn-error btn-sm"
                                onclick="confirm('Are you sure?') || event.stopImmediatePropagation()">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10">কোনো ক্রয় পাওয়া যায়নি।</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $purchases->links() }}
        </div>
    </div>
</div>
