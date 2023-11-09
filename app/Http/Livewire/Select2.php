<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Select2 extends Component
{
    public $selected = '';
    public $items = [];

    public function addItems($items)
    {
        $this->items[] = $items;
    }
    public function render()
    {
        return view('livewire.select2');
    }
}