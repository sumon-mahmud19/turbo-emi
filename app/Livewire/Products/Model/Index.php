<?php

namespace App\Livewire\Products\Model;

use App\Models\Product;
use App\Models\ProductModel;
use Livewire\Component;
use Livewire\WithPagination;    

class Index extends Component
{
     use WithPagination;

    public $model_id;
    public $product_id;
    public $model_name;
    public $isOpen = false;
    public $search = '';
    public $perPage = 10;

    public $products;

    protected $rules = [
        'product_id' => 'required|exists:products,id',
        'model_name' => 'required|string|max:255',
    ];

    public function mount()
    {
        $this->products = Product::all();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $models = ProductModel::with('product')
            ->whereHas('product', function($query){
                $query->where('product_name', 'like', "%{$this->search}%");
            })
            ->orWhere('model_name', 'like', "%{$this->search}%")
            ->orderBy('id','desc')
            ->paginate($this->perPage);

        return view('livewire.products.model.index', compact('models'));
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function resetInputFields()
    {
        $this->model_id = null;
        $this->product_id = '';
        $this->model_name = '';
        $this->closeModal();
    }

    public function store()
    {
        $this->validate();

        ProductModel::create([
            'product_id' => $this->product_id,
            'model_name' => $this->model_name,
        ]);

        session()->flash('success', 'Product model created successfully.');
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $model = ProductModel::findOrFail($id);
        $this->model_id = $id;
        $this->product_id = $model->product_id;
        $this->model_name = $model->model_name;

        $this->openModal();
    }

    public function update()
    {
        $this->validate();

        if ($this->model_id) {
            $model = ProductModel::find($this->model_id);
            $model->update([
                'product_id' => $this->product_id,
                'model_name' => $this->model_name,
            ]);

            session()->flash('success', 'Product model updated successfully.');
            $this->resetInputFields();
        }
    }

    public function delete($id)
    {
        ProductModel::find($id)->delete();
        session()->flash('error', 'Product model deleted successfully.');
    }
}
