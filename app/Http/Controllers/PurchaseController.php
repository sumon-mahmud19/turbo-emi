<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Installment;
use App\Models\InstallmentPayment;
use App\Models\Invoice;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductModel;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:purchase-list|purchase-create|purchase-edit|purchase-delete'], ['only' => ['index', 'show']]);
        $this->middleware(['permission:purchase-create'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:purchase-edit'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:purchase-delete'], ['only' => ['destroy']]);
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $purchases = Purchase::with('customer', 'product')->orderBy('created_at', 'desc')->paginate(100);
        $totalPurchases = Purchase::count();
        return view('purchases.index', compact('purchases', 'totalPurchases'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::all();
        $products = Product::all();
        $locations = Location::all();
        return view('purchases.create', compact('customers', 'products', 'locations'));
    }


    public function getModels($productId)
    {
        $models = ProductModel::where('product_id', $productId)->get(['id', 'model_name']);
        return response()->json($models);
    }




    public function autocomplete(Request $request)
    {
        $term = $request->input('term');

        $customers = Customer::query()
            ->where('customer_id', 'like', '%' . $term . '%')
            ->orWhere('customer_phone', 'like', '%' . $term . '%')
            ->select('id', 'customer_id', 'customer_name')
            ->limit(10)
            ->get();

        $results = $customers->map(function ($customer) {
            return [
                'id' => $customer->id,
                'customer_id' => $customer->customer_id,
                'customer_name' => $customer->customer_name,
               
            ];
        });

        return response()->json($results);
    }




    public function store(Request $request)
    {
        $request->validate([
            'customer_id'  => 'required|exists:customers,id',
            'product_id'   => 'required|exists:products,id',
            'model_id'     => 'required',
            'sales_price'  => 'required|numeric',
            'down_price'   => 'required|numeric',
            'net_price'    => 'required|numeric',
            'emi_plan'     => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            // Save purchase
            $purchase = Purchase::create([
                'customer_id' => $request->customer_id,
                'product_id' => $request->product_id,
                'model_id' => $request->model_id,
                'sales_price' => $request->sales_price,
                'down_price' => $request->down_price,
                'net_price' => $request->net_price,
                'emi_plan' => $request->emi_plan,
            ]);

            // Total due = net - down
            $totalDue = $purchase->net_price - $purchase->down_price;

            // EMI rounding
            $rawEmi = $totalDue / $purchase->emi_plan;
            $baseEmi = floor($rawEmi);
            $emiAmount = ($rawEmi - $baseEmi) >= 0.5 ? $baseEmi + 1 : $baseEmi;

            // Create installments
            $installments = [];
            for ($i = 0; $i < $purchase->emi_plan; $i++) {
                $installments[] = Installment::create([
                    'customer_id' => $purchase->customer_id,
                    'product_id' => $purchase->product_id,
                    'purchase_id' => $purchase->id,
                    'amount' => $emiAmount,
                    'status' => 'pending',
                    'due_date' => Carbon::now()->addMonths($i + 1)->startOfMonth(),
                ]);
            }

            // Rounding adjustment
            $totalInstallment = $emiAmount * $purchase->emi_plan;
            $adjustment = $totalInstallment - $totalDue;

            if ($adjustment !== 0) {
                $lastInstallment = end($installments);
                $lastInstallment->amount -= $adjustment;
                $lastInstallment->save();
            }

            // Apply down payment to first installment
            if ($purchase->down_price > 0 && isset($installments[0])) {
                InstallmentPayment::create([
                    'installment_id' => $installments[0]->id,
                    'amount' => $purchase->down_price,
                    'paid_at' => now(),
                ]);
            }

            DB::commit();

            // Redirect immediately after successful transaction.
            // NOTE: PDF generation (mpdf) can be expensive and may block the request for a long time.
            // If you need to generate PDFs, consider dispatching a background job that creates the PDF
            // asynchronously so the user gets a fast response here.
            return redirect()->to('/customers/' . $purchase->customer_id . '/emi-plans')
                ->with('success', 'Purchase created successfully and EMI plan generated.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }




    /**
     * Display the specified resource.
     */
    public function show(Purchase $purchase)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
   public function edit(Purchase $purchase)
{
    $customers = Customer::all();
    $products  = Product::all();
    $locations = Location::all();

    $models = ProductModel::where('product_id', $purchase->product_id)->get();

    return view('purchases.edit', compact(
        'purchase',
        'customers',
        'products',
        'locations',
        'models'
    ));
}


   public function update(Request $request, Purchase $purchase)
{
    $request->validate([
        'customer_id' => 'required|exists:customers,id',
        'product_id'  => 'required|exists:products,id',
        'model_id'    => 'required|exists:product_models,id',
        'sales_price' => 'required|numeric',
        'down_price'  => 'required|numeric',
        'net_price'   => 'required|numeric',
        'emi_plan'    => 'required|integer|min:1',
    ]);

    DB::beginTransaction();

    try {
        $purchase->update([
            'customer_id' => $request->customer_id,
            'product_id'  => $request->product_id,
            'model_id'    => $request->model_id,
            'sales_price' => $request->sales_price,
            'down_price'  => $request->down_price,
            'net_price'   => $request->net_price,
            'emi_plan'    => $request->emi_plan,
        ]);

        DB::commit();

        return redirect()
            ->to('/customers/' . $purchase->customer_id . '/emi-plans')
            ->with('success', 'Purchase updated successfully');

    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->withErrors(['error' => $e->getMessage()]);
    }
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase)
    {
        $purchase->delete();
        return redirect()->to('/customers/' . $purchase->customer_id . '/emi-plans')
                ->with('success', 'Purchase deleted successfully');
    }
}
