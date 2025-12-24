<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $user_id;
    public $name, $email, $password;
    public $roles = [];
    public $allRoles;

    public $isOpen = false;

    protected function rules()
    {
        return [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email',
            'password' => $this->user_id ? 'nullable|min:3' : 'required|min:3',
            'roles'    => 'required|array'
        ];
    }

    public function mount()
    {
        $this->allRoles = Role::all();
    }

    public function render()
    {
        return view('livewire.users.index', [
            'users' => User::latest()->paginate(10),
        ]);
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

    private function resetInput()
    {
        $this->user_id = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->roles = [];
    }

    public function store()
    {
        $this->validate();

        $user = User::create([
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => Hash::make($this->password),
        ]);

        $user->syncRoles($this->roles);

        session()->flash('success', 'User Created Successfully');

        $this->closeModal();
        $this->resetInput();
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        $this->user_id = $id;
        $this->name    = $user->name;
        $this->email   = $user->email;
        $this->roles   = $user->roles->pluck('name')->toArray();

        $this->openModal();
    }

    public function update()
    {
        $this->validate();

        $user = User::findOrFail($this->user_id);

        $data = [
            'name'  => $this->name,
            'email' => $this->email,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        $user->update($data);
        $user->syncRoles($this->roles);

        session()->flash('success', 'User Updated Successfully');

        $this->closeModal();
        $this->resetInput();
    }

    public function delete($id)
    {
        User::findOrFail($id)->delete();
        session()->flash('success', 'User Deleted Successfully');
    }
}
