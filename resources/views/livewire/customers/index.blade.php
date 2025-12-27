<div class="max-w-7xl mx-auto p-6">

    {{-- Flash Message --}}
    @if (session()->has('message'))
        <div class="alert alert-success mb-4 shadow-lg">
            {{ session('message') }}
        </div>
    @endif


    {{-- Customer Form --}}
    @if ($openForm)
        <div class="mb-6 p-6 bg-base-100 rounded-2xl shadow-lg space-y-6">
            <h2 class="text-2xl font-bold text-gray-800">{{ $updateMode ? 'Edit Customer' : 'Add Customer' }}</h2>

            {{-- Name & Customer ID --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="text" wire:model="customer_name" placeholder="Name"
                    class="input input-bordered w-full @error('customer_name') input-error @enderror">
                <input type="text" wire:model="customer_id" placeholder="Customer ID"
                    class="input input-bordered w-full @error('customer_id') input-error @enderror">
            </div>

            {{-- Phones --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="text" wire:model="customer_phone" placeholder="Phone"
                    class="input input-bordered w-full @error('customer_phone') input-error @enderror">
                <input type="text" wire:model="customer_phone2" placeholder="Phone 2 (optional)"
                    class="input input-bordered w-full">
            </div>

            {{-- Landlord & Location --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="text" wire:model="landlord_name" placeholder="Landlord Name (optional)"
                    class="input input-bordered w-full">
                <select wire:model="location_id" class="select select-bordered w-full">
                    <option value="">Select Location</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Location Details --}}
            <input type="text" wire:model="location_details" placeholder="Location Details (optional)"
                class="input input-bordered w-full">

            {{-- File Upload & Preview --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                <input type="file" wire:model="customer_image" class="file-input file-input-bordered w-full"
                    accept="image/*">
                @if ($customer_image)
                    <div class="flex justify-center md:justify-start mt-2 md:mt-0">
                        <img src="{{ $customer_image->temporaryUrl() }}" alt="Preview"
                            class="w-32 h-32 md:w-40 md:h-40 rounded-full object-cover shadow-md hover:scale-105 transition-transform duration-200">
                    </div>
                @endif
            </div>

            {{-- Buttons --}}
            <div class="flex gap-2 justify-end mt-4">
                @if ($updateMode)
                    <button wire:click="update" class="btn btn-primary">Update</button>
                    <button wire:click="resetInputFields" class="btn btn-ghost">Cancel</button>
                @else
                    <button wire:click="store" class="btn btn-success">Add Customer</button>
                @endif
            </div>
        </div>
    @else
        {{-- Show Add Button --}}
        <div class="flex justify-end mb-4">
            <button wire:click="$set('openForm', true)" class="btn btn-success">Add New Customer</button>
        </div>
    @endif

    {{-- Search, Pagination & Export --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

        {{-- Search --}}
        <input type="text" wire:model.live="search" placeholder="Search Customers..."
            class="input input-bordered w-full md:w-80">

        {{-- Search Result Count --}}
        <div class="text-gray-700 text-sm md:text-base">
            @if ($search)
                <b>Search Result:</b> {{ $customers->total() }} item(s) found.
            @endif
        </div>

        {{-- Export (Admin) --}}
        @role('admin')
            <button wire:click="exportExcel" wire:loading.attr="disabled" class="btn btn-success">
                <span wire:loading>Exporting...</span>
                <span wire:loading.remove>Export Excel</span>
            </button>
        @endrole

        {{-- Per Page --}}
        <select wire:model.live="perPage" class="select select-bordered w-40">
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="250">250</option>
        </select>
    </div>

    {{-- Customers Table --}}
    <div class="overflow-x-auto">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th>#Id</th>
                    <th>Name</th>
                    <th>Customer ID</th>
                    <th>Phone</th>
                    <th>Landlord</th>
                    <th>Location</th>
                    <th>Image</th>
                    {{-- <th>Details</th> --}}
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                    <tr>
                        <td>{{ $customer->id }}</td>
                        <td class="px-4 py-3 text-sm text-blue-600 font-medium">
                            <a target="_blank" href="{{ route('customers.emi_plans', $customer->id) }}">{{ $customer->customer_name }}

                            </a>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">
                            <a target="_blank"
                                href="{{ route('report.print', $customer->id) }}" class="hover:underline">
                                {{ $customer->customer_id }}
                            </a>
                        </td>
                        <td>
                            <a href="tel:{{ $customer->customer_phone }}" class="text-primary hover:underline">
                                {{ $customer->customer_phone }}
                            </a>
                            @if ($customer->customer_phone2)
                                <br>
                                <a href="tel:{{ $customer->customer_phone2 }}" class="text-primary hover:underline">
                                    {{ $customer->customer_phone2 }}
                                </a>
                            @endif
                        </td>
                        <td>{{ $customer->landlord_name }}</td>
                        <td>{{ $customer->location->name ?? '-' }}</td>
                        <td>

                            @if ($customer->customer_image)
                                <img src="{{ $customer->customer_image ? asset('storage/' . $customer->customer_image) : asset($customer->customer_image) }}
"
                                    alt="{{ $customer->customer_name }}"
                                    class="w-14 h-14 rounded-full object-cover cursor-pointer hover:scale-110 transition-transform duration-200 shadow-sm"
                                    loading="lazy" wire:click="openModal({{ $customer->id }})" />
                            @endif
                        </td>
                        {{-- <td>{{ $customer->location_details }}</td> --}}
                        <td class="flex gap-1">
                            <button wire:click="edit({{ $customer->id }})" class="btn btn-xs btn-warning">Edit</button>
                            <button onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                wire:click="delete({{ $customer->id }})" class="btn btn-xs btn-error">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-gray-400">No customers found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $customers->links() }}
    </div>

    {{-- Customer Modal --}}
    @if ($showModal && $viewCustomerData)
        <div class="modal modal-open">
            <div class="modal-box max-w-xl p-0 rounded-3xl shadow-2xl overflow-hidden">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 bg-primary text-primary-content">
                    <h3 class="text-xl font-bold">Customer Information</h3>
                    <button wire:click="closeModal"
                        class="btn btn-sm btn-circle btn-ghost text-xl hover:bg-primary-focus">✕</button>
                </div>

                {{-- Body --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6 bg-base-100">

                    {{-- Left Info --}}
                    <div class="md:col-span-2 space-y-4">
                        <p class="text-2xl font-semibold text-gray-800">{{ $viewCustomerData->customer_name }}</p>
                        <div class="space-y-2 text-sm md:text-base text-gray-700">
                            <p><span class="font-semibold">Customer ID:</span> {{ $viewCustomerData->customer_id }}
                            </p>
                            <p class="flex items-center gap-2">
                                <span class="font-semibold">Phone:</span>
                                <a href="tel:{{ $viewCustomerData->customer_phone }}"
                                    class="btn btn-xs md:btn-sm btn-outline btn-primary gap-1 hover:scale-105 transition-transform duration-200">
                                    📞 {{ $viewCustomerData->customer_phone }}
                                </a>
                            </p>
                            @if ($viewCustomerData->customer_phone2)
                                <p class="flex items-center gap-2">
                                    <span class="font-semibold">Phone 2:</span>
                                    <a href="tel:{{ $viewCustomerData->customer_phone2 }}"
                                        class="btn btn-xs md:btn-sm btn-outline btn-primary gap-1 hover:scale-105 transition-transform duration-200">
                                        📞 {{ $viewCustomerData->customer_phone2 }}
                                    </a>
                                </p>
                            @endif
                            <p><span class="font-semibold">Location:</span>
                                {{ $viewCustomerData->location->name ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Right Image --}}
                    <div class="flex w-full justify-center md:justify-end">
                        @if ($viewCustomerData->customer_image)
                            <div
                                class="rounded-3xl overflow-hidden w-44 h-44 hover:shadow-xl transition-shadow duration-300">
                                <img loading="lazy" src="{{ asset('storage/' . $viewCustomerData->customer_image) }}"
                                    alt="Customer Image"
                                    class="w-full h-full rounded-full object-cover hover:scale-105 transition-transform duration-300">
                            </div>
                        @else
                            <div
                                class="w-56 h-56 flex items-center justify-center bg-base-200 rounded-3xl text-gray-400">
                                No Image
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    @endif

</div>
