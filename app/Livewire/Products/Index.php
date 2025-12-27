<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $product_id;
    public $product_name;
    public $isOpen = false;
    public $search = '';
    public $perPage = 25;

    protected $rules = [
        'product_name' => 'required|string|max:255',
    ];

    // Reset pagination when searching
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $products = Product::with('models') // This loads the relationship
            ->where('product_name', 'like', "%{$this->search}%")
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.products.index', compact('products'));
    }

    // Open modal
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

    // Reset form
    public function resetInputFields()
    {
        $this->product_name = '';
        $this->product_id = null;
        $this->closeModal();
    }

    // Store product
    public function store()
    {
        $this->validate();

        Product::create([
            'product_name' => $this->product_name,
        ]);

        session()->flash('success', 'Product created successfully.');
        $this->resetInputFields();
    }

    // Edit product
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $this->product_id = $id;
        $this->product_name = $product->product_name;

        $this->openModal();
    }

    // Update product
    public function update()
    {
        $this->validate();

        if ($this->product_id) {
            $product = Product::find($this->product_id);
            $product->update([
                'product_name' => $this->product_name,
            ]);

            session()->flash('success', 'Product updated successfully.');
            $this->resetInputFields();
        }
    }

    // Delete product
    public function delete($id)
    {
        Product::find($id)->delete();
        session()->flash('error', 'Product deleted successfully.');
    }
}
