<?php

namespace App\Livewire\Roles;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\WithPagination;

class Index extends Component
{
   use WithPagination;

    public $role_id;
    public $name;
    public $permissions = [];
    public $allPermissions;

    public $isOpen = false;
    public $search = '';
    public $perPage = 10;

    protected $rules = [
        'name' => 'required|string|max:255|unique:roles,name',
        'permissions' => 'required|array|min:1',
    ];

    public function mount()
    {
        $this->allPermissions = Permission::all();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $roles = Role::where('name','like',"%{$this->search}%")
            ->orderBy('id','desc')
            ->paginate($this->perPage);

        return view('livewire.roles.index', compact('roles'));
    }

    public function create()
    {
        $this->resetInput();
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

    public function resetInput()
    {
        $this->role_id = null;
        $this->name = '';
        $this->permissions = [];
        $this->closeModal();
    }

    public function store()
    {
        $this->validate();

        $role = Role::create(['name' => $this->name]);
        $role->syncPermissions($this->permissions);

        session()->flash('success', 'Role created successfully.');
        $this->resetInput();
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $this->role_id = $id;
        $this->name = $role->name;
        $this->permissions = $role->permissions()->pluck('name')->toArray();
        $this->openModal();
    }

    public function update()
    {
        $role = Role::findOrFail($this->role_id);

        $this->validate([
            'name' => 'required|string|max:255|unique:roles,name,'.$role->id,
            'permissions' => 'required|array|min:1',
        ]);

        $role->update(['name' => $this->name]);
        $role->syncPermissions($this->permissions);

        session()->flash('success', 'Role updated successfully.');
        $this->resetInput();
    }

    public function delete($id)
    {
        Role::findOrFail($id)->delete();
        session()->flash('error', 'Role deleted successfully.');
    }
}
