<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use App\Models\InstallmentPayment;
use App\Models\Location;
use Flasher\Notyf\Prime\Notyf;
use App\Exports\CustomersExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use App\Jobs\GenerateCustomerReportPdf;


use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class Index extends Component
{
    use WithPagination, WithFileUploads;



    protected $paginationTheme = 'tailwind';

    public $openForm = false;
    public $showDeleted = false;
    public $perPage = 50;

    public $customer_primary_id;

    public $customer_name;
    public $customer_id;
    public $customer_phone;
    public $customer_phone2;
    public $customer_image;
    public $landlord_name;
    public $location_id;
    public $location_details;

    public $search = '';
    public $updateMode = false;

    // Modal
    public $showModal = false;
    public $viewCustomerData = null;

    /* -------------------- MODAL -------------------- */

    public function openModal($id)
    {
        $this->viewCustomerData = Customer::with('location')->findOrFail($id);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->reset(['showModal', 'viewCustomerData']);
    }

    /* -------------------- SEARCH -------------------- */

    public function updatedSearch()
    {
        $this->resetPage(); // important!
    }


    /* -------------------- RULES -------------------- */

    protected function rules()
    {
        return [
            'customer_name'     => 'required|string|max:255',
            'customer_id'       => 'required|integer|unique:customers,customer_id,' . $this->customer_primary_id,
            'customer_phone'    => 'required|string|unique:customers,customer_phone,' . $this->customer_primary_id,
            'customer_phone2'   => 'nullable|string|max:20',
            'customer_image'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'landlord_name'     => 'nullable|string|max:255',
            'location_id'       => 'required|exists:locations,id',
            'location_details'  => 'nullable|string|max:255',
        ];
    }


    public function updatedPerPage()
    {
        $this->resetPage(); // optional, reset page when items per page changes
    }


    public function openAddForm()
    {
        $this->resetInputFields();
        $this->openForm = true;
    }


    /* -------------------- RENDER -------------------- */


    public function render()
    {
        // Cache locations for faster queries
        $locations = Cache::remember('locations_list', 3600, function () {
            return Location::select('id', 'name')->get();
        });

        $search = trim($this->search);

        // Base query
        $customersQuery = Customer::with('location:id,name');

        // Show trashed or active customers based on toggle
        if ($this->showDeleted) {
            $customersQuery->onlyTrashed();
        }

        // Apply search filter
        if (!empty($search)) {
            $customersQuery->where(function ($query) use ($search) {
                $query->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_id', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhereHas('location', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Get paginated results with small ID first
        $customers = $customersQuery
            ->orderBy('customer_id', 'asc') // OR ->orderBy('id', 'asc')
            ->paginate($this->perPage);

        return view('livewire.customers.index', [
            'customers' => $customers,
            'locations' => $locations,
        ]);
    }




    /* -------------------- RESET -------------------- */

    public function resetInputFields()
    {
        $this->reset([
            'customer_primary_id',
            'customer_name',
            'customer_id',
            'customer_phone',
            'customer_phone2',
            'customer_image',
            'landlord_name',
            'location_id',
            'location_details',
            'updateMode',
        ]);

        $this->openForm = false;
    }

    /* -------------------- STORE -------------------- */

    public function store()
    {
        $this->validate();

        $imagePath = $this->customer_image
            ? $this->customer_image->store('customers', 'public')
            : null;

        Customer::create([
            'customer_name'     => $this->customer_name,
            'customer_id'       => $this->customer_id,
            'customer_phone'    => $this->customer_phone,
            'customer_phone2'   => $this->customer_phone2,
            'customer_image'    => $imagePath,
            'landlord_name'     => $this->landlord_name,
            'location_id'       => $this->location_id,
            'location_details'  => $this->location_details,
        ]);



        sweetalert()->success('Customer Created Successfully.');
        $this->resetInputFields();
        $this->openForm = false;
    }

    /* -------------------- EDIT -------------------- */

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);

        $this->customer_primary_id = $customer->id;
        $this->customer_name       = $customer->customer_name;
        $this->customer_id         = $customer->customer_id;
        $this->customer_phone      = $customer->customer_phone;
        $this->customer_phone2     = $customer->customer_phone2;
        $this->landlord_name       = $customer->landlord_name;
        $this->location_id         = $customer->location_id;
        $this->location_details    = $customer->location_details;

        $this->updateMode = true;
        $this->openForm = true;
    }

    /* -------------------- UPDATE -------------------- */

    public function update()
    {
        $this->validate();

        $customer = Customer::findOrFail($this->customer_primary_id);

        $imagePath = $customer->customer_image;

        if ($this->customer_image instanceof TemporaryUploadedFile) {
            if ($customer->customer_image) {
                Storage::disk('public')->delete($customer->customer_image);
            }
            $imagePath = $this->customer_image->store('customers', 'public');
        }

        $customer->update([
            'customer_name'     => $this->customer_name,
            'customer_id'       => $this->customer_id,
            'customer_phone'    => $this->customer_phone,
            'customer_phone2'   => $this->customer_phone2,
            'customer_image'    => $imagePath,
            'landlord_name'     => $this->landlord_name,
            'location_id'       => $this->location_id,
            'location_details'  => $this->location_details,
        ]);


        sweetalert()->success('Customer updated successfully.');
        $this->resetInputFields();
        $this->openForm = false;
        $this->resetPage();
    }

    /* -------------------- DELETE -------------------- */

    // Soft Delete (active customer)
    public function delete($id)
    {
        $customer = Customer::findOrFail($id); // Only active
        $customer->delete();
        sweetalert()->success('Customer soft deleted successfully.');
        $this->resetPage();
    }

    // Restore (only trashed customer)
    public function restore($id)
    {
        $customer = Customer::onlyTrashed()->findOrFail($id);
        $customer->restore();
        sweetalert()->success('Customer restored successfully.');
        $this->resetPage();
    }

    // Force Delete (only trashed customer)
    public function forceDelete($id)
    {
        $customer = Customer::onlyTrashed()->findOrFail($id);

        // Delete image from storage if exists
        if ($customer->customer_image) {
            Storage::disk('public')->delete($customer->customer_image);
        }

        $customer->forceDelete();
        sweetalert()->success('Customer permanently deleted.');
        $this->resetPage();
    }


    public function customerEmiPlans($id)
    {
        $customer = Customer::with('purchases.installments')->findOrFail($id);

        $paymentHistory = InstallmentPayment::with('installment.purchase.product')
            ->whereHas('installment.purchase', function ($query) use ($id) {
                $query->where('customer_id', $id);
            })
            ->orderBy('paid_at', 'desc')
            ->get();

        return view('livewire.customers.emi', compact('customer', 'paymentHistory'));
    }





    public function exportExcel()
    {
        $fileName = 'customers_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new CustomersExport($this->search), $fileName);
    }
}
