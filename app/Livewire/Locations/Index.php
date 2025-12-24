<?php

namespace App\Livewire\Locations;

use App\Models\Location;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind'; // DaisyUI compatible

    public $name;
    public $locationId;
    public $isEdit = false;
    public $perPage = 30;

    protected $rules = [
        'name' => 'required|string|max:255',
    ];

    public function store()
    {
        $this->validate();

        Location::create([
            'name' => $this->name,
        ]);

        $this->resetInput();
        $this->resetPage(); // important

        session()->flash('success', 'Location added successfully');
    }

    public function edit($id)
    {
        $location = Location::findOrFail($id);

        $this->locationId = $id;
        $this->name = $location->name;
        $this->isEdit = true;
    }

    public function update()
    {
        $this->validate();

        Location::where('id', $this->locationId)->update([
            'name' => $this->name,
        ]);

        $this->resetInput();

        session()->flash('success', 'Location updated successfully');
    }

    public function delete($id)
    {
        Location::findOrFail($id)->delete();
        $this->resetPage(); // fix pagination bug

        session()->flash('success', 'Location deleted successfully');
    }

    public function resetInput()
    {
        $this->name = '';
        $this->locationId = null;
        $this->isEdit = false;
    }

    public function render()
    {
        return view('livewire.locations.index', [
            'locations' => Location::latest()->paginate($this->perPage),
        ]);
    }
}
