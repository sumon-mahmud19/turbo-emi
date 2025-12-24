<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use App\Models\Location;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Create extends Component
{
     use WithFileUploads;

    public $editId = null;

    public $customer_name;
    public $customer_id;
    public $customer_phone;
    public $customer_phone2;
    public $customer_image;
    public $landlord_name;
    public $location_id;
    public $location_details;

    public $locations = [];

    public function mount($customerId = null)
    {
        $this->locations = Location::all();

        if ($customerId) {
            $this->editId = $customerId;
            $customer = Customer::findOrFail($customerId);

            $this->customer_name = $customer->customer_name;
            $this->customer_id = $customer->customer_id;
            $this->customer_phone = $customer->customer_phone;
            $this->customer_phone2 = $customer->customer_phone2;
            $this->landlord_name = $customer->landlord_name;
            $this->location_id = $customer->location_id;
            $this->location_details = $customer->location_details;
            $this->customer_image = $customer->customer_image; // store path if exists
        }
    }

    protected function rules()
    {
        return [
            'customer_name' => 'required|string|max:255',
            'customer_id' => 'required|integer|unique:customers,customer_id,' . $this->editId,
            'customer_phone' => 'required|string|unique:customers,customer_phone,' . $this->editId,
            'customer_phone2' => 'required|string|unique:customers,customer_phone2,' . $this->editId,
            'customer_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'landlord_name' => 'nullable|string|max:255',
            'location_id' => 'required|exists:locations,id',
            'location_details' => 'nullable|string|max:255',
        ];
    }

    public function save()
    {
        $data = $this->validate();

        // Handle image upload
        if ($this->customer_image instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            // Delete old image if exists
            if ($this->editId) {
                $old = Customer::find($this->editId)?->customer_image;
                if ($old && Storage::disk('public')->exists($old)) {
                    Storage::disk('public')->delete($old);
                }
            }

            $filename = time() . '.' . $this->customer_image->getClientOriginalExtension();
            $data['customer_image'] = $this->customer_image->storeAs('customers', $filename, 'public');
        } else {
            // Keep old image if editing
            if ($this->editId) {
                $data['customer_image'] = $this->customer_image;
            }
        }

        Customer::updateOrCreate(['id' => $this->editId], $data);

        session()->flash('success', $this->editId ? 'Customer updated!' : 'Customer created!');

        return redirect()->route('customers.index');
    }


    public function render()
    {
        return view('livewire.customers.create', [
            'locations' => Location::all(),
        ]);
    }
}
