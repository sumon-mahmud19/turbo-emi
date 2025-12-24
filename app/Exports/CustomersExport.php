<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomersExport implements FromQuery, WithHeadings
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    // Query filtered customers
    public function query()
    {
        return Customer::query()
            ->with('location:id,name')
            ->when($this->search, function ($q) {
                $q->where('customer_name', 'like', "%{$this->search}%")
                  ->orWhere('customer_id', 'like', "%{$this->search}%")
                  ->orWhere('customer_phone', 'like', "%{$this->search}%")
                  ->orWhereHas('location', function($q2) {
                      $q2->where('name', 'like', "%{$this->search}%");
                  });
            })
            ->select([
                'id',
                'customer_name',
                'customer_id',
                'customer_phone',
                'customer_phone2',
                'landlord_name',
                'location_id',
            ]);
    }

    // Excel headers
    public function headings(): array
    {
        return [
            'ID',
            'Customer Name',
            'Customer ID',
            'Phone',
            'Phone 2',
            'Landlord Name',
            'Location',
        ];
    }
}
