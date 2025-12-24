<?php

namespace App\Livewire\Purchases;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductModel;
use App\Models\Purchase;
use App\Models\Installment;
use App\Models\InstallmentPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;


class Index extends Component
{
 
  public $product_id;
    public $product_model_id;
    public $products = [];
    public $models = [];

    public function mount()
    {
        $this->products = Product::all();
    }

    public function updatedProductId($value)
    {
        $this->models = ProductModel::where('product_id', $value)->get();
        $this->product_model_id = null;

        $this->dispatch('refreshSelect2'); // trigger JS to refresh Select2
    }

    public function render()
    {
        return view('livewire.purchases.index');
    }

}
