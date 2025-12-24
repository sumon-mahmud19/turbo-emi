<?php

namespace App\Livewire;

use Livewire\Component;

class Navbar extends Component
{
      public $currentRoute;

    public function mount()
    {
        $this->currentRoute = request()->routeIs() ? request()->route()->getName() : '';
    }

    public function setActive($route)
    {
        $this->currentRoute = $route;
    }

    public function render()
    {
        return view('livewire.navbar');
    }
}
