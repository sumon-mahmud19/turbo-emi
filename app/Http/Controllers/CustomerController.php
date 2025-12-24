<?php

namespace App\Http\Controllers;

use App\Mail\NewCustomerNotification;
use App\Models\Customer;
use App\Models\Installment;
use App\Models\InstallmentPayment;
use App\Models\Location;
use App\Models\Notice;
use Illuminate\Http\Request;
use Livewire\WithPagination;

class CustomerController extends Controller
{
    
    
    
    /**
     * Display a listing of the resource.
     */

    public function __construct()
    {
        // $this->middleware(['permission:installment-pay|installment-create|installment-edit|installment-delete'], ['only' => ['index', 'show']]);
        $this->middleware(['permission:customer-list|customer-create|customer-edit|customer-delete'], ['only' => ['index', 'show']]);
        $this->middleware(['permission:customer-create'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:customer-edit'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:customer-delete'], ['only' => ['destroy']]);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $locations = Location::all();
        return view('customers.create', compact('locations'));
    }

    /**
     * Store a newly created resource in storage.
     */


    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_id' => 'required|integer|unique:customers,customer_id',
            'customer_phone'   => 'required|string|unique:customers,customer_phone',
            'customer_phone2'   => 'required|string|unique:customers,customer_phone2',
            'customer_image'   => 'nullable|image|mimes:jpg,jpeg,png',
            'landlord_name'    => 'nullable|string|max:255',
            'location_id'      => 'required|exists:locations,id',
            'location_details' => 'nullable|string|max:255',
        ]);

        // Handle image upload (if image exists)
        $customerImage = 'images/default.png'; // Default image
        if ($request->hasFile('customer_image')) {
            $image = $request->file('customer_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('image/customers'), $imageName);
            $customerImage = 'image/customers/' . $imageName;
        }


        // Insert the customer data in a single query
        $customer = Customer::create([
            'customer_name'    => $validated['customer_name'],
            'customer_id'      => $validated['customer_id'],
            'customer_phone'   => $validated['customer_phone'],
            'customer_phone2'   => $validated['customer_phone2'],
            'customer_image'   => $customerImage,
            'landlord_name'    => $validated['landlord_name'],
            'location_id'      => $validated['location_id'],
            'location_details' => $validated['location_details'],
        ]);

        // toastr()->addSuccess('Customer created!');
        // // Mail::to('sclsumonislam@gmail.com')->send(new NewCustomerNotification($customer));

        // Redirect to the customer index page with a success message
        return redirect()->route('customers.index');
    }


    // app/Http/Controllers/CustomerController.php

    public function customerEmiPlans($id)
    {
        $customer = Customer::with('purchases.installments')->findOrFail($id);

        $paymentHistory = InstallmentPayment::with('installment.purchase.product')
            ->whereHas('installment.purchase', function ($query) use ($id) {
                $query->where('customer_id', $id);
            })
            ->orderBy('paid_at', 'desc')
            ->get();

        return view('customers.emi_plans', compact('customer', 'paymentHistory'));
    }

}
